<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'creditor_name',
        'member_id',
        'amount',
        'remaining_amount',
        'type',
        'status',
        'due_date',
        'description',
        'user_id'
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
