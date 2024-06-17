<?php

namespace Coyote\Topic;

// use Illuminate\Database\Eloquent\Model;

class Track extends \Tests\Legacy\Services\Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['topic_id', 'forum_id', 'marked_at', 'guest_id'];

    /**
     * The database table used by the \Tests\Legacy\Services\Model.
     *
     * @var string
     */
    protected $table = 'topic_track';

    /**
     * @var array
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:se';

    /**
     * @var array
     */
    public $casts = ['marked_at' => 'datetime'];
}
