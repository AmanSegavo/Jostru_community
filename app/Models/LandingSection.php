<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSection extends Model
{
    protected $fillable = [
        'section_key', 'section_label', 'icon', 'badge_text',
        'heading', 'subheading', 'cta_text', 'cta_link',
        'media_path', 'media_type', 'is_visible', 'sort_order',
    ];

    protected $casts = ['is_visible' => 'boolean'];

    /**
     * Get a section by key, with fallback defaults.
     */
    public static function getSection(string $key): ?self
    {
        return static::where('section_key', $key)->first();
    }

    /**
     * Get all visible sections as key => model map.
     */
    public static function allKeyed(): array
    {
        return static::where('is_visible', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('section_key')
            ->all();
    }

    public function getMediaUrlAttribute(): ?string
    {
        if (!$this->media_path) return null;
        return asset('landing/' . $this->media_path);
    }
}
