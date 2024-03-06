<?php

namespace App\Models;

use xcesaralejandro\lti1p3\Models\ResourceLink as BaseResourceLink;

class ResourceLink extends BaseResourceLink
{
    public function recordings()
    {
        return $this->hasMany(Recording::class, 'resourcelink_id', 'id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'resourcelink_id', 'id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'resourcelink_id', 'id');
    }

}
