<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;
    protected $fillable = ['path', 'original_name', 'mime_type', 'size'];
    protected $appends = ['url'];

    public function attachable(): MorphTo
    {
        return $this->morphTo(); 
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}