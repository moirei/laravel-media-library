<?php

namespace MOIREI\MediaLibrary\Traits;

use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Models\SharedContent;

trait MediaItem
{
  /**
   * Public file fields
   * @var array
   */
  protected $public_file_fields = [
    'id', 'name', 'description',
    'image', 'extension',
    'private', 'disk', 'size', 'location',
  ];

  /**
   * Public folder fields
   * @var array
   */
  protected $public_folder_fields = [
    'id', 'name', 'description',
    'private', 'disk', 'size', 'location',
    'folders', 'files',
  ];

  /**
   * Share this file or folder
   *
   * @return SharedContent
   */
  public function share(): SharedContent
  {
    return SharedContent::make($this);
  }

  /**
   * Get all of the file's shares.
   */
  public function shares(): MorphMany
  {
    return $this->morphMany(
      SharedContent::class,
      'shareable',
      'shareable_type',
      app(self::class)->getKeyType() === 'string'
        ? 'shareable_uuid'
        : 'shareable_id'
    );
  }

  public function getPublicUrlAttribute()
  {
    return $this->is_folder ? null : Api::getPublicUrl($this);
  }

  public function getUrlAttribute()
  {
    return $this->is_folder ? null : Api::getUrl($this);
  }

  public function getProtectedUrlAttribute()
  {
    return $this->is_folder ? null : Api::getProtectedUrl($this);
  }

  public function getTypeAttribute()
  {
    return $this->attributes['type'] ?? ($this->is_folder ? 'folder' : 'file');
  }

  public function getPathAttribute()
  {
    return $this->type == 'folder' ? Api::joinPaths($this->location, $this->name) : Api::joinPaths($this->location, $this->id, $this->filename);
  }

  public function getIsFolderAttribute()
  {
    return Api::isFolder($this);
  }

  public function toArray()
  {
    $this->setVisible(Api::isFolder($this) ? $this->public_folder_fields : $this->public_file_fields);
    $array = parent::toArray();
    $array['type'] = $this->getTypeAttribute();
    $array['url'] = $this->getPublicUrlAttribute();

    return $array;
  }

  /**
   * Convert the model to its string representation.
   *
   * @return string
   */
  public function __toString()
  {
    return Api::getPublicUrl($this);
  }
}
