<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $role = $this->user()?->role?->slug;
        return in_array($role, ['gerante', 'caissier'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $role = $this->user()?->role?->slug;

        if ($role === 'caissier') {
            return [
                'type' => ['required', 'in:boisson'],
                'product_id' => [
                    'required',
                    Rule::exists('products', 'id')->where('type', 'boisson'),
                ],
                'from_location' => ['required', 'in:serveur'],
                'to_location' => ['required', 'in:frigo_vente', 'different:from_location'],
                'quantity' => ['required', 'integer', 'min:1'],
                'justification' => ['nullable', 'string', 'max:500'],
            ];
        }

        return [
            'type' => ['required', 'in:nourriture,boisson'],
            'product_id' => ['required', 'exists:products,id'],
            'from_location' => ['nullable', 'in:central_nourriture,central_boisson'],
            'to_location' => ['required', 'in:serveur,frigo_cuisine,frigo_vente', 'different:from_location'],
            'quantity' => ['required', 'integer', 'min:1'],
            'justification' => ['nullable', 'string', 'max:500'],
        ];
    }
}
