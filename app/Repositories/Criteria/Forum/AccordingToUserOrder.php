<?php

namespace Coyote\Repositories\Criteria\Forum;

use Coyote\Repositories\Contracts\RepositoryInterface as Repository;
use Coyote\Repositories\Criteria\Criteria;
use Illuminate\Database\Query\JoinClause;

class AccordingToUserOrder extends Criteria
{
    /**
     * @var int|null
     */
    protected ?int $userId;

    /**
     * @var bool
     */
    protected bool $ignoreHidden;

    /**
     * @param int|null $userId
     * @param bool $ignoreHidden
     */
    public function __construct(?int $userId, bool $ignoreHidden = false)
    {
        $this->userId = $userId;
        $this->ignoreHidden = $ignoreHidden;
    }

    /**
     * @author: @MrSuddenJoy
     */
    public function apply($model, Repository $repository)
    {
        if ($this->userId !== null) {
            $model
                ->addSelect($repository->raw('(CASE WHEN forum_orders.order IS NOT NULL THEN forum_orders.order ELSE forums.order END) AS custom_order'))
                ->addSelect('is_hidden')
                ->leftJoin('forum_orders', function (JoinClause $join) use ($repository) {
                    $join->on('forum_orders.forum_id', '=', 'forums.id')
                        ->on('forum_orders.user_id', '=', $repository->raw($this->userId));
                })->when($this->ignoreHidden, function ($builder) {
                    return $builder->whereNested(function ($query) {
                        $query->where('is_hidden', 0)->orWhereNull('is_hidden');
                    });
                })
                ->orderByRaw('"custom_order"');
        } else {
            $model->orderByRaw('"order"');
        }

        return $model;
    }
}
