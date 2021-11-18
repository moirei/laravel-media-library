<?php

namespace MOIREI\MediaLibrary\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Models\File;

class MediaImages extends MediaCastArray implements CastsAttributes
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
        $value = json_decode($value, true);
        $fileClass = config('media-library.models.file');
        $key = Api::fileClassKey();

        return collect(is_array($value) ? $fileClass::find($value) : [])->map(function ($file) use ($key) {
            // private file urls' dynamic public url is resolved in ResponsiveImage::class cast
            if (!$file) return [];
            $key = Api::fileClassKey();
            $image = (array)$file->image;
            $image[$key] = $file->id;
            return $image;
        });
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
        $key = Api::fileClassKey();
        $value = collect($value ?? [])->map(fn ($item) => is_string($item) ? $item : data_get($item, $key))->toArray();

        return json_encode($value);
    }
}
