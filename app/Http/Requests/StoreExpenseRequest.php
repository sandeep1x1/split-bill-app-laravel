<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for validating expense creation and updates.
 * 
 * Centralizes all expense validation logic including custom rules
 * for ensuring data integrity within bill context.
 */
class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bill = $this->route('bill');
        
        return [
            'title' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'paid_by' => [
                'required',
                'exists:friends,id',
                function ($attribute, $value, $fail) use ($bill) {
                    if (!$bill->friends->contains('id', $value)) {
                        $fail('The selected person must be a friend in this bill.');
                    }
                }
            ],
            'shared_by' => [
                'required',
                'array',
                'min:1',
                'max:20'
            ],
            'shared_by.*' => [
                'exists:friends,id',
                function ($attribute, $value, $fail) use ($bill) {
                    if (!$bill->friends->contains('id', $value)) {
                        $fail('All selected friends must be part of this bill.');
                    }
                }
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Expense title is required.',
            'title.min' => 'Expense title must be at least 2 characters.',
            'title.max' => 'Expense title cannot exceed 255 characters.',
            'title.regex' => 'Expense title can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least $0.01.',
            'amount.max' => 'Amount cannot exceed $999,999.99.',
            'amount.regex' => 'Amount must have maximum 2 decimal places.',
            'paid_by.required' => 'Please select who paid for this expense.',
            'paid_by.exists' => 'The selected person is not valid.',
            'shared_by.required' => 'Please select who shares this expense.',
            'shared_by.min' => 'At least one person must share this expense.',
            'shared_by.max' => 'Maximum 20 people can share an expense.',
            'shared_by.*.exists' => 'One or more selected friends are not valid.'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation: paid_by must be in shared_by
            if (!in_array($this->paid_by, $this->shared_by ?? [])) {
                $validator->errors()->add('paid_by', 'The person who paid must also share this expense.');
            }
        });
    }
}
