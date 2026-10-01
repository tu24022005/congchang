<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    protected $fillable = [
        'badge', 'title', 'description', 'image_path', 'image_url',
        'video_path', 'video_url', 'video_autoplay', 'video_loop', 'video_muted',
        'alt_text', 'button_text', 'button_url', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'video_autoplay' => 'boolean',
        'video_loop' => 'boolean',
        'video_muted' => 'boolean',
    ];

    public function getImageSourceAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : $this->image_url;
    }

    public function getVideoSourceAttribute(): ?string
    {
        return $this->video_path
            ? asset('storage/' . $this->video_path)
            : $this->video_url;
    }

    public function getHasVideoAttribute(): bool
    {
        return !empty($this->video_path) || !empty($this->video_url);
    }

    public function getVideoTypeAttribute(): ?string
    {
        if (!$this->has_video) {
            return null;
        }

        $url = $this->video_url ?? '';
        if ($this->youtube_id) {
            return 'youtube';
        }
        if ($this->vimeo_id) {
            return 'vimeo';
        }

        return 'direct';
    }

    public function getYoutubeIdAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i', $this->video_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;
        if (!$id) {
            return null;
        }

        return "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&mute=1&loop=1&playlist={$id}&controls=0&playsinline=1&enablejsapi=1&rel=0&modestbranding=1";
    }

    public function getVimeoIdAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        if (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/i', $this->video_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getVimeoEmbedUrlAttribute(): ?string
    {
        $id = $this->vimeo_id;
        if (!$id) {
            return null;
        }

        return "https://player.vimeo.com/video/{$id}?autoplay=1&loop=1&muted=1&autopause=0&controls=0";
    }

    public function getVideoMimeTypeAttribute(): string
    {
        $src = strtolower($this->video_source ?? '');
        if (str_ends_with($src, '.webm')) {
            return 'video/webm';
        }
        if (str_ends_with($src, '.ogg')) {
            return 'video/ogg';
        }

        return 'video/mp4';
    }
}
