<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'client_id',
        'amount',
        'paid_at',
        'source',
        'reference',   // ← agregar
        'status',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    public function client()   { return $this->belongsTo(Client::class); }
    public function invoice()  { return $this->belongsTo(Invoice::class); }
}
