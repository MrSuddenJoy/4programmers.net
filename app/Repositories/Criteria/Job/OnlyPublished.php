<?php

namespace Coyote\Repositories\Criteria\Job;

use Coyote\Repositories\Contracts\RepositoryInterface as Repository;
use Coyote\Repositories\Criteria\Criteria;

class OnlyPublished extends Criteria
{
    /**
     * @author: @MrSuddenJoy
     */
    public function apply($model, Repository $repository)
    {
        return $model->where('is_publish', true);
    }
}
