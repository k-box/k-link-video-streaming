# Development

This project is based on the [Laravel Framework](https://laravel.com/docs)

## Local Development

### K-Link Registry

While developing locally, i.e. when `APP_ENV` is set to `local`, the K-Link Registry connection is not necessary. 

If you want to perform integration testing while in a `local` environment, the variable `KLINK_REGISTRY_URL` can be added to the `.env` file.

In production, or when running the Docker image, the `KLINK_REGISTRY_URL` environment variable is mandatory.

### Database

The service stores information in a SQlite database. During local development the database file resides in `./database` and is named `database.sqlite`.

Laravel needs an existing database file, so you can create a new file with

```
php -r "touch('./database/database.sqlite');"
```

You can also configure the [location of the database](./../config/database.php) via [environment variables](https://laravel.com/docs/5.5/configuration#environment-configuration).
To do so use the `DB_DATABASE` variable, which expects the path of the database file

### Video processing

The video processing is performed thanks to the [Video Processing CLI](https://git.klink.asia/main/video-processing-cli). The binary, and its dependencies, are not downloaded/installed by default.

To pull them execute

```
composer run install-video-cli
```

### Common Problems

#### How I can upload a video file?

To upload a file you need to follow the [video upload guide](./video-upload.md) by simulating the API requests.

For the `video.add` request you could use any program that will let you generate JSON requests, like Curl or [Insomnia](https://insomnia.rest/) for example.

For the `video.uploads` part you need a TUS client. A client that can be used is [tus-client-cli](https://github.com/avvertix/tus-client-cli), which is Open Source and available as a single command line executable.

#### Video Pipeline is not working, `ffprobe` or `ffmpeg` missing

This is because the `video-processing-cli` expects to find the binaries in a `./bin` folder in the same folder it is launched. Make sure to launch the `video-processing-cli` executable from the `/bin` (located in the root of the project).

In addition check that the binary files are executable
