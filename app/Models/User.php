<?php

namespace App\Models;

use xcesaralejandro\lti1p3\Models\User as BaseUser;

class User extends BaseUser
{
   public function grades()
   {
       return $this->hasMany(Grade::class, 'user_id', 'id');
   }

   public function recordings()
   {
       return $this->hasMany(Recording::class, 'user_id','id');
   }

   public function hasRecording($resourcelink_id)
   {
       return $this->hasMany(Recording::class, 'user_id','id')->where('resourcelink_id', $resourcelink_id)->first();
   }
}
