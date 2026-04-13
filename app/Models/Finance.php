<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = ['user_id', 'amount', 'type', 'description', 'transaction_date'];
    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
