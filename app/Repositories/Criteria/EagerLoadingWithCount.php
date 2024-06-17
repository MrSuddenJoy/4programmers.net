<?php

namespace Coyote\Repositories\Criteria;

use Coyote\Repositories\Contracts\RepositoryInterface as Repository;

class EagerLoadingWithCount extends Criteria
{
    /**
     * @var string|\string[]
     */
    private $relations;

    /**
     * @param string[] $relations
     */
    public function __construct($relations)
    {
        $this->relations = $relations;
    }

    public function apply($model, Repository $repository)
    {
        return $model->withCount($this->relations);
    }
}
