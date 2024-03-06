class Recorder {
    constructor(url) {
        this.src = url;
        this.audioData = [];
        this.recordingData = [];
        this.resultRecording = [];
        this.concatenatedAudios = [];
        this.init();
        this.blobHeaderSize = 0;
        this.cueCount = 0;
    }

    async init() {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        this.ctx = new AudioCtx;
        this.sampleRate = this.ctx.sampleRate;
        var files = await this.getFile(this.src);
        this.audioBuffer = files.audioBuffer;
        this.audioBlob = files.audioBlob;
        this.getCabeceraSize(this.audioBlob).then((size) => {
            this.blobHeaderSize = size;
            console.log("El tamaño de la cabecera es: " + this.blobHeaderSize + " bytes.");
        }).catch((error) => {
            console.log("Error al leer el archivo: " + error);
        });

    }
    async getFile(url) {
        const response = await fetch(url)
        if (!response.ok) {
            throw new Error(`${response.url} ${response.statusText}`);
        }
        const audioBlob = await response.blob();
        const arrayBuffer = await audioBlob.arrayBuffer();
        const audioBuffer = await this.ctx.decodeAudioData(arrayBuffer);
        console.log(audioBuffer, arrayBuffer, audioBlob);
        return {audioBuffer: audioBuffer, audioBlob: audioBlob};
    }

    //FUNCION PARA GENERAR EL AUDIO RESULTADO CON GRABACIONES Y AUDIO ORIGINAL
    play = (start, end, cue, rol, slowValue) => {
        let track = document.getElementById("audioTrack");
        let cues = track.track.cues;
        let audioRate = this.sampleRate;
        let headerSizeSeconds = (this.blobHeaderSize * 8)/256000;
        let slowValueNormalized = slowValue !== 0  ? slowValue : 1;
        let timeOut = Math.round( ((end - start) / slowValueNormalized) * 1000)-100;
        let blobSize = this.audioBlob.size;
        let startSlice = 0;
        let endSlice = 0;

        const tasaDeBits = 320; // Tasa de bits en kbps
        const bytesPorSegundo = (tasaDeBits * 1000) / 8;

        const duracionSegundos = this.audioBuffer.duration;
        // const bytesPorSegundo = (this.audioBlob.size / duracionSegundos);

        const inicioBytes = Math.floor(bytesPorSegundo * start);
        const finalBytes = Math.floor(bytesPorSegundo * end);

        console.log('Cue Count: '+this.cueCount);
        console.log('blob_Header_Size: '+this.blobHeaderSize);
        console.log(cues[1]);
        console.log('blob_Header_Size_Segundos: '+headerSizeSeconds);
        // let startSlice = (start - 0.2) * audioRate;
        // let endSlice = (end - 0.2) * audioRate;
        // console.log(startSlice, endSlice);

        console.log('blob_Size: '+blobSize);
        console.log('sampleRate: '+audioRate);
        console.log('slow value: '+slowValue);
        console.log('timeOut: '+timeOut);
        console.log('now: '+((cue.endTime - cue.startTime) / slowValue) * 1000);
        console.log('rate: '+this.audioBuffer.sampleRate);
        console.log('START: '+start, 'End: '+end);

        this.audioData.push([]);
        if (cue.id.includes(rol)) {
            this.record()

            setTimeout(function () {
                // console.log(((cue.endTime - cue.startTime) /slowValue) * 1000 + " milisegundos");
                recordings.stop()
                //calcular la duracion del cue, expandirlo (encontrar formula) y pasarlo a milisegundos
            }, (timeOut));
        } else {
            if(this.cueCount < 15){
                 startSlice = (start+headerSizeSeconds) * audioRate;
                 endSlice = (end+headerSizeSeconds) * audioRate;
                 console.log('inicio+cabecera: '+startSlice, 'final+cabecera'+endSlice);
            } else {
                startSlice = start * audioRate;
                 endSlice = end * audioRate;
            }
            console.log('slow value on play: '+slowValue);
            console.log('slice: '+startSlice, endSlice);
            let blobStartSlice = Math.round((startSlice * blobSize) / this.audioBuffer.length);
            let blobEndSlice = Math.round((endSlice * blobSize) / this.audioBuffer.length);

            try {
                console.log('Blob_star_slice: '+blobStartSlice, 'Blob_end_slice: '+blobEndSlice)
                let blob = this.audioBlob.slice(inicioBytes+this.blobHeaderSize, finalBytes+this.blobHeaderSize, 'audio/mpeg');
                console.log('START_SLICE: '+startSlice, 'End_SLICE: '+endSlice);

                let url = URL.createObjectURL(blob);
                let audio = new Audio(url);
                audio.play();
                audio.setAttribute('id', 'played');
                console.log(audio)
                this.audioData[this.audioData.length - 1].push(blob);
                this.resultRecording.push([]);
                this.resultRecording[this.resultRecording.length - 1].push(blob);
                this.cueCount++;
            } catch (error) {
                console.log(error)
            }
        }

        console.log(this.resultRecording.flat())

    };

    //FUNCION PARA GRABAR (FALTA COMPATIBILIDAD CON IOS Y SAFARI)
    record = () => {
        navigator.mediaDevices.getUserMedia({audio: true}).then(stream => {
            recordings = new MediaRecorder(stream);
            recordings.ondataavailable = evt => {
                this.resultRecording.push([]);
                this.resultRecording[this.resultRecording.length - 1].push(evt.data)
                this.recordingData.push([]);
                this.recordingData[this.recordingData.length - 1].push(evt.data)
            };
            recordings.start();
        });
    }

    //FUNCION PARA DETENER LA GRABACION
    stopRecord = () => {
        // console.log(recData);
        recordings.stop()
    };

    //FUNCION PARA CONCATENAR LAS GRABACIONES
    concatRecordings = () => {
        let blob = new Blob(this.recordingData.flat()),
            url = URL.createObjectURL(blob),
            audio = new Audio(url);

        console.log(audio)
        audio.play();
    };

    //FUNCION PARA CONCATENAR LAS PARTES DEL AUDIO DEL ROL NO SELECCIONADO
    concatAudios = () => {
        let blob = new Blob(this.audioData.flat()),
            url = URL.createObjectURL(blob),
            audio = new Audio(url);
        //console.log(blob)
        audio.play();
    };

    //FUNCION PARA CONCATENAR EL ARRAY RESULTANTE Y GENERAR UN AUDIO FINAL
    concatBoth = async () => {
        let audioFinal = null
        console.log(isMobile)
        let sampleRate = this.sampleRate;
        console.log('rate : ' + sampleRate)
        console.log(this.resultRecording.flat())
        const crunker = new Crunker.default({sampleRate});

        await this.blobToAudioBuffer(this.ctx, this.resultRecording.flat());

        // const test = await this.iterateBlobs(this.ctx,this.resultRecording.flat());

        console.log('audios cortados y mezcaldos ' + this.concatenatedAudios.flat())

        const concat = await crunker.concatAudio(this.concatenatedAudios.flat())
        const output = await crunker.export(concat, 'audio/mp3');

        // const mp3encoder = new Lame.Mp3Encoder(2, sampleRate, 128);
        // console.log(mp3encoder);

        if (self.crossOriginIsolated) {
            //CODIFICACION DE AUDIO A MP3 CON FFPEG
            console.log('La página está aislada de origen cruzado');
            audioFinal = this.processWithCodification(output)
        } else {
            //EXPORTAR AUDIO EN WAV
            console.log('La página no está aislada de origen cruzado');
            audioFinal = this.processWithoutCodification(output)
        }
        return audioFinal;
    };

    //FUNCION PARA CONVERTIR LOS BLOBS RESULTANTES EN AUDIOBUFFER
    blobToAudioBuffer = async (audioContext, blobs) => {
        for (let blob of blobs) {
            const arrayBuffer = await blob.arrayBuffer();
            const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
            console.log(audioBuffer)
            await this.concatenatedAudios.push([]);
            await this.concatenatedAudios[this.concatenatedAudios.length - 1].push(audioBuffer);

        }
        console.log(this.concatenatedAudios)
        // await Promise.all(blobs.map( async blob => {
        //     const arrayBuffer = await blob.arrayBuffer();
        //     const decode = await audioContext.decodeAudioData(arrayBuffer);
        //     console.log(arrayBuffer)
        //     console.log(decode)
        //     await this.concatenatedAudios.push([]);
        //     await this.concatenatedAudios[this.concatenatedAudios.length - 1].push(decode);
        //     return this.concatenatedAudios;
        // }));
    }

    //FUNCION PARA RESETEAR ARRAY GRABACIONES, ARRAY CON PARTES DE AUDIO ORIGINAL Y ARRAY CON AUDIO FINAL
    resetAudios = () => {
        this.audioData.length = 0;
        this.recordingData.length = 0;
        this.resultRecording.length = 0;
        this.concatenatedAudios.length = 0;
    }

    //FUNCTION TO ASK FOR PERMISSION TO RECORD MICROPHONE
    askForPermission = async () => {
        try {
            this.ctx = new AudioContext();
            const stream = await navigator.mediaDevices.getUserMedia({audio: true});
            this.stream = stream;
        } catch (e) {
            console.log('No live audio input: ' + e);
        }
    }

    //FUNCION PARA ENVIAR AUDIO POR LTI A MOODLE
    sendRecording(blob) {
        console.log(blob)
        let formData = new FormData(), request = new XMLHttpRequest();
        let btn = document.getElementById('send');
        // let spinner = document.createElement('span');
        let token = $('meta[name="csrf-token"]').attr('content');
        let responseRecording = document.createElement('li');
        // let resultAudio = document.getElementById('resultAudio');
        // console.log(resultAudio.getAttribute('src'));
        let spinner = document.getElementById('spinner');
        let sendIcon = document.getElementById('send-icon');
        let doneIcon = document.getElementById('done-icon');


        formData.append("user", user);
        formData.append("activity", activity);
        formData.append("file", blob);
        console.log(formData);
        btn.disabled = true;
        // spinner.setAttribute('class', "spinner-border spinner-border-sm");
        // btn.appendChild(spinner);

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
                    btn.removeAttribute("disabled");
                    spinner.style.display= "none";
                    // sendIcon.classList.remove('hide');
                    sendIcon.style.display = 'none';
                    doneIcon.style.display = "inline-block";
                    btn.classList.add('send-done');
                    M.toast({html: data.status, duration: 8000,classes: 'rounded green darken-1'});
                    resultAudio.setAttribute('data-id', data.recording.id);
                    deleteBtn.setAttribute('data-id', data.recording.id);
                    document.getElementById('upload_date').innerHTML = document.getElementById('upload_date').innerHTML.replace("Creation", "Uploaded");


                    // btn.remove();
                    // $('#success-div').css("text-align", "center");
                    // $("#success-div").html(data.message).fadeIn('slow');
                    // $('#success-div').delay(4000).fadeOut('slow');
                    // recordingsList.innerHTML = "";
                    // responseRecording.innerHTML = data.html;
                    // recordingsList.appendChild(responseRecording);
                }
            },
            error: function (qxhr, status, error) {
                btn.removeAttribute("disabled");
                spinner.classList.add('hide');
                sendIcon.classList.remove('hide');
                $("#error-div").html(error).fadeIn('slow');
                $('#error-div').delay(4000).fadeOut('slow');
            }
        });
    }

    //FUNCTION TO DISABLE PERMISSION TO RECORD MICROPHONE
    disablePermission = async () => {
        this.stream.getTracks().forEach(track => track.stop());
    }

    //FUNCTION TO ITERATE BLOS AND CONVERT TO AUDIOBUFFER
    iterateBlobs = (audioContext, blobs) => {
        const promises = blobs.map(async blob => {
            const arrayBuffer = await blob.arrayBuffer();
            const decode = audioContext.decodeAudioData(arrayBuffer, function (buffer) {
                if (typeof onLoadCallback === 'function') {
                    onLoadCallback(null, {buffer: buffer});
                }
            });
            this.concatenatedAudios.push([]);
            this.concatenatedAudios[this.concatenatedAudios.length - 1].push(decode);
            return this.concatenatedAudios;
        }).reduce((promise, next) => promise.then(() => next), Promise.resolve());
        return promises;
    }
    getCabeceraSize(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onloadend = () => {
                const bytes = new Uint8Array(reader.result);

                // La cabecera del archivo MP3 comienza con los 3 bytes "ID3"
                if (bytes[0] === 73 && bytes[1] === 68 && bytes[2] === 51) {
                    const size = ((bytes[6] & 0x7f) << 21) | ((bytes[7] & 0x7f) << 14) | ((bytes[8] & 0x7f) << 7) | (bytes[9] & 0x7f);
                    console.log("El tamaño de la cabecera es: " + size + " bytes.");
                    resolve(size);
                } else {
                    console.log("No se encontró una cabecera ID3 válida.");
                    resolve(0);
                }
            };

            reader.onerror = () => {
                reject(reader.error);
            };

            reader.readAsArrayBuffer(file.slice(0, 10));
        });
    }
    processWithCodification = async (output) => {
        let recordingsList = document.getElementById("recordingList");
        await ffmpeg.load();
        ffmpeg.FS('writeFile', 'test.wav', await fetchFile(output.url));
        await ffmpeg.run('-i','test.wav', 'test.mp3');
        const finalOutput =  ffmpeg.FS('readFile', 'test.mp3');
        let audio = new Blob([finalOutput.buffer], {type: 'audio/mp3'});

        var au = document.createElement('audio');
        au.setAttribute('id', 'resultAudio');
        var li = document.createElement('li');
        li.setAttribute('class', 'center-align');
        au.controls = true;
        au.src = URL.createObjectURL(audio);
        li.appendChild(au);
        let deleteButton = "<p id='upload_date' class=\"center-align\"></p>" +
            "<a id='delete-recording'  class='btn-small red btn-floating tooltipped' data-position=\"bottom\" " +
            "data-tooltip=\"Delete\" type=\"submit\" onclick=\"return confirm('Are you sure you want to discard this audio?')?deleteRecording():'';\">" +
            "<i class='material-icons'>delete</i>" +
            "</a>";
        li.innerHTML += deleteButton;
        li.setAttribute('class', "recordings");
        recordingsList.appendChild(li);
        let resultContainer = document.getElementById("resultContainer");
        resultContainer.style.display = "block";
        console.log(audio.url)
        let uploadDate = document.getElementById("upload_date");
        uploadDate.innerHTML = "Creation date: " + new Date().toLocaleDateString();

        return await audio;

    }

    processWithoutCodification = async (output) => {
        let recordingsList = document.getElementById("recordingList");
        let audio = new Audio(output.url);
        var au = document.createElement('audio');
        au.setAttribute('id', 'resultAudio');
        var li = document.createElement('li');
        li.setAttribute('class', 'center-align');
        au.controls = true;
        au.src = output.url;
        li.appendChild(au);
        let deleteButton = "<p id='upload_date' class=\"center-align\"></p>" +
            "<a id='delete-recording'  class='btn-small red btn-floating tooltipped' data-position=\"bottom\" " +
            "data-tooltip=\"Delete\" type=\"submit\" onclick=\"return confirm('Are you sure you want to discard this audio?')?deleteRecording():'';\">" +
            "<i class='material-icons'>delete</i>" +
            "</a>";
        li.innerHTML += deleteButton;
        li.setAttribute('class', "recordings");
        recordingsList.appendChild(li);
        let resultContainer = document.getElementById("resultContainer");
        resultContainer.style.display = "block";
        console.log(audio.url)
        let uploadDate = document.getElementById("upload_date");
        uploadDate.innerHTML = "Creation date: " + new Date().toLocaleDateString();
        return await output.blob;
    }

}


