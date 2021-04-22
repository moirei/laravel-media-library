<?php

namespace MOIREI\MediaLibrary;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use MOIREI\MediaLibrary\Models\Folder;
use MOIREI\MediaLibrary\Models\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SFile;

class Api
{
  /**
   *
   * @param string|File|array $files
   * @return Model|array
   */
  public static function getMedia(string | File | array $files)
  {
    $is_array = is_string($files);

    if (!$is_array) $files = [$files];

    if (!count($files)) {
      return $is_array ? [] : null;
    }

    $class = config('media-library.models.file');
    $files = collect($files)
      ->map(fn ($file) => is_string($file) ? $class::find($file) : $file)
      ->map(fn ($file) => $file->toArray());

    return $is_array ? $files : $files[0];
  }

  /**
   * Browse location and return all media items
   *
   * @param string $location
   * @param string|null $namespace
   * @param string|null $disk
   * @return Collection
   */
  public static function browse(string $location, string | null $namespace = null, string | null $disk = null): Collection
  {
    if (self::isRootLocation($location, $namespace)) {
      $folders = self::rootFolders();
      $files = self::rootFiles();

      $results = collect($folders)->merge($files);
    } else {
      $folder = self::resolveFolder($location, $namespace);

      $results = collect($folder->folders ?? [])
        ->merge($folder->files ?? []);
    }

    if ($disk) {
      $results = $results->where('disk', $disk);
    }

    return $results;
  }

  /**
   * Create a upload
   * Profile a file request input, public url or local path
   *
   * @param UploadedFile|string $item
   * @return Upload
   */
  public static function upload(UploadedFile | SFile | string $item): Upload
  {
    $uploadable = $item;
    $name = 'file';

    if (is_string($item)) {
      return Upload::fromUrl($item, $name);
    } elseif ($item instanceof SFile) {
      $uploadable = new UploadedFile($item->getRealPath(), $item->getFilename(), $item->getMimeType());
    }

    return new Upload($uploadable, $name);
  }

  /**
   * Resolve a given location to include config base
   *
   * @param string $location
   * @param string|null $namespace
   * @param bool $prepend_base
   * @return string
   */
  public static function resolveLocation(string $location, string | null $namespace = null, bool $prepend_base = true): string
  {
    if (!is_string($location)) $location = '';
    $base_path = $prepend_base ? config('media-library.folder', '') : '';

    return rtrim(self::joinPaths('/', $base_path, $namespace, $location), '/');
  }

  /**
   * Resolve folder from location string
   *
   * @param string $location
   * @param string|null $namespace
   * @param bool $create
   * @return Folder|null
   */
  public static function resolveFolder(string $location, string | null $namespace = null, bool $create = false): Folder | null
  {
    $path = self::resolveLocation($location, $namespace);
    $folder = Folder::firstWhere([
      'name' => basename($path),
      'location' => dirname($path),
    ]);

    if ($create and !$folder) {
      $folder = self::assertFolder($path, true);
    }

    return $folder;
  }

  /**
   * Get folder or create one if it doesnt exist
   *
   * @param string $path
   * @param bool $absolute if the path includes base Media Folder
   * @param string $disk
   * @return Folder|null
   */
  public static function assertFolder(string $path, bool $absolute = false, string $disk = null): Folder | null
  {
    $path = self::resolveLocation($path, null, !$absolute);
    $location = dirname($path);
    $param = [
      'name' => basename($path),
      'location' => $location,
    ];
    if (is_string($disk)) $param['disk'] = $disk;
    $folder = Folder::firstWhere($param);
    if ($folder) return $folder;

    $parent = self::resolveFolder($location);

    $data = [
      'name' => basename($path),
      // 'location' => $location,
    ];

    if ($parent) {
      $data['disk'] = is_string($disk) ? $disk : $parent->disk;
      $data['private'] = $parent->private;
      $folder = $parent->folders()->create($data);
    } else {
      $data['disk'] = is_string($disk) ? $disk : config('media-library.disk');
      $data['private'] = config('media-library.private');

      $segments = explode('/', trim($path, '/'));
      array_shift($segments); // remove base

      if (count($segments) == 0) {
        // this is a root path.
        return null;
      }
      if (count($segments) == 1) {
        return Folder::create($data);
      }

      $first = array_shift($segments);
      $last = array_pop($segments);

      $parent = self::assertFolder($first);
      foreach ($segments as $name) {
        $parent = $parent->folders()->create(
          array_merge($data, [
            'name' => $name,
            'location' => self::joinPaths($parent->location, $parent->name),
          ])
        );
      }
      $folder = $parent->folders()->create(
        array_merge($data, [
          'name' => $last,
          'location' => self::joinPaths($parent->location, $parent->name),
        ])
      );
    }

    return $folder;
  }

