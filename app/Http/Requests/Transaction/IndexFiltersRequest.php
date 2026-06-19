<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexFiltersRequest extends FormRequest
{
    private const SESSION_KEY_COMMON = 'transaction_filters.common';

    private const SESSION_KEY_SPECIFIC = 'transaction_filters.specific';

    /** @var list<string> */
    private const COMMON_KEYS = ['account_id', 'date_from', 'date_to', 'search', 'sort_by', 'sort_dir'];

    /** @var list<string> */
    private const SPECIFIC_KEYS = ['category_id', 'type', 'group_by'];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $session = $this->session();

        if ($this->boolean('reset')) {
            $session->forget(self::SESSION_KEY_COMMON);
            $session->forget(self::SESSION_KEY_SPECIFIC);

            return;
        }

        $saved = $session->get(self::SESSION_KEY_COMMON, []);

        if ($this->isTransactionRoute()) {
            $saved = array_merge($saved, $session->get(self::SESSION_KEY_SPECIFIC, []));
        }

        $incoming = array_filter($this->except(['reset', '_f']), fn ($v) => $v !== null && $v !== '');

        if ($this->boolean('_f')) {
            // User is actively filtering: absent keys mean "cleared", don't restore from session
            $this->merge($incoming);
        } else {
            // Fresh navigation: restore session for absent keys
            $this->merge(array_merge($saved, $incoming));
        }
    }

    protected function passedValidation(): void
    {
        if ($this->boolean('reset')) {
            return;
        }

        $session = $this->session();
        $validated = $this->validated();

        $session->put(self::SESSION_KEY_COMMON, array_intersect_key($validated, array_flip(self::COMMON_KEYS)));

        if ($this->isTransactionRoute()) {
            $session->put(self::SESSION_KEY_SPECIFIC, array_intersect_key($validated, array_flip(self::SPECIFIC_KEYS)));
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reset' => ['nullable', 'boolean'],
            '_f' => ['nullable', 'boolean'],
            'account_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
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

    public function categoryId(): ?int
    {
        return $this->integer('category_id') ?: null;
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

    private function isTransactionRoute(): bool
    {
        return $this->route()?->getName() === 'transactions.index';
    }
}
