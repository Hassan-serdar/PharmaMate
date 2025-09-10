<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\FeedbackStatusEnum;
use App\Enums\FeedbackPriorityEnum;
use App\Enums\Role;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class UpdateFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // نتأكد أن المستخدم هو أدمن أو سوبر أدمن
        return $this->user()->role === Role::ADMIN || $this->user()->role === Role::SUPER_ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'required', new Enum(FeedbackStatusEnum::class)],
            'priority' => ['sometimes', 'required', new Enum(FeedbackPriorityEnum::class)],
            'assigned_to_user_id' => [
                'sometimes',
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('role', [Role::ADMIN->value, Role::SUPER_ADMIN->value]);
                }),
            ],
        ];
    }
}
