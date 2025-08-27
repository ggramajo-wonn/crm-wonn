<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Olt extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'localidad'];

    public function naps()
    {
        return $this->hasMany(\App\Models\Nap::class);
    }
}
