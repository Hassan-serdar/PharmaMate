<?php

namespace App\Models;

use App\Enums\FeedbackStatusEnum;
use App\Enums\FeedbackTypeEnum;
use App\Enums\FeedbackPriorityEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'subject', 'message', 'status', 'priority', 'assigned_to_user_id'
    ];

    protected $casts = [
        'type' => FeedbackTypeEnum::class,
        'status' => FeedbackStatusEnum::class,
        'priority' => FeedbackPriorityEnum::class,
    ];

    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function assignedTo(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
    public function comments(): HasMany 
    {
         return $this->hasMany(FeedbackComment::class)->latest(); 
    }
    public function attachments(): MorphMany 
    {
         return $this->morphMany(Attachment::class, 'attachable'); 
    }
}