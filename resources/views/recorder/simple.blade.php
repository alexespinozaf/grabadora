@extends('layouts.recorderTemplate')
@section('css')
    <style>
        #main {
            background-color: rgb(231, 231, 231)!important;
            box-shadow: none;
            height: 95%;
            overflow: auto;
            padding: 0;
            border-radius: 25px!important;
            overflow-y: scroll;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12">
                <div id="main"  class="card">
                        <div class="card-action">
                            <div class="flexbox">
                                <div class="row">
                                    <div class="col s12 center-align audio">
                                        <br>
                                            <div id="currentTime" class=""><span id="min">00</span>:<span id="sec">00</span><br></div>
                                    </div>
                                    <div class="col s12 center-align player">
                                        <a id="record" class="btn-large btn-floating @if($recording) disabled @endif">
                                            <i id="recordBtn" class="material-icons">fiber_manual_record</i>
                                        </a>
                                        <a id="stop"  class="btn-large btn-floating disabled">
                                            <i class="material-icons">stop</i>
                                        </a>
                                        <a id="send" class="btn-large" disabled="disabled">
                                            <div id="spinner" class="preloader-wrapper small active middle" style="display:none;">
                                                <div class="spinner-layer spinner-green-only">
                                                    <div class="circle-clipper left">
                                                        <div class="circle"></div>
                                                    </div>
                                                    <div class="gap-patch">
                                                        <div class="circle"></div>
                                                    </div>
                                                    <div class="circle-clipper right">
                                                        <div class="circle"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="send-icon">
                                                <i  class="material-icons left">ios_share</i> Send
                                            </div>
                                            <div id="done-icon" style='display: none'>
                                                <i  class="material-icons left" >done</i> Done
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="container">
                                @if($recording)
                                    <div id="resultContainer" class="card center-align">
                                        <div class="card-title">My recording</div>
                                        <ul id="recordingList">
                                            <li class="center-align">
                                                <audio controls>
                                                    <source src="{{url('/storage'.$recording->file)}}" type="audio/mp3">
                                                </audio>
                                                <p id='upload_date' class="center-align">Upload date: {{$recording->updated_at->format('d-m-Y')}}</p>
                                                @if($recording->grade)
                                                    <span class="tooltipped" data-position="bottom" data-tooltip="Your recording is evaluated, you cannot discard it">
                                                <a id='delete-recording' class='btn-small red btn-floating'
                                                   type="submit" disabled>
                                                    <i class='material-icons'>delete</i>
                                                </a>
                                            </span>
                                                @else
                                                    <a id='delete-recording' data-id="{{$recording->id}}" class='btn-small red btn-floating tooltipped'
                                                       data-position="bottom" data-tooltip="Delete" type="submit"
                                                       onclick="return confirm('Are you sure you want to discard this recording?')?deleteRecording():'';">
                                                        <i class='material-icons'>delete</i>
                                                    </a>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <div id="resultContainer" class="card center-align" style="display: none">
                                        <div class="card-title">My recording</div>
                                        <ul id="recordingList">
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{ URL::asset('/js/ffmpeg/ffmpeg/dist/ffmpeg.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/ffmpeg/core/dist/ffmpeg-core.js') }}"></script>
    <script>
        const { createFFmpeg, fetchFile } = FFmpeg;
        const ffmpeg = createFFmpeg({ log: true });
        let user = {!! json_encode($user) !!};
        let activity = {!! json_encode($activity) !!};
        // Obtener elementos del DOM
        const recordButton = document.getElementById('record');
        const stopButton = document.getElementById('stop');
        const sendButton = document.getElementById('send');
        const spinner = document.getElementById('spinner');
        const sendIcon = document.getElementById('send-icon');
        const doneIcon = document.getElementById('done-icon');
        const recordingList = document.getElementById('recordingList');
        const resultContainer = document.getElementById('resultContainer');

        // Variables globales
        let recorder;
        let chunks = [];
        let isRecording = false;

        var Clock = {
            totalSeconds: 0,
            start: function () {
                if (!this.interval) {
                    var self = this;

                    function pad(val) {
                        return val > 9 ? val : "0" + val;
                    }

                    this.interval = setInterval(function () {
                        self.totalSeconds += 1;

                        document.getElementById("min").innerHTML = pad(Math.floor(self.totalSeconds / 60 % 60));
                        document.getElementById("sec").innerHTML = pad(parseInt(self.totalSeconds % 60));
                    }, 1000);
                }
            },
            reset: function () {
                Clock.totalSeconds = null;
                clearInterval(this.interval);
                document.getElementById("min").innerHTML = "00";
                document.getElementById("sec").innerHTML = "00";
                delete this.interval;
            },
            pause: function () {
                clearInterval(this.interval);
                delete this.interval;
            },

            resume: function () {
                this.start();
            },

            restart: function () {
                this.reset();
                Clock.start();
            }
        };

        // Función para comenzar la grabación
        function startRecording() {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    // Cambiar estado de grabación
                    isRecording = true;

                    // Deshabilitar botón de grabar y habilitar botón de detener
                    recordButton.classList.add('disabled');
                    stopButton.classList.remove('disabled');

                    // Crear objeto MediaRecorder
                    recorder = new MediaRecorder(stream);

                    // Evento para guardar los fragmentos de audio
                    recorder.ondataavailable = e => chunks.push(e.data);

                    // Evento para finalizar la grabación
                    recorder.onstop = async e => {
                        //mostrar resultContainer
                        resultContainer.style.display = 'block';

                        // Crear objeto Blob con los fragmentos de audio
                        const audioBlob = new Blob(chunks, { type: 'audio/mpeg' });
                        console.log(audioBlob);
                        let result = audioBlob;
                        if (self.crossOriginIsolated) {
                            console.log('La página está aislada de origen cruzado');
                            //Transforma el audio a mp3
                            result = await audioMp3(audioBlob);
                        }
                        // Crear elemento audio y establecer atributos
                        const audioElement = document.createElement('audio');
                        audioElement.setAttribute('controls', '');
                        audioElement.src = URL.createObjectURL(result);
                        audioElement.setAttribute('id', 'resultAudio');
                        // Crear elemento li y añadir audio
                        const li = document.createElement('li');
                        li.classList.add('center-align');
                        li.appendChild(audioElement);
                        const date = document.createElement('p');
                        date.setAttribute('id', 'upload_date');
                        date.classList.add('center-align');
                        date.innerHTML = 'Upload date: ' + new Date().toLocaleDateString();
                        li.appendChild(date);

                        // Añadir botón para eliminar grabación
                        const deleteButton = document.createElement('a');
                        deleteButton.classList.add('btn-small', 'red', 'btn-floating', 'tooltipped');
                        deleteButton.setAttribute('data-position', 'bottom');
                        deleteButton.setAttribute('data-tooltip', 'Eliminar');
                        deleteButton.setAttribute('id', 'delete-recording');
                        deleteButton.setAttribute('onclick', 'return confirm("Are you sure you want to discard this recording?")?deleteRecording():\'\'');
                        deleteButton.innerHTML = '<i class="material-icons">delete</i>';
                        li.appendChild(deleteButton);

                        // Añadir elemento li a la lista de grabaciones
                        recordingList.appendChild(li);

                        // Habilitar botón de enviar
                        sendButton.removeAttribute('disabled');
                        recordButton.classList.remove('disabled');

                        // Cambiar iconos de botón de enviar
                        spinner.style.display = 'none';
                        sendIcon.style.display = 'inline-block';
                        doneIcon.style.display = 'none';

                        recordButton.classList.add('disabled');
                    };

                    // Comenzar la grabación
                    recorder.start();
                    Clock.restart()
                    Clock.start();
                })
                .catch(console.error);
        }

        // Función para detener la grabación
        function stopRecording() {
            // Cambiar estado de grabación
            isRecording = false;

            // Habilitar botón de grabar y deshabilitar botón de detener
            recordButton.classList.add('disabled');
            stopButton.classList.add('disabled');

            // Detener la grabación
            recorder.stop();
            Clock.pause()
        }

        async function sendRecording(){
            let token = $('meta[name="csrf-token"]').attr('content');
            // Cambiar iconos de botón de enviar
            spinner.style.display = 'inline-block';
            sendIcon.style.display = 'none';
            doneIcon.style.display = 'none';

            // Obtener grabaciones
            let recordings = recordingList.children;

            // Crear objeto FormData
            let formData = new FormData();

            let recording = recordings[0];
            let audio = recording.querySelector('audio');
            let blob = await fetch(audio.src).then(response => response.blob());
            formData.append('file', blob);
            formData.append("user", user);
            formData.append("activity", activity);

            $.ajax({
                url: window.location.origin + '/recordings/upload',
                type: 'POST',
                headers: {'x-csrf-token': token},
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'html',
                success: function (response) {
                    let data = JSON.parse(response);
                    let resultAudio = document.getElementById('resultAudio');
                    let deleteBtn = document.getElementById('delete-recording');
                    if (data.status) {
                        sendButton.classList.remove('disabled');
                        spinner.style.display= "none";
                        // sendIcon.classList.remove('hide');
                        sendIcon.style.display = 'none';
                        doneIcon.style.display = "inline-block";
                        sendButton.classList.add('send-done');
                        M.toast({html: data.status, duration: 8000,classes: 'rounded green darken-1'});
                        resultAudio.setAttribute('data-id', data.recording.id);
                        deleteBtn.setAttribute('data-id', data.recording.id);
                    }
                },
                error: function (qxhr, status, error) {
                    sendButton.classList.remove('disabled');
                    spinner.classList.add('hide');
                    sendIcon.classList.remove('hide');
                    $("#error-div").html(error).fadeIn('slow');
                    $('#error-div').delay(4000).fadeOut('slow');
                }
            });
        }

        function deleteRecording()
        {
            let deleteBtn = document.getElementById('delete-recording');
            let id = deleteBtn.getAttribute('data-id');
            let token = $("meta[name='csrf-token']").attr("content");
            let recordingList = document.getElementById("recordingList");
            let recordButton = document.getElementById("record");
            let resultContainer = document.getElementById("resultContainer");
            let activityMedia = {!! json_encode($activityMedia) !!};
            let sendButton = document.getElementById("send");
            let sendIcon = document.getElementById("send-icon");
            let doneIcon = document.getElementById("done-icon");

            if(id !== null){
                $.ajax(
                    {
                        url: window.location.origin + '/recorder/' + id,
                        type: 'DELETE',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function (response) {
                            if(response.status === 200){
                                // console.log(recordingList);
                                recordingList.innerHTML = "";
                                resultContainer.style.display = "none";
                                recordButton.classList.remove('disabled');
                                sendButton.classList.remove('send-done');
                                sendIcon.style.display = "inline-block";
                                doneIcon.style.display = 'none';
                                sendButton.setAttribute("disabled", "disabled");
                                M.toast({html: response.message, duration: 18000, classes: 'rounded green darken-1'});
                            } else {
                                M.toast({html: response.message, duration: 8000, classes: 'rounded red darken-1'});
                            }
                        },
                        error: function (qxhr, status, error) {
                            console.log(qxhr)
                            M.toast({html: error, duration: 8000, classes: 'rounded red darken-1'});
                        }
                    });
            } else {
                recordingList.innerHTML = "";
                resultContainer.style.display = "none";
                recordButton.classList.remove('disabled');
                sendButton.setAttribute("disabled", "disabled");
                sendButton.classList.remove('send-done');
                M.toast({html: 'Recording deleted successfully.', duration: 18000, classes: 'rounded green darken-1'});
                sendIcon.style.display = "inline-block";
            }
            document.addEventListener("DOMContentLoaded", function() {
                var elems = document.querySelectorAll(".tooltipped");
                var instances = M.Tooltip.init(elems);
            });
        }

        // Añadir eventos a los botones
        recordButton.addEventListener('click', startRecording);
        stopButton.addEventListener('click', stopRecording);
        sendButton.addEventListener('click', sendRecording);

        const audioMp3 = async  (audioBlob) => {
            await ffmpeg.load();
            ffmpeg.FS('writeFile', 'test.wav', await fetchFile(URL.createObjectURL(audioBlob)));
            await ffmpeg.run('-i','test.wav', 'test.mp3');
            const finalOutput =  ffmpeg.FS('readFile', 'test.mp3');
            let audio = new Blob([finalOutput.buffer], {type: 'audio/mp3'});
            console.log(audio)
            return audio;
        }

    </script>
@endsection
