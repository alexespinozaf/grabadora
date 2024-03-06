<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Character;
use Illuminate\Support\Facades\Storage;
use xcesaralejandro\lti1p3\Models\ResourceLink;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $activities = Activity::all();
        return view('activities.index', compact('activities'));
    }
    //CREATE FUNCTION
    public function create()
    {
        $resourceLinks = ResourceLink::all();
        $characters = Character::all();
        return view('activities.create', compact('resourceLinks', 'characters'));
    }
    //STORE FUNCTION
    public function store(Request $request)
    {
        if($request->type === 'simple'){
            $this->validate($request, [
                'name' => 'required',
                'description' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $activity = new Activity;
            $activity->name = $request->name;
            $activity->description = $request->description;
            $activity->resourceLink_id = $request->resource;
            $activity->start_date = $request->start_date;
            $activity->end_date = $request->end_date;
            $activity->type = $request->type;
            $activity->save();
        }else {
            $this->validate($request, [
                'name' => 'required',
                'description' => 'required',
                'audio' => 'required',
                'sub' => 'required',
                'characters' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $activityName = str_replace(' ', '-', $request->name);
            $path = 'activities/'.$activityName;
            $audio = $request->audio;
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path, $mode = 0777, true);
            }
            Storage::disk('public')->put($path . '/' . $activityName.'.'.$audio->getClientOriginalExtension(), file_get_contents($audio));
            $subtitle = $request->sub;
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path, $mode = 0777, true);
            }
            Storage::disk('public')->put($path . '/' . $activityName.'.'.$subtitle->getClientOriginalExtension(), file_get_contents($subtitle));
            $activity = new Activity;
            $activity->name = $request->name;
            $activity->description = $request->description;
            $activity->audio = $path . '/' . $activityName.'.'.$audio->getClientOriginalExtension();
            $activity->sub = $path . '/' . $activityName.'.'.$subtitle->getClientOriginalExtension();
            $activity->resourceLink_id = $request->resource;
            $activity->start_date = $request->start_date;
            $activity->end_date = $request->end_date;
            $activity->type = $request->type;
            $activity->save();
            $activity->characters()->attach($request->characters);
        }
        return redirect('/activities');
    }
    //EDIT FUNCTION
    public function edit($id)
    {
        $activity = Activity::find($id);
        $characters = Character::all();
        $resourceLinks = ResourceLink::all();
        return view('activities.edit', compact('activity', 'characters', 'resourceLinks'));
    }
    //UPDATE FUNCTION
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'characters' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $activity = Activity::find($id);
        $activityOldName = str_replace(' ', '-', $activity->name);
        $activityNewName = str_replace(' ', '-', $request->name);

        $activity->name = $request->name;
        $activity->description = $request->description;
        $activity->start_date = $request->start_date;
        $activity->end_date = $request->end_date;
        $activity->type = $request->type;
        $activity->resourceLink_id = $request->resource;
        $oldPath = 'activities/'.$activityOldName;
        $newPath = 'activities/'.$activityNewName;

        if($request->name != $activityOldName){
            // Handle new files or rename existing files
            if(isset($request->audio)){
                $audio = $request->audio;
                $activity->audio = $newPath . '/' . $activityNewName.'.'.$audio->getClientOriginalExtension();
                Storage::disk('public')->put($activity->audio, file_get_contents($audio));
            } else {
                $oldAudioPath = $activity->audio;
                $newAudioPath = str_replace($activityOldName, $activityNewName, $oldAudioPath);
                if (Storage::disk('public')->exists($oldAudioPath)) {
                    Storage::disk('public')->move($oldAudioPath, $newAudioPath);
                }
                $activity->audio = $newAudioPath;
            }

            if(isset($request->sub)){
                $subtitle = $request->sub;
                $activity->sub = $newPath . '/' . $activityNewName.'.'.$subtitle->getClientOriginalExtension();
                Storage::disk('public')->put($activity->sub, file_get_contents($subtitle));
            } else {
                $oldSubPath = $activity->sub;
                $newSubPath = str_replace($activityOldName, $activityNewName, $oldSubPath);
                if (Storage::disk('public')->exists($oldSubPath)) {
                    Storage::disk('public')->move($oldSubPath, $newSubPath);
                }
                $activity->sub = $newSubPath;
            }

            // Delete old directory
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->deleteDirectory($oldPath);
            }

        } else {
            // Handle new files
            if(isset($request->audio)){
                $audio = $request->audio;
                $activity->audio = $oldPath . '/' . $activityOldName.'.'.$audio->getClientOriginalExtension();
                Storage::disk('public')->put($activity->audio, file_get_contents($audio));
            }
            if(isset($request->sub)){
                $subtitle = $request->sub;
                $activity->sub = $oldPath . '/' . $activityOldName.'.'.$subtitle->getClientOriginalExtension();
                Storage::disk('public')->put($activity->sub, file_get_contents($subtitle));
            }
        }
        $activity->save();
        $activity->characters()->sync($request->characters);
        return redirect('/activities');
    }
    //DELETE FUNCTION
    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activityName = str_replace(' ', '-', $activity->name);
        Storage::disk('public')->deleteDirectory('activities/'.$activityName);
        $activity->characters()->detach();
        $activity->delete();
        return redirect('/activities');
    }
}
