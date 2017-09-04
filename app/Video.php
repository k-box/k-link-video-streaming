<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Avvertix\TusUpload\TusUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Describe a video added to the service
 *
 * For accessing and manipulating models {@see \App\VideoRepository}
 *
 * @property int $id the autoincrement identifier of the video
 * @property string $application_id The identifier of the application that submitted the video
 * @property string $video_id The public identifier of the video
 * @property integer $upload_id The upload queue entry identifier
 * @property string $title The title assigned to this video (could be null)
 * @property string $original_video_filename The original file name of the video
 * @property string $original_video_mimetype The original mime type of the video (could be null)
 * @property string $path The path on disk where the video file is stored and where processed files can be found
 * @property /Carbon/Carbon $cancelled_at When the processing was cancelled
 * @property /Carbon/Carbon $completed_at When the video is available for viewing
 * @property /Carbon/Carbon $failed_at When the processing failed
 * @property boolean $completed indicates if the video upload and processing has been completed
 * @property boolean $cancelled indicates if the video processing has been cancelled
 * @property string $fail_reason The reason of the processing failure
 * @property-read string $status The status of the video @see STATUS_PENDING, STATUS_UPLOADING, STATUS_PROCESSING, STATUS_COMPLETED, STATUS_CANCELLED, STATUS_FAILED
 */
class Video extends Model
{

    /**
     * The video entry has been created and is waiting to see video 
     * chunks being uploaded.
     *
     * @var string
     */
    const STATUS_PENDING = 'pending';

    /**
     * The video file upload is in progress.
     *
     * @var string
     */
    const STATUS_UPLOADING = 'uploading';

    /**
     * The video file has been queued for processing.
     *
     * @var string
     */
    const STATUS_QUEUED = 'queued';

    /**
     * The video file is being processed.
     *
     * @var string
     */
    const STATUS_PROCESSING = 'processing';

    /**
     * The video file is ready to be served.
     *
     * @var string
     */
    const STATUS_COMPLETED = 'completed';

    /**
     * The video file upload or processing has been cancelled.
     *
     * @var string
     */
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The video file upload or processing encoutered an error.
     *
     * @var string
     */
    const STATUS_FAILED = 'failed';


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
     protected $dates = [
        'created_at',
        'updated_at',
        'cancelled_at',
        'completed_at',
        'queued_at',
        'failed_at',
    ];

    /**
     * The attributes that should be hidden from the serialization.
     *
     * @var array
     */
     protected $hidden = [
         'id', 
         'completed_at', 
         'cancelled_at', 
         'queued_at', 
         'failed_at',
         'application_id',
         'upload_id',
         'upload',
         'original_video_filename',
         'original_video_mimetype',
         'path',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
     protected $appends = ['status'];

    /**
     * Set the completed attribute
     *
     * @param  bool  $completed
     * @return void
     */
     public function setCompletedAttribute($completed)
     {
        if($this->cancelled){
            return ;
         }
         
         if ($completed && ! $this->completed_at) {
             $this->attributes['completed_at'] = Carbon::now();
         }
 
         if (! $completed && $this->completed_at) {
             $this->attributes['completed_at'] = null;
         }
     }
 
     /**
      * Get if the upload is complete.
      *
      * @param  mixed  $value not taken into account
      * @return bool
      */
     public function getCompletedAttribute($value = null)
     {
         return isset($this->attributes['completed_at']) && !is_null($this->attributes['completed_at']);
     }
 
     /**
      * Set the cancelled attribute
      *
      * @param  bool  $cancelled
      * @return void
      */
     public function setCancelledAttribute($cancelled)
     {
         if($this->completed){
            return ;
         }

         if ($cancelled && ! $this->cancelled_at) {
             $this->attributes['cancelled_at'] = Carbon::now();
         }
 
         if (! $cancelled && $this->cancelled_at) {
             $this->attributes['cancelled_at'] = null;
         }
     }
 
     /**
      * Get if the upload was cancelled.
      *
      * @param  mixed  $value not taken into account
      * @return bool
      */
     public function getCancelledAttribute($value = null)
     {
         return isset($this->attributes['cancelled_at']) && !is_null($this->attributes['cancelled_at']);
     }
 
     /**
      * Set the queued attribute
      *
      * @param  bool  $cancelled
      * @return void
      */
     public function setQueuedAttribute($queued)
     {
         if($this->completed){
            return ;
         }

         if ($queued && ! $this->queued_at) {
             $this->attributes['queued_at'] = Carbon::now();
         }
 
         if (! $queued && $this->queued_at) {
             $this->attributes['queued_at'] = null;
         }
     }
 
     /**
      * Get if the video was queued for processing.
      *
      * @param  mixed  $value not taken into account
      * @return bool
      */
     public function getQueuedAttribute($value = null)
     {
         return isset($this->attributes['queued_at']) && !is_null($this->attributes['queued_at']);
     }
 
     /**
      * Get if the upload/processing failed.
      *
      * @param  mixed  $value not taken into account
      * @return bool
      */
     public function getFailedAttribute($value = null)
     {
         return isset($this->attributes['failed_at']) && !is_null($this->attributes['failed_at']);
     }
 
      
     /**
      * Set the failure reason.
      *
      * Also set the failed_at attribute if null
      *
      * @param  string  $reason the reason of the failure
      * @return void
      */
      public function setFailReasonAttribute($reason)
      {
        $this->attributes['fail_reason'] = $reason;

        if(empty($reason)){
            $this->attributes['failed_at'] = null;
        }
        else {
            $this->attributes['failed_at'] = Carbon::now();
        }
      }
 
 
     /**
      * Determine if the video upload and processing is completed.
      *
      * @return bool
      * @see getCompletedAttribute()
      */
     public function completed()
     {
         return $this->completed;
     }
 
     /**
      * Determine if the video upload/processing has been cancelled.
      *
      * @return bool
      */
     public function cancelled()
     {
         return $this->cancelled;
     }
 
     /**
      * Determine if the video was enqueued for processing.
      *
      * @return bool
      */
     public function queued()
     {
         return $this->queued;
     }
    
     /**
      * Determine if the video upload/processing has failed.
      *
      * @return bool
      */
     public function failed()
     {
         return $this->failed;
     }

     /**
      * Get the video status.
      *
      * @param  mixed  $value not taken into account
      * @return string
      */
      public function getStatusAttribute($value = null)
      {

        $status = self::STATUS_PENDING;
        
        if($this->upload){

            if($this->upload->started)
              $status = self::STATUS_UPLOADING;
  
            if($this->upload->cancelled)
              $status = self::STATUS_CANCELLED;
        }

          if($this->completed)
            $status = self::STATUS_COMPLETED;

          if($this->cancelled)
            $status = self::STATUS_CANCELLED;
          
          if($this->failed)
            $status = self::STATUS_FAILED;
          
          if($this->queued)
            $status = self::STATUS_QUEUED;
        
          return $status;
      }

    /**
     * Get the upload entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
     public function upload()
     {
         return $this->hasOne(TusUpload::class, 'id', 'upload_id');
     }

     /**
      * Get the original video file.
      *
      * @param  mixed  $value not taken into account
      * @return \SplFileInfo
      */
      public function getFileAttribute($value = null)
      {

        $absolutePath = Storage::disk('local')->path($this->path . '/' . $this->video_id . '.mp4');

        // todo: check if Laravel has a File instance that can be used to wrap this file

        return new \SplFileInfo($absolutePath);
      }

     /**
      * Get the file instance of the original uploaded video file
      */
     public function file()
     {
         return $this->file;
     }
}
