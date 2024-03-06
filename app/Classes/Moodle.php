<?php

namespace App\Classes;

use Illuminate\Http\Request;
use Daniieljc\LaravelMoodle\Clients\Adapters\RestClient;
use Daniieljc\LaravelMoodle\Connection;
use Daniieljc\LaravelMoodle\Services\Course;
use Daniieljc\LaravelMoodle\Services\User;
use ErrorException;

class Moodle {
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
        //dd($idCourse);
        $parameters = array('courseid' => $idCourse);
        $groups = $this->client->sendRequest('core_group_get_course_groups', $parameters);
        return $groups;
    }

    public function getUsersIds($idGroup)
    {
        $parameters = array('groupids[0]' => $idGroup);
        $ids = $this->client->sendRequest('core_group_get_group_members', $parameters);
        return $ids[0]['userids'];
    }
     public function members($idGroup)
     {
        $ids = $this->getUsersIds($idGroup);
        $members = array();
        if(!empty($ids)){
            $parameters = array('field' => 'id',
                                'values' => $ids);
            $members = $this->client->sendRequest('core_user_get_users_by_field', $parameters);
        }
        return $members;
     }
}
