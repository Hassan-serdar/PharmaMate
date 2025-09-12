<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Models\User;

class PharmacyInventoryService
{
    /**
     * إضافة دواء موجود بالكتالوج إلى مخزون الصيدلية
     */
    public function addMedicineToPharmacy(Pharmacy $pharmacy, Medicine $medicine, array $data): void
    {
        $pharmacy->medicines()->attach($medicine->id, [
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
    }

    /**
     * تحديث كمية أو سعر دواء في مخزون الصيدلية
     */
    public function updateMedicineInPharmacy(Pharmacy $pharmacy, Medicine $medicine, array $data): void
    {
        $update = [];

        if (array_key_exists('quantity', $data)) {
            $update['quantity'] = $data['quantity'];
        }

        if (array_key_exists('price', $data)) {
            $update['price'] = $data['price'];
        }

        if (empty($update)) {
            return;
        }

        $pharmacy->medicines()->updateExistingPivot($medicine->id, $update);
    }

    /**
     * حذف دواء من مخزون الصيدلية
     */
    public function removeMedicineFromPharmacy(Pharmacy $pharmacy, Medicine $medicine): void
    {
        $pharmacy->medicines()->detach($medicine->id);
    }
    
    /**
     * إنشاء اقتراح دواء جديد من قبل الصيدلاني
     */
    public function createSuggestion(User $pharmacist, array $data): void
    {
        $pharmacist->medicineSuggestions()->create($data);
        
        // TODO: Notify admins about the new suggestion
    }
}
