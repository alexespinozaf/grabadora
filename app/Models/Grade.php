<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grades';

    protected $fillable = ['grade', 'group_id', 'state','comment', 'user_id', 'resourcelink_id', 'recording_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function activity()
    {
        return $this->belongsTo(ResourceLink::class, 'resourcelink_id');
    }

    public function recording()
    {
        return $this->belongsTo(Recording::class, 'recording_id');
    }
}
