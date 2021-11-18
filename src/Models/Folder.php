<?php

namespace MOIREI\MediaLibrary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use MOIREI\MediaLibrary\Casts\ResponsiveImage;
use MOIREI\MediaLibrary\Traits\MediaItem;
use MOIREI\MediaLibrary\Traits\UsesUuid;

class Folder extends Model
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
     protected $touches = ['parent'];

     /**
      * The relationships that should always be loaded.
      *
      * @var array
      */
     protected $with = [
          'folders',
          'files',
     ];

     /**
      * Get the parent folder that the folder belongs to.
      */
     public function parent(): BelongsTo
     {
          return $this->belongsTo(Folder::class, 'parent_id');
     }

     /**
      * The child folders of the folder.
      */
     public function folders(): HasMany
     {
          return $this->hasMany(Folder::class, 'parent_id');
     }

     /**
      * The files of the folder.
      */
     public function files(): HasMany
     {
          return $this->hasMany(File::class);
     }

     /**
      * The files of all subfolders.
      */
     public function child_files()
     {
          return $this->hasManyThrough(
               File::class,
               Folder::class,
               'parent_id', // Foreign key on the folders table...
               'folder_id',  // Foreign key on the files table...
               'id',         // Local key on the shared_contents table...
               'id'          // Local key on the folders table...
          );
     }

     /**
      * Get all files associated to the folder
      */
     public function getAllFilesAttribute()
     {
          return collect($this->files)->merge($this->child_files());
     }

     /**
      * Scope query to only include media files of a given disk.
      *
      * @param  \Illuminate\Database\Eloquent\Builder  $query
      * @param  mixed  $disk
      * @return \Illuminate\Database\Eloquent\Builder
      */
     public function scopeDisk($query, $disk)
     {
          return $query->where('disk', $disk);
     }

     /**
      * Prune the stale (empty) folders from the system.
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
               ->whereDoesntHave('folders')
               ->whereDoesntHave('files');

          if (!is_null($age)) {
               $query = $query->where('created_at', '<=', $age);
          }

          $query->chunk(100, function ($folders) {
               $folders->each->forceDelete();
          });
     }
}
