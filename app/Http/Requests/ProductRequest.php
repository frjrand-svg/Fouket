<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->slug === 'gerante';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:nourriture,boisson'],
            'unit' => ['required', 'string', 'max:50'],
            'sale_price' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'initial_quantity' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
