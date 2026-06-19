<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_account_id' => ['required', 'integer', Rule::exists('accounts', 'id')->where('user_id', $this->user()->id)->whereNull('deleted_at')],
            'to_account_id' => ['required', 'integer', Rule::exists('accounts', 'id')->where('user_id', $this->user()->id)->whereNull('deleted_at'), 'different:from_account_id'],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'label' => ['required', 'string', 'max:255'],
            'transacted_at' => ['required', 'date'],
        ];
    }
}
