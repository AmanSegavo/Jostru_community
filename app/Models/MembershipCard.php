<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipCard extends Model
{
    protected $fillable = ['user_id', 'status', 'pdf_path', 'png_path'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
