<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Feedback;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\FeedbackService;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Http\Requests\Admin\UpdateFeedbackRequest;
use App\Http\Requests\Admin\StoreFeedbackCommentRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminFeedbackController extends Controller
{
    use ApiResponser, AuthorizesRequests;
    
    public function __construct(protected FeedbackService $feedbackService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Feedback::class);
        $query = Feedback::query()->with(['user', 'assignedTo'])->latest();
        $query->when($request->query('status'), function ($q, $status) {
            if (\App\Enums\FeedbackStatusEnum::tryFrom($status)) {
                $q->where('status', \App\Enums\FeedbackStatusEnum::from($status));
            }
        });
        $feedback = $query->paginate(20);
        return FeedbackResource::collection($feedback);
    }
    
    public function show(Feedback $feedback)
    {
        $this->authorize('view', $feedback);
        $feedback->load(['user', 'assignedTo', 'attachments', 'comments.user']);
        return new FeedbackResource($feedback);
    }

    public function updateDetails(UpdateFeedbackRequest $request, Feedback $feedback)
    {
        
        $updatedFeedback = $this->feedbackService->updateFeedbackByAdmin($feedback, $request->validated());

        return $this->success(new FeedbackResource($updatedFeedback), 'Feedback updated successfully.');
    }

    public function storeComment(StoreFeedbackCommentRequest $request, Feedback $feedback)
    {
        $this->authorize('update', $feedback); 

        $this->feedbackService->addComment($feedback, auth()->user(), $request->validated());
        return $this->success(new FeedbackResource($feedback->load('comments')), 'Comment added.');
    }

    public function destroy(Feedback $feedback)
    {
        $this->authorize('delete', $feedback);
        $feedback->delete();
        return $this->success(message: 'Feedback has been deleted successfully.');
    }
}