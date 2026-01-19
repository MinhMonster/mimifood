<?php

namespace App\Mail;

use App\Models\TopUpTransactions;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminTopUpNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public TopUpTransactions $transaction;

    public function __construct(User $user, TopUpTransactions $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    public function build()
    {
        return $this->subject('🔔 Có yêu cầu nạp tiền mới')
            ->view('emails.admin.top-up-notification');
    }
}
