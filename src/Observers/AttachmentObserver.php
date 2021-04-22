<?php

namespace MOIREI\MediaLibrary\Observers;

use Illuminate\Support\Facades\Storage;
use MOIREI\MediaLibrary\Api;
use MOIREI\MediaLibrary\Exceptions\MediaLocationUpdateException;
use MOIREI\MediaLibrary\Models\Attachment;

class AttachmentObserver
{
  public function updating(Attachment $attachment)
  {
    if ($attachment->isDirty('location')) {
      throw new MediaLocationUpdateException('Attachment');

      // Example update but must also update associated model urls:
      // $old_path = Api::joinPaths($attachment->getOriginal('location'), $attachment->attachment);
      // $path = Api::joinPaths($attachment->location, $attachment->attachment);
      // Storage::disk($attachment->disk)->move($old_path, $path);
    }
  }

  public function deleted(Attachment $attachment)
  {
    $path = Api::joinPaths($attachment->location, $attachment->attachment);
    if (Storage::disk($attachment->disk)->exists($path)) {
      Storage::disk($attachment->disk)->delete($path);
    }
  }
}
