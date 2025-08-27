<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
    'name', 'cuit', 'logo_path',
    'mt_name', 'mt_host', 'mt_user', 'mt_pass',
    ];

    protected $casts = [
    'mail_settings' => 'array',
    ];
}