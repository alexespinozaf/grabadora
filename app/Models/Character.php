<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $table = 'characters';

    protected $fillable = ['name', 'description', 'image', 'rol_order'];

    public function activities(){
        return $this->belongsToMany(Activity::class, 'activity_character', 'character_id', 'activity_id');
    }
}
