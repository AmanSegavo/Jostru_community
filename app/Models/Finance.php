<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = ['user_id', 'division_id', 'rab_id', 'budget_id', 'amount', 'type', 'kategori', 'description', 'transaction_date', 'proof_path', 'status', 'proofs'];
    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
            'proofs' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function rab()
    {
        return $this->belongsTo(Rab::class);
    }
}
