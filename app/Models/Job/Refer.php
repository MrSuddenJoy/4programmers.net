<?php

namespace Coyote\Job;

// use Illuminate\Database\Eloquent\Model;

class Refer extends \Tests\Legacy\Services\Model
{
    /**
     * The database table used by the \Tests\Legacy\Services\Model.
     *
     * @var string
     */
    protected $table = 'job_refers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['guest_id', 'job_id', 'email', 'name', 'phone', 'friend_name', 'friend_email'];

    /**
     * @var bool
     */
    public $timestamps = false;
}
