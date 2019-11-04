<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Repo\PasswordResetTokenRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class CheckValidPasswordResetToken implements Rule
{
    /**
     * Initialization to add elements to the request
     */
    protected $request;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $token = app(PasswordResetTokenRepository::class)->findTokenByEmail($this->request->email);

        if (is_null($token)) {
            return false;
        }

        // $this->request->merge(['tokenInstance' => $token]);

        return ! tokenExpired($token->created_at) && Hash::check($value, $token->token);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __(Password::INVALID_TOKEN);
    }
}
