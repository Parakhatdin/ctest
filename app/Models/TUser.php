<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TUser extends Model
{
    use HasFactory;

    public $fillable = [
        "telegram_id",
        "fio",
        "birthday",
        "gender",
        "address",
        "phone_number"
    ];
}
