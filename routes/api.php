<?php

// @deprecated
$this->any('token/verify', ['uses' => 'User\SessionTokenController@verifyToken']);
$this->get('users/{user}', ['uses' => 'User\UserApiController@get']);

$this->prefix('v1')->group(function () {
    $this->get('microblogs', ['uses' => 'Api\Microblog\HomeController@index']);
    $this->post('login', ['uses' => 'Api\LoginController@login']);
});

