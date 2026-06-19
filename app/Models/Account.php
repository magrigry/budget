<?php

namespace App\Models;

use App\Enums\TransactionType;
use Carbon\CarbonImmutable;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $currency
 * @property int $initial_balance_cents
 * @property string|null $color
 * @property string|null $icon
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read int $balance_cents
 * @property-read User $user
 *
 * @method static \Database\Factories\AccountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereInitialBalanceCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account withoutTrashed()
 *
 * @property-read Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 *
 * @mixin \Eloquent
 */
#[Fillable(['name', 'currency', 'initial_balance_cents', 'color', 'icon'])]
class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory, SoftDeletes;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Transaction, $this> */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getBalanceCentsAttribute(): int
    {
        $txAdjustment = $this->transactions()
            ->selectRaw(
                'SUM(CASE WHEN type = ? THEN amount_cents ELSE -amount_cents END) as adjustment',
                [TransactionType::Income->value],
            )
            ->value('adjustment') ?? 0;

        $transferOut = Transfer::where('from_account_id', $this->id)->sum('amount_cents');
        $transferIn = Transfer::where('to_account_id', $this->id)->sum('amount_cents');

        return $this->initial_balance_cents + (int) $txAdjustment - (int) $transferOut + (int) $transferIn;
    }

    protected function casts(): array
    {
        return [
            'initial_balance_cents' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }
}
