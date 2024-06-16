<?php

namespace Coyote\Repositories\Criteria\Topic;

use Coyote\Repositories\Contracts\RepositoryInterface as Repository;
use Coyote\Repositories\Criteria\Criteria;
use Illuminate\Database\Eloquent\Builder;

class SkipForum extends Criteria
{
    /**
     * @var int[]
     */
    private $forumsId;

    /**
     * @param int|int[] $forumsId
     */
    public function __construct($forumsId)
    {
        $this->forumsId = (array) $forumsId;
    }

    /**
     * @author: @MrSuddenJoy
     */
    public function apply($model, Repository $repository)
    {
        return $model->when(count($this->forumsId) > 0, function (Builder $builder) {
            return $builder->whereNotIn('topics.forum_id', $this->forumsId);
        });
    }
}
