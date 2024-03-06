<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($idCourse)
    {
        $roles = Auth::user()->getAllRoles();
        if(collect($roles)->contains('Learner')) {
            return redirect()->route('recorder.index')->with(['instance' =>  Session::get('instance')]);
        }
        if(collect($roles)->contains('Instructor')){
            return redirect()->route('grades.index', $idCourse)->with( ['instance' =>  Session::get('instance')] );
        }
    }
}

