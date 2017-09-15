<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OembedController extends VideoController
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $format = $request->input('format', 'json');
        $url = $request->input('url', '');
        $maxwidth = $request->input('maxwidth', 960);
        $maxheight = $request->input('maxheight', 540);

        // if format is not json, abort
        abort_if($format !== 'json', 501);

        // Get the video id from the given URL
        
        $base_url = route('video.show', '') . '/';
        
        // if the url don't start with this application URL, abort
        abort_unless(starts_with($url, $base_url), 404);
        // if the url contains query parameters
        abort_if(str_contains($url, '?') || str_contains($url, '#'), 404);
        
        $id = e(str_replace($base_url, '', $url));

        $video = $this->videos->find($id);
        
        // if video not found simply return
        abort_if(is_null($video), 404);

        $width = min([480, $maxwidth]);
        $height = min([360, $maxheight]);

        $embed_url = route('video.embed', $video->video_id);

        $data = [
            "version" => "1.0",
            "type" => "video",
            "provider_name" => config('app.name'),
            "provider_url" => config('app.url'),
            "width" => $width,
            "height" => $height,
            "title" => $video->title, // optional
            "html" => "<iframe width=\"$width\" height=\"$height\" src=\"$embed_url\" frameborder=\"0\" allowfullscreen></iframe>",
        ];

        return response()->json($data, 200);
    }
}
