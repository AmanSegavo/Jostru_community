<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'status', 'pdf_path', 'png_path'])]
class MembershipCard extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
