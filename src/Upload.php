<?php

namespace MOIREI\MediaLibrary;

use Exception;
use Illuminate\Database\Eloquent\Model;
use MOIREI\MediaLibrary\Models\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use MOIREI\MediaLibrary\Exceptions\FileValidationException;
use MOIREI\MediaLibrary\Models\Folder;

class Upload
{
  protected UploadedFile $upload;
  protected string $name;
  protected string | null $id = null;
  protected string $filename;
  protected string $clientOriginalName;
  protected string $disk;
  protected string $mimetype;
  protected string $type;
  protected string $extension;
  protected bool $private;
  protected string $location;
  protected string|null $namespace = null;
  protected int $size = 0;
  protected int $original_size = 0;
  protected array $image = [];
  protected array $responsive = [];
  protected $folder = null;

  protected array $readable = [
    'name', 'filename', 'disk',
    'mimetype', 'type', 'extension',
    'private', 'location', 'namespace',
  ];

  public function __construct(UploadedFile $upload, string | null $name = null)
  {
    $this->upload = $upload;
    $this->clientOriginalName = $upload->getClientOriginalName();
    $this->name = $name ?? pathinfo($this->clientOriginalName, PATHINFO_FILENAME);
    $this->filename = $this->clientOriginalName;
    $this->disk = config('media-library.disk', 'local');
    $this->mimetype = strtolower($upload->getMimeType());
    $this->extension = strtolower($upload->getClientOriginalExtension());
    $this->type = $this->getType($this->mimetype, $this->extension);
    $this->private = config('media-library.private', false);
    $this->size = $upload->getSize();
    $this->original_size = $upload->getSize();
  }

  /**
   * Read only variables
   * @return mixed
   */
  public function __get($name)
  {
    if (!in_array($name, $this->readable)) return null;

    return $this->$name;
  }

  /**
   * Clean filename; replace spaces and special characters
   *
   * @param bool $special_characters allow special characters
   * @return self
   */
  public function cleanFilename(bool $special_characters = false)
  {
    $this->filename = str_replace(' ', config('media-library.clean_file_name.replace_spaces', '-'), $this->filename);
    if (!$special_characters)
      $this->filename = preg_replace('/[^A-Za-z0-9\-]/', '', pathinfo($this->filename, PATHINFO_FILENAME)) . ".$this->extension";

    return $this;
  }

  /**
   * Set file location
   *
   * @param string $location
   * @param string|null $namespace
   * @return self
   */
  public function location(string $location, string | null $namespace = null)
  {
    $this->location = $location;
    $this->namespace = $namespace ?? '';
    $this->folder = null;

    return $this;
  }

  /**
   * Set file location from folder
   *
   * @param Folder $folder
   * @return self
   */
  public function folder(Folder $folder)
  {
    $this->location = Api::joinPaths($folder->location, $folder->name);
    $this->folder = $folder;

    return $this;
  }

