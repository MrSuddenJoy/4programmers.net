<?php

namespace Coyote\Events;

use Coyote\User;


class SuccessfulLogin
{
    

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $ip;

    /**
     * @var string
     */
    public $browser;

    /**
     * @param User $user
     * @param string $ip
     * @param string $browser
     */
    public function __construct(User $user, string $ip, string $browser)
    {
        $this->user = $user;
        $this->ip = $ip;
        $this->browser = $browser;
    }
}
