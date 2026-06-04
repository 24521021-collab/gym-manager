<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DefaultPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $password;

    public function __construct($name, $password)
    {
        $this->name = $name;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Mật khẩu mặc định mới cho tài khoản của bạn')
                    ->view('emails.default_password');
    }
}
