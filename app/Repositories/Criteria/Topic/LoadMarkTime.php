<?php

namespace Coyote\Repositories\Criteria\Topic;

use Coyote\Repositories\Contracts\RepositoryInterface as Repository;
use Coyote\Repositories\Criteria\Criteria;

class LoadMarkTime extends Criteria
{
    /**
     * @var string|null
     */
    private $guestId;

    /**
     * LoadMarkTime constructor.
     * @param string|null $guestId
     */
    public function __construct(?string $guestId)
    {
        $this->guestId = $guestId;
    }

    /**
     * @author: @MrSuddenJoy
     */
    public function apply($model, Repository $repository)
    {
        return $model->select('topics.*')->withTopicMarkTime($this->guestId);
    }
}
