<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\ResourceLink;
use App\Models\Recording;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use File;
use View;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RecorderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $activity = Session::get('instance')->resourceLink->id;
        $activityMedia = Activity::where('resourcelink_id', $activity)->first();
        if($activityMedia === null)
        {
            return view('recorder.empty')->with(['user' => $user->id, 'activity' => $activity]);
        }
        else {
            $recording = $user->hasRecording($activity);
            if ($activityMedia !== null && $activityMedia->type === 'simple') {
                return view('recorder.simple')->with(['user' => $user->id, 'activity' => $activity, 'recording' => $recording, 'activityMedia' => $activityMedia]);
            }else {
                return view('recorder.mix')->with(['user' => $user->id, 'activity' => $activity, 'recording' => $recording, 'activityMedia' => $activityMedia]);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $blob = $request->file("file");
        $user = User::where('id', $request->input("user"))->firstOrFail();
        $resourceLink = ResourceLink::where('id', $request->input("activity"))->firstOrFail();
        $filename = $user->name . '-' . $resourceLink->title . '.mp3';
        $filename = str_replace(' ', '-', $filename);

        // Guardar archivo WAV en la carpeta temporal de Laravel
        $tempPath = $blob->storeAs('tmp', $filename, 'local');

        // Ruta del archivo WAV en la carpeta temporal
        $wavFilePath = storage_path('app/' . $tempPath);

        // Convertir el archivo WAV a MP3 con LAME
        $mp3FilePath = $this->convertWavToMp3($wavFilePath, $resourceLink, $user);

        $recording = Recording::updateOrCreate(
            [
                'user_id' => $user->id,
                'resourcelink_id' => $resourceLink->id
            ],
            [
                'file' => $mp3FilePath
            ]
        );

        $returnHTML = view('recorder.recordings')->with('recording', $recording)->render();

        return response()->json(['status' => 'Uploaded successfully', 'message' => 'Audio enviado con exito.', 'html' => $returnHTML, 'recording' => $recording]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $recording = Recording::findOrFail($id);
            if (Storage::disk('public')->exists($recording->file)) {
                Storage::disk('public')->delete($recording->file);
                $recording->delete();
                $httpCode = 200;
                $message = 'Recording deleted successfully.';
            } else {
                $httpCode = 404;
                $message = 'Recording does not exist.';
            }
            return response()->json(['status' => $httpCode, 'message' => $message]);

        } catch (\Exception $e) {
            $httpCode = 500;
            $message = 'Grabación no pudo ser eliminada.';
            return response()->json(['status' => $httpCode, 'message' => $message]);
        }
    }

    public function mix()
    {
        $recording = Recording::first();
        return view('recorder.mix')->with('recording', $recording);
    }

    public function format()
    {

        // Obtener la ruta del archivo webvtt
        $rutaArchivo = storage_path('/app/public/activities/Lesson-3/Lesson-3.vtt');


        // Leer el contenido del archivo
        $contenidoArchivo = file_get_contents($rutaArchivo);

//        dd($contenidoArchivo);

        // Definir el índice inicial
        $indice = 1;

        // Identificar las líneas de tiempo en el archivo
        preg_match_all("/([0-9]{2}:[0-9]{2}:[0-9]{2}\.[0-9]{3} --> [0-9]{2}:[0-9]{2}:[0-9]{2}\.[0-9]{3})/", $contenidoArchivo, $matches);


        // Recorrer cada línea de tiempo encontrada y clonarla con los nuevos IDs
        foreach ($matches[0] as $match) {
            dd($match   );
            // Clonar la línea de tiempo y agregarle el ID de nombre rX
            $nuevaLineaNombre = str_replace("ID", "r".$indice, $match)." name=r".$indice."\n";
            $contenidoArchivo .= "\n".$nuevaLineaNombre;

            // Clonar la línea de tiempo y agregarle el ID de mensaje rX
            $nuevaLineaMensaje = str_replace("ID", "r".$indice, $match)." message=r".$indice."\n";
            $contenidoArchivo .= "\n".$nuevaLineaMensaje;

            // Incrementar el índice para la siguiente línea de tiempo
            $indice = ($indice == 1) ? 2 : 1;
        }

        // Guardar los cambios en el archivo
        file_put_contents($rutaArchivo, $contenidoArchivo);

        return "El archivo webvtt ha sido procesado correctamente.";
    }

    public function convertWavToMp3(string $inputFile, $resourceLink, $user): string
    {
        // Generar un nombre de archivo único para el archivo de salida
        $outputFile = pathinfo($inputFile, PATHINFO_FILENAME) . '-' . uniqid() . '.mp3';
/*	
	$destinationPath1 = '/home/marcos/public_html/storage/app/public/audios/' . $resourceLink->title;
	if(!file_exists( $destinationPath1 )
		($destinationPath1);

	$destinationPath2 = '/home/marcos/public_html/storage/app/public/audios/' . $resourceLink->title .'/' . $user->name;
        if(!file_exists( $destinationPath2 )
		($destinationPath2);
 */
	$nombreDirectorio1 = '/home/marcos/public_html/storage/app/public/audios/' . $resourceLink->title;         
	if (!File::isDirectory($nombreDirectorio1))   File::makeDirectory($nombreDirectorio1, 0755, true);             
	

	$nombreDirectorio2 = '/home/marcos/public_html/storage/app/public/audios/' . $resourceLink->title .'/' . $user->name;         
        if (!File::isDirectory($nombreDirectorio2))   File::makeDirectory($nombreDirectorio2, 0755, true);             
        


        // Ruta de destino para el archivo de salida
        //$destinationPath = '/audios/' . $resourceLink->title . '/' . $user->name;
        //$destinationPath = '/audios/' . $resourceLink->title . '/'. $user->name .'/'.$outputFile;
        $destinationPath = '/home/marcos/public_html/storage/app/public/audios/' . $resourceLink->title . '/'. $user->name .'/'.$outputFile;
        //$destinationPath = '/home/marcos/public_html/storage/app/public/audios/' . $resourceLink->title . '/' . $user->name;

        // Comando FFmpeg para convertir a MP3
        $command = [
            'ffmpeg',
            '-i',
            $inputFile,
            '-vn',
            '-ar',
            '44100',
            '-ac',
            '2',
            '-ab',
            '128k',
            '-f',
            'mp3',
            $destinationPath,
        ];

        // Ejecutar el comando utilizando Process
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

	$destinationPath = '/audios/' . $resourceLink->title . '/'. $user->name .'/'.$outputFile;

        return $destinationPath;
    }
}
