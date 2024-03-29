var audioContext = new AudioContext();
var analyser = audioContext.createAnalyser();
var source = audioContext.createBufferSource();
var url = 'https://example.com/audio.mp3';

var xhr = new XMLHttpRequest();
xhr.open('GET', url);
xhr.responseType = 'arraybuffer';
xhr.onerror = handleError;
xhr.onload = function() {
    if (xhr.status === 200) {
        handleBuffer(xhr.response);
    } else {
        console.error(xhr.statusText);
    }
};
xhr.send();

function handleError(error) {
    console.error(error);
}

function handleBuffer(audioData) {
    audioContext.decodeAudioData(audioData, decodeDone);
}

function decodeDone(buffer) {
    var begin = 50000;
    var end = begin + 20000;

    AudioBufferSlice(buffer, begin, end, function(error, slicedAudioBuffer) {
        if (error) {
            console.error(error);
        } else {
            source.buffer = slicedAudioBuffer;

            var gainNode = audioContext.createGain();
            gainNode.gain.value = 1;
            source.connect(gainNode);
            gainNode.connect(audioContext.destination);
        }
    });
}
