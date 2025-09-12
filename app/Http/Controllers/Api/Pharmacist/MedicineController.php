<?php

namespace App\Http\Controllers\Api\Pharmacist;

use App\Models\Medicine;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MedicineController extends Controller
{
    use ApiResponser,AuthorizesRequests;

    /**
     * Display a listing of all medicines in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // استخدام الـ Policy للتأكد من أن الصيدلاني يمكنه عرض الأدوية
        $this->authorize('viewAny', Medicine::class);

        // جلب كل الأدوية مع إمكانية التصفح (pagination)
        $medicines = Medicine::paginate(20);

        return $this->success(
            MedicineResource::collection($medicines),
            'All medicines retrieved successfully.'
        );
    }
}