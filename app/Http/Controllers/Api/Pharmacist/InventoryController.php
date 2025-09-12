<?php

namespace App\Http\Controllers\Api\Pharmacist;

use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Traits\ApiResponser;
use GuzzleHttp\Psr7\Request;
use App\Http\Controllers\Controller;
use App\Services\PharmacyInventoryService;
use App\Http\Resources\PharmacyMedicineResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Pharmacist\AddMedicineToInventoryRequest;
use App\Http\Requests\Pharmacist\UpdateMedicineInInventoryRequest;

class InventoryController extends Controller
{
    use AuthorizesRequests,ApiResponser;

    public function __construct(protected PharmacyInventoryService $inventoryService) {}

    private function getUserPharmacy()
    {
        $pharmacy = auth()->user()->pharmacy;
        if (! $pharmacy) {
            return $this->unauthorized('You do not have a pharmacy assigned to your account.');
        }
        return $pharmacy;
    }

    public function index()
    {
        $pharmacy = $this->getUserPharmacy();
        if ($pharmacy instanceof \Illuminate\Http\JsonResponse) return $pharmacy;

        $this->authorize('manageInventory', $pharmacy);

        $medicines = $pharmacy->medicines()->paginate(20);

        return $this->success(
            PharmacyMedicineResource::collection($medicines),
            'Pharmacy inventory retrieved successfully.'
        );
    }

    public function store(AddMedicineToInventoryRequest $request)
    {
        $pharmacy = $this->getUserPharmacy();
        if ($pharmacy instanceof \Illuminate\Http\JsonResponse) return $pharmacy;

        $this->authorize('manageInventory', $pharmacy);

        $medicine = Medicine::findOrFail($request->validated('medicine_id'));
        $this->inventoryService->addMedicineToPharmacy($pharmacy, $medicine, $request->validated());

        return $this->success(null, "Medicine '{$medicine->name}' added to inventory successfully.", 201);
    }

    public function update(UpdateMedicineInInventoryRequest $request, Medicine $medicine)
    {
        $pharmacy = $this->getUserPharmacy();
        if ($pharmacy instanceof \Illuminate\Http\JsonResponse) return $pharmacy;

        $this->authorize('manageInventory', $pharmacy);

        if (! $pharmacy->medicines()->where('medicine_id', $medicine->id)->exists()) {
            return $this->notFound("Medicine '{$medicine->name}' not found in pharmacy inventory.");
        }

        $this->inventoryService->updateMedicineInPharmacy($pharmacy, $medicine, $request->validated());

        return $this->success(null, "Inventory for '{$medicine->name}' updated successfully.");
    }

    public function destroy(Medicine $medicine)
    {
        $pharmacy = $this->getUserPharmacy();
        if ($pharmacy instanceof \Illuminate\Http\JsonResponse) return $pharmacy;

        $this->authorize('manageInventory', $pharmacy);

        if (! $pharmacy->medicines()->where('medicine_id', $medicine->id)->exists()) {
            return $this->notFound("Medicine '{$medicine->name}' does not exist in pharmacy inventory.");
        }

        $this->inventoryService->removeMedicineFromPharmacy($pharmacy, $medicine);

        return $this->success(null, "Medicine '{$medicine->name}' removed from inventory successfully.");
    }
}