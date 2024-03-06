<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Daniieljc\LaravelMoodle\Clients\Adapters\RestClient;
use Daniieljc\LaravelMoodle\Connection;
use Daniieljc\LaravelMoodle\Services\Course;
use Daniieljc\LaravelMoodle\Services\User;

class MoodleController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new RestClient();
    }
    public function courses()
    {
        $parameters = array();
        $courses = $this->client->sendRequest('core_course_get_courses', $parameters);
        return view('moodle.courses', [
            'courses' => $courses
        ]);
    }

    public function groups($idCourse)
    {
        $parameters = array('courseid' => $idCourse);
        $groups = $this->client->sendRequest('core_group_get_course_groups', $parameters);
        return $groups;
    }
}
