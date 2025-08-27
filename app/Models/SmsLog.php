<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id','phone','message','status','provider','meta','sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'meta'    => 'array',
    ];

    public function client() { return $this->belongsTo(Client::class); }
}
