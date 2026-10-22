<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    protected $account;

    public function __construct($account)
    {
        $this->account = $account;
    }

    public function via($_notifiable): array
    {
        return ['database'];
    }

    public function toArray($_notifiable): array
    {
        return [
            'account_id' => $this->account->id,
            'account_number' => $this->account->account_number,
            'balance' => $this->account->balance,
            'message' => 'Your account balance is at or below the minimum threshold.',
        ];
    }
}
