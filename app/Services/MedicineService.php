<?php

namespace App\Services;

use App\Enums\SuggestionStatusEnum;
use App\Models\Medicine;
use App\Models\MedicineSuggestion;
use App\Models\User;
use App\Notifications\Pharmacist\SuggestionStatusUpdatedNotification;
use Illuminate\Support\Facades\Notification;

class MedicineService
{
    /**
     * إنشاء دواء جديد في الكتالوج المركزي من قبل الأدمن
     */
    public function createMedicine(array $data): Medicine
    {
        // TODO: Handle image upload if provided
        return Medicine::create($data);
    }

    /**
     * تحديث دواء موجود في الكتالوج المركزي
     */
    public function updateMedicine(Medicine $medicine, array $data): Medicine
    {
        // TODO: Handle image upload if provided
        $medicine->update($data);
        return $medicine;
    }

    /**
     * معالجة اقتراح دواء جديد من قبل الأدمن
     */
    public function handleSuggestion(MedicineSuggestion $suggestion, bool $isApproved, ?string $rejectionReason = null): MedicineSuggestion
    {
        if ($isApproved) {
            // إنشاء دواء جديد
            $this->createMedicine([
                'name' => $suggestion->name,
                'active_ingredient' => $suggestion->active_ingredient,
                'dosage' => $suggestion->dosage,
                'type' => $suggestion->type,
            ]);
        }

        // تحديث حالة الاقتراح
        $suggestion->status = $isApproved ? SuggestionStatusEnum::APPROVED : SuggestionStatusEnum::REJECTED;
        $suggestion->rejection_reason = $isApproved ? null : $rejectionReason;
        $suggestion->save();

        // إشعار الصيدلاني
        if ($suggestion->pharmacist) {
            $suggestion->pharmacist->notify(
                new SuggestionStatusUpdatedNotification(
                    $suggestion,
                    $isApproved
                        ? "Your suggestion for '{$suggestion->name}' has been approved and added to the catalog."
                        : "Your suggestion for '{$suggestion->name}' has been rejected. Reason: {$rejectionReason}"
                )
            );
        }
        return $suggestion;
    }
}
