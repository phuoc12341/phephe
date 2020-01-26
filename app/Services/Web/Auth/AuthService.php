<?php

namespace App\Services\Web\Auth;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $app;

    public function __construct()
    {
        $this->app = app(Application::class);
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset(User $user)
    {
        return $user->email;
    }

    /**
     * Get the password broker configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfigAuthPassword($name)
    {
        return $this->app['config']["auth.passwords.{$name}"];
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
