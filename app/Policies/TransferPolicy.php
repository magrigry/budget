<?php

namespace App\Policies;

use App\Models\Transfer;
use App\Models\User;

class TransferPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transfer $transfer): bool
    {
        return $user->accounts->contains($transfer->from_account_id)
            && $user->accounts->contains($transfer->to_account_id);
    }

    public function delete(User $user, Transfer $transfer): bool
    {
        return $user->accounts->contains($transfer->from_account_id)
            && $user->accounts->contains($transfer->to_account_id);
    }
}
