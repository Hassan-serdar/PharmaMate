<?php

namespace App\Http\Controllers\Api\Pharmacist;

use App\Traits\ApiResponser;
use App\Models\MedicineSuggestion;
use App\Http\Controllers\Controller;
use App\Services\PharmacyInventoryService;
use App\Http\Resources\MedicineSuggestionResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Pharmacist\StoreMedicineSuggestionRequest;

class MedicineSuggestionController extends Controller
{
    use AuthorizesRequests,ApiResponser;

    public function __construct(protected PharmacyInventoryService $inventoryService) {}

    public function index()
    {
        $suggestions = auth()->user()->medicineSuggestions()->latest()->paginate(15);

        return $this->success(
            MedicineSuggestionResource::collection($suggestions),
            'Your medicine suggestions retrieved successfully.'
        );
    }

    public function store(StoreMedicineSuggestionRequest $request)
    {
        $this->authorize('create', MedicineSuggestion::class);

        $suggestion = $this->inventoryService->createSuggestion(auth()->user(), $request->validated());

        return $this->success(
            new MedicineSuggestionResource($suggestion),
            "Suggestion for '{$suggestion->name}' submitted successfully.",
            201
        );
    }
}
