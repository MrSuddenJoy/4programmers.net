<?php

namespace Coyote\Http\Controllers\Api;

use \Coyote\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Factory as Auth;

class UserController extends Controller
{
    /**
     * @param Auth $auth
     * @return mixed
     */
    public function index(Auth $auth)
    {
        return $auth->guard('api')->user();
    }
}
