<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\TransferFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $from_account_id
 * @property int $to_account_id
 * @property int $amount_cents
 * @property string $label
 * @property CarbonImmutable $transacted_at
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Account $fromAccount
 * @property-read Account $toAccount
 *
 * @method static \Database\Factories\TransferFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereAmountCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereFromAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereToAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereTransactedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transfer withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[Fillable(['from_account_id', 'to_account_id', 'amount_cents', 'label', 'transacted_at'])]
class Transfer extends Model
{
    /** @use HasFactory<TransferFactory> */
    use HasFactory, SoftDeletes;

    /** @return BelongsTo<Account, $this> */
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    /** @return BelongsTo<Account, $this> */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
            'transacted_at' => 'date:Y-m-d',
            'deleted_at' => 'datetime',
        ];
    }
}
