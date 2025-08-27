<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'total', 'issued_at', 'due_at', 'status'];

    protected $casts = [
        'issued_at' => 'datetime',
        'due_at'    => 'datetime',
        'total'     => 'decimal:2',
    ];

    public function client()  { return $this->belongsTo(Client::class); }
    public function payments(){ return $this->hasMany(Payment::class); }

    public function paidSum(): float
    {
        return (float) $this->payments()->where('status','acreditado')->sum('amount');
    }

    public function balance(): float
    {
        return max(0, (float)$this->total - $this->paidSum());
    }

    // Cuánto de lo pagado proviene de saldo a favor aplicado
    public function creditApplied(): float
    {
        return (float) $this->payments()
            ->where('status','acreditado')
            ->where('source', 'saldo')
            ->sum('amount');
    }
}