  /**
   * Set upload file name
   *
   * @param string $name
   * @return self
   */
  public function name(string $name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Set upload file privacy
   *
   * @param string $private
   * @return self
   */
  public function private(bool $private = true)
  {
    $this->private = $private;
    return $this;
  }

  /**
   * Set upload disk
   *
   * @param string $disk
   * @return self
   */
  public function disk(string $disk)
  {
    $this->disk = $disk;
    return $this;
  }

  /**
   * Set upload file extension
   *
   * @param string $extension
   * @return self
   */
  public function extension(string $extension)
  {
    $this->extension = strtolower($extension);
    return $this;
  }

  /**
   * Check file size is below max limit
   *
   * @param int|null $size, bool == throw error
   * @param bool $throw
   * @throws Exception
   * @return bool
   */
  public function checkSize(int | null | bool $size = null, bool $throw = false): bool
  {
    if (is_bool($size)) {
      $throw = $size;
      $size = null;
    } elseif (!is_int($size)) {
      $size = config('media-library.max_size.' . $this->type);
    }

    if (!$size) $size = config('media-library.max_size.*');

    $ok = $size and $size >= $this->size;

    if (!$ok and $throw) {
      throw FileValidationException::limitExceeded();
    }

    return $ok;
  }

  /**
   * Check if file is of allowed types
   *
   * @param bool $throw
   * @throws Exception
   * @return bool
   */
  public function checkType(bool|array $types = false, bool|array $throw = false): bool
  {
    $ok = false;
    if (is_bool($types)) {
      $throw = $types;
      $types = config('media-library.types');
    }
    if (!is_array($types)) {
      $ok = false;
    } else {
      $ok = $this->isInTypes($types);
    }

    if (!$ok and $throw) {
      throw FileValidationException::forbiddenFormat();
    }

    return $ok;
  }

  /**
   * Check if file is an image
   * @return bool
   */
  public function isImage(): bool
  {
    return $this->type == 'image';
  }

  /**
   * Check if type/extension is in mime types
   *
   * @param array $types
   * @return bool
   */
  public function isInTypes(array $types): bool
  {
    foreach ($types as $label => $array) {
      if (
        ($this->type == $label) and
        (in_array($this->extension, $array) or in_array('*', $array))
      ) {
        return true;
        break;
      }
    }

    return false;
  }

  /**
   * Save image
   *
   * @return Model
   */
  public function save(): Model
  {
    if ($this->folder) {
      $folder = $this->folder;
    } else {
      $folder = Api::resolveFolder($this->location, $this->namespace, true);
    }
    if ($folder) {
      $this->location = Api::joinPaths($folder->location, $folder->name);
    } else {
      $this->location = Api::resolveLocation($this->location, $this->namespace);
    }

    if (!$this->id) $this->id = (string) Str::uuid();
    $path = Api::joinPaths($this->location, $this->id);

    // Ensure directory exists
    Storage::disk($this->disk)->makeDirectory($path);

    // Save file(s)
    $this->images();
    Storage::disk($this->disk)->put(
      Api::joinPaths($path, $this->filename),
      $this->data(),
      Api::visibility($this->private),
    );

    $data = [
      'id' => $this->id,
      'location' => $this->location,
      'name' => $this->name,
      'filename' => $this->filename,
      'disk' => $this->disk,
      'mimetype' => $this->mimetype,
      'type' => $this->type,
      'extension' => $this->extension,
      'private' => $this->private,
      'image' => $this->image,
      'responsive' => $this->responsive,
      'size' => $this->size,
      'original_size' => $this->original_size,
    ];

    if ($folder) {
      return $folder->files()->create($data);
    }

    return File::create($data);
  }

  /**
   * Get saveable file data
   */
  public function data()
  {
    $width  = config('media-library.images.resize.original.0');
    $height = config('media-library.images.resize.original.1');
    $up_size = config('media-library.images.resize.original.2');

    if (!is_int($width))  $width = null;
    if (!is_int($height)) $height = null;

    $data = file_get_contents($this->upload);

    if (!$this->isImage()) {
      return $data;
    }

    if (config('media-library.images.optimize')) {
      $data = $this->optimizeImageData($data);
    }

    if (!$this->canResize($width, $height)) {
      return $data;
    }

    $manager = new ImageManager(['driver' => config('media-library.images.resize.driver')]);
    $image = $manager->make($this->upload);

    $data = $image->resize($width, $height, function ($constraint) use ($width, $height, $up_size) {
      if (!$width or !$height) $constraint->aspectRatio();
      if (true !== $up_size) $constraint->upSize();
    })->stream(null, config('media-library.images.resize.quality'))->__toString();

    $this->original_size = strlen($data);
    $this->size += strlen($data);

    return $data;
  }

  /**
   * Save images
   */
  public function images()
  {
    if ($this->isImage()) {
      return $this->saveSizes(config('media-library.images.resize.sizes'));
    }

    $types = config('media-library.file_thumbs.types');
    if (!$this->isInTypes($types)) {
      return;
    }

    return $this->saveSizes(config('media-library.file_thumbs.sizes'));
  }

  /**
   * Save image sizes
   */
  public function saveSizes(array $sizes)
  {
    if (!$this->isImage()) {
      return $this->image = [];
    }

    if (count($this->responsive)) {
      // can only call once
      return;
    }

    $size = 0;

    if (!$this->id) $this->id = (string) Str::uuid();
    $folder = '/';
    $path = Api::joinPaths($this->location, $this->id, $folder);
    $responsive = [];
    $original_name = pathinfo($this->clientOriginalName, PATHINFO_FILENAME);

    $manager = new ImageManager(['driver' => config('media-library.images.resize.driver')]);
    $quality = config('media-library.images.resize.quality');

    foreach ($sizes as $key => $values) {

      $name = "$original_name-$key";
      $width  = data_get($values, '0');
      $height  = data_get($values, '1');
      $up_size  = data_get($values, '2');
      // $up_wh  = data_get($values, '3');

      array_push($responsive, [
        'folder' => $folder,
        'name' => $name,
        'key' => $key,
        'width' => $width,
        'height' => $height,
      ]);

      $fn = ($width and $height) ? 'fit' : 'resize';
      $data = (string)$manager->make($this->upload)->$fn($width, $height, function ($constraint) use ($width, $height, $up_size) {
        if (!$width or !$height) $constraint->aspectRatio();
        if ($up_size !== true) $constraint->upsize();
      })->stream(null, $quality);

      $size += strlen($data);

      Storage::disk($this->disk)->put(Api::joinPaths($path, "$name.$this->extension"), $data);
    }

    $this->responsive = $responsive;
    $this->size += $size;
  }

  /**
   * Check if (image) file can be resized against width and height
   */
  public function canResize($width, $height)
  {
    if (!$this->isImage()) return false;

    list($w, $h) = getimagesize($this->upload);

    $upWH = config('media-library.images.resize.original.3');

    if (
      !is_numeric($w) or !is_numeric($h) or
      !$upWH and
      (!$width or $width > $w) and
      (!$height or $height > $h)
    ) {
      return true;
    }

    return false;
  }

  /**
   * Get file type
   * @param string mimetype
   * @return string
   */
  public function getType(string $mimetype, string $extension = null): string
  {
    [$type, $ext] = explode('/', "$mimetype/"); // predense with '/' to ensure element in array destruction
    $extension = $extension ? $extension : $ext;

    return ($type == 'application' and in_array($extension, config('media-library.doc_types'))) ? 'docs' : $type;
  }

  /**
   * Optimize image data
   *
   * @param mixed
   * @return mixed
   */
  protected function optimizeImageData($data)
  {
    //
    return $data;
  }

  /**
   * Upload file from a public url or local path
   *
   * @param string $url
   * @param string $name
   * @return Upload
   */
  public static function fromUrl(string $url, string $name = null): Upload
  {
    //
    if (Str::startsWith($url, ['http://', 'https://'])) {
      if (!$stream = @fopen($url, 'r')) {
        throw FileValidationException::unreachableUrl($url);
      }
      $temp_file = tempnam(sys_get_temp_dir(), 'media-library');
      file_put_contents($temp_file, $stream);
      // file_put_contents($temp_file, file_get_contents($url));
    } else {
      // local path
      $temp_file = tempnam(sys_get_temp_dir(), 'media-library');
      file_put_contents($temp_file, file_get_contents($url));
    }

    $filename = basename(parse_url($url, PHP_URL_PATH));
    $filename = urldecode($filename);
    $mimetype = mime_content_type($temp_file);

    if ($filename !== '' and is_null($name)) {
      $name = pathinfo($filename, PATHINFO_FILENAME);
    } else {
      // $filename = $name;
    }

    $uploadable = new UploadedFile($temp_file, $filename, $mimetype);

    return new self($uploadable, $name);
  }
}
