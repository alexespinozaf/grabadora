window.AudioContext = window.AudioContext || window.webkitAudioContext;
var context = new AudioContext();
var playbackTrack = null;

var recordBtn = document.getElementById("record");
var audio = document.getElementById("audio");
//var url = audio.getAttribute("src").replace(/ /g,"%20");
var url = 'https://grabadora.galeriapp.cl/storage/audios/test.mp3'


let originalAudio;
//AQUI SE CARGARIA EL AUDIO DEPENDIENDO DE LA ACTIVIDAD
fetch('https://grabadora.galeriapp.cl/storage/audios/test.mp3')
    .then(data => data.arrayBuffer())
    .then(arrayBuffer => context.decodeAudioData(arrayBuffer))
    .then(decodedAudio => {
        originalAudio = decodedAudio;
    });



function handleError(error) {
    console.error(error);
}

function handleBuffer(audiodata){
    var freader = new FileReader();

    freader.onload = function (e) {
        context.decodeAudioData(e.target.result, function (buf) {

            playbackTrack = context.createBufferSource();
            playbackTrack.buffer = buf;
        });
    };

    freader.readAsArrayBuffer(audiodata);

}

const sprite = {
    rol1f1: [0, 2500],
    rol2f1: [2800, 4000],
    rol1f2: [6300, 8000],
    rol2f2: [14200, 4000],
    rol1f3: [18000, 6000],
};
var sound = new Howl({
    src: ['https://grabadora.galeriapp.cl/storage/audios/test.mp3'],
    html5: true,
    sprite: sprite
});

function testRecording () {
    request = new XMLHttpRequest();
    request.open('GET', 'https://grabadora.galeriapp.cl/storage/audios/test.mp3', true);
    request.responseType = 'arraybuffer';
    request.onload = function () {
        var audioData = request.response;
        context.decodeAudioData(audioData, function(buffer){
            console.log(buffer.length)
            var offlineContext = new OfflineAudioContext(2, buffer.length, 44100);
            playBackTrack = offlineContext.createBufferSource();
            playBackTrack.buffer = buffer;
            playBackTrack.connect(offlineContext.destination);
            playbackTrack.start();
            offlineContext.startRendering().then(function(renderedBuffer){
                console.log('renderizando offline');
                var song = context.createBufferSource();
                song.buffer = renderedBuffer;
                song.connect(context.destination);
                song.start();
            }).catch(function(err){
                console.log('error '+ err);
            });
        })
    }
    request.send();
}

function startKaraoke(){

    var offlineContext = new OfflineAudioContext(2, originalAudio.length, 44100);
    offlinesrc = offlineContext.createBufferSource();
    offlinesrc.buffer = originalAudio;
    offlinesrc.connect(offlineContext.destination);
    offlinesrc.start();
    offlineContext.startRendering().then(function(renderedBuffer) {
        console.log('renderizado offline')
        playbackTrack = context.createBufferSource();
        playbackTrack.buffer =  renderedBuffer;
        playbackTrack.connect(context.destination);
        //playbackTrack.start();
    });

    //var originalAudio = await getAudio('https://grabadora.galeriapp.cl/storage/audios/test.mp3');
    //sound.play('line3');
    navigator.mediaDevices.getUserMedia({audio: true,video: false})
        .then(function(stream) {

            var mixedAudio = context.createMediaStreamDestination();
            var merger = context.createChannelMerger(2);
            var splitter = context.createChannelSplitter(2);

            var duration = 7000;

            var chunks = [];
            var channel1 = [0, 1];
            var channel2 = [1, 0];

            var gainNode = context.createGain();

            var microphone = context.createMediaStreamSource(stream);
            microphone.connect(splitter);
            splitter.connect(merger, channel2[0], channel2[1]);
            playbackTrack.connect(splitter);
            splitter.connect(merger, channel1[0], channel1[1]);

            merger.connect(mixedAudio);
            merger.connect(gainNode);
            gainNode.connect(context.destination);
            gainNode.gain.value = 0; // From 0 to 1

            playbackTrack.start(0);
            var mediaRecorder = new MediaRecorder(mixedAudio.stream);
            mediaRecorder.start(1);

            stopMix(duration, mediaRecorder);

            mediaRecorder.ondataavailable = function (event) {
                chunks.push(event.data);

            }
            mediaRecorder.onstop = function(event) {
                var player = new Audio();

                player.controls = "controls";

                var blob = new Blob(chunks, {
                    "type": "audio/mp3"
                });
                audioDownload = URL.createObjectURL(blob);
                var a = document.createElement("a");
                a.download = "file." + blob.type.replace(/.+\/|;.+/g, "");
                a.href = audioDownload;
                a.innerHTML = a.download;
                player.src = audioDownload;
                document.body.appendChild(a);
                document.body.appendChild(player);
            };
            // setTimeout(function(){
            //     mediaRecorder.pause()
            // },5000);
            // setTimeout(function(){
            //     mediaRecorder.resume()
            // },10000);

        })
        .catch(function(error) {
            console.log('error: ' + error);
        });

}


// function handleFileSelect(event){
//
//     var file = event.files[0];
//     var freader = new FileReader();
//
//     freader.onload = function (e) {
//         context.decodeAudioData(e.target.result, function (buf) {
//
//             playbackTrack = context.createBufferSource();
//             playbackTrack.buffer = buf;
//
//             var karaokeButton = document.getElementById("karaoke_start");
//             karaokeButton.style.display = "inline-block";
//             karaokeButton.addEventListener("click", function(){
//                 startKaraoke();
//             });
//         });
//     };
//
//     freader.readAsArrayBuffer(file);
// }

function stopMix(duration, mediaRecorder) {
    setTimeout(function(mediaRecorder) {
        mediaRecorder.stop();
        context.close();
    }, 15000, mediaRecorder)
}
function pauseMix(duration, mediaRecorder) {
    setTimeout(function(mediaRecorder) {
        mediaRecorder.pause();
    }, duration, mediaRecorder)
}
function resumeMix(duration, mediaRecorder) {
    setTimeout(function(mediaRecorder) {
        mediaRecorder.resume();
    }, duration, mediaRecorder)
}

function getAudio(url) {
    return new Promise(function (resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('get', url, true);
        xhr.responseType = 'blob';
        xhr.onload = function () {
            var status = xhr.status;
            if (status == 200) {
                handleBuffer(xhr.response);
            } else {
                reject(status);
            }
        };
        xhr.send();
    });
}

