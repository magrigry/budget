<?php

namespace App\Http\Requests\Transaction;

use App\Enums\TransactionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
            'account_id' => ['required', 'integer', Rule::exists('accounts', 'id')->where('user_id', $this->user()->id)->whereNull('deleted_at')],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'label' => ['required', 'string', 'max:255'],
            'transacted_at' => ['required', 'date'],
        ];
    }
}
