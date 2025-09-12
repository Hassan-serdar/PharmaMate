<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Pharmacy;
use App\Traits\ApiResponser;
use App\Services\PharmacyService;
use App\Http\Controllers\Controller;
use App\Http\Resources\PharmacyResource;
use App\Http\Requests\Admin\StorePharmacyRequest;
use App\Http\Requests\Admin\UpdatePharmacyRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminPharmacyController extends Controller
{
    use ApiResponser, AuthorizesRequests;

    public function __construct(protected PharmacyService $pharmacyService) {}

    public function index()
    {
        $pharmacies = $this->pharmacyService->getAllPharmacies();
        return $this->success(PharmacyResource::collection($pharmacies), "All pharmacies retrieved successfully.");
    }

    public function show(Pharmacy $pharmacy)
    {
        $pharmacy = $this->pharmacyService->getPharmacyWithMedicines($pharmacy);
        return $this->success(new PharmacyResource($pharmacy), "Pharmacy details retrieved successfully.");
    }

    public function store(StorePharmacyRequest $request)
    {
        $this->authorize('store',Pharmacy::class);
        $pharmacy = $this->pharmacyService->createPharmacy($request->validated());
        return $this->success(new PharmacyResource($pharmacy), "Pharmacy created successfully.", 201);
    }

    public function update(UpdatePharmacyRequest $request, Pharmacy $pharmacy)
    {
        $this->authorize('update', $pharmacy);

        $pharmacy = $this->pharmacyService->updatePharmacy($pharmacy, $request->validated());
        return $this->success(new PharmacyResource($pharmacy), "Pharmacy updated successfully.");
    }

    public function destroy(Pharmacy $pharmacy)
    {
        $this->authorize('delete', $pharmacy);
        $this->pharmacyService->deletePharmacy($pharmacy);

        return $this->success(null, "Pharmacy deleted successfully.");
    }
}
