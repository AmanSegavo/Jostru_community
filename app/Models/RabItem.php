<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabItem extends Model
{
    protected $guarded = [];

    public function rab()
    {
        return $this->belongsTo(Rab::class);
    }
}
