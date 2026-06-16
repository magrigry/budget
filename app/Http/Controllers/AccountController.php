<?php

namespace App\Http\Controllers;

use App\Domain\Account\AccountManager;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Account::class);

        $showArchived = $request->boolean('archived');

        $accounts = Account::query()
            ->where('user_id', $request->user()->id)
            ->when($showArchived, fn (Builder $q) => $q->withTrashed())
            ->orderBy('name')
            ->get()
            ->map(fn (Account $account) => [
                'id' => $account->id,
                'name' => $account->name,
                'currency' => $account->currency,
                'initial_balance_cents' => $account->initial_balance_cents,
                'balance_cents' => $account->balance_cents,
                'color' => $account->color,
                'icon' => $account->icon,
                'archived' => $account->trashed(),
            ]);

        return Inertia::render('accounts/Index', [
            'accounts' => $accounts,
            'showArchived' => $showArchived,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Account::class);

        return Inertia::render('accounts/Create');
    }

    public function store(StoreAccountRequest $request, AccountManager $manager): RedirectResponse
    {
        $this->authorize('create', Account::class);

        $validated = $request->validated();

        $manager->store(
            $request->user(),
            $validated['name'],
            $validated['currency'],
            $validated['initial_balance_cents'],
            $validated['color'] ?? null,
            $validated['icon'] ?? null,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Compte créé.']);

        return to_route('accounts.index');
    }

    public function show(Account $account): Response
    {
        $this->authorize('view', $account);

        return Inertia::render('accounts/Show', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'currency' => $account->currency,
                'initial_balance_cents' => $account->initial_balance_cents,
                'balance_cents' => $account->balance_cents,
                'color' => $account->color,
                'icon' => $account->icon,
            ],
        ]);
    }

    public function edit(Account $account): Response
    {
        $this->authorize('update', $account);

        return Inertia::render('accounts/Edit', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'currency' => $account->currency,
                'initial_balance_cents' => $account->initial_balance_cents,
                'color' => $account->color,
                'icon' => $account->icon,
            ],
        ]);
    }

    public function update(UpdateAccountRequest $request, Account $account, AccountManager $manager): RedirectResponse
    {
        $this->authorize('update', $account);

        $validated = $request->validated();

        $manager->update(
            $account,
            $validated['name'],
            $validated['currency'],
            $validated['color'] ?? null,
            $validated['icon'] ?? null,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Compte mis à jour.']);

        return to_route('accounts.index');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $this->authorize('delete', $account);

        $account->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Compte archivé.']);

        return to_route('accounts.index');
    }

    public function restore(int $id): RedirectResponse
    {
        $account = Account::withTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $this->authorize('restore', $account);

        $account->restore();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Compte restauré.']);

        return to_route('accounts.index');
    }
}
