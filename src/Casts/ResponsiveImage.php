<?php

namespace MOIREI\MediaLibrary\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Models\File;

class ResponsiveImage extends MediaCast implements CastsAttributes
{
  /**
   * Cast the given value.
   *
   * @param  \Illuminate\Database\Eloquent\Model  $model
   * @param  string  $key
   * @param  mixed  $value
   * @param  array  $attributes
   * @return object|null
   */
  public function get($model, $key, $value, $attributes)
  {
    // public files may have responsive images saved
    $value = is_string($value) ? json_decode($value) : $value;

    if (!$model->private and !empty($value)) return $value;

    return $value ? Api::getResponsivePublicUrl($model) : (object)Api::placeholderImages();
  }

  /**
   * Prepare the given value for storage.
   *
   * @param  \Illuminate\Database\Eloquent\Model  $model
   * @param  string  $key
   * @param  File|string|null  $value
   * @param  array  $attributes
   * @return string|null
   */
  public function set($model, $key, $value, $attributes)
  {
    return is_string($value) ? $value : json_encode($value);
  }
}
