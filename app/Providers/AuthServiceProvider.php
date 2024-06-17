<?php

namespace Coyote\Providers;

use Coyote\Comment;
use Coyote\Firm;
use Coyote\Forum;
use Coyote\Guide;
use Coyote\Job;
use Coyote\Microblog;
use Coyote\Pm;
use Coyote\Policies\CommentPolicy;
use Coyote\Policies\FirmPolicy;
use Coyote\Policies\ForumPolicy;
use Coyote\Policies\GuidePolicy;
use Coyote\Policies\JobPolicy;
use Coyote\Policies\MicroblogPolicy;
use Coyote\Policies\PmPolicy;
use Coyote\Policies\PostCommentPolicy;
use Coyote\Policies\PostPolicy;
use Coyote\Policies\TopicPolicy;
use Coyote\Policies\WikiCommentPolicy;
use Coyote\Post;
use Coyote\Topic;
use Coyote\User;
use Coyote\Wiki;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends \Coyote\Providers\Neon\ServiceProvider
{
    // cache permission for 1 month
    const CACHE_TTL = 60 * 60 * 24 * 30;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Microblog::class    => MicroblogPolicy::class,
        Forum::class        => ForumPolicy::class,
        Topic::class        => TopicPolicy::class,
        Post::class         => PostPolicy::class,
        Post\Comment::class => PostCommentPolicy::class,
        Job::class          => JobPolicy::class,
        Firm::class         => FirmPolicy::class,
        Pm::class           => PmPolicy::class,
        Wiki\Comment::class => WikiCommentPolicy::class,
        Guide::class        => GuidePolicy::class,
        Comment::class      => CommentPolicy::class,
    ];

    /**
     * Global permissions
     *
     * @var array
     */
    protected $abilities = [
        'adm-access',
        'adm-group',
        'adm-payment',
        'forum-delete',
        'forum-update',
        'forum-lock',
        'forum-move',
        'forum-merge',
        'forum-sticky',
        'forum-emphasis',
        'job-update',
        'job-delete',
        'firm-update',
        'firm-delete',
        'wiki-admin',
        'pastebin-delete',
        'microblog-update',
        'microblog-delete',
        'guide-update',
        'guide-delete',
        'comment-update',
        'comment-delete',
    ];

    /**
     * Users' permissions.
     * A little cache so we don't have to request db/redis every time.
     *
     * @var array
     */
    protected $permissions = [];

    public function boot(): void
    {
        /** @var Gate $gate */
        $gate = $this->app[Gate::class];

        foreach ($this->abilities as $ability) {
            $gate->define($ability, function (User $user) use ($ability) {
                $permissions = $this->getUserPermissions($user);
                return $permissions[$ability] ?? false;
            });
        }
    }

    /**
     * @param User $user
     * @return mixed
     */
    private function getUserPermissions(User $user)
    {
        if (isset($this->permissions[$user->id])) {
            return $this->permissions[$user->id];
        }

        // file cache driver does not support tagging.
        if (config('cache.default') !== 'file') {
            $cache = $this->app[CacheManager::class];
            $result = $cache->tags('permissions')->remember('permission:' . $user->id, self::CACHE_TTL, function () use ($user) {
                return $user->getPermissions()->toArray();
            });
        } else {
            $result = $user->getPermissions()->toArray();
        }
        return $this->permissions[$user->id] = $result;
    }
}
