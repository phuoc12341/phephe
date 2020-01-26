<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Web\WebController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\Request;
use App\Services\Web\Auth\ForgotPasswordService;

class ForgotPasswordController extends WebController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected $forgotPasswordService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ForgotPasswordService $forgotPasswordService)
    {
        $this->middleware('guest');
        $this->forgotPasswordService = $forgotPasswordService;
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  App\Http\Requests\Auth\ForgotPasswordRequest $request
     * @return \Illuminate\Http\RedirectRes ponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        return $this->forgotPasswordService->sendResetLinkEmail($request);
    }
}
