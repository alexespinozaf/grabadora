<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use xcesaralejandro\lti1p3\Facades\Ags;
use xcesaralejandro\lti1p3\DataStructure\Instance;
use xcesaralejandro\lti1p3\Classes\Content;
use App\Models\ResourceLink;
use App\Models\Platform;
use App\Models\Context;
use App\Models\User;
use App\Models\Grade;
use App\Models\Activity;
use App\Classes\Moodle;
use Session;
use Carbon\Carbon;


class GradesController extends Controller
{
    public function __construct()
    {
        //create variable scale
        $this->scale = [
            1 => '1.0',
            2 => '1.1',
            3 => '1.2',
            4 => '1.3',
            5 => '1.4',
            6 => '1.5',
            7 => '1.6',
            8 => '1.7',
            9 => '1.8',
            10 => '1.9',
            11 => '2.0',
            12 => '2.1',
            13 => '2.2',
            14 => '2.3',
            15 => '2.4',
            16 => '2.5',
            17 => '2.6',
            18 => '2.7',
            19 => '2.8',
            20 => '2.9',
            21 => '3.0',
            22 => '3.1',
            23 => '3.2',
            24 => '3.3',
            25 => '3.4',
            26 => '3.5',
            27 => '3.6',
            28 => '3.7',
            29 => '3.8',
            30 => '3.9',
            31 => '4.0',
            32 => '4.1',
            33 => '4.2',
            34 => '4.3',
            35 => '4.4',
            36 => '4.5',
            37 => '4.6',
            38 => '4.7',
            39 => '4.8',
            40 => '4.9',
            41 => '5.0',
            42 => '5.1',
            43 => '5.2',
            44 => '5.3',
            45 => '5.4',
            46 => '5.5',
            47 => '5.6',
            48 => '5.7',
            49 => '5.8',
            50 => '5.9',
            51 => '6.0',
            52 => '6.1',
            53 => '6.2',
            54 => '6.3',
            55 => '6.4',
            56 => '6.5',
            57 => '6.6',
            58 => '6.7',
            59 => '6.8',
            60 => '6.9',
            61 => '7.0'
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($idCourse)
    {
        //
        $instance = collect(Session::get('instance'))->toArray();
        $content = collect($instance['content']->getRawJwt())->toArray();
        $moodle = new Moodle;
        $groups = $moodle->groups($idCourse);
        $groups = collect($groups)->sortBy('id');
        $activity = Activity::where('resourcelink_id', $instance['resourceLink']['id'])->first();
        return view('grades.index')->with(compact('groups', 'idCourse', 'instance', 'content', 'activity'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //FUNCION PARA GUARDAR CALIFICACIONES DENTRO DE LA GRABADORA
    public function store(Request $request)
    {
        //
        try {

            if (empty($request->input('grade'))) {
                $grade = Grade::where  ([
                    'user_id' => $request->input('studentId'),
                    'resourcelink_id' => $request->input('activity'),
                    'recording_id' => $request->input('recordingId')
                    ])->firstOrFail();

                if($grade){
                    $grade->comment = $request->input('comment');
                    $grade->save();
                } else {
                    $httpCode = 500;
                    $message = 'No se pudo registrar el comentario.';
                    return response()->json(['status' => $httpCode, 'message' => $message]);
                }
            } else {
                $grade = Grade::updateOrCreate  ([
                        'user_id' => $request->input('studentId'),
                        'resourcelink_id' => $request->input('activity'),
                        'recording_id' => $request->input('recordingId')
                    ],
                    [
                        'grade' => $request->input('grade'),
                        'group_id' => $request->input('idGroup')
                    ]);

            }
            $httpCode = 200;
            $message = 'Evaluación cargada con éxito.';
            return response()->json(['status' => $httpCode, 'message' => $message,'grade_date' => $grade->updated_at->format('d/m/Y')]);

        } catch (\Exception $e) {
//            dd($e);
            $httpCode = 500;
            $message = 'No se pudo registrar la evaluación.';
            return response()->json(['status' => $httpCode, 'message' => $message]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //FUNCION PARA CARGAR EL DATATABLE CON LOS MIEMBROS DE UN GRUPO CURSO
    public function members(Request $request)
    {
        $moodle = new Moodle;
        $idGroup = $request->post('idGroup');
        $platformId = $request->post('platform_id');
        $moodleMembers = $moodle->getUsersIds($idGroup);
        $recorderMembers = User::whereIn('lti_id', $moodleMembers)->get();
        $data = array();
        if(count($moodleMembers) > count($recorderMembers)){
            $moodleUserMembers = $moodle->members($idGroup);
            $this->syncMoodleMembers($moodleUserMembers, $platformId);
            $recorderMembers = User::whereIn('lti_id', $moodleMembers)->get();
        }
        foreach ($recorderMembers as $key => $row) {
            $hasRecording = $row->hasRecording($request->post('resourcelink_id'));
            $dataRow = array();
            $dataRow['number'] = ($key+1);
            $dataRow['id'] = $row['id'];
            $dataRow['name'] = $row['name'];
            $dataRow['email'] = $row['email'];
            $dataRow['recording'] = (empty($hasRecording)) ? 0 : $hasRecording;
            $dataRow['recording_date'] = (empty($hasRecording)) ? '' : $hasRecording->created_at->format('d-m-Y');
            if($hasRecording){
                $hasGrade = $hasRecording->hasGrade($request->post('resourcelink_id'), $row['id']);
                $dataRow['grade'] = (empty($hasGrade)) ? 0 : $hasGrade->grade;
                $dataRow['comment'] = (empty($hasGrade->comment)) ? '' : (($hasGrade->comment == 'null') ? '' : $hasGrade->comment);
                $dataRow['grade_date'] = (empty($hasGrade)) ? '' : $hasGrade->updated_at->format('d-m-Y');
            }else{
                $dataRow['grade'] = 0;
                $dataRow['comment'] = '';
                $dataRow['grade_date'] = '';
            }
            //dd($dataRow);
            $data[] = $dataRow;
        }
        //dd($members);
        return Datatables::of($data)->make(true);
    }

    //FUNCION PARA PUBLICAR UNA NOTA EN MOODLE
    public function sendEvaluation(Request $request)
    {
        $grade = $request->input('grade');
        $studentId = $request->input('studentId');
        $comment = $request->input('comment');
        $indice = $this->obtenerIndiceDeNota($this->scale, $grade);
        $data = array(
            "timestamp" => date('Y-m-d H:i:s'),
            "scoreGiven" => $indice,
            "scoreMaximum" => '61',
            "comment" => $comment,
            "activityProgress" => "Completed",
            "gradingProgress" => "FullyGraded",
            "userId" => $studentId
        );
        $requestInstance = json_decode($request->input('instance'), true);
        $content = json_decode($request->input('content'), true);
        $content = new Content(json_decode(json_encode($content)));
        $instance = new Instance;
        $instance->platform = new Platform($requestInstance['platform']);
        $instance->context = new Context($requestInstance['context']);
        $instance->resourceLink = new ResourceLink($requestInstance['resourceLink']);
        $instance->user = new User($requestInstance['user']);
        $instance->content = $content;
        $instance->jwt = $requestInstance['jwt'];
        Ags::init($instance);
        $response = Ags::putGrade($data)->getStatusCode();
        if($response = 200){
            Session::flash('message', 'Nota publicada con exito.');
            Session::flash('alert-class', 'alert-success');
        }else{
            Session::flash('message', 'La nota no pudo ser publicada.');
            Session::flash('alert-class', 'alert-danger');
        }
        return redirect()->route('grades.index', $instance->context->lti_id)->with(['instance' =>  $instance]);

    }

    //FUNCION PARA CREAR USUARIOS A PARTIR DE LOS MIEMBROS DE UN GRUPO CURSO
    public function syncMoodleMembers($moodleMembers, $platformId){
        $members = array();
        foreach ($moodleMembers as $member){
            $fields = [
                'name' => $member["fullname"],
                'given_name' => $member["firstname"],
                'family_name' => $member["lastname"],
                'email' => $member["email"],
                'roles' => "http://purl.imsglobal.org/vocab/lis/v2/membership#Learner",
                'password' => bcrypt($member["username"]),
            ];
            $conditions = ['lti_id' => $member["id"], 'platform_id' => $platformId];
            $members[] = User::updateOrCreate($conditions, $fields);
        }
        return $members;
    }

    //FUNCION PARA PUBLICAR LAS NOTAS EN MOODLE
    public function publishGrades(Request $request)
    {
        try {
            $requestInstance = json_decode($request->input('instance'), true);
            $content = json_decode($request->input('content'), true);
            $content = new Content(json_decode(json_encode($content)));
            $instance = new Instance;
            $instance->platform = new Platform($requestInstance['platform']);
            $instance->context = new Context($requestInstance['context']);
            $instance->resourceLink = new ResourceLink($requestInstance['resourceLink']);
            $instance->user = new User($requestInstance['user']);
            $instance->content = $content;
            $instance->jwt = $requestInstance['jwt'];
            $resourceLink = ResourceLink::where('lti_id',$instance->resourceLink->lti_id)->first();
            Ags::init($instance);
            $grades = Grade::where('resourcelink_id', $resourceLink->id)->where('group_id', $request->input('groupId'))->get();
            $moodleGrades = array();
            foreach ($grades as $grade) {
                $indice = $this->obtenerIndiceDeNota($this->scale, $grade->grade);
                $moodleGrades[] = array(
                    "timestamp" => date('Y-m-d H:i:s'),
                    "scoreGiven" => $indice,
                    "scoreMaximum" => '61',
                    "comment" => $grade->comment,
                    "activityProgress" => "Completed",
                    "gradingProgress" => "FullyGraded",
                    "userId" => $grade->user->lti_id
                );
            }
            $response = Ags::putMultiplegrades($moodleGrades)->getStatusCode();
            $httpCode = 200;
            $message = 'Evaluaciones publicadas con éxito.';
            return response()->json(['status' => $httpCode, 'message' => $message]);
        }
        catch (\Exception $e){
            $httpCode = 500;
            $message = 'No fue posible publicar las evaluaciones.';
            return response()->json(['status' => $httpCode, 'message' => $message , 'error' => $e]);
        }
    }
    //funcion para contar cuantos alumnos tienen una nota del grupo seleccionado
    public function countGrades(Request $request)
    {
        $requestInstance = json_decode($request->input('instance'), true);
        $content = json_decode($request->input('content'), true);
        $content = new Content(json_decode(json_encode($content)));
        $instance = new Instance;
        $instance->platform = new Platform($requestInstance['platform']);
        $instance->context = new Context($requestInstance['context']);
        $instance->resourceLink = new ResourceLink($requestInstance['resourceLink']);
        $instance->user = new User($requestInstance['user']);
        $instance->content = $content;
        $instance->jwt = $requestInstance['jwt'];

        $idGroup = $request->post('idGroup');
        $platformId = $request->post('platform_id');
        $resourceLink = ResourceLink::where('lti_id',$instance->resourceLink->lti_id)->first();
        $grades = Grade::where('resourcelink_id', $resourceLink->id)->where('group_id', $idGroup)->get();
        $countGrades = count($grades);
        $moodle = new Moodle;
        $moodleMembers = $moodle->getUsersIds($idGroup);
        $recorderMembers = User::whereIn('lti_id', $moodleMembers)->get();
        $countAll = count($recorderMembers);
        $difference = $countAll - $countGrades;
        $endDate = Carbon::parse(Activity::where('resourcelink_id', $resourceLink->id)->first()->end_date);
        $dateNow = Carbon::now();
        $remainingTime = $dateNow->diffInDays($endDate, false);
        $endDate = $endDate->format('d/m/Y');
        return response()->json(['countGrades' => $countGrades, 'countAll' => $countAll, 'difference' => $difference,
            'remainingTime' => $remainingTime, 'endDate' => $endDate, 'dateNow' => $dateNow]);
    }

    function obtenerIndiceDeNota($arrayNotas, $notaBuscada) {
        $indice = null;

        foreach ($arrayNotas as $key => $nota) {
            if ($nota == $notaBuscada) {
                $indice = $key;
                break;
            }
        }

        return $indice;
    }
}
