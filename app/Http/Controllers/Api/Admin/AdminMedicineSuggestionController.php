<?php

namespace App\Http\Controllers\Api\Admin;

use App\Traits\ApiResponser;
use App\Services\MedicineService;
use App\Models\MedicineSuggestion;
use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineSuggestionResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Admin\HandleMedicineSuggestionRequest;

class AdminMedicineSuggestionController extends Controller
{
    use AuthorizesRequests,ApiResponser;

    public function __construct(protected MedicineService $medicineService) {}

    public function index()
    {
        $this->authorize('manage', MedicineSuggestion::class);

        $suggestions = MedicineSuggestion::with('pharmacist')->latest()->paginate(20);

        return $this->success(
            MedicineSuggestionResource::collection($suggestions),
            'All medicine suggestions retrieved successfully.'
        );
    }

    public function handle(HandleMedicineSuggestionRequest $request, MedicineSuggestion $suggestion)
    {
        // Authorization
        $this->authorize('manage', MedicineSuggestion::class);

        // استخدم الـ Service لتنفيذ المنطق
        $updatedSuggestion = $this->medicineService->handleSuggestion(
            $suggestion,
            $request->boolean('approved'),
            $request->input('rejection_reason')
        );

        return $this->success(
            new MedicineSuggestionResource($updatedSuggestion->load('pharmacist')),
            $updatedSuggestion->approved
                ? "Suggestion '{$updatedSuggestion->name}' approved and added to catalog."
                : "Suggestion '{$updatedSuggestion->name}' rejected. Reason: {$updatedSuggestion->rejection_reason}"
        );
    }
}
