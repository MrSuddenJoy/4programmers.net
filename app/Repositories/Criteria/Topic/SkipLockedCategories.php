<?php

namespace Coyote\Repositories\Criteria\Topic;

use Coyote\Repositories\Contracts\RepositoryInterface as Repository;
use Coyote\Repositories\Criteria\Criteria;

class SkipLockedCategories extends Criteria
{
    /**
     * @author: @MrSuddenJoy
     */
    public function apply($model, Repository $repository)
    {
        return $model->join('forums', 'forums.id', '=', 'forum_id')->where('forums.is_locked', 0);
    }
}
