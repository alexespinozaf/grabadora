@extends('layouts.app')

@section('css')
    <link href="{{ URL::asset('css/recordings.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
@endsection

@section('content')
    <div class="container">
        <div class="card text-center">
            <div class="card-header">
                <h3>Grabadora</h3>
            </div>
            <div class="card-body">
                <span id="min">00</span>:<span id="sec">00</span><br>
                <button id="recordButton">Grabar</button>
                <button id="stopButton" disabled>Parar</button>
            </div>
            <div class="card-footer">
                <h5>Log</h5>
                <pre id="log"></pre>
            </div>
        </div>
        <br>
        <h3 class="text-center">Grabaciones</h3>
        <br>
        <ol id="recordingsList">
            @if($recording)
                <li>
                    <audio id="{{$recording->id}}" controls>
                        <source src="{{url('/storage/').$recording->file}}" type="audio/mpeg">
                        <track kind="captions" src="{{url('/subs/sub.vtt')}}" srclang="en" label="English" default>
                    </audio>
                    <button data-id="{{$recording->id}}" id="deleteRecording" class="btn btn-danger"
                            onclick="return confirm('Esta seguro que quiere descartar este audio?')"
                            style="margin-left: 10px;">Descartar
                    </button>
                    <br>
                    <p style="margin-left: 10px; margin-top: auto">{{$recording->updated_at}}</p>
                </li>
{{--                <div id="sub" class="text-center"></div>--}}
            @endif
        </ol>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ URL::asset('/js/recorder.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/webRecorder/WebAudioRecorder.js') }}"></script>
    <script>
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
        var user = {!! json_encode($user) !!};
        var activity = {!! json_encode($activity) !!};

        if ({!! json_encode($recording) !!}) {
            document.getElementById("recordButton").disabled = true;
        }

        // $(function() {
        //     $('audio')[0].textTracks[0].oncuechange = function() {
        //         if (this.activeCues.length > 0){
        //             var currentCue = this.activeCues[0];
        //             $('#sub').html(currentCue.text);
        //             console.log(currentCue)
        //         }
        //     }
        //
        //     var recordingsList = document.getElementById("recordingsList");
        //     var recordButton = document.getElementById("recordButton");
        //     $('#deleteRecording').on("click", function () {
        //         var id = $(this).data("id");
        //         var token = $("meta[name='csrf-token']").attr("content");
        //
        //         $.ajax(
        //             {
        //                 url: window.location.origin + '/recorder/' + id,
        //                 type: 'DELETE',
        //                 data: {
        //                     "id": id,
        //                     "_token": token,
        //                 },
        //                 success: function (response) {
        //                     console.log(response);
        //                     recordingsList.innerHTML = "";
        //                     recordButton.disabled = false;
        //                     $('#success-div').css("text-align", "center");
        //                     $("#success-div").html(response.message).fadeIn('slow');
        //                     $('#success-div').delay(4000).fadeOut('slow');
        //                 },
        //                 error: function (qxhr, status, error) {
        //                     $("#error-div").html(error).fadeIn('slow');
        //                     $('#error-div').delay(4000).fadeOut('slow');
        //                 }
        //             });
        //
        //     });
        // });
    </script>
@endsection
