# Video Processing

Everytime a video is uploaded it is enqueued for further processing. Processing means downscaling, transcoding and generating a [Dash](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP) manifest.

Video processing can take a lot of time, therefore is handled asynchrounously with a queue runner.

Videos are always enqueue in the `video-processing` queue. To run the queue worker issue the following command:

```bash
php artisan queue:work --queue:video-processing
```

(For more information see [Laravel Queue documentation](https://laravel.com/docs/5.4/queues#running-the-queue-worker))

The jobs added to the queue are stored in the database and processed sequentially.


## Requirements

The Video convertion and DASH manifest generation are delegated to the [Video Processing CLI](https://github.com/OneOffTech/video-processing-cli). 

> Please download the binaries and save it under `./bin` before proceeding


## Output

The processing output are a set of video files and a mdp file.

The number of video files depends on the original video resolution. If the original video was a 1080p then 4 video files are generated: 1080p, 720p, 540p and 360p. The lower the resolution is, the less videos are generated. For example if the original video is 360p, a repacked 360p is generated.

The video files are named according to the pattern

```
{original_name}[-{resolution}]_{stream}.mp4
```

- `{original_name}` is the original video filename without extension
- `{resolution}` is the video frame height, can be `720`, `540` o `360`. It is optional, the original video will still be named
- `{stream}` indicates if the file contains the `video` or the `audio`.

As an example consider a video named `Butterfly.mp4` recorded with a 1920x1080 pixel resolution.

The pipeline will generate:

- `Butterfly.mdp`
- `Butterfly-1080_video.mp4`
- `Butterfly-1080_audio.mp4`
- `Butterfly-720_video.mp4`
- `Butterfly-720_audio.mp4`
- `Butterfly-540_video.mp4`
- `Butterfly-540_audio.mp4`
- `Butterfly-360_video.mp4`
- `Butterfly-360_audio.mp4`

