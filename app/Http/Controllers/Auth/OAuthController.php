<?php

namespace Coyote\Http\Controllers\Auth;

use Coyote\Http\Controllers\Controller;
use Coyote\Http\Factories\MediaFactory;
use Coyote\Repositories\Contracts\UserRepositoryInterface as User;
use Coyote\Services\Stream\Activities\Login as Stream_Login;
use Coyote\Services\Stream\Activities\Create as Stream_Create;
use Coyote\Services\Stream\Objects\Person as Stream_Person;
use Laravel\Socialite\Contracts\Factory as Socialite;

class OAuthController extends Controller
{
    use MediaFactory;

    /**
     * @var User
     */
    private $user;

    /**
     * OAuthController constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function login($provider)
    {
        return $this->getSocialiteFactory()->driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback($provider)
    {
        $oauth = $this->getSocialiteFactory()->driver($provider)->user();
        $user = $this->user->findWhere(['provider' => $provider, 'provider_id' => $oauth->getId()])->first();

        if (!$user) {
            $user = $this->user->findByEmail($oauth->getEmail());

            if ($user !== null && $user->provider === null) {
                // merge with existing user account
                $user->provider = $provider;
                $user->provider_id = $oauth->getId();
                $user->save();

                stream(Stream_Login::class);
            } else {
                $name = $oauth->getName() ?: $oauth->getNickName();

                // it's important to check login name using case insensitive...
                if ($this->user->findByName($name)) {
                    // komunikatu bledu nie mozemy przekazac w sesji poniewaz z jakiegos powodu
                    // jest ona gubiona
                    return redirect()->route('register', [
                        'error' => sprintf('Uuups. Niestety login %s jest już zajęty.', $name)
                    ]);
                }

                // create new user in database
                $photoUrl = isset($oauth->avatar_original) ? $oauth->avatar_original : $oauth->getAvatar();
                $filename = null;

                if ($photoUrl) {
                    $media = $this->getMediaFactory()->make('user_photo')->put(file_get_contents($photoUrl));
                    $filename = $media->getFilename();
                }

                $user = $this->user->newUser([
                    'name' => $name,
                    'email' => $oauth->getEmail(),
                    'photo' => $filename,
                    'is_active' => 1,
                    'is_confirm' => 1,
                    'provider' => $provider,
                    'provider_id' => $oauth->getId()
                ]);

                stream(Stream_Create::class, new Stream_Person($user->toArray()));
            }
        } else {
            // put information into the activity stream...
            stream(Stream_Login::class);
        }

        auth()->login($user, true);
        return redirect()->intended(route('home'));
    }

    /**
     * @return Socialite
     */
    public function getSocialiteFactory()
    {
        return app(Socialite::class);
    }
}
