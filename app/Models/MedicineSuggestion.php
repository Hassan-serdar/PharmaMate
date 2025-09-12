<?php

namespace App\Models;

use App\Enums\MedicineTypeEnum;
use App\Enums\SuggestionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacist_id', 'name', 'active_ingredient', 'dosage', 'type', 'status', 'rejection_reason'
    ];

    protected $casts = [
        'type' => MedicineTypeEnum::class,
        'status' => SuggestionStatusEnum::class,
    ];

    public function pharmacist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }
}