<?php

namespace App\Policies;

use App\Models\Channel;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class ChannelPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }
}
