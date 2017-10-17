
<video id="the-player" 
       data-dash="{{ $video->dash_stream }}" 
       controls preload="none"
       poster="{{ $video->poster }}">

</video>

<script src="{{ mix_url('js/app.js') }}"></script>
<script>
    (function () {
        var player = new StreamPlayer(document.querySelector('#the-player'));
    })();
</script>