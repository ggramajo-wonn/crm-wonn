<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plan_id',
        'name',
        'price',
        'status',
        'started_at',
        'suspended_at',

        // nuevos
        'address',
        'locality',
        'postal_code',
        'gps',
        'ip',
        'router',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'suspended_at' => 'datetime',
        'price'        => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
