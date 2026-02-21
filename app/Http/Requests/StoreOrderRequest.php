<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->shop !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order.customer_name' => 'required|string|max:255',
            'order.customer_email' => 'nullable|email|max:255',
            'order.customer_phone' => 'nullable|string|max:50',
            'order.customer_address' => 'nullable|string|max:500',
            'order.products' => 'required|array|min:1',
            'order.products.*' => 'required|integer|exists:products,id',
            'order.quantities' => 'required',
            'order.Docs' => 'required|in:Invoice,Quote',
            'order.remise' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order.customer_name.required' => 'Le nom du client est obligatoire.',
            'order.customer_email.email' => 'L\'adresse email n\'est pas valide.',
            'order.products.required' => 'Veuillez sélectionner au moins un produit.',
            'order.products.min' => 'Veuillez sélectionner au moins un produit.',
            'order.products.*.exists' => 'Un des produits sélectionnés n\'existe pas.',
            'order.quantities.required' => 'Les quantités sont obligatoires.',
            'order.Docs.required' => 'Veuillez sélectionner un type de document.',
            'order.Docs.in' => 'Le type de document doit être Facture ou Devis.',
            'order.remise.numeric' => 'La remise doit être un nombre.',
            'order.remise.min' => 'La remise ne peut pas être négative.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $order = $this->input('order', []);
        
        // Convertir les quantités en tableau si c'est une chaîne
        if (isset($order['quantities']) && is_string($order['quantities'])) {
            $order['quantities'] = array_map('trim', explode(',', $order['quantities']));
            $this->merge(['order' => $order]);
        }
    }

    /**
     * Get validated order data with parsed quantities.
     */
    public function getOrderData(): array
    {
        $data = $this->validated()['order'];
        
        // S'assurer que quantities est un tableau
        if (is_string($data['quantities'])) {
            $data['quantities'] = array_map('trim', explode(',', $data['quantities']));
        }
        
        return $data;
    }
}
