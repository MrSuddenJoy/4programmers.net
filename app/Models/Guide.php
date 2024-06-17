<?php

namespace Coyote;

use Coyote\Guide\Role;
use Coyote\Guide\Vote;
use Coyote\Models\Asset;
use Coyote\Models\Subscription;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property User $user
 * @property string $title
 * @property string $excerpt
 * @property string $text
 * @property string $html
 * @property Tag[] $tags
 * @property int $user_id
 * @property Comment[] $comments
 * @property Comment[] $commentsWithChildren
 * @property string $slug
 * @property int $votes
 * @property string $role
 * @property Role[] $roles
 */
class Guide extends \Tests\Legacy\Services\Model
{
    use Taggable, SoftDeletes;

    protected $fillable = ['title', 'excerpt', 'text'];

    public function getSlugAttribute(): string
    {
        return str_slug($this->title, '_');
    }

    /**
     * @return null
     */
    public function voters()
    {
        return $this->hasMany(Vote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscribers()
    {
        return $this->morphMany(Subscription::class, 'resource');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function page()
    {
        return $this->morphOne(Page::class, 'content');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'resource', 'tag_resources');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'resource');
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function assets()
    {
        return $this->morphMany(Asset::class, 'content');
    }

    public function commentsWithChildren()
    {
        $userRelation = fn ($builder) => $builder->select(['id', 'name', 'photo', 'deleted_at', 'is_blocked', 'is_online'])->withTrashed();

        return $this
            ->comments()
            ->whereNull('parent_id')
            ->orderBy('id', 'DESC')
            ->with([
                'children' => function ($builder) use ($userRelation) {
                    return $builder->orderBy('id')->with(['user' => $userRelation]);
                },
                'user' => $userRelation
            ]);
    }

    public function loadUserVoterRelation(?int $userId): void
    {
        if ($userId === null) {
            return;
        }

        $this->load(['voters' => fn ($builder) => $builder->select(['id', 'guide_id', 'user_id'])->where('user_id', $userId)]);
    }

    public function loadUserRoleRelation(?int $userId): void
    {
        if ($userId === null) {
            return;
        }

        $this->load(['roles' => fn ($builder) => $builder->select(['id', 'guide_id', 'role'])->where('user_id', $userId)]);
    }
}
