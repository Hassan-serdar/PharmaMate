<?php

namespace App\Models;

use App\Enums\PharmacyStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        /**
     * Scope a query to only include online pharmacies.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnline($query)
    {
        return $query->where('status', PharmacyStatusEnum::ONLINE);
    }
    
    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'medicine_pharmacy')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

}
