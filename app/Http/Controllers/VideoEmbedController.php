<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoEmbedController extends VideoController
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
       
       return view('embed', compact('video'));
    }
}
