<?php

namespace Coyote\Wiki;

use Coyote\Models\Scopes\ForUser;
use Coyote\User;
// use Illuminate\Database\Eloquent\Model;

/**
 * @property User $user
 */
class Subscriber extends \Tests\Legacy\Services\Model
{
    use ForUser;

    /**
     * The database table used by the \Tests\Legacy\Services\Model.
     *
     * @var string
     */
    protected $table = 'wiki_subscribers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['wiki_id', 'user_id'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
