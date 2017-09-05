<?php

namespace App\Http\Controllers;

use App\Video;
use App\VideoRepository;
use Illuminate\Http\Request;

class VideoPlaybackController extends VideoController
{

    /**
     * Display the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $video = $this->videos->find($id);

        return view('video', compact('video'));
    }


}
