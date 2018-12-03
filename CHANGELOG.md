# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/0.3.0/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [0.3.1] - 2018-12-03

### Changed

- Updated video processing cli to 0.5.3

## [0.3.0] - 2018-10-09

### Changed

- Upgrade to Laravel 5.5 ([#5](https://github.com/k-box/k-link-video-streaming/pull/5))
- Update laravel-tus package to version 0.5.0 ([#5](https://github.com/k-box/k-link-video-streaming/pull/5))
- PHP version requirement changed to PHP 7.1

### Fixed

- Request authentication via K-Link Registry ([#4](https://github.com/k-box/k-link-video-streaming/pull/4))

## [0.2.1] - 2017-11-20
### Changed

- Updated video processing cli to version 0.3.1

## [0.2.0] - 2017-11-14

### Added

- Allow KLINK_REGISTRY_URL if APP_ENV is local
- Automatic Download of video transcoding pipeline
- OEMBED Support
- Sub-folder style deployment

### Fixed

- URL typos in configuration examples
- Using outdated K-Registry version

## [0.1.0] - 2017-09-19

### Added 

- API for managing upload, retrieval and deletion of video files
- Processing of mp4 video files to obtain the thumbnail and the DASH playlist for playback on supported players
- Integration with the K-Link Registry to authorize/deny video uploads
- Basic playback page with integrated player
