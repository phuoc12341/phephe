<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $token;
    protected $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token, $url = null)
    {
        $this->user = $user;
        $this->token = $token;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->user->email)
            ->view('emails.mail_forgot_password')
            ->with([
                'subject' => 'Reset Password Notification',
                'url' => $this->url ? $this->url : url(sprintf(config('api.auth.reset_password.url'), $this->token)),
                'name' => $this->user->name,
                'timeOut' => config('api.auth.reset_password.token_timeout_minutes'),
            ]);
    }
}
