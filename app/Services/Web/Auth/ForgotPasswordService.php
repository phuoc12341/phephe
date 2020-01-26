<?php

namespace App\Services\Web\Auth;

use App\Repo\PasswordResetTokenRepository;
use App\Repo\UserRepositoryInterface;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use UnexpectedValueException;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Hashing\Hasher;

class ForgotPasswordService extends AuthService
{
    protected $passwordResetTokenRepository;
    protected $userRepository;
    protected $userService;

    /**
     * The Hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    public function __construct(
        PasswordResetTokenRepository $passwordResetTokenRepository,
        UserRepositoryInterface $userRepository,
        UserService $userService,
        Hasher $hasher
    ) {
        $this->passwordResetTokenRepository = $passwordResetTokenRepository;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->hasher = $hasher;
        parent::__construct();
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Send a password reset link to a user.
     *
     * @param  array  $credentials
     * @return string
     */
    public function sendResetLink(array $credentials)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return Password::INVALID_USER;
        }

        if (method_exists($this, 'recentlyCreatedToken') &&
            $this->recentlyCreatedToken($user)) {
            // return Password::RESET_THROTTLED;
            return 'passwords.throttled';
        }

        // Once we have the reset token, we are ready to send the message out to this
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $token = $this->create($user);
        $this->sendPasswordResetNotification($user, $token);

        return Password::RESET_LINK_SENT;
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  User  $user
     * @return bool
     */
    public function recentlyCreatedToken(User $user)
    {
        $tokenRecord = $this->passwordResetTokenRepository->findTokenByEmail($user->email);

        return $tokenRecord && $this->tokenRecentlyCreated($tokenRecord->created_at);
    }


    /**
     * Determine if the token was recently created.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenRecentlyCreated($createdAt)
    {
        $throttleOfPasswordResetToken = $this->getConfigAuthPassword('users.throttle') ?? 60;
        if ($throttleOfPasswordResetToken <= 0) {
            return false;
        }

        return Carbon::parse($createdAt)->addSeconds($throttleOfPasswordResetToken)->isFuture();
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|null
     *
     * @throws \UnexpectedValueException
     */
    public function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token']);

        $user = $this->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof User) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->userRepository->createQuery();
        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }
            if (is_array($value) || $value instanceof Arrayable) {
                $this->userRepository->chainQueryWhereIn($query, $key, $value);
            } else {
                $this->userRepository->chainQueryWhere($query, $key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }

    /**
     * Create a new token record.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create(User $user)
    {
        $email = $this->getEmailForPasswordReset($user);

        $this->passwordResetTokenRepository->deleteTokenExistingByEmail($email);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $resetToken = $this->createNewToken();

        $this->passwordResetTokenRepository->store($this->getPayload($email, $resetToken));

        return $resetToken;
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->getKeyForHashToken());
    }

    /**
     * Get a key from APP_Key for hash token 
     *
     * @param  void
     * @return void
     */
    protected function getKeyForHashToken()
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return $key;
    }

    /**
     * Build the record payload for the table.
     *
     * @param  string  $email
     * @param  string  $token
     * @return array
     */
    protected function getPayload($email, $token)
    {
        return [
            'email' => $email,
            'token' => $this->hasher->make($token),
            'created_at' => Carbon::now(),
        ];
    }
    
    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', __($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($response)]);
    }

    /**
     * Send the password reset notification.
     *
     * @param  User  $user
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification(User $user, string $token)
    {
        $user->notify(new ResetPassword($token));
    }
}
