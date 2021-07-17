<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMetaData extends Model
{
    use HasFactory;

    public function metaData()
    {
        return $this->hasOne(UserMetaData::class);
    }
}
