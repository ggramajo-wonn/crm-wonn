<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ipv4Network extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'network',   // e.g. 192.168.0.0
        'cidr',      // e.g. 24
        'router_id', // FK to routers.id (optional)
        'type',      // 'ESTATICO'
    ];

    // Relationships
    public function router()
    {
        return $this->belongsTo(\App\Models\Router::class, 'router_id');
    }
}
