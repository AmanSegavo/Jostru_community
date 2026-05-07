<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['user_id', 'content', 'image_path', 'media_path', 'media_type', 'file_size', 'link_url', 'tags', 'pinned'];

    public function getTagsArrayAttribute(): array
    {
        if (empty($this->tags)) return [];
        return array_filter(array_map('trim', explode(',', $this->tags)));
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
