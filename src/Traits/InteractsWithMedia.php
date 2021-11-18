<?php

namespace MOIREI\MediaLibrary\Traits;

use ArrayAccess;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use MOIREI\MediaLibrary\Models\File;
use MOIREI\MediaLibrary\Models\Attachment;
use MOIREI\MediaLibrary\Casts\MediaCast;
use MOIREI\MediaLibrary\Casts\MediaCastArray;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\AttachmentApi;

trait InteractsWithMedia
{
    protected array $queuedFiles = [];
    protected array $castAttachFiles = [];
    protected array $castArrayAttachFiles = [];

    /**
     * Get media casts array.
     *
     * @return array
     */
    protected function getMediaCasts()
    {
        $casts = $this->getCasts();
        $mediaClasses = [];

        foreach ($casts as $key => $cast) {
            $castType = $this->parseCasterClass($cast);
            if (!in_array($castType, static::$primitiveCastTypes)) {
                if (class_exists($castType)) {
                    if (is_subclass_of($castType, MediaCast::class)) {
                        $mediaClasses[$key] = $castType;
                    }
                }
            }
        }

        return $mediaClasses;
    }

    /**
     * Determine whether a value is Media Cast castable.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isMediaCastable($key)
    {
        return in_array($key, array_keys($this->getMediaCasts()));
    }

    /**
     * get rich text fields
     *
     * @return array
     */
    public function richTextFields()
    {
        return $this->richTextFields;
    }

    public static function bootInteractsWithMedia()
    {
        static::creating(function (Model $model) {
            foreach ($model->getMediaCasts() as $key => $class) {
                if (is_subclass_of($class, MediaCastArray::class)) array_push($model->castArrayAttachFiles, $model->$key);
                else array_push($model->castAttachFiles, $model->$key);
            }
        });

        static::created(function (Model $model) {
            // handle attachments
            if (property_exists($model, 'richTextFields')) {
                AttachmentApi::persistAttachments($model, true);
            }

            // handle media files
            $files = collect($model->queuedFiles)
                ->merge($model->castAttachFiles)
                ->merge(collect($model->castArrayAttachFiles)->flatten())
                ->filter(fn ($item) => !empty((array)$item))
                ->toArray();

            if (count($files) === 0) {
                return;
            }
            $model->attachFiles($files);
            $model->queuedFiles = [];
            $model->castAttachFiles = [];
            $model->castArrayAttachFiles = [];
        });

        static::updating(function (Model $model) {
            // handle attachments
            if (property_exists($model, 'richTextFields')) {
                AttachmentApi::persistAttachments($model);
            }

            // handle media files
            $attaching = [];
            $detaching = [];
            foreach ($model->getMediaCasts() as $key => $class) {
                if (!$model->originalIsEquivalent($key)) {
                    $value = $model->getAttribute($key);
                    $original = $model->getOriginal($key);
                    if (is_subclass_of($class, MediaCastArray::class)) {
                        $fileKey = Api::fileClassKey();
                        if (is_string($value)) $original = json_decode($value);
                        if (is_string($original)) $original = json_decode($original);
                        $adding = collect($value ?? [])->map(fn ($item) => is_string($item) ? $item : $item->$fileKey)->filter();
                        $deleting = collect($original ?? [])->map(fn ($item) => is_string($item) ? $item : $item->$fileKey)->diff($value);
                        $attaching = $adding->merge($attaching);
                        $detaching = $adding->merge($deleting);
                    } else {
                        if (!empty($value)) array_push($attaching, $value);
                        if (!empty($original)) array_push($detaching, $original);
                    }
                }
            }

            if (count($detaching) > 0) {
                $model->detachFiles($detaching);
            }
            if (count($attaching) > 0) {
                $model->attachFiles($attaching);
            }
        });

        static::deleting(function ($model) {
            $model->attachments->each->purge();
            $model->media()->detach();
        });
    }

    public function files(): MorphToMany
    {
        return $this->morphToMany(config('media-library.models.file'), 'fileable');
    }
    public function media(): MorphToMany
    {
        return $this->files();
    }
    public function attachments(): MorphMany
    {
        return $this->morphMany(
            Attachment::class,
            'attachable',
            'attachable_type',
            app(self::class)->getKeyType() === 'string'
                ? 'attachable_uuid'
                : 'attachable_id'
        );
    }

    public function setMediaAttribute(string | array | ArrayAccess | File $files)
    {
        if (!$this->exists) {
            $this->queuedFiles = $files;
            return;
        }

        $this->syncMedia($files);
    }

    public function getMediaType(string $type)
    {
        return $this->media->filter(fn (File $media) => $media->type === $type);
    }

    public function syncMedia(array | ArrayAccess $files): static
    {
        $className = config('media-library.models.file');

        $files = collect($className::find($files));

        $key  = Api::fileClassKey();
        $this->media()->sync($files->pluck($key)->toArray());

        return $this;
    }

    public function attachFiles(array | ArrayAccess $files): static
    {
        $key  = Api::fileClassKey();
        $ids = collect($files)->map(fn ($file) => is_string($file) ? $file : data_get($file, $key));

        $this->media()->syncWithoutDetaching($ids);

        return $this;
    }

    public function attachFile(string | File | array $file)
    {
        return $this->attachFiles(is_array($file) ? $file : [$file]);
    }

    public function syncFiles(array | ArrayAccess $files): static
    {
        $key  = Api::fileClassKey();
        $ids = collect($files)->map(fn ($file) => is_string($file) ? $file : data_get($file, $key));

        $this->media()->sync($ids);

        return $this;
    }

    public function detachFiles(array | ArrayAccess $files): static
    {
        $key  = Api::fileClassKey();
        $ids = collect($files)->map(fn ($file) => is_string($file) ? $file : data_get($file, $key));

        $this->media()->detach($ids);

        return $this;
    }

    public function detachFile(string | File | array $file): static
    {
        return $this->detachFiles(is_array($file) ? $file : [$file]);
    }

    public function clearMedia()
    {
        return $this->media()->detach();
    }
}
