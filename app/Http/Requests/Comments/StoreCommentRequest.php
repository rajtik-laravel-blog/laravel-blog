<?php

namespace App\Http\Requests\Comments;

use App\Models\Comment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->filled('parent_id')) {
            $parentComment = Comment::find($this->input('parent_id'));

            if ($parentComment && $parentComment->user_id === $this->user()->id) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:comments,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'parent_id' => 'You cannot reply to your own comment.',
        ];
    }
}
