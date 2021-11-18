<?php

namespace MOIREI\MediaLibrary\Exceptions;

class AttachmentLocationException extends \Exception
{
    public function __construct()
    {
        parent::__construct(__('Attachments not assignable to folders'));
    }
}
