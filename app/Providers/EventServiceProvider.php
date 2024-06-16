<?php

namespace Coyote\Providers;

use Coyote\Events\CommentSaved;
use Coyote\Events\FirewallWasDeleted;
use Coyote\Events\FirewallWasSaved;
use Coyote\Events\ForumSaved;
use Coyote\Events\MicroblogSaved;
use Coyote\Events\PostSaved;
use Coyote\Events\StreamSaved;
use Coyote\Events\SuccessfulLogin;
use Coyote\Listeners\ActivitySubscriber;
use Coyote\Listeners\ChangeImageUrl;
use Coyote\Listeners\DispatchMicroblogNotifications;
use Coyote\Listeners\DispatchPostCommentNotification;
use Coyote\Listeners\DispatchPostNotifications;
use Coyote\Listeners\FlagSubscriber;
use Coyote\Listeners\FlushFirewallCache;
use Coyote\Listeners\IndexCategory;
use Coyote\Listeners\IndexStream;
use Coyote\Listeners\LogSentMessage;
use Coyote\Listeners\MicroblogListener;
use Coyote\Listeners\PostListener;
use Coyote\Listeners\SendLockoutEmail;
use Coyote\Listeners\SendSuccessfulLoginEmail;
use Coyote\Listeners\SetupLoginDate;
use Coyote\Listeners\SetupWikiLinks;
use Coyote\Listeners\UserSubscriber;
use Coyote\Listeners\WikiListener;
use Coyote\Listeners\PageSubscriber;
use Coyote\Listeners\TopicListener;
use Coyote\Listeners\JobListener;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;

class EventServiceProvider extends \Coyote\Providers\Neon\ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Lockout::class => [SendLockoutEmail::class],
        FirewallWasSaved::class => [FlushFirewallCache::class],
        FirewallWasDeleted::class => [FlushFirewallCache::class],
        SuccessfulLogin::class => [SendSuccessfulLoginEmail::class],
        Login::class => [SetupLoginDate::class],
        MessageSending::class => [ChangeImageUrl::class, LogSentMessage::class],
        ForumSaved::class => [IndexCategory::class],
        StreamSaved::class => [IndexStream::class],
        PostSaved::class => [DispatchPostNotifications::class],
        MicroblogSaved::class => [DispatchMicroblogNotifications::class],
        CommentSaved::class => [DispatchPostCommentNotification::class]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        PageSubscriber::class,
        TopicListener::class,
        JobListener::class,
        MicroblogListener::class,
        WikiListener::class,
        SetupWikiLinks::class,
        ActivitySubscriber::class,
        UserSubscriber::class,
        FlagSubscriber::class,
        PostListener::class
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
