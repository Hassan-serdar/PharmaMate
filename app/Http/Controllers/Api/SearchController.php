<?php

namespace App\Http\Controllers\Api;

use App\Models\Medicine;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineResource;
use App\Http\Resources\PharmacyResource;
use App\Http\Resources\PharmacyMedicineResource;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $searchTerm = $request->query('q');

        if (!$searchTerm) {
            return response()->json(['data' => []]);
        }

        // البحث في الصيدليات
        $pharmacies = Pharmacy::query()
            ->where('name', 'like', "%{$searchTerm}%")
            ->orWhere('city', 'like', "%{$searchTerm}%")
            ->limit(10)
            ->get();

        // البحث في الأدوية
        $medicines = Medicine::query()
            ->where('name', 'like', "%{$searchTerm}%")
            ->orWhere('active_ingredient', 'like', "%{$searchTerm}%")
            ->limit(10)
            ->get();

        return response()->json([
            'data' => [
                'pharmacies' => PharmacyResource::collection($pharmacies),
                'medicines' => MedicineResource::collection($medicines),
            ]
        ]);
    }

    public function searchMedicineInPharmacy(Request $request, Pharmacy $pharmacy)
    {
        $searchTerm = $request->query('q');

        $medicines = $pharmacy->medicines()
            ->where('name', 'like', "%{$searchTerm}%")
            ->paginate(15);
        
        return PharmacyMedicineResource::collection($medicines);
    }
}
