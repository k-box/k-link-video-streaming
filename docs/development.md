# Development

## Local Development

### K-Link Registry

While developing locally, i.e. when `APP_ENV` is set to `local`, the K-Link Registry connection is not necessary. 

If you want to perform integration testing while in a `local` environment, the variable `KLINK_REGISTRY_URL` can be added to the `.env` file.

In production, or when running the Docker image, the `KLINK_REGISTRY_URL` environment variable is mandatory.


### Common Problems

#### How I can upload a video file?

To upload a file you need to follow the [video upload guide](./video-upload.md) by simulating the API requests.

For the `video.add` request you could use any program that will let you generate JSON requests, like Curl or [Insomnia](https://insomnia.rest/) for example.

For the `video.uploads` part you need a TUS client. An client that can be used is [tus-client-cli](https://github.com/avvertix/tus-client-cli), which is Open Source and available as a single command line executable.

#### Video Pipeline is not working, `ffprobe` or `ffmpeg` missing

This is because the `video-processing-cli` expects to find the binaries in a `./bin` folder in the same folder it is launched. Make sure to launch the `video-processing-cli` executable from the `/bin` (located in the root of the project)
