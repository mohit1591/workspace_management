<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.max' => 'The file must not be larger than 5MB.',
            'file.mimes' => 'The file must be a PDF, JPG, or PNG.',
        ];
    }
}