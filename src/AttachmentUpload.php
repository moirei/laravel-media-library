<?php

namespace MOIREI\MediaLibrary;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use MOIREI\MediaLibrary\Models\Attachment;
use Illuminate\Support\Str;
use MOIREI\MediaLibrary\Exceptions\AttachmentLocationException;
use MOIREI\MediaLibrary\Models\Folder;

class AttachmentUpload extends Upload
{

  /**
   * Check if file is of allowed types
   *
   * @param bool $throw
   * @throws Exception
   * @return bool
   */
  public function checkType(bool|array $types = false, bool|array $throw = false): bool
  {
    if (is_bool($types)) {
      $throw = $types;
      $types = config('media-library.attachments.types');
    }

    return parent::checkType($types, $throw);
  }

  /**
   * Save image
   *
   * @return Model
   */
  public function save(): Model
  {
    $attachment = Str::uuid()->toString() . '-' . $this->filename; // prepend with uuid for uniqueness
    $path = Api::joinPaths($this->location, $attachment);
    Storage::disk($this->disk)->put(
      $path,
      $this->data(),
    );

    $url = Storage::disk($this->disk)->url($path);

    return Attachment::create([
      'attachment' => $attachment,
      'alt' => $this->name,
      'location' => $this->location,
      'url' => $url,
      'disk' => $this->disk,
    ]);
  }

  /**
   * Get saveable file data
   */
  public function data()
  {
    $width  = config('media-library.attachments.resize.0');
    $height = config('media-library.attachments.resize.1');
    $up_size = config('media-library.attachments.resize.2');

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

  public function folder(Folder $folder)
  {
    throw new AttachmentLocationException;
  }
}
