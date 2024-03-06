class Player{
    constructor() {
        this.play = document.getElementById("audio");
        this.playBtn = document.getElementById("play")
    }
    //FUNCION PARA REPRODUCIR AUDIO COMO EJEMPLO
    playAudio(value){
        let audiotag = document.getElementById("audio");
        let text = document.getElementById("play").firstChild;
        if(value === "play"){
            if (audio.paused === true) {
                audiotag.play();
                text.innerHTML = "pause";
            } else if (audio.paused !== true) {
                audiotag.pause();
                text.innerHTML = "play_arrow";
            }
        }else {
            audiotag.currentTime = 0;
            audiotag.play();
        }
    }

    //FUNCION PARA PAUSAR AUDIO DE EJEMPLO
    stopAudio = () => {
        let audiotag = document.getElementById("audio");
        let text = document.getElementById("play").firstChild;
        audiotag.pause();
        audiotag.currentTime = 0;
        text.innerHTML = "play_arrow";
    };

    //FUNCION PARA CARGAR LOS SUBTITLOS EN UN DIV
    displayCues = (cues, subDiv) => {
        for(var i=0, len = cues.length; i < len; i++) {
            var cue = cues[i];
            var transText="";
            let clickableTransText = "";
            transText = cue.text;
            if(cue.id.includes("name")) {
                clickableTransText = "<li class='cues "+ cue.id +"' data-state='0' data-start="+cue.startTime+" id=" + cue.id +  "" + ">" + transText + "</li>";
             }else {
                clickableTransText = "<li class='message cues "+ cue.id +"' data-state='0' data-start="+cue.startTime+" id=" + cue.id +  " onclick='player.jumpTo(" + cue.startTime + ");'" + ">" + transText + " " +
                    "<a style='display: none' id='record-indicator' class='btn-small red btn-floating pulse red'><i id='indicator-icon' class='material-icons'>keyboard_voice</i></a></li>";
            }
            subDiv.innerHTML += clickableTransText;
        }
    };
    //FUNCION PARA REPRODUCIR AUDIO EN LA FRASE SELECCIONADA
    jumpTo = time => {
        let audiotag = document.getElementById("audio");
        let li = document.querySelector('[data-start="'+time+'"]');
        let text = document.getElementById("play").firstChild;
        text.innerHTML = "pause";
        li.classList.remove('sub-inactive');
        console.log(li.classList);
        audiotag.currentTime = time;
        audiotag.play();
    };


    //function to detect play button
    playButton = () => {
        let audiotag = document.getElementById("audio");
        let text = document.getElementById("play").firstChild;
        if (audio.paused === true) {
            audiotag.play();
            text.data =  "Pause";
        } else if (audio.paused !== true) {
            audiotag.pause();
            text.data = "Play";
        }
    }
    //function to ask for microphone permission
    getPermission = () => {
        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(function (stream) {
                    var audio = document.getElementById("audio");
                    audio.srcObject = stream;
                    audio.play();
                })
                .catch(function (err) {
                    console.log("An error occurred: " + err);
                });
        }
    }
}
