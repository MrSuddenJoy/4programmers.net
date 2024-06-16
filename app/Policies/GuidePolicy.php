<?php

namespace Coyote\Policies;

use Coyote\Guide;
use Coyote\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuidePolicy
{
    // Think of sth that can be used here (in `use` directive)

    /**
     * @param User $user
     * @param Guide $guide
     * @return bool
     */
    public function update(User $user, Guide $guide): bool
    {
        return $user->id === $guide->user_id || $user->can('guide-update');
    }

    /**
     * @param User $user
     * @param Guide $guide
     * @return bool
     */
    public function delete(User $user, Guide $guide): bool
    {
        return $user->id === $guide->user_id || $user->can('guide-delete');
    }
}
