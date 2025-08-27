<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'cel1',
        'cel2',
        'dni',
        'address',
        'localidad',
        'cp',
        'gps_lat',
        'gps_lng',
        'status',
    ];

    // Relaciones que ya tengas
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
