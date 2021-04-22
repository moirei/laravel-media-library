<?php

namespace MOIREI\MediaLibrary\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Exceptions\MediaLocationUpdateException;

class FolderObserver
{
  public function creating(Model $folder)
  {
    $parent = Api::resolveFolder($folder->location ?? '/');

    if ($parent) {
      $folder->location = Api::joinPaths($parent->location, $parent->name);
    } elseif (!$folder->location) {
      $folder->location = Api::resolveLocation('/');
    }

    if (!isset($folder->private)) {
      $folder->private = $parent ? $parent->private : config('media-library.private');
    }

    if (!isset($folder->disk)) {
      $folder->disk = $parent ? $parent->disk : config('media-library.disk');
    }

    if (Schema::hasColumn($folder->getTable(), 'image')) {
      if (!$folder->private and !empty($folder->responsive)) {
        $folder->image = Api::getResponsivePublicUrl($folder);
      }
    }

    Storage::disk($folder->disk)->makeDirectory(Api::joinPaths($folder->location, $folder->name));
  }

  public function updating(Model $folder)
  {
    if ($folder->isDirty('location')) {
      throw new MediaLocationUpdateException('Folder');
    }
    if ($folder->__location) {
      $folder->location = $folder->__location;
      unset($folder->__location);
    }

    if ($folder->isDirty('private')) {
      $path = Api::joinPaths($folder->location, $folder->name);
      Storage::disk($folder->disk)->setVisibility($path, Api::visibility($folder->private));

      // update child folders privacy
      $folder->folders()->update([
        'private' => $folder->private,
      ]);

      // update files privacy
      $folder->files()->update([
        'private' => $folder->private,
      ]);
    }
  }

  public function deleted(Model $folder)
  {
    if (
      in_array(SoftDeletes::class, class_uses_recursive($folder)) &&
      !$folder->isForceDeleting()
    ) {

      // only required if self-ref relation is not at DB level
      $folder->folders()->delete();
      $folder->shares()->delete();
      return;
    }

    // only required if self-ref relation is not at DB level
    $folder->folders()->forceDelete();
    $folder->shares()->forceDelete();

    Storage::disk($folder->disk)->deleteDirectory(Api::joinPaths($folder->location, $folder->name));
  }
}
