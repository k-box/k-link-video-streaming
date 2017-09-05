@extends('layouts.global')

@section('content')

    <div class="video">
        
        @includeWhen($video->completed, 'partials.player', ['video' => $video])
        
        @includeWhen(!$video->completed, 'partials.no-video')

    </div>

@endsection
