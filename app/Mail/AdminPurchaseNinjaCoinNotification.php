<?php

namespace App\Mail;

use App\Models\NinjaCoinTransaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPurchaseNinjaCoinNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public NinjaCoinTransaction $transaction;

    public function __construct(User $user, NinjaCoinTransaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    public function build()
    {
        return $this->subject('🔔 Giao dịch mua xu Ninja)')
            ->view('emails.admin.purchase_ninja_coin');
    }
}
