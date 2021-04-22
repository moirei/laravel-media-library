<?php

namespace MOIREI\MediaLibrary\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use MOIREI\MediaLibrary\Api;

class MediaFiles extends MediaCastArray implements CastsAttributes
{
  /**
   * Cast the given value.
   *
   * @param  \Illuminate\Database\Eloquent\Model  $model
   * @param  string  $key
   * @param  mixed  $value
   * @param  array  $attributes
   * @return Collection
   */
  public function get($model, $key, $value, $attributes)
  {
    $value = json_decode($value, true);
    $fileClass = config('media-library.models.file');
    return collect(is_array($value) ? $fileClass::find($value) : []);
  }

  /**
   * Prepare the given value for storage.
   *
   * @param  \Illuminate\Database\Eloquent\Model  $model
   * @param  string  $key
   * @param  array  $value
   * @param  array  $attributes
   * @return array
   */
  public function set($model, $key, $value, $attributes)
  {
    $key = Api::fileClassKey();
    $value = collect($value ?? [])->map(fn ($item) => is_string($item) ? $item : data_get($item, $key))->toArray();

    return json_encode($value);
  }
}
