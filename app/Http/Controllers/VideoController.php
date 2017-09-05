<?php

namespace App\Http\Controllers;

use App\Video;
use App\VideoRepository;
use Illuminate\Http\Request;

class VideoController extends Controller
{

    /**
     * @var \App\VideoRepository
     */
    protected $videos = null;
    

    public function __construct(VideoRepository $videos)
    {
        $this->videos = $videos;
    }


}
