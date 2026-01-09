<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPurchaseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $account;
    public $history;

    public function __construct($user, $account, $history)
    {
        $this->user = $user;
        $this->account = $account;
        $this->history = $history;
    }

    public function build()
    {
        return $this
            ->subject('🔔 Có đơn mua tài khoản mới')
            ->view('emails.admin.purchase');
    }
}
