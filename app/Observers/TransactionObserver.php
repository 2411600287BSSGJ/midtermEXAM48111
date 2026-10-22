<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Exception;

class TransactionObserver
{
    // Use 'creating' so we can PREVENT the save if funds are insufficient
    public function creating(Transaction $transaction): void
    {
        $account = Account::find($transaction->account_id);

        if (! $account) {
            return;
        }

        $amount = (float) $transaction->amount;

        // 1. Handle Withdrawal and Transfer (Subtract)
        if (in_array($transaction->type, ['withdrawal', 'transfer'], true)) {
            $newBalance = $account->balance - $amount;

            // CRITICAL RULE: Throw exception BEFORE saving if below minimum
            if ($newBalance < $account->minimum_balance) {
                throw new Exception(
                    "Insufficient funds. Balance cannot fall below minimum balance of {$account->minimum_balance}"
                );
            }

            $account->balance -= $amount;
            $account->save();

            // Trigger Notification if balance dropped to or below minimum
            // Note: We call ->user->notify() because notifications go to the User
            if ($account->balance <= $account->minimum_balance && !$account->low_balance_notified) {
                $account->user->notify(new LowBalanceNotification($account));
                $account->update(['low_balance_notified' => true]);
            }
        } 
        // 2. Handle Deposit (Add)
        else {
            $account->balance += $amount;
            $account->save();

            // Reset the notification flag if balance recovers above minimum
            if ($account->balance > $account->minimum_balance && $account->low_balance_notified) {
                $account->update(['low_balance_notified' => false]);
            }
        }
    }
}