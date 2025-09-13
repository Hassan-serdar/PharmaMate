<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PharmacyResource;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    /**
     */
    public function index(Request $request)
    {
        $pharmaciesQuery = Pharmacy::query()->with('user'); // جلب الصيدليات مع معلومات اليوزر

        $pharmaciesQuery->when($request->query('status') === 'online', function ($query) {
            return $query->online();
        }); // عم اعمل فلترة فقط ع الصيدليات الأونلاين 
        $pharmaciesQuery->when($request->query('status') === 'offline', function ($query) {
            return $query->offline();
        }); // عم اعمل فلترة فقط ع الصيدليات الأونلاين 

        $pharmacies = $pharmaciesQuery->paginate(8);

        return PharmacyResource::collection($pharmacies);
    }
}
