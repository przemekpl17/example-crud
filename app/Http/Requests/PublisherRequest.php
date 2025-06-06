<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublisherRequest extends FormRequest
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
            'name' => 'required|string|max:150|unique:publishers,name',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ];
    }
}
