<?php

namespace App\Models;

use App\Enums\MedicineTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'active_ingredient', 'dosage', 'type', 'image_path', 'description', 'manufacturer'
    ];

    protected $casts = [
        'type' => MedicineTypeEnum::class,
    ];

    /**
     */
    public function pharmacies(): BelongsToMany
    {
        return $this->belongsToMany(Pharmacy::class, 'medicine_pharmacy')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // استخدام الحدث 'deleting'
        static::deleting(function ($medicine) {
            // التحقق إذا كان للدواء صورة وحذفها
            if ($medicine->image_path) {
                Storage::disk('public')->delete($medicine->image_path);
            }
        });
    }

}