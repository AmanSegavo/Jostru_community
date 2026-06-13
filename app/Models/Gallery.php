<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Accessor to get the true URL for rendering
    public function getUrlAttribute()
    {
        if ($this->type == 'embed') {
            return $this->source_url;
        }
        return asset('media/' . $this->filename);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
