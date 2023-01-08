<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function canLockThread(?User $user)
    {
        if ($user === null) return false;

        return $user->isAdmin();
    }

    public function upload(User $user, User $profileUser)
    {
        return $user->is($profileUser);
    }
}
