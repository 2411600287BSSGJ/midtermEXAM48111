<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('account')->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = Account::all();
        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        // 1. Validation (Added receiver_account_id for transfers)
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:deposit,withdrawal,transfer',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
            'receiver_account_id' => 'required_if:type,transfer|exists:accounts,id|different:account_id',
        ]);

        $account = Account::findOrFail($validated['account_id']);

        // 2. Custom Validation for sufficient funds
        if (\in_array($validated['type'], ['withdrawal', 'transfer'], true)) {
            $potentialBalance = $account->balance - (float) $validated['amount'];

            if ($potentialBalance < $account->minimum_balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient funds. Balance cannot fall below minimum balance of ' . $account->minimum_balance,
                ]);
            }
        }

        // 3. Database Transaction
        try {
            DB::transaction(function () use ($validated, $account) {
                
                // Create the transaction for the SENDER (Transfer or Withdrawal)
                Transaction::create([
                    'account_id' => $validated['account_id'],
                    'type' => $validated['type'],
                    'amount' => $validated['amount'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                // If it's a transfer, ALSO create a DEPOSIT transaction for the RECEIVER
                if ($validated['type'] === 'transfer') {
                    Transaction::create([
                        'account_id' => $validated['receiver_account_id'],
                        'type' => 'deposit', // The receiver gets a deposit
                        'amount' => $validated['amount'],
                        'notes' => 'Transfer received from ' . $account->account_number . '. ' . ($validated['notes'] ?? ''),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction processed successfully.');
    }
}