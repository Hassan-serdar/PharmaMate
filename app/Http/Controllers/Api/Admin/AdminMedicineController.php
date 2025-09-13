<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMedicineRequest;
use App\Http\Requests\Admin\UpdateMedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use App\Services\MedicineService;
use App\Traits\ApiResponser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class AdminMedicineController extends Controller
{
    use AuthorizesRequests,ApiResponser;

    public function __construct(protected MedicineService $medicineService) {}

    public function index()
    {
        $this->authorize('manage', Medicine::class);
        return MedicineResource::collection(Medicine::paginate(20));
    }

    public function store(StoreMedicineRequest $request)
    {
        $this->authorize('manage', Medicine::class);
        $medicine = $this->medicineService->createMedicine($request->validated());
        return new MedicineResource($medicine);
    }

    public function show(Medicine $medicine)
    {
        $this->authorize('manage', Medicine::class);
        return new MedicineResource($medicine);
    }

    public function update(UpdateMedicineRequest $request, Medicine $medicine)
    {
        $this->authorize('manage', Medicine::class);
        $this->medicineService->updateMedicine($medicine, $request->validated());
        return new MedicineResource($medicine);
    }

    public function destroy(Medicine $medicine)
    {
        $this->authorize('manage', Medicine::class);
        $medicine->delete();
        return $this->success();
    }
}
