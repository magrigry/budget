<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_accounts(): void
    {
        $this->get('/accounts')->assertRedirect('/login');
    }

    public function test_user_can_list_their_accounts(): void
    {
        $user = User::factory()->create();
        Account::factory()->count(3)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get('/accounts')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('accounts/Index')
                ->has('accounts', 3)
            );
    }

    public function test_user_cannot_see_other_users_accounts(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Account::factory()->count(2)->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get('/accounts')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('accounts', 0));
    }

    public function test_user_can_create_an_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/accounts', [
                'name' => 'Compte courant',
                'currency' => 'EUR',
                'initial_balance_cents' => 150000,
                'color' => '#3b82f6',
                'icon' => null,
            ])
            ->assertRedirect('/accounts');

        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'name' => 'Compte courant',
            'currency' => 'EUR',
            'initial_balance_cents' => 150000,
        ]);
    }

    public function test_user_can_update_their_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patch("/accounts/{$account->id}", [
                'name' => 'Nouveau nom',
                'currency' => 'USD',
                'color' => null,
                'icon' => null,
            ])
            ->assertRedirect('/accounts');

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Nouveau nom',
            'currency' => 'USD',
            'initial_balance_cents' => $account->initial_balance_cents,
        ]);
    }

    public function test_user_cannot_update_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->patch("/accounts/{$account->id}", [
                'name' => 'Hack',
                'currency' => 'EUR',
                'initial_balance_cents' => 0,
            ])
            ->assertForbidden();
    }

    public function test_user_can_archive_their_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete("/accounts/{$account->id}")
            ->assertRedirect('/accounts');

        $this->assertSoftDeleted('accounts', ['id' => $account->id]);
    }

    public function test_archived_accounts_excluded_by_default(): void
    {
        $user = User::factory()->create();
        Account::factory()->create(['user_id' => $user->id]);
        Account::factory()->create(['user_id' => $user->id, 'deleted_at' => now()]);

        $this->actingAs($user)
            ->get('/accounts')
            ->assertInertia(fn ($page) => $page->has('accounts', 1));
    }

    public function test_archived_accounts_visible_with_param(): void
    {
        $user = User::factory()->create();
        Account::factory()->create(['user_id' => $user->id]);
        Account::factory()->create(['user_id' => $user->id, 'deleted_at' => now()]);

        $this->actingAs($user)
            ->get('/accounts?archived=true')
            ->assertInertia(fn ($page) => $page->has('accounts', 2));
    }

    public function test_user_can_restore_archived_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'deleted_at' => now()]);

        $this->actingAs($user)
            ->patch("/accounts/{$account->id}/restore")
            ->assertRedirect('/accounts');

        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'deleted_at' => null]);
    }

    public function test_balance_cents_equals_initial_balance_cents_without_transactions(): void
    {
        $account = Account::factory()->make(['initial_balance_cents' => 123456]);

        $this->assertEquals(123456, $account->balance_cents);
    }
}
