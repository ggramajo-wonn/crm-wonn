<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'mb_down', 'mb_up', 'description'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
