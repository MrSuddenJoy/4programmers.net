<?php

namespace Coyote\Guide;

use Coyote\Guide;
use Coyote\Models\Scopes\ForUser;
use Coyote\User;
// use Illuminate\Database\Eloquent\Model;

class Role extends \Tests\Legacy\Services\Model
{
    use ForUser;

    const JUNIOR = 'junior';
    const MID = 'mid';
    const SENIOR = 'senior';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['guide_id', 'user_id', 'role'];

    /**
     * The database table used by the \Tests\Legacy\Services\Model.
     *
     * @var string
     */
    protected $table = 'guide_roles';

    /**
     * @var array
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
