<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MikrotikRouter extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'ip', 'api_user', 'api_password'];

    // Encripta/descifra automáticamente el password en BD (Laravel 10+/11/12)
    protected $casts = [
        'api_password' => 'encrypted',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
