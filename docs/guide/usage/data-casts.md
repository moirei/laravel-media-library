# Data Casts

This package provides a number of casts to help interface media files to native array or object types. Casts also allows you to use media files at attribute level rather than associating them to your entire model. The stored value in your database column is the file ID or an array on IDs.

Ascribing the `MOIREI\MediaLibrary\Traits\InteractsWithMedia` trait will automatically detect and associate files to the model.



**Battery-included Casts**

| Cast                                          | Description                                                  |
| --------------------------------------------- | ------------------------------------------------------------ |
| `MOIREI\MediaLibrary\Casts\MediaFile`         | Casts files to view/frontend ready key-value array. See [File](/data#file). |
| `MOIREI\MediaLibrary\Casts\MediaFiles`        | An array of `MOIREI\MediaLibrary\Casts\MediaFile`            |
| `MOIREI\MediaLibrary\Casts\MediaImage`        | Key-value array of display image links of the file. Empty for non-image files with no thumbnail generated |
| `MOIREI\MediaLibrary\Casts\MediaImages`       | An array of `MOIREI\MediaLibrary\Casts\MediaImage`           |
| `MOIREI\MediaLibrary\Casts\MediaObjectImage`  | An `object` version of `MOIREI\MediaLibrary\Casts\MediaImage` |
| `MOIREI\MediaLibrary\Casts\MediaObjectImages` | An array of `MOIREI\MediaLibrary\Casts\MediaObjectImage`     |
| `MOIREI\MediaLibrary\Casts\MediaUrl`          | An array containing a `url` key to the file. A temporal or signed url is returned for private files. |
| `MOIREI\MediaLibrary\Casts\MediaUrls`         | An array of `MOIREI\MediaLibrary\Casts\MediaUrl`             |


## Accessors

You can further defined the following to help access your model's media contents.

```php

public function getImagesAttribute()
{
  return $this->media()->ofType('image')->get();
}

public function getAudiosAttribute()
{
  return $this->media()->ofType('audio')->get();
}

public function getVideosAttribute()
{
  return $this->media()->ofType('video')->get();
}
```

