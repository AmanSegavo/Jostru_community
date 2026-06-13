<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rab extends Model
{
    protected $guarded = [];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function items()
    {
        return $this->hasMany(RabItem::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }
}
