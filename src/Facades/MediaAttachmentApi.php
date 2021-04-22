<?php

namespace MOIREI\MediaLibrary\Facades;

use Illuminate\Support\Facades\Facade;

class MediaAttachmentApi extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return 'mediaAttachmentApi';
  }
}
