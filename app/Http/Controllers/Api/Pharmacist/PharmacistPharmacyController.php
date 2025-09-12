<?php

namespace App\Http\Controllers\Api\Pharmacist;

use App\Models\Pharmacy;
use App\Traits\ApiResponser;
use App\Services\PharmacyService;
use App\Http\Controllers\Controller;
use App\Http\Resources\PharmacyResource;
use App\Http\Requests\Admin\UpdatePharmacyRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PharmacistPharmacyController extends Controller
{
    use AuthorizesRequests, ApiResponser;

    public function __construct(protected PharmacyService $pharmacyService) {}

    public function update(UpdatePharmacyRequest $request, Pharmacy $pharmacy)
    {
        $this->authorize('update', $pharmacy);

        $pharmacy = $this->pharmacyService->updatePharmacy($pharmacy, $request->validated());
        return $this->success(new PharmacyResource($pharmacy), "Your Pharmacy updated successfully.");
    }
}
