<?php

namespace App\Services;

use App\Models\Pharmacy;

class PharmacyService
{
    // جلب كل الصيدليات
    public function getAllPharmacies()
    {
        return Pharmacy::all();
    }

    // جلب صيدلية معينة مع أدوية
    public function getPharmacyWithMedicines(Pharmacy $pharmacy)
    {
        return $pharmacy->load('medicines');
    }

    // إنشاء صيدلية جديدة
    public function createPharmacy(array $data): Pharmacy
    {
        return Pharmacy::create($data);
    }

    // تحديث صيدلية
    public function updatePharmacy(Pharmacy $pharmacy, array $data): Pharmacy
    {
        $pharmacy->update($data);
        return $pharmacy;
    }

    // حذف صيدلية
    public function deletePharmacy(Pharmacy $pharmacy): void
    {
        $pharmacy->delete();
    }
}
