<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use App\Traits\ApiResponser;
use App\Services\FeedbackService;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Http\Requests\StoreFeedbackRequest;
use App\Http\Requests\UpdateFeedbackRequest;
use App\Http\Requests\StoreFeedbackCommentRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeedbackController extends Controller
{
    use ApiResponser, AuthorizesRequests;

    public function __construct(protected FeedbackService $feedbackService) {}

    public function index()
    {
        $feedback = auth()->user()->feedback()->with(['attachments'])->latest()->paginate(10);
        return FeedbackResource::collection($feedback);
    }
    
    public function show(Feedback $feedback)
    {
        $this->authorize('view', $feedback);
        $feedback->load(['attachments', 'comments' => fn($q) => $q->where('is_private', false)]);
        return new FeedbackResource($feedback);
    }

    public function store(StoreFeedbackRequest $request)
    {
        $feedback = $this->feedbackService->store(
            auth()->user(),
            $request->safe()->except('attachments'),
            $request->file('attachments', [])
        );
        return $this->success(new FeedbackResource($feedback->load('attachments')), 'Feedback submitted.', 201);
    }

    public function update(UpdateFeedbackRequest $request, Feedback $feedback)
    {
        $this->authorize('update', $feedback);
        $updatedFeedback = $this->feedbackService->updateFeedbackByUser($feedback, $request->validated());
        return $this->success(new FeedbackResource($updatedFeedback), 'Your feedback has been updated.');
    }
    
        public function storeComment(StoreFeedbackCommentRequest $request, Feedback $feedback)
    {
        // منستدعي "الحارس الأمني" لنتأكد من الصلاحيات
        $this->authorize('addComment', $feedback);

        // منسلم الشغل للـ Service
        $this->feedbackService->addUserComment($feedback, auth()->user(), $request->validated());

        return $this->success(
            new \App\Http\Resources\FeedbackResource($feedback->load('comments')),
            'Your reply has been added successfully.'
        );
    }


    public function destroy(Feedback $feedback)
    {
        $this->authorize('delete', $feedback);
        $feedback->delete();
        return $this->success(message: 'Your feedback has been deleted.');
    }
}