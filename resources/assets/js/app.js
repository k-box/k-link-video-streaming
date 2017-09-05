
/**
 * Application javascript.
 */

const shaka = require('shaka-player');
const plyr = require('plyr');


function initPlayer(video) {
    // Create a Player instance.
    var manifestUri = video.dataset.dash.trim();

    var player = new shaka.Player(video);

    // Listen for error events.
    player.addEventListener('error', onErrorEvent);

    // Try to load a manifest.
    // This is an asynchronous process.
    player.load(manifestUri).then(function () {
        // This runs if the asynchronous load is successful.
        console.log('The video has now been loaded!');
    }).catch(onError);  // onError is executed if the asynchronous load fails.
}

function onErrorEvent(event) {
    // Extract the shaka.util.Error object from the event.
    onError(event.detail);
}

function onError(error) {
    // Log the error.
    console.error('Error code', error.code, 'object', error);
}

/**
 * 
 * @param {string} selector the DOM selector to get the video tag, or the DOMElement
 * @param {*} options player configuration options
 * @return {Plyr} the player instance
 */
function _StreamPlayer(selector, options){

    options = options || {};

    selector = typeof selector === 'string' ? document.querySelector(selector) : selector;

    

    var player = plyr.setup(selector);
    shaka.polyfill.installAll();
    
    // Check to see if the browser supports the basic APIs Shaka needs.
    // This is an asynchronous check.
    if (shaka.Player.isBrowserSupported()) {
        // Everything looks good!
        initPlayer(selector);
    } else {
        // This browser does not have the minimum set of APIs we need.
        console.error('Browser not supported!');
    }

    return player;
}

window.StreamPlayer = _StreamPlayer;

