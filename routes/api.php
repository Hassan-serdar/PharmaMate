<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\User\PharmacyController;
use App\Http\Controllers\Api\Admin\AdminFeedbackController;
use App\Http\Controllers\Api\Admin\AdminMedicineController;
use App\Http\Controllers\Api\Admin\AdminPharmacyController;
use App\Http\Controllers\Api\Pharmacist\InventoryController;
use App\Http\Controllers\Api\Admin\AdminMedicineSuggestionController;
use App\Http\Controllers\Api\Pharmacist\MedicineSuggestionController;
use App\Http\Controllers\Api\Pharmacist\PharmacistPharmacyController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/pharmacies', [PharmacyController::class, 'index']);
Route::get('/search', [SearchController::class, 'search']);
Route::get('/pharmacies/{pharmacy}/medicines', [SearchController::class, 'searchMedicineInPharmacy']);


// --- روابط المستخدم المسجل  ---
Route::middleware('auth:sanctum')->group(function () {
    // -- الملف الشخصي --
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::delete('/profile', [AuthController::class, 'destroyAccount']);
    
    // -- الشكاوي والاقتراحات الخاصة بالمستخدم --
    Route::get('/my-feedback', [FeedbackController::class, 'index']);          // عرض كل رسائلي
    Route::post('/feedback', [FeedbackController::class, 'store']);             // إرسال رسالة جديدة
    Route::post('/feedback/{feedback}/comments', [FeedbackController::class, 'storeComment']);
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show']);      // عرض تفاصيل رسالة واحدة
    Route::put('/feedback/{feedback}', [FeedbackController::class, 'update']);      // تحديث رسالة (إذا كانت new)
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy']);  // حذف رسالة (إذا كانت new)

});


Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // -- إدارة المستخدمين --
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    
    // -- إدارة الشكاوي والاقتراحات --
    Route::get('/feedback', [AdminFeedbackController::class, 'index']);             // عرض كل الرسائل مع الفلترة
    Route::get('/feedback/{feedback}', [AdminFeedbackController::class, 'show']);       // عرض تفاصيل رسالة مع كل الملاحظات
    Route::put('/feedback/{feedback}', [AdminFeedbackController::class, 'updateDetails']);   // تحديث حالة، أولوية، أو إسناد الرسالة
    Route::post('/feedback/{feedback}/comments', [AdminFeedbackController::class, 'storeComment']); // إضافة رد أو ملاحظة
    Route::delete('/feedback/{feedback}', [AdminFeedbackController::class, 'destroy']);       // حذف أي رسالة

    // مشاهدة جميع الصيدليات
    Route::get('/pharmacies', [AdminPharmacyController::class, 'index']);

    // مشاهدة صيدلية معينة مع الأدوية
    Route::get('/pharmacies/{pharmacy}', [AdminPharmacyController::class, 'show']);

    // إضافة صيدلية جديدة
    Route::post('/pharmacies', [AdminPharmacyController::class, 'store']);
    Route::put('/pharmacies/{pharmacy}', [AdminPharmacyController::class, 'update']); // تعديل
    Route::delete('/pharmacies/{pharmacy}', [AdminPharmacyController::class, 'destroy']); // حذف

    Route::apiResource('medicines', AdminMedicineController::class);
    
    // -- Medicine Suggestion Management --
    Route::get('/suggestions', [AdminMedicineSuggestionController::class, 'index']);
    Route::post('/suggestions/{suggestion}', [AdminMedicineSuggestionController::class, 'handle']);

});

Route::middleware('auth:sanctum')->prefix('pharmacist')->group(function () {
    // -- Inventory Management --
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::post('/inventory', [InventoryController::class, 'store']);
    Route::put('/inventory/{medicine}', [InventoryController::class, 'update']);
    Route::delete('/inventory/{medicine}', [InventoryController::class, 'destroy']);

    // -- Medicine Suggestions --
    Route::get('/suggestions', [MedicineSuggestionController::class, 'index']);
    Route::post('/suggestions', [MedicineSuggestionController::class, 'store']);

    Route::put('/{pharmacy}', [PharmacistPharmacyController::class, 'update']); // تعديل

});


