<?php

namespace Coyote\Models;

use Coyote\Models\Scopes\ExcludeBlocked;
use Coyote\Models\Scopes\ForUser;
use Coyote\User;
// use Illuminate\Database\Eloquent\Model;

class Subscription extends \Tests\Legacy\Services\Model
{
    use ForUser, ExcludeBlocked;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id'];

    /**
     * The database table used by the \Tests\Legacy\Services\Model.
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * @var array
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resource()
    {
        return $this->morphTo();
    }
}
