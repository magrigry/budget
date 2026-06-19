<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexFiltersRequest extends FormRequest
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
            'account_id' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', Rule::in(['income', 'expense'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:100'],
            'sort_by' => ['nullable', 'string', Rule::in(['transacted_at', 'amount_cents'])],
            'sort_dir' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'group_by' => ['nullable', 'string', Rule::in(['none', 'day', 'week', 'month', 'year'])],
        ];
    }

    public function accountId(): ?int
    {
        return $this->integer('account_id') ?: null;
    }

    public function type(): ?string
    {
        return $this->string('type')->value() ?: null;
    }

    public function dateFrom(): ?string
    {
        return $this->string('date_from')->value() ?: null;
    }

    public function dateTo(): ?string
    {
        return $this->string('date_to')->value() ?: null;
    }

    public function search(): ?string
    {
        return $this->string('search')->value() ?: null;
    }

    public function sortBy(): string
    {
        return $this->string('sort_by')->value() ?: 'transacted_at';
    }

    /** @return 'asc'|'desc' */
    public function sortDir(): string
    {
        return $this->string('sort_dir')->value() === 'asc' ? 'asc' : 'desc';
    }

    /** @return 'none'|'day'|'week'|'month'|'year' */
    public function groupBy(): string
    {
        $value = $this->string('group_by')->value();

        return in_array($value, ['day', 'week', 'month', 'year'], true) ? $value : 'none';
    }

    /** @return array<string, mixed> */
    public function toFilters(): array
    {
        return [
            'account_id' => $this->accountId(),
            'date_from' => $this->dateFrom(),
            'date_to' => $this->dateTo(),
            'search' => $this->search(),
            'sort_by' => $this->sortBy(),
            'sort_dir' => $this->sortDir(),
            'group_by' => $this->groupBy(),
        ];
    }
}
