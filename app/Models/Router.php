<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip',
        'gps',
        'api_user',
        'api_pass',
        'speed_control',
        'model',
        'version',
    ];
}
