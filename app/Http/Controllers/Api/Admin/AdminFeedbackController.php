<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use App\Services\FeedbackService;
use App\Traits\ApiResponser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Enums\FeedbackStatusEnum;
use App\Enums\FeedbackPriorityEnum;
use Illuminate\Validation\Rules\Enum;
use App\Http\Requests\Admin\UpdateFeedbackRequest; 

class AdminFeedbackController extends Controller
{
    use ApiResponser, AuthorizesRequests;
    
    public function __construct(protected FeedbackService $feedbackService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Feedback::class);
        $query = Feedback::query()->with(['user', 'assignedTo'])->latest();
        $query->when($request->query('status'), function ($q, $status) {
            if (FeedbackStatusEnum::tryFrom($status)) {
                $q->where('status', FeedbackStatusEnum::from($status));
            }
        });        $feedback = $query->paginate(20);
        return FeedbackResource::collection($feedback);
    }
    
    public function show(Feedback $feedback)
    {
        $this->authorize('view', $feedback);
        $feedback->load(['user', 'assignedTo', 'attachments', 'comments.user']);
        return new FeedbackResource($feedback);
    }

    public function update(Request $request, Feedback $feedback)
    {
        $this->authorize('update', $feedback);
        $data = $request->validate([
            'status' => ['sometimes', 'required', new Enum(FeedbackStatusEnum::class)],
            'priority' => ['sometimes', 'required', new Enum(FeedbackPriorityEnum::class)],
            'assigned_to_user_id' => 'sometimes|nullable|exists:users,id',
        ]);

        $this->feedbackService->updateStatus($feedback, $data);
        return $this->success(new FeedbackResource($feedback), 'Feedback updated successfully.');
    }

    public function storeComment(Request $request, Feedback $feedback)
    {
        $this->authorize('update', $feedback); 
        $data = $request->validate([
            'comment' => 'required|string|max:5000',
            'is_private' => 'required|boolean',
        ]);

        $this->feedbackService->addComment($feedback, auth()->user(), $data);
        return $this->success(new FeedbackResource($feedback->load('comments')), 'Comment added.');
    }

    public function destroy(Feedback $feedback)
    {
        $this->authorize('delete', $feedback);
        $feedback->delete();
        return $this->success(message: 'Feedback has been deleted successfully.');
    }
}