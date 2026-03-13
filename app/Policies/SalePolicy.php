<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function view(User $user, Sale $sale): bool
    {
        return in_array($user->role?->slug, ['caissier', 'gerante'], true);
    }

    public function cancel(User $user, Sale $sale): bool
    {
        return in_array($user->role?->slug, ['caissier', 'gerante'], true);
    }
}
