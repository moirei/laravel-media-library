<?php

namespace MOIREI\MediaLibrary;

use Illuminate\Database\Eloquent\Model;
use MOIREI\MediaLibrary\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachmentApi
{
  /**
   * Delete from model
   *
   * @param UploadedFile $attachment
   * @return string
   */
  public static function store(UploadedFile $attachment, string $location = null, string $disk = null): string
  {
    $location = Api::resolveLocation(
      Api::joinPaths(config('media-library.attachments.location', 'attachments'), $location)
    );
    if (is_null($disk)) $disk = config('media-library.attachments.disk', 'public');
    if (is_null($location)) $location = '/';

    $attachment = $attachment->store($location, $disk);
    $url = Storage::disk($disk)->url(Api::joinPaths($location, $attachment));

    Attachment::create([
      'attachment' => $attachment,
      'disk' => $disk,
      'location' => $location,
      'alt' => pathinfo($attachment, PATHINFO_FILENAME),
      'url' => $url,
    ]);

    return $url;
  }

  /**
   * Delete from model
   *
   * @param Model $model
   * @return void
   */
  public static function delete(Model $model)
  {
    $query = Attachment::where('attachable_type', $model->getMorphClass());
    if ($model->getKeyType() === 'string') $query = $query->where('attachable_uuid', $model->getKey());
    else $query = $query->where('attachable_id', $model->getKey());

    $query->get()
      ->each
      ->purge();
  }

  /**
   * Purge attachment
   *
   * @param string $url
   * @return void
   */
  public static function purge(string $url)
  {
    Attachment::where('url', $url)
      ->get()
      ->each
      ->purge();
  }

  /**
   * Discard pending attachment
   * @return void
   */
  public static function discardPending()
  {
    Attachment::where('pending', true)
      ->get()
      ->each
      ->purge();
  }

  /**
   * Persist pending attachment.
   *
   * @param string $url
   * @return void
   */
  public function persist(string $url)
  {
    Attachment::where('url', $url)
      ->get()
      ->each
      ->persist();
  }

  /**
   * Persist all pending attachments in the models' text fields
   *
   * @param Model $model
   * @param bool $force force operation
   * @return void
   */
  public static function persistAttachments(Model $model, bool $force = false)
  {
    $richtext_match = config('media-library.attachments.richtext_match', []);
    if (!count($richtext_match)) {
      return;
    }

    $urls = [];
    $fields = $model->richTextFields();

    foreach ($fields as $field) {
      if ($model->isClean($field) && !$force) continue;
      $content = data_get($model, $field, '');
      if (!empty($content)) {
        foreach ($richtext_match as $match) {
          $matches = [];
          preg_match_all($match, $content, $matches);
          if (count($matches[1]) !== 0) $urls = array_merge($urls, $matches[1]);
        }
      }
    }

    $attachments = Attachment::where('pending', true)->whereIn('url', $urls)->get();

    // perist and associate all with model
    $attachments->each(fn ($attachment) => $attachment->persist()->attach($model));
  }
}
