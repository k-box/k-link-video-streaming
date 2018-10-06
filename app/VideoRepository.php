<?php

namespace App;

use App\Video;
use OneOffTech\TusUpload\TusUpload;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use EndyJasmi\Cuid;
use App\Exceptions\VideoNotFoundException;

class VideoRepository
{
    /**
     * The local storage where videos are saved
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem 
     */
    private $storage = null;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage->disk('videos');
    }

    /**
     * Get a video by the given Video ID.
     *
     * @param  int  $video_id
     * @return \App\Video|null
     */
    public function find($video_id)
    {
        return Video::where('video_id', $video_id)->first();
    }

    /**
     * Get a video by the given Upload ID.
     *
     * @param  int  $upload_id
     * @return \App\Video|null
     */
    public function findByUpload($upload_id)
    {
        return Video::where('upload_id', $upload_id)->first();
    }


    /**
     * Get the videos added by an application.
     *
     * @param  mixed  $applicationId
     * @return \Illuminate\Database\Eloquent\Collection the videos added by the application, ordered by the creation date
     */
    public function forApplication($applicationId)
    {
        if($applicationId instanceof Model){
            $applicationId = $applicationId->getKey();
        }

        return Video::where('application_id', $applicationId)
                        ->orderBy('created_at', 'asc')->get();
    }

    /**
     * Store a new video.
     *
     * @param  string  $application The application (identifier) that is performing the upload
     * @param  string  $uploadId The upload identifier in the queue
     * @param  string  $filename The name of the file video file (will not be disclosed publicly) 
     * @param  string  $mimeType The mime type of the original video file (default: null)
     * @param  string  $title The user defined title for the video (default: null)
     * @return \App\Video
     */
    public function create($application, $uploadId, $filename, $mimeType = null, $title = null)
    {
        // the unique video identifier, will be used by clients
        $videoId = Cuid::make();

        // where to store the video files
        $path = './' . $videoId . '/';

        if(!$this->storage->exists($path)){
            $this->storage->makeDirectory($path);
        }

        $video = (new Video)->forceFill([
            'application_id' => $application instanceof Model ? $application->getKey() : $application,
            'video_id' => $videoId,
            'upload_id' => $uploadId,
            'original_video_filename' => $filename,
            'original_video_mimetype' => $mimeType,
            'path' => $path
        ]);

        $video->save();

        return $video->fresh();
    }

    /**
     * Delete a video by the given Video ID.
     *
     * @param  int  $video_id
     * @return \App\Video|null
     */
     public function delete($video_id)
     {
         $video = $this->find($video_id);

         if(is_null($video)){
            throw new VideoNotFoundException();
         }

         if($video->upload){
             $video->upload->delete();
         }

         $video->delete(); // also delete the upload entry in the queue, if exists

         $this->storage->deleteDirectory($video->path);

         return $video;
     }


}