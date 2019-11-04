<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Web\WebController;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Web\Auth\RegisterService;
use Illuminate\Support\Facades\Hash;


class RegisterController extends WebController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $registerService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RegisterService $registerService)
    {
        $this->middleware('guest');
        $this->registerService = $registerService;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \App\Http\Requests\RegistersUsers $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }

        return $this->registerService->register($request);
    }
}
