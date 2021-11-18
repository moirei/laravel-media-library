<?php

namespace MOIREI\MediaLibrary\Traits;

use MOIREI\MediaLibrary\Models\File;

trait HasMediaAttributes
{
    // /**
    //  * @param string $field
    //  * @return Media
    //  */
    // public function __get(string $field)
    // {
    //   $attributes = data_get($this, 'media', []);

    //   if (!count($attributes) || !in_array($field, $attributes))
    //     return parent::__get($field);

    //   return Media::from($this->attributes[$field]);
    // }

    /**
     * @param string $field
     * @param File|string $value
     * @return mixed
     */
    public function __set(string $field, File | string $value)
    {
        $attributes = data_get($this, 'media', []);

        if (!count($attributes) || !in_array($field, $attributes))
            return parent::__set($field, $value);

        return $this->attributes[$field] = is_string($value) ? $value : $value->id;
    }

    /**
     * Get array serialisation
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        $attributes = data_get($this, 'media', []);
        foreach ($attributes as $attribute) {
            $array[$attribute] = $this->$attribute;
        }
    }

    /**
     * Get json serialisation
     * @return json
     */
    public function toJson()
    {
        $json = parent::toJson();
        $attributes = data_get($this, 'media', []);
        foreach ($attributes as $attribute) {
            $value = $this->$attribute;
            $json->$attribute = is_array($value) ? json_encode($value) : $value;
        }
    }
}
