<?php

namespace MOIREI\MediaLibrary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use MOIREI\MediaLibrary\Casts\ResponsiveImage;
use MOIREI\MediaLibrary\Traits\MediaItem;
use MOIREI\MediaLibrary\Traits\UsesUuid;

class File extends Model
{
    use UsesUuid, SoftDeletes, MediaItem;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'meta' => AsCollection::class,
        'image' => ResponsiveImage::class,
        'responsive' => 'json',
        'private' => 'boolean',
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['folder'];

    /**
     * Check if the file is of type
     *
     * @param string $type
     * @return bool
     */
    public function ofType(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * Get the folder that the file belongs to.
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Models that are associated with this file
     *
     * @return Collection
     */
    public function getModelsAttribute()
    {
        return $this->fileables->map(function ($fileable) {
            $class = '\\' . $fileable->fileable_type;
            return $class::find($fileable->fileable_id);
        });
    }

    /**
     * Filables of models that are associated with this file
     *
     * @return Collection
     */
    public function fileables()
    {
        return $this->hasMany(Fileable::class);
    }

    /**
     * Scope query to only include media files of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope query to only include media files of a given extension.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $$extension
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfExtension($query, $extension)
    {
        return $query->where('extension', $extension);
    }

    /**
     * Scope query to only include media files of a given disk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisk($query, $disk)
    {
        return $query->where('disk', $disk);
    }

    /**
     * Prune the stale (lonely) files from the system.
     *
     * @param Carbon|int|null $age
     * @return void
     */
    public function pruneStale(Carbon | int | null $age = null)
    {
        if (is_int($age)) {
            $age = now()->subDays($age);
        }

        $query = self::withTrashed()
            ->whereDoesntHave('fileables');

        if (!is_null($age)) {
            $query = $query->where('created_at', '<=', $age);
        }

        $query->chunk(100, function ($files) {
            $files->each->forceDelete();
        });
    }
}
