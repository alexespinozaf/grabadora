<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recording extends Model
{
    use HasFactory;

    protected $table = 'recordings';

    protected $fillable = ['id','file', 'user_id', 'resourcelink_id','created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function activity()
    {
        return $this->belongsTo(ResourceLink::class, 'resourcelink_id');
    }

    public function grade()
    {
        return $this->hasOne(Grade::class, 'recording_id', 'id');
    }

    public function hasGrade($resourcelink_id, $student_id)
    {
        return $this->hasOne(Grade::class, 'recording_id', 'id')->where('resourcelink_id', $resourcelink_id)->where('user_id', $student_id)->first();
    }
}
