@extends('layouts.global')

@push('meta')
    <link rel="alternate" type="application/json+oembed" href="{{ route('oembed', ['url' => $video->url, 'format' => 'json']) }}" title="{{ $video->title }}">
@endpush

@section('content')

    <div class="video">
        
        @includeWhen($video->completed, 'partials.player', ['video' => $video])
        
        @includeWhen(!$video->completed, 'partials.no-video')

    </div>

@endsection
