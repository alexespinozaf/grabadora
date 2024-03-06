<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';

//    protected $fillable = ['grade', 'group_id', 'state','comment', 'user_id', 'resourcelink_id', 'recording_id'];
    protected $fillable = ['name', 'description', 'audio', 'sub', 'characters', 'start_date', 'end_date', 'resourcelink_id'];

    public function resourceLink(){
        return $this->belongsTo(ResourceLink::class, 'resourcelink_id');
    }

    public function characters(){
        return $this->belongsToMany(Character::class, 'activity_character', 'activity_id', 'character_id');
    }

}
