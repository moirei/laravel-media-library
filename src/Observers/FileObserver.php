<?php

namespace MOIREI\MediaLibrary\Observers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Exceptions\MediaLocationUpdateException;

class FileObserver
{
  public function creating(Model $file)
  {
    if (Schema::hasColumn($file->getTable(), 'image')) {
      if (!$file->private and !empty($file->responsive)) {
        $file->image = Api::getResponsivePublicUrl($file);
      }
    }
  }

  public function updating(Model $file)
  {
    $path = Api::joinPaths($file->location, $file->id);

    if ($file->isDirty('location')) {
      throw new MediaLocationUpdateException('File');
    }

    if ($file->__location) {
      $file->location = $file->__location;
      unset($file->__location);

      if (Schema::hasColumn($file->getTable(), 'image')) {
        if (!$file->private and !empty($file->responsive)) {
          $file->image = Api::getResponsivePublicUrl($file);
        }
      }
    }

    if ($file->isDirty('private')) {
      Storage::disk($file->disk)->setVisibility($path, Api::visibility($file->private));
    }
  }

  public function deleted(Model $file)
  {
    if (
      in_array(SoftDeletes::class, class_uses_recursive($file)) &&
      !$file->isForceDeleting()
    ) {
      // only required if self-ref relation is not at DB level
      $file->shares()->delete();
      return;
    }

    // only required if self-ref relation is not at DB level
    $file->shares()->forceDelete();

    Storage::disk($file->disk)->deleteDirectory(Api::joinPaths($file->location, $file->id));
  }
}
