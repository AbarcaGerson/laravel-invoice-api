<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'string', Rule::in(['01', '03', '07', '08'])],
            'operation_type' => ['sometimes', 'string', 'size:4'],
            'series' => ['required', 'string', 'max:10'],
            'number' => ['required', 'integer', 'min:1'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'customer_document_type' => ['required', 'string', Rule::in(['0', '1', '4', '6', '7'])],
            'customer_document_number' => ['required', 'string', 'max:20'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_code' => ['nullable', 'string', 'max:50'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.unit_code' => ['sometimes', 'string', 'max:10'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_affectation_type' => ['sometimes', 'string', 'size:2'],
        ];
    }
}