  /**
   * Get file's public url
   *
   * @param string|File $file
   * @param Carbon|int|null $ttl
   * @return string
   */
  public static function getPublicUrl(string | File $file, Carbon | int | null $ttl = null): string
  {
    $file = is_string($file) ? File::findOrFail($file) : $file;

    if ($file->private) {
      if (is_int($ttl)) $ttl = now()->addMinutes($ttl);
      elseif (is_null($ttl)) $ttl = now()->addMinutes(30);

      if (self::isSignableDisk($file->disk)) {
        $route_name = config('media-library.route.name', '');
        return URL::temporarySignedRoute(
          $route_name . 'file.signed',
          $ttl,
          ['file' => $file->id]
        );
      }
      return Storage::disk($file->disk)->temporaryUrl(
        self::joinPaths($file->location, $file->id, $file->filename),
        $ttl
      );
    }

    return Storage::disk($file->disk)->url(self::joinPaths($file->location, $file->id, $file->filename));
  }

  /**
   * Get file's responsive image public url
   *
   * @param string|File $file
   * @param Carbon|int|null $ttl
   * @return array
   */
  public static function getResponsivePublicUrl(string | File $file, Carbon | int | null $ttl = null): array
  {
    $file = is_string($file) ? File::findOrFail($file) : $file;
    $images = [
      'alt' => $file->name,
    ];

    foreach ($file->responsive as $item) {

      $filename = $item['name'] . ".$file->extension";
      $path = self::joinPaths($file->location, $file->id, $item['folder'], $filename);

      if ($file->private) {
        if (is_int($ttl)) $ttl = now()->addMinutes($ttl);
        elseif (is_null($ttl)) $ttl = now()->addMinutes(30);

        if (self::isSignableDisk($file->disk)) {
          $route_name = config('media-library.route.name', '');
          $url =  URL::temporarySignedRoute(
            $route_name . 'file.signed',
            $ttl,
            ['file' => $file->id]
          );
        } else {
          $url = Storage::disk($file->disk)->temporaryUrl($path, $ttl);
        }
      } else {
        $url = Storage::disk($file->disk)->url($path);
      }

      $images[$item['key']] = $url;
    }

    return $images;
  }

  /**
   * Get file's url
   */
  public static function getProtectedUrl(string | File $file): string
  {
    $file = is_string($file) ? File::findOrFail($file) : $file;
    $route_name = config('media-library.route.name', '');

    return route($route_name . 'file.protected', ['file' => $file->id]);
  }

  /**
   * Get file's internal url
   *
   * @param strine|File $file
   * @param Carbon|int|null $ttl
   * @return string
   */
  public static function getUrl(string | File $file, Carbon | int | null $ttl = null): string
  {
    $file = is_string($file) ? File::findOrFail($file) : $file;
    $route_name = config('media-library.route.name', '');

    if ($file->private) {
      if (is_int($ttl)) $ttl = now()->addMinutes($ttl);
      elseif (is_null($ttl)) $ttl = now()->addMinutes(30);

      return URL::temporarySignedRoute(
        $route_name . 'file.signed',
        $ttl,
        ['file' => $file->id]
      );
    }

    return route($route_name . 'file', ['file' => $file->id]);
  }

