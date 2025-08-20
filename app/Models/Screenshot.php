<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Screenshot extends Model
{
    protected $fillable = [
        'filename',
        'path',
        'size',
        'mime_type',
        'captured_at',
        'session_id',
        'user_agent',
        'ip_address'
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'size' => 'integer'
    ];

    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
