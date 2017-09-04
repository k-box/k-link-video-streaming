<?php

namespace App\VideoProcessing\Exceptions;

use Exception;

class VideoProcessingFailedException extends Exception
{

    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