  /**
   * Get file's download url
   *
   * @param strine|File $file
   * @param Carbon|int|null $ttl
   * @return string
   */
  public static function getDowloadUrl(string | File $file, Carbon | int | null $ttl = null): string
  {
    $file = is_string($file) ? File::findOrFail($file) : $file;
    $route_name = config('media-library.route.name', '');

    if ($file->private) {
      if (is_int($ttl)) $ttl = now()->addMinutes($ttl);
      elseif (is_null($ttl)) $ttl = now()->addMinutes(30);

      return URL::signedRoute($route_name . 'download.signed', $ttl, ['file' => $file->id]);
    }

    return route($route_name . 'download', ['file' => $file->id]);
  }

  /**
   * Dynamically get image file's size
   */
  public static function getDynamicSize(string | File $file, int $width, int | null $height = null)
  {
    if (!is_numeric($width)) {
      throw new \Exception('Size required');
    }

    $height = is_numeric($height) ? $height : $width;

    $file = ($file instanceof File) ? $file : File::findOrFail($file);
    if ($file->type != 'image') {
      throw new \Exception('File is not an image type');
    }

    $manager = new ImageManager(['driver' => config('media-library.images.resize.driver')]);
    $content = Storage::disk($file->disk)->get(self::joinPaths($file->location, $file->id, $file->filename));
    $upSize = config('media-library.images.resize.original.2');

    return $manager->cache(function ($image) use ($content, $width, $height, $upSize) {
      $image->make($content)->resize($width, $height, function ($constraint) use ($width, $height, $upSize) {
        if (!$width or !$height) $constraint->aspectRatio();
        if (true !== $upSize) $constraint->upsize();
      })->stream(null, config('media-library.images.resize.quality'))->__toString();
    });
  }

  /**
   * Get a general placeholder images
   *
   * @return array
   */
  public static function placeholderImages(): array
  {
    $images = [
      'alt' => 'Placeholder image',
    ];

    foreach (config('media-library.images.resize.sizes') as $size => $v) {
      $images[$size] = config('media-library.images.placeholder');
    }

    return $images;
  }

  /**
   * Join paths
   *
   * TODO: use `DIRECTORY_SEPARATOR` instead of `/`??
   * @example joinPaths('my/paths/', '/are/', 'a/r/g/u/m/e/n/t/s/')
   * @return string
   */
  public static function joinPaths()
  {
    $paths = [];

    foreach (func_get_args() as $arg) {
      if ($arg !== '') {
        $paths[] = $arg;
      }
    }

    return preg_replace('#/+#', '/', join('/', $paths));
  }

  /**
   * Get visbilitye
   *
   * @param bool $private
   * @return string
   */
  public static function visibility(bool $private): string
  {
    return $private ? 'private' : 'public';
  }

  /**
   * Check if disk is route signable
   *
   * @param string $disk
   * @return bool
   */
  public static function isSignableDisk(string $disk): bool
  {
    return $disk == 'local';
  }

  /**
   * Check if item is a file or folder
   *
   * @param Model $item
   * @return bool
   */
  public static function isFolder(Model $item): bool
  {
    return config('media-library.models.folder') == get_class($item);
  }

  /**
   * Check whether the location and namespace is root
   *
   * @param string $location
   * @param string|null $namespace
   * @return bool
   */
  public static function isRootLocation(string $location, string | null $namespace = null): bool
  {
    $path = self::resolveLocation($location, $namespace);
    $base_path = rtrim(self::joinPaths('/', config('media-library.folder', '')));

    return $base_path == $path;
  }

  /**
   * Get all root folders
   *
   * @return Folder
   */
  public static function rootFolders(): Collection
  {
    $base_path = rtrim(self::joinPaths('/', config('media-library.folder', '')));
    $folderClass = config('media-library.models.folder');
    return $folderClass::where('location', $base_path)->get();
  }

