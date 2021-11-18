<?php

namespace MOIREI\MediaLibrary\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Models\File;

class MediaUrl extends MediaCast implements CastsAttributes
{
    /**
     * Cast the given value.
     * Private file urls' dynamic public url is resolved in ResponsiveImage::class cast.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return object|null
     */
    public function get($model, $key, $value, $attributes)
    {
        $fileClass = config('media-library.models.file');
        $file = is_string($value) ? $fileClass::find($value) : $value;
        if (!$file) return [];
        $key = Api::fileClassKey();

        return [
            'url' => Api::getPublicUrl($file),
            $key => $file->id,
        ];
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
        if (is_string($value)) return $value;
        $key = Api::fileClassKey();
        return data_get($value, $key);
    }
}
