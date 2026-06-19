<?php

namespace App\Http\Controllers\Transaction;

use App\Data\AccountData;
use App\Data\Transaction\TransactionData;
use App\Domain\Transaction\TransactionManager;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\IndexFiltersRequest;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    use WithAccountList;

    public function index(IndexFiltersRequest $request): Response
    {
        $this->authorize('viewAny', Transaction::class);

        $entries = $this->baseQuery($request)
            ->with('account')
            ->orderBy($request->sortBy(), $request->sortDir())
            ->paginate(25);

        $accounts = $request->user()
            ->accounts()
            ->orderBy('name')
            ->get()
            ->map(fn (Account $a) => AccountData::fromModel($a));

        $totals = $this->computeTotals($request);

        return Inertia::render('transactions/Index', [
            'transactions' => [
                'items' => collect($entries->items())->map(
                    fn (Transaction $t) => TransactionData::from([
                        ...$t->toArray(),
                        'account' => AccountData::fromModel($t->account),
                    ])
                ),
                'meta' => [
                    'current_page' => $entries->currentPage(),
                    'last_page' => $entries->lastPage(),
                    'from' => $entries->firstItem(),
                    'to' => $entries->lastItem(),
                    'total' => $entries->total(),
                    'links' => $entries->linkCollection()->toArray(),
                ],
            ],
            'accounts' => $accounts,
            'filters' => [...$request->toFilters(), 'type' => $request->type()],
            'totals' => $totals,
        ]);
    }

    /** @return Builder<Transaction> */
    private function baseQuery(IndexFiltersRequest $request): Builder
    {
        return Transaction::query()
            ->where('user_id', $request->user()->id)
            ->when($request->accountId(), fn ($q) => $q->where('account_id', $request->accountId()))
            ->when($request->type(), fn ($q) => $q->where('type', $request->type()))
            ->when($request->dateFrom(), fn ($q) => $q->whereDate('transacted_at', '>=', $request->dateFrom()))
            ->when($request->dateTo(), fn ($q) => $q->whereDate('transacted_at', '<=', $request->dateTo()))
            ->when($request->search(), fn ($q) => $q->where('label', 'like', "%{$request->search()}%"));
    }

    /** @return array<string, array{income: int, expense: int, net: int}> */
    private function computeTotals(IndexFiltersRequest $request): array
    {
        if ($request->groupBy() === 'none') {
            return [];
        }

        $periodExpr = match ($request->groupBy()) {
            'day' => "strftime('%Y-%m-%d', transacted_at)",
            'week' => "strftime('%Y-%W', transacted_at)",
            'year' => "strftime('%Y', transacted_at)",
            default => "strftime('%Y-%m', transacted_at)",
        };

        return $this->baseQuery($request)
            ->selectRaw("$periodExpr as period, type, SUM(amount_cents) as total")
            ->groupBy('period', 'type')
            ->orderBy('period', 'desc')
            ->get()
            ->groupBy('period')
            ->map(fn ($rows) => [
                'income' => (int) $rows->where('type', 'income')->sum('total'),
                'expense' => (int) $rows->where('type', 'expense')->sum('total'),
                'net' => (int) ($rows->where('type', 'income')->sum('total') - $rows->where('type', 'expense')->sum('total')),
            ])
            ->all();
    }

    public function create(): Response
    {
        $this->authorize('create', Transaction::class);

        return Inertia::render('transactions/Create', [
            'accounts' => $this->accountList(),
        ]);
    }

    public function store(StoreTransactionRequest $request, TransactionManager $manager): RedirectResponse
    {
        $this->authorize('create', Transaction::class);

        $validated = $request->validated();

        $manager->store(
            $request->user(),
            $validated['account_id'],
            TransactionType::from($validated['type']),
            $validated['amount_cents'],
            $validated['label'],
            CarbonImmutable::parse($validated['transacted_at']),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('transactions.flash.created')]);

        return to_route('transactions.index');
    }

    public function edit(Transaction $transaction): Response
    {
        $this->authorize('update', $transaction);

        $transaction->load('account');

        return Inertia::render('transactions/Edit', [
            'transaction' => TransactionData::from([
                ...$transaction->toArray(),
                'account' => AccountData::fromModel($transaction->account),
            ]),
            'accounts' => $this->accountList(),
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction, TransactionManager $manager): RedirectResponse
    {
        $this->authorize('update', $transaction);

        $validated = $request->validated();

        $manager->update(
            $transaction,
            $validated['account_id'],
            $validated['amount_cents'],
            $validated['label'],
            CarbonImmutable::parse($validated['transacted_at']),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('transactions.flash.updated')]);

        return to_route('transactions.index');
    }

    public function destroy(Transaction $transaction, TransactionManager $manager): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $manager->delete($transaction);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('transactions.flash.deleted')]);

        return to_route('transactions.index');
    }
}
