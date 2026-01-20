<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $user = $this->user();
        
        return [
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['open', 'closed'])],
            'assigned_user_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && !$user->isSystemAdmin()) {
                        $assignedUser = \App\Models\User::find($value);
                        if ($assignedUser && $assignedUser->workspace_id !== $user->workspace_id) {
                            $fail('The assigned user must belong to your workspace.');
                        }
                    }
                },
            ],
        ];
    }
}