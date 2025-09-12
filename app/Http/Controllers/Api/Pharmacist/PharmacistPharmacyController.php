<?php

namespace App\Http\Controllers\Api\Pharmacist;

use App\Models\User;
use App\Models\Pharmacy;
use App\Traits\ApiResponser;
use App\Services\PharmacyService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PharmacyResource;
use App\Http\Requests\Pharmacist\UpdateMyPharmacyRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PharmacistPharmacyController extends Controller
{
    use AuthorizesRequests, ApiResponser;

    public function __construct(protected PharmacyService $pharmacyService) {}

    public function index()
    {
        $pharmacy = auth()->user()->pharmacy;
        return $this->success(
            new PharmacyResource($pharmacy),
            'Your pharmacy retrieved successfully.'
        );
    }
    public function update(UpdateMyPharmacyRequest $request, PharmacyService $pharmacyService): JsonResponse
    {
        $pharmacy = $request->user()->pharmacy;

        $updatedPharmacy = $pharmacyService->updatePharmacy($pharmacy, $request->validated());
        return $this->success(
            new PharmacyResource($updatedPharmacy->load('user')),
            'Your pharmacy has been updated successfully.'
        );
    }
}
