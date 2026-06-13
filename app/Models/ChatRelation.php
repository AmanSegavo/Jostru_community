<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_user_id',
        'target_type', // 'user', 'division', 'all'
        'target_id',   // user_id or division_id, nullable if 'all'
    ];

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }
}
