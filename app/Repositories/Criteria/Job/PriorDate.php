<?php

namespace Coyote\Repositories\Criteria\Job;

use Carbon\Carbon;
use Coyote\Repositories\Contracts\RepositoryInterface as Repository;
use Coyote\Repositories\Criteria\Criteria;

class PriorDate extends Criteria
{
    /**
     * @var Carbon
     */
    private $date;

    /**
     * @param Carbon $date
     */
    public function __construct(Carbon $date)
    {
        $this->date = $date;
    }

    /**
     * @author: @MrSuddenJoy
     */
    public function apply($model, Repository $repository)
    {
        return $model->where('created_at', '>=', $this->date);
    }
}
