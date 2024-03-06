@extends('layouts.recorderTemplate')
@section('content')
    <div id="app">

            <div class="card recorder-container">
                <!--<div class="rol-container" id="rol-container scale-transition">-->
                <div class="rol-container" id="rol-container">
                    <div class="container rol-row">
                        @foreach($activityMedia->characters as $character)
                            @if ($activityMedia->type == 'q&a' && $character->rol_order == 'r1')
                                <div id="{{$character->rol_order}}" class="rol-card rol card-disabled">
                                    @else
                                        @if($recording)
                                            <div id="{{$character->rol_order}}" class="rol-card rol card-disabled">
                                                @else
                                        <div id="{{$character->rol_order}}" class="rol-card rol">
                                            @endif
                                            @endif
                            <div class="card">
                                <div role="button" class="col-content">
                                    <img id="male" src="{{ url('storage/characters/'.$character->genre.'.png') }}" alt="" class="circle">
                                    <div class="card-title">
                                        <input id="rol-1" type="radio" >
                                        <label for="rol-1">{{$character->name}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-content">
                    <div class="container white">
                        <div class="center-align dialogos">
                            <ul id="sub">
                            </ul>
                        </div>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col s6 center-align player">
                                <a id="play" onclick="player.playAudio('play')" class="btn btn-floating">
                                    <i class="material-icons">play_arrow</i>
                                </a>
                                <a id="stop" onclick="player.stopAudio();recorder.resetAudios()" class="btn btn-floating">
                                    <i class="material-icons">stop</i>
                                </a>
                                <a id="record" onclick="player.playAudio('play')" class="btn btn-floating" disabled="disabled">
                                    <i id="recordBtn" class="material-icons">fiber_manual_record</i>
                                </a>
                                <a id="send" class="btn" disabled="disabled">
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
                                    <div id="done-icon" style="display:none;">
                                        <i  class="material-icons left" >done</i> Done
                                    </div>
                                </a>
                            </div>
                            <div class="col s6 center-align audio">
                                <audio id="audio"  muted="muted"
                                       src="{{url('/storage/'.$activityMedia->audio)}}" type="audio/mpeg"
                                       class="text-center">
                                    <track id="audioTrack" kind="captions" src="{{url('/storage/'.$activityMedia->sub)}}"
                                           srclang="en" label="English" default="default">
                                </audio>
                                <div id="bar">
                                    <div id="currentTime" class="">00:00</div>
                                    <div id="timeline" class="">
                                        <div id="playhead"></div>
                                    </div>
                                    <div id="totalTime" class=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <div class="container">
                        <div class="row">
                            <div class="col s6 offset-s3 center-align">
                        @if($recording)
                            <div id="resultContainer" class="card center-align">
                                <div class="my-recording">My recording <small id="upload_date" class="center-align">(Upload date: {{$recording->updated_at->format('d-m-Y')}})</small></div>
                                <ul id="recordingList">
                                    <li class="center-align">
                                        <audio controls>
                                            <source src="{{url('/storage/'.$recording->file)}}" type="audio/mpeg">
                                        </audio>
                                        @if($recording->grade)
                                            <span class="tooltipped" data-position="bottom" data-tooltip="Your recording is evaluated, you cannot discard it">
                                                <a id='delete-recording' class='btn red btn-floating'
                                                   type="submit" disabled>
                                                    <i class='material-icons'>delete</i>
                                                </a>
                                            </span>
                                        @else
                                            <a id='delete-recording' data-id="{{$recording->id}}" class='btn red btn-floating tooltipped'
                                               data-position="bottom" data-tooltip="Delete" type="submit"
                                               onclick="return confirm('Are you sure you want to discard this recording?')?deleteRecording():'';">
                                                <i class='material-icons'>delete</i>
                                            </a>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div id="resultContainer" class="card center-align" style="display: none;">
                                <div class="my-recording">My recording</div>
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
    <script type="text/javascript" src="{{ URL::asset('js/clases/recorder.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/clases/player.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/clases/rateController.js') }}"></script>
    <script crossorigin="anonymous" src="https://unpkg.com/crunker@latest/dist/crunker.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/ffmpeg/ffmpeg/dist/ffmpeg.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/ffmpeg/core/dist/ffmpeg-core.js') }}"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
                <script>
                    console.log(self.crossOriginIsolated)
                    if (self.crossOriginIsolated) {
                        console.log('La página está aislada de origen cruzado');
                    } else {
                        console.log('La página no está aislada de origen cruzado');
                    }

        const { createFFmpeg, fetchFile } = FFmpeg;
        const ffmpeg = createFFmpeg({ log: true });

        let user = {!! json_encode($user) !!};
        let activity = {!! json_encode($activity) !!};

        let audioUrl = {!! json_encode($activityMedia->audio) !!};
        console.log('url: '+audioUrl)
        let isMobile = window.matchMedia('(hover: none)').matches;
        {{--const recorder = new Recorder('{!! url('/storage/audios/audio-original.mp3') !!}');--}}
        const recorder = new Recorder(location.protocol + '//' + location.host + '/storage/'+audioUrl);
        const rateController = new RateController();
        const player = new Player();
        {{--const url = '{!! url('/subs/sub-test.vtt') !!}'--}}
        const url = document.URL + '/subs/sub-test.vtt';
        let recordings, recData = [];

        const browser = rateController.getBrowser();
        const os = rateController.getOS();
        console.log(browser, os)

        $(function () {
            let audiotag = document.getElementById("audio");
            let select = document.getElementById('rol');
            let selection = document.querySelectorAll('.rol');
            let subDiv = document.getElementById('sub');

            audiotag.onloadedmetadata = function() {
                $('#totalTime').html(formatTime(audiotag.duration))
            };

            //INICIALIZA LOS SUBTITULOS PARA SER MOSTRADOS
            audioTrack.addEventListener(`load`, evt => {
                const {track} = audioTrack;
                track.mode = "showing";
                const {cues} = track;
                // console.log(`${cues.length} cues found`);
                player.displayCues(cues, subDiv);
            });

            // recorder.askForPermission();

            //SIGUE LOS SUBTITULOS A MEDIDA QUE AVANZA EL AUDIO
            audiotag.textTracks[0].oncuechange = function () {
                if (this.activeCues.length > 0) {
                    let activeElement = Array.prototype.find.call(selection, item => item.classList.contains('card-active'));
                    //subDiv.innerHTML = currentCue.text;
                    let currentCue = this.activeCues[1];
                    console.log(currentCue);
                    console.log('active')
                    console.log(this.activeCues);
                    let startTime = currentCue.startTime;
                    let endTime = currentCue.endTime;
                    let duration = (currentCue.endTime - currentCue.startTime) * 1000;
                    let slowValue = 0;

                    //SELECCIONA EL SUBTITULO Y LO DESTACA
                    let li = document.querySelectorAll('.message[data-start="' + currentCue.startTime + '"]')[0];
                    li.classList.add('current')
                    li.style.opacity=1;
                    li.scrollIntoView({ behavior: 'smooth', block: 'center' , inline:'center'});

                    //CONSULTA SI HAY UN ROL SELECCIONADO
                    if (activeElement) {
                        audiotag.muted = true;
                        let elementId = activeElement.id
                        // console.log(elementId)

                        //DESACTIVA LA REPRODUCCION POR LINEA MIENTRAS SE GRABA
                        const subLi = [...document.getElementById('sub').querySelectorAll('li')];
                        subLi.forEach(li => {
                            li.onclick = function (e) {
                                e.preventDefault();
                            }
                        });

                        //CONSULTA SI EL ID DEL SUBTITULO CORRESPONDE AL ID DEL ROL SELECCIONADO
                        if (currentCue.id.includes(elementId)) {
                            let recorderIcon = li.getElementsByTagName("a");
                            recorderIcon[0].style.display = "inline-block";
                            //OPCION QUE GRABA
                            if(duration < 900){
                                slowValue = 0.1;
                                console.log('duration: '+duration, 'slowvalue: '+slowValue)
                            }else {
                                slowValue = 0.4;
                                console.log('duration: '+duration, 'slowvalue: '+slowValue)
                            }
                            audiotag.playbackRate = slowValue;
                            // console.log(audiotag.duration/0.5);
                            recorder.play(startTime, endTime, currentCue, elementId, slowValue)
                            setTimeout(function () {
                                // $(li).removeClass('sub-active');
                                li.classList.remove('sub-active','current');
                                li.classList.add('sub-inactive')
                                recorderIcon[0].style.display = "none";
                            }, duration / slowValue);
                        } else {
                            //OPCION QUE REPRODUCE
                            audiotag.playbackRate = 1;
                            recorder.play(startTime, endTime, currentCue, elementId, slowValue)
                            setTimeout(function () {
                                // $(li).removeClass('sub-active');
                                li.classList.remove('sub-active','current');
                                li.classList.add('sub-inactive')
                            }, duration);
                        }
                    } else {
                        setTimeout(function () {
                            // $(li).removeClass('sub-active');
                            li.classList.remove('sub-active', 'current');
                            li.classList.add('sub-inactive')
                        }, duration);
                    }
                }
            }
            //CONTROLA LA SELECCION DE ROL
            $(".rol-card").click(function () {
                const element = $(this)[0];
                const isActive = element.classList.contains('card-active');
                let btnPlay = document.getElementById('play');
                let btnStop = document.getElementById('stop');
                let btnRecord = document.getElementById('record');
                let dialogues = Array.from(document.getElementsByClassName('cues ' + element.id));
                let lis = Array.from(document.getElementsByClassName('cues'));
                let difference = lis.filter(x => !dialogues.includes(x));

                recorder.askForPermission();

                selection.forEach(div => div.classList.remove('card-active'));
                lis.forEach(li => li.classList.remove('rol-active', 'sub-active', 'me','him', 'sub-inactive'));
                lis.forEach(li => li.style.opacity = 1);
                if (audiotag.paused !== true) {
                    audiotag.pause();
                    audiotag.currentTime = 0;
                    recorder.resetAudios();
                    audioPlay.stopAudio();
                }

                $(this).toggleClass('card-active', !isActive);
                if (isActive) {
                    btnRecord.setAttribute("disabled", "disabled");
                    btnPlay.disabled = true;
                    btnStop.disabled = true;
                    dialogues.forEach(li => li.classList.remove('rol-active','me'));
                    difference.forEach(li => li.classList.remove('him'));
                } else {
                    btnRecord.removeAttribute("disabled");
                    btnPlay.disabled = false;
                    btnStop.disabled = false;
                    dialogues.forEach(li => li.classList.add('rol-active','me'));
                    difference.forEach(li => li.classList.add('him'));
                }
            });

            $(".cues").click(function () {

            });

            audiotag.addEventListener("ended", function () {
                let activeElement = Array.prototype.find.call(selection, item => item.classList.contains('card-active'));
                let btnSend = document.getElementById('send');
                let spinner = document.getElementById('spinner');
                let sendIcon = document.getElementById('send-icon');
                let btnRecord = document.getElementById('record');
                console.log("ended");
                btnRecord.setAttribute("disabled", "disabled");
                player.stopAudio()
                if (activeElement) {
                    recorder.concatBoth().then(function (blob) {
                        console.log(blob)
                        btnSend.addEventListener("click", () => {
                            recorder.sendRecording(blob);
                            btnSend.setAttribute("disabled", "disabled");
                            sendIcon.style.display = "none";
                            spinner.style.display = "inline-block";
                        });
                        btnSend.removeAttribute("disabled");
                        // console.log(btnSend)
                    });


                    // const subLi = [...document.getElementById('sub').querySelectorAll('li')];
                    // //reactivate onclick event to all li
                    // subLi.forEach(li => {
                    //     // li.unbind('click');
                    //     li.prop("onclick", null).on("click");
                    // });

                }

            });
            let playhead = document.getElementById("playhead");
            audiotag.addEventListener("timeupdate", function(){
                let duration = this.duration;
                let currentTime = this.currentTime;
                let percentage = (currentTime / duration)*100;
                let timelineWidth = document.getElementById('timeline').clientWidth-18;
                let movement = ((percentage * timelineWidth)/100);
                // console.log(timelineWidth);
                // console.log((percentage * timelineWidth)/100 + 'px');
                playhead.style.left = movement + 'px';
                $('#totalTime').html(formatTime(duration))
                $('#currentTime').html(formatTime(this.currentTime))
            });

            function formatTime(seconds) {
                minutes = Math.floor(seconds / 60);
                minutes = (minutes >= 10) ? minutes : "" + minutes;
                seconds = Math.floor(seconds % 60);
                seconds = (seconds >= 10) ? seconds : "0" + seconds;
                return minutes + ":" + seconds;
            }
            document.getElementById('stop').addEventListener('click', function () {
                let lis = Array.from(document.getElementsByClassName('cues'));
                let btnRecord = document.getElementById('record');
                let recorderIndicator = document.getElementById('recorder-indicator');
                lis.forEach(li => li.classList.remove('rol-active', 'sub-active', 'me','him', 'sub-inactive'));
                lis.forEach(li => li.style.opacity = 1);
                selection.forEach(div => div.classList.remove('card-active'));
                btnRecord.setAttribute("disabled", "disabled");
                recorderIndicator.style.display = "none";
            });
            document.getElementById('record').addEventListener('click', function () {
                let lis = Array.from(document.getElementsByClassName('message'));
                lis.forEach(li => li.style.opacity = .6);
            });


            let lastScrollTop = 0;
            $('.card-content').scroll(function(event){
                let st = $(this).scrollTop();
                if (st >= 5){
                    // downscroll code
                    $('.rol-container').addClass('scrolled');
                } else if(st < 30){
                    // upscroll code
                    $('.rol-container').removeClass('scrolled');
                }
                // console.log("scrolling", st, lastScrollTop);
                lastScrollTop = st;
            });
        });
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
            recorder.resetAudios()

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
                                recordButton.removeAttribute("disabled");
                                sendButton.setAttribute("disabled", "disabled");
                                sendButton.classList.remove('send-done');
                                M.toast({html: response.message, duration: 18000, classes: 'rounded green darken-1'});
                                sendIcon.style.display = "inline-block";
                                doneIcon.style.display = 'none';
                                if(activityMedia.type === 'q&a'){
                                    // console.log('media')
                                    activityMedia.characters.forEach(character => {
                                        // console.log(character.rol_order);
                                        if(character.rol_order === 'r2'){
                                            let rol = document.getElementById('r2');
                                            // console.log(rol)
                                            rol.classList.remove('card-disabled');
                                        }
                                    });
                                }else {
                                    let rol = Array.from(document.getElementsByClassName('rol-card'));
                                    rol.forEach(div => div.classList.remove('card-disabled'));
                                }
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
                recordButton.removeAttribute("disabled");
                sendButton.setAttribute("disabled", "disabled");
                sendButton.classList.remove('send-done');
                M.toast({html: 'Recording deleted successfully.', duration: 18000, classes: 'rounded green darken-1'});
                sendIcon.style.display = "inline-block";
                doneIcon.style.display = 'none';
                if(activityMedia.type === 'q&a'){
                    activityMedia.characters.forEach(character => {
                        if(character.rol_order === 'r2'){
                            let rol = document.getElementById('r2');
                            rol.classList.remove('card-disabled');
                        }
                    });
                }else {
                    let rol = Array.from(document.getElementsByClassName('rol-card'));
                    rol.forEach(div => div.classList.remove('card-disabled'));
                }
            }
            document.addEventListener("DOMContentLoaded", function() {
                var elems = document.querySelectorAll(".tooltipped");
                var instances = M.Tooltip.init(elems);
            });
        }

    </script>
@endsection
