<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Exception;

class AccountObserver
{
    public function creating(Transaction $transaction): void
    {
        $account = Account::find($transaction->account_id);

        if (! $account) {
            return;
        }

        $amount = (float) $transaction->amount;

        if (in_array($transaction->type, ['withdrawal', 'transfer'], true)) {
            $newBalance = $account->balance - $amount;

            if ($newBalance < $account->minimum_balance) {
                throw new Exception(
                    "Insufficient funds. Balance cannot fall below minimum balance of {$account->minimum_balance}"
                );
            }

            $account->balance -= $amount;
            $account->save();

            if ($account->balance <= $account->minimum_balance && !$account->low_balance_notified) {
                $account->user->notify(new LowBalanceNotification($account));
                $account->update(['low_balance_notified' => true]);
            }
        } else {
            $account->balance += $amount;
            $account->save();

            if ($account->balance > $account->minimum_balance && $account->low_balance_notified) {
                $account->update(['low_balance_notified' => false]);
            }
        }
    }
}