  /**
   * Get all root files
   *
   * @return File
   */
  public static function rootFiles(): Collection
  {
    $base_path = rtrim(self::joinPaths('/', config('media-library.folder', '')));
    $fileClass = config('media-library.models.file');
    return $fileClass::where('location', $base_path)->get();
  }

  /**
   * Set visibility
   *
   * @param Model $item
   * @return void
   */
  public static function setVisibility(Model $item): void
  {
    if (self::isFolder($item)) {
      $path = Api::joinPaths($item->location, $item->name);
    } else {
      $path = Api::joinPaths($item->location, $item->id);
    }

    Storage::disk($item->disk)->setVisibility($path, Api::visibility($item->private));
  }

  /**
   * Get file class key
   *
   * @return string
   */
  public static function fileClassKey(): string
  {
    $class = config('media-library.models.file');
    return app($class)->getKeyName();
  }

  /**
   * Get folder class key
   *
   * @return string
   */
  public static function folderClassKey(): string
  {
    $class = config('media-library.models.folder');
    return app($class)->getKeyName();
  }

  /**
   * Move a file to a new lcation
   *
   * @param Attachment $attachment
   * @param Model|string $location
   * @param string|null $namespace
   * @return void
   */
  public static function move(Model $file, Model | string $location, string | null $namespace = null)
  {
    $is_root = false;

    if (is_string($location)) {
      $is_root = Api::isRootLocation($location, $namespace);
      if (!$is_root) {
        $location = Api::resolveFolder($location, $namespace, true);
      }
    }

    if ($is_root) {
      $new_location = Api::resolveLocation($location, $namespace);
    } else {
      $new_location = Api::joinPaths($location->location, $location->name);
    }

    $old_path = Api::joinPaths($file->location, $file->id);
    $path = Api::joinPaths($new_location, $file->id);

    if (Storage::disk($file->disk)->exists($path)) {
      return;
    }

    Storage::disk($file->disk)->move($old_path, $path);

    $file->__location = $new_location;

    if ($is_root) {
      $file->folder()->dissociate();
    } else {
      $file->folder()->associate($location);
    }

    $file->save();
  }

  /**
   * Move a folder to a new lcation
   *
   * @param Attachment $attachment
   * @param Model|string $location
   * @param string|null $namespace
   * @return void
   */
  public static function moveFolder(Model $folder, Model | string $location, string | null $namespace = null)
  {
    $is_root = false;

    if (is_string($location)) {
      $is_root = Api::isRootLocation($location, $namespace);
      if (!$is_root) {
        $location = Api::resolveFolder($location, $namespace, true);
      }
    }

    if ($is_root) {
      $new_location = Api::resolveLocation($location, $namespace);
    } else {
      $new_location = Api::joinPaths($location->location, $location->name);
    }

    $old_path = Api::joinPaths($folder->location, $folder->name);
    $path = Api::joinPaths($new_location, $folder->name);

    if (Storage::disk($folder->disk)->exists($path)) {
      return;
    }

    Storage::disk($folder->disk)->move($old_path, $path);

    // update files and child folders location
    self::updateFolderLocation($folder, $new_location);

    // re-assign parent
    if ($is_root) {
      $folder->parent()->dissociate();
    } else {
      $folder->parent()->associate($location);
    }
    $folder->save();
  }

  /**
   * TODO: consider creating jobs for deeply nested subdirectories
   *
   * @param Model folder
   * @param string $location
   */
  protected static function updateFolderLocation(Model $folder, string $location)
  {
    $folder->__location = $location;
    $folder->save();

    // Child folders
    $folder->folders->each(function ($folder) use ($location) {
      $folder->__location = $location;
      $folder->save();
      $folder->folders->each(fn ($folder) => self::updateFolderLocation($folder, $location));
    });

    // Files
    $file_location = self::joinPaths($location, $folder->name);
    $folder->files->each(function ($file) use ($file_location) {
      $file->__location = $file_location;
      $file->save();
    });
  }

  /**
   * Check if string is UUID
   *
   * @param string $str
   * @return bool
   */
  public static function isUuid(string $str)
  {
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $str) === 1;
  }
}
