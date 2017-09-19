<?php

namespace App\Http\Controllers\Api;

use App\Video;
use App\VideoRepository;
use App\Http\Requests\VideoAddRequest;
use App\Http\Requests\VideoGetRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Avvertix\TusUpload\TusUploadRepository;

class VideoController extends Controller
{
    /**
     * @var \Avvertix\TusUpload\TusUploadRepository
     */
     private $uploads = null;
    
     /**
     * @var \App\VideoRepository
     */
     private $videos = null;
     
    public function __construct(TusUploadRepository $uploads, VideoRepository $videos)
    {
        $this->uploads = $uploads;
        $this->videos = $videos;
    }

    /**
     * Store a newly created App\Video resource in storage and the corresponding TusUpload entry 
     * in the queue to follow the upload and processing.
     *
     * @param  \App\Http\Requests\VideoAddRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VideoAddRequest $request)
    {
        $upload = $this->uploads->create(
            $request->user()->getAuthIdentifier() /* as application_id */, 
            $request->input('id'), 
            $request->input('params.filename'), 
            (int)$request->input('params.filesize'),
            $request->input('params.filetype', null));

        $video = $this->videos->create(
            $request->user()->getAuthIdentifier() /* as application_id */, 
            $upload->id,
            $request->input('params.filename'),
            $request->input('params.filetype', null),
            $request->input('params.title', null) );

        $data = [
            // the video identifier to be used for grabbing the videos status and for all video related actions
            'video_id' => $video->video_id,
            // the original request_id so the client knows what request we processed
            'request_id' => $upload->request_id,
            
            // the information for the tus uploader client, 
            // the token to authenticate the upload and the endpoint url to use
            'upload_token' => $upload->upload_token,
            'upload_location' => tus_url(),
        ];

        return response()->json($data, 202);
    }

    /**
     * Display the information about a video file.
     *
     * @param  \App\Http\Requests\VideoGetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(VideoGetRequest $request)
    {
        $video = $this->videos->find($request->input('params.video_id'));

        $data = [
            'request_id' => $request->input('id'),
            'response' => $video,
        ];

        return response()->json($data);

    }

    /**
     * Remove the specified video.
     *
     * @param  \App\Http\Requests\VideoGetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(VideoGetRequest $request)
    {
        $video = $this->videos->delete($request->input('params.video_id'));
        
        $video->deleted = true;

        $data = [
            'request_id' => $request->input('id'),
            'response' => $video,
        ];

        return response()->json($data);
    }
}
