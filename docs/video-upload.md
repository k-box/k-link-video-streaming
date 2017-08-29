# Video Upload

The video file upload is handled via the [Tus.io](https://tus.io/) resumable protocol.

Before submitting the video file, a request must be send to the [`video.add`](./api.md#videoadd) API endpoint. The `video.add` endpoint will validate the upload request and grant a specific upload token that must be used to start the upload. In addition the unique video identifier will be generated and returned.

The request to [`video.add`](./api.md#videoadd) must contain the following properties:

- `filename`: The name of the file being uploaded (with extension);
- `filesize`: The size, in bytes, of the video file
- `filetype`: The mime type of the video file 

> **Please consider that currently only `video/mp4` ([RFC4337](https://tools.ietf.org/html/rfc4337)) are supported**

An example of the full request body is presented in the next code block.

```json
{
    "id": "request_id",
    "params": {
        "filename": "something.mp4",
        "filesize": 1000,
        "filetype": "video/mp4",
        "title": "optional title of something",
    }
}
```

The response, in case of succesfull validation and authorization, will contain:

- `video_id`: the identifier of the video
- `request_id`: the id of the request, as it was specified by the client
- `upload_token`: an upload authentication token
- `upload_location`: the URL where the TUS endpoint is listening for connections

At this point you can perform the real file upload with the Tus protocol. The action for the tus upload is `video.upload`. The full endpoint URL is also sent in the `upload_location` property of the video add response.

When performing the upload add the following metadata to the tus request

- `upload_request_id`, with the `id` of the request sent to the `video.add` action
- `token`, with the previously received `upload_token`

Not adding the `token` or the `upload_request_id` will cause the upload to fail.


While the upload and the video processing are in progress you can also fetch the status information via [`video.get`](./api.md#videoget), using the `video_id` obtained from the Video Add response
