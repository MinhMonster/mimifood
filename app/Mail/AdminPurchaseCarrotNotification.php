<?php

namespace App\Mail;

use App\Models\User;
use App\Models\CarrotTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPurchaseCarrotNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public CarrotTransaction $transaction;

    public function __construct(User $user, CarrotTransaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    public function build()
    {
        return $this
            ->subject("🔔 Có đơn nạp Carrot mới #{$this->transaction->id}")
            ->view('emails.admin.purchase_carrot');
    }
}
