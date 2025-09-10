<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackComment extends Model
{
    use HasFactory;
    protected $fillable = ['feedback_id', 'user_id', 'comment', 'is_private'];

    public function user(): BelongsTo 
    { 
    return $this->belongsTo(User::class); 
    }
    public function feedback(): BelongsTo
    { 
    return $this->belongsTo(Feedback::class); 
    }
}