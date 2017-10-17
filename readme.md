# K-Link Video Streaming service

An API driven service for uploading, converting and delivering video streams.

**Features**

* [x] Compatible with [K-Link](https://klink.asia) and [K-Box](https://github.com/k-box/k-box)
* [x] Hosts and deliver mp4 videos
* [x] Generate different video resolution for playback under different network conditions
* [x] Support [Dynamic Adaptive Streaming over HTTP (DASH)](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP) for media playback
* [ ] Support [HTTP Live Streaming](https://en.wikipedia.org/wiki/HTTP_Live_Streaming) for media playback

**Browser support**

Target browser support includes modern web browsers: Edge 15+, Chrome, Firefox, Safari, Opera together with their mobile counterparts.

In addition to modern browsers we support IE10 (on best effort) and IE11. IE 9 and below are not supported.

> Currently streaming is not supported on Safari and Mobile Safari (on iOS), we are working on it.

## Getting started

### Installation

The K-Link Video Streaming service is available as single Docker image

```
docker pull docker.klink.asia/images/video-streaming-service
```

An [example Docker Compose file](./docker-compose.yml) is available in the root of this repository.

**configuration**

There are 3 parameters that must be configured in order to run the Docker image:

1. `APP_URL`: the URL on which the service will be publicly accessible, e.g. `https://domain.com/video` or `https://video.domain.com`
2. `APP_KEY`: the 32 characters random string to be used for encrypting cookies or other values that might need a secure storage
3. `KLINK_REGISTRY_URL`: the URL of the K-Link Registry service that will authorize applications to add or delete videos

By default the storage folder is not persisted, therefore is highly suggested to [mount a volume](https://docs.docker.com/compose/compose-file/compose-file-v1/#volumes-volume_driver) pointing to `/var/www/vss/storage`.

If you are running the service behind a reverse proxy see [Running behind a (reverse) proxy](./docs/behind-proxy.md).

> sub-folder deployments, like `https://domain.com/video`, are available only if the service is [running behind a reverse proxy](./docs/behind-proxy.md#sub-folder-deployment-with-a-proxy)

## Usage and Documentation

The documentation is available in the [`/docs`](./docs/) sub-folder.

Here is a brief table of contents:

- The developer API is documented in [`docs/api.md`](./docs/api.md)
- The [`docs/video-upload.md`](./docs/video-upload.md) file documents the actions to upload a video file
- [Running behind a (reverse) proxy](./docs/behind-proxy.md)

## Contributing

Thank you for considering contributing to the K-Link Streaming Service! The contribution guide is not available yet, but in the meantime you can still submit Pull Requests.

For every problem please refer to [`docs/development.md`](./docs/development.md)

## License

This project is licensed under the AGPL v3 license, see [LICENSE.txt](./LICENSE.txt).
