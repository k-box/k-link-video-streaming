# Video Storage

Videos are stored under `storage/app/videos`.

Each time a new video is created a folder named with the `video_id` is created under the video storage location.

Currently the storage location is [public](https://laravel.com/docs/5.4/filesystem#the-public-disk), therefore can be accessed by browsers using a specific path. In this case `/storage`.

This is done to let the webserver serve the static files and deliver range requests response for video playback.


### What means **public**?

Public means that the folder `storage/app/videos` is linked, via a symbolic link, to `public/storage`. This make still possible to use the storage location, but at the same time it make the content accessible directly from the webserver.

By default the symbolic link is not avaialable, you have to explicitly create it with the command

```bash
php artisan videostorage:link
```
