window.onload = () => {

    //stream de getUserMedia()
    var gumStream;

    //objeto de WebAudioRecorder
    var recorder;

    //grabacion de MediaStreamAudioSourceNode
    var input;

    //eleccion de formato
    var encodingType;

    //bandera de codificacion
    var encodeAfterRecord = true;

    //variables de contexto
    var AudioContext = window.AudioContext || window.webkitAudioContext;
    var audioContext

    //botones
    //var encodingTypeSelect = document.getElementById("encodingTypeSelect");
    var recordButton = document.getElementById("recordButton");
    var stopButton = document.getElementById("stopButton");

    //eventos
    recordButton.addEventListener("click", startRecording);
    stopButton.addEventListener("click", stopRecording);


    //funcion de grabacion
    function startRecording() {
        console.log("llamada a iniciar grabacion");
        var constraints = {audio: true, video: false}
        navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
            console.log(stream)
            audioContext = new AudioContext();

            gumStream = stream;

            input = audioContext.createMediaStreamSource(stream);

            //input.connect(audioContext.destination);

            encodingType = "mp3";

            //encodingTypeSelect.disabled = true;

            recorder = new WebAudioRecorder(input, {
                workerDir: window.location.origin + '/js/',
                encoding: encodingType,
                onEncoderLoading: function (recorder, encoding) {
                    //__log("Cargando "+encoding+ " codificador...");
                },
                onEncoderLoaded: function (recorder, encoding) {
                    //__log(encoding+" codificador cargado")
                }
            });
            recData.push([]);
            recorder.onComplete = function (recorder, blob) {
                //__log("Codificacion completa");
                createButtons(blob, recorder.encoding);
                console.log(blob)
                recData[recData.length - 1].push(blob);
                //encodingTypeSelect.disabled = false;
            }

            recorder.setOptions({
                timeLimit: 120,
                encodeAfterRecord: encodeAfterRecord,
                ogg: {
                    quality: 0.6
                },
                mp3: {
                    bitRate: 192
                }
            });

            recorder.startRecording();
            Clock.restart()
            Clock.start();
            __log("Grabacion iniciada");

        }).catch(function (err) {
            recordButton.disabled = true;
            stopButton.disabled = false;
        });
        recordButton.disabled = true;
        stopButton.disabled = false;
    }

    function stopRecording() {
        console.log("llamada a stopRecording()");
        gumStream.getAudioTracks()[0].stop();
        var recordingsList = document.getElementById("recordingsList");

        stopButton.disabled = true;
        recorder.finishRecording();
        recordButton.disabled = false;
        if (recordingsList.getElementsByTagName("li").length < 1) {
            console.log('no hay elementos')
            recorder.createButtons
        }
        Clock.pause()
        __log("grabacion terminada");
    }

    function createButtons(blob, encoding) {
        console.log(blob)
        var url = URL.createObjectURL(blob);
        var au = document.createElement('audio');
        var li = document.createElement('li');
        //var link = document.createElement('a');

        li.setAttribute('class', 'text-center');
        au.controls = true;
        au.src = url;
        li.appendChild(au);
        li.setAttribute('class', "recordings");
        //link.href = url;
        //link.download = new Date().toISOString()+'.'+encoding;
        //link.innerText = "Descargar";
        //link.setAttribute('class',"btn btn-primary");
        //link.setAttribute('style', 'margin-left: 10px;');
        //li.appendChild(link);
        // var discard = document.createElement('button');
        // discard.innerText = "Descartar";
        // discard.setAttribute('class', 'btn btn-danger');
        // discard.setAttribute('style', 'margin-left: 10px; color: white;');
        // discard.setAttribute('data-toggle', 'modal');
        // discard.setAttribute('data-target', '#confirmDiscard');
        // li.appendChild(discard);

        var recording = document.createElement('a');
        recording.innerText = "Enviar ";
        recording.setAttribute('id', "sendButton");
        recording.setAttribute('class', "btn btn-success");
        recording.setAttribute('style', 'margin-left: 5px; color: white;');
        recording.setAttribute('onclick', 'return confirm("Esta seguro que quiere enviar este audio?")');
        recording.addEventListener("click", function (event) {
            sendRecording(blob)
        });
        li.appendChild(recording);

        recordingsList.appendChild(li);
    }

    function __log(e, data) {
        log.innerHTML += "\n" + e + " " + (data || '');
    }


}

function sendRecording(blob) {
    var formData = new FormData(), request = new XMLHttpRequest();
    var btn = event.target;
    var spinner = document.createElement('span');
    var token = $('meta[name="csrf-token"]').attr('content');
    var responseRecording = document.createElement('li');

    formData.append("user", user);
    formData.append("activity", activity);
    formData.append("file", blob);
    btn.disabled = true;
    spinner.setAttribute('class', "spinner-border spinner-border-sm");
    btn.appendChild(spinner);

    $.ajax({
        url: window.location.origin + '/recordings/upload',
        type: 'POST',
        headers: {'x-csrf-token': token},
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'html',
        success: function (response) {
            var data = JSON.parse(response);
            if (data.status) {
                btn.disabled = false;
                btn.removeChild(spinner);
                btn.remove();
                $('#success-div').css("text-align", "center");
                $("#success-div").html(data.message).fadeIn('slow');
                $('#success-div').delay(4000).fadeOut('slow');
                recordingsList.innerHTML = "";
                responseRecording.innerHTML = data.html;
                recordingsList.appendChild(responseRecording);
            }
        },
        error: function (qxhr, status, error) {
            btn.disabled = false;
            btn.removeChild(spinner);
            $("#error-div").html(error).fadeIn('slow');
            $('#error-div').delay(4000).fadeOut('slow');
        }
    });
}
function deleteRecording() {
    var recordingsList = document.getElementById("recordingsList");
    var recordButton = document.getElementById("recordButton");
    var recordingElement = document.getElementById("deleteRecording");
    var id = recordingElement.dataset.id;
    var token = $("meta[name='csrf-token']").attr("content");

    $.ajax(
        {
            url: window.location.origin + '/recorder/' + id,
            type: 'DELETE',
            data: {
                "id": id,
                "_token": token,
            },
            success: function (response) {
                recordingsList.innerHTML = "";
                recordButton.disabled = false;
                $('#success-div').css("text-align", "center");
                $("#success-div").html(response.message).fadeIn('slow');
                $('#success-div').delay(4000).fadeOut('slow');
            },
            error: function (qxhr, status, error) {
                $("#error-div").html(error).fadeIn('slow');
                $('#error-div').delay(4000).fadeOut('slow');
            }
        });
}

