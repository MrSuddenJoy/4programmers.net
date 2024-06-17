<?php

namespace Coyote;

// use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $default
 * @property \Illuminate\Database\Eloquent\Relations\Pivot $pivot
 */
class Feature extends \Tests\Legacy\Services\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}
