<?php

namespace App\Http\Requests;

use App\Enums\BookStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'published_at' => 'nullable|date',
            'author_id' => 'required|exists:authors,id',
            'publisher_id' => 'required|exists:publishers,id',
            'status' => ['sometimes', new Enum(BookStatus::class)],
            'price' => 'required|numeric|min:0',
        ];
    }
}
