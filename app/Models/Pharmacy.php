<?php

namespace App\Models;

use App\Enums\PharmacyStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pharmacy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'address_line_1',
        'city',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => PharmacyStatusEnum::class,
    ];

    /**
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
