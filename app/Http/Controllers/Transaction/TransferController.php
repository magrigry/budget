<?php

namespace App\Http\Controllers\Transaction;

use App\Data\AccountData;
use App\Data\Transaction\TransferData;
use App\Domain\Transaction\TransferManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\IndexFiltersRequest;
use App\Http\Requests\Transaction\StoreTransferRequest;
use App\Http\Requests\Transaction\UpdateTransferRequest;
use App\Models\Account;
use App\Models\Transfer;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TransferController extends Controller
{
    use WithAccountList;

    public function index(IndexFiltersRequest $request): Response
    {
        $this->authorize('viewAny', Transfer::class);

        $userId = $request->user()->id;

        $transfers = Transfer::query()
            ->with('fromAccount', 'toAccount')
            ->whereIn('from_account_id', Account::withTrashed()->where('user_id', $userId)->select('id'))
            ->when($request->accountId(), fn ($q) => $q->where(fn ($q2) => $q2
                ->where('from_account_id', $request->accountId())
                ->orWhere('to_account_id', $request->accountId())
            ))
            ->when($request->dateFrom(), fn ($q) => $q->whereDate('transacted_at', '>=', $request->dateFrom()))
            ->when($request->dateTo(), fn ($q) => $q->whereDate('transacted_at', '<=', $request->dateTo()))
            ->when($request->search(), fn ($q) => $q->where('label', 'like', "%{$request->search()}%"))
            ->orderBy($request->sortBy(), $request->sortDir())
            ->paginate(25);

        $accounts = $request->user()
            ->accounts()
            ->orderBy('name')
            ->get()
            ->map(fn (Account $a) => AccountData::fromModel($a));

        return Inertia::render('transactions/transfers/Index', [
            'transfers' => [
                'items' => collect($transfers->items())->map(
                    fn (Transfer $t) => TransferData::from([
                        ...$t->toArray(),
                        'from_account' => AccountData::fromModel($t->fromAccount),
                        'to_account' => AccountData::fromModel($t->toAccount),
                    ])
                ),
                'meta' => [
                    'current_page' => $transfers->currentPage(),
                    'last_page' => $transfers->lastPage(),
                    'from' => $transfers->firstItem(),
                    'to' => $transfers->lastItem(),
                    'total' => $transfers->total(),
                    'links' => $transfers->linkCollection()->toArray(),
                ],
            ],
            'accounts' => $accounts,
            'filters' => $request->toFilters(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Transfer::class);

        return Inertia::render('transactions/transfers/Create', [
            'accounts' => $this->accountList(),
        ]);
    }

    public function store(StoreTransferRequest $request, TransferManager $manager): RedirectResponse
    {
        $this->authorize('create', Transfer::class);

        $validated = $request->validated();

        $manager->store(
            $request->user(),
            $validated['from_account_id'],
            $validated['to_account_id'],
            $validated['amount_cents'],
            $validated['label'],
            CarbonImmutable::parse($validated['transacted_at']),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('transactions.flash.created')]);

        return to_route('transactions.transfers.index');
    }

    public function edit(Transfer $transfer): Response
    {
        $this->authorize('update', $transfer);

        $transfer->load('fromAccount', 'toAccount');

        return Inertia::render('transactions/transfers/Edit', [
            'transfer' => TransferData::from([
                ...$transfer->toArray(),
                'from_account' => AccountData::fromModel($transfer->fromAccount),
                'to_account' => AccountData::fromModel($transfer->toAccount),
            ]),
            'accounts' => $this->accountList(),
        ]);
    }

    public function update(UpdateTransferRequest $request, Transfer $transfer, TransferManager $manager): RedirectResponse
    {
        $this->authorize('update', $transfer);

        $validated = $request->validated();

        $manager->update(
            $transfer,
            $validated['from_account_id'],
            $validated['to_account_id'],
            $validated['amount_cents'],
            $validated['label'],
            CarbonImmutable::parse($validated['transacted_at']),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('transactions.flash.updated')]);

        return to_route('transactions.transfers.index');
    }

    public function destroy(Transfer $transfer, TransferManager $manager): RedirectResponse
    {
        $this->authorize('delete', $transfer);

        $manager->delete($transfer);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('transactions.flash.deleted')]);

        return to_route('transactions.transfers.index');
    }
}
