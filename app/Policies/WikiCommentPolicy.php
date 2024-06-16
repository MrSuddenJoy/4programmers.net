<?php

namespace Coyote\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Coyote\User;
use Coyote\Wiki\Comment;

class WikiCommentPolicy
{
    // Think of sth that can be used here (in `use` directive)

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    private function check(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id || $user->can('wiki-admin');
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function update(User $user, Comment $comment)
    {
        return $this->check($user, $comment);
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function delete(User $user, Comment $comment)
    {
        return $this->check($user, $comment);
    }
}
