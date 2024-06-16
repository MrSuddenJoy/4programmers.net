<?php

namespace Coyote\Policies;

use Coyote\Comment;
use Coyote\User;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        return !$comment->exists || $user->id === $comment->user_id || $user->can('comment-update');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->can('comment-delete');
    }
}
