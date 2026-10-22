<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Transaction</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
        }

        .header {
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .header a {
            display: inline-block;
            margin-top: 8px;
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .header a:hover {
            text-decoration: underline;
        }

        .form-card {
            background: var(--card-bg);
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #fff;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }

        button:hover {
            background-color: var(--primary-hover);
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .error-box ul {
            margin-left: 20px;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="header">
            <h1>New Transaction</h1>
            <a href="{{ route('dashboard') }}">← Back to Dashboard</a>
        </div>

        <div class="form-card">
            @if($errors->any())
                <div class="error-box">
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf

                <!-- Source Account -->
                <div class="form-group">
                    <label>Source Account (From)</label>
                    <select name="account_id" required>
                        <option value="">Select an account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->account_number }} (Balance: ${{ number_format($account->balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Transaction Type -->
                <div class="form-group">
                    <label>Transaction Type</label>
                    <select name="type" id="type" required>
                        <option value="deposit" {{ old('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="withdrawal" {{ old('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>Transfer to another
                            Account</option>
                    </select>
                </div>

                <!-- Receiver Account (Hidden by default, shows when Transfer is selected) -->
                <div class="form-group" id="receiver-group" style="display: none;">
                    <label>Receiver Account (To)</label>
                    <select name="receiver_account_id" id="receiver_account_id">
                        <option value="">Select receiver account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('receiver_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->account_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" step="0.01" min="1" value="{{ old('amount') }}"
                        placeholder="e.g. 100.00" required>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="notes" rows="3"
                        placeholder="What is this transaction for?">{{ old('notes') }}</textarea>
                </div>

                <button type="submit">Process Transaction</button>
            </form>
        </div>
    </div>

    <!-- JavaScript to toggle the Receiver dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const receiverGroup = document.getElementById('receiver-group');
            const receiverSelect = document.getElementById('receiver_account_id');

            function toggleReceiver() {
                if (typeSelect.value === 'transfer') {
                    receiverGroup.style.display = 'block';
                    receiverSelect.setAttribute('required', 'required');
                } else {
                    receiverGroup.style.display = 'none';
                    receiverSelect.removeAttribute('required');
                    receiverSelect.value = ''; // Clear selection
                }
            }

            typeSelect.addEventListener('change', toggleReceiver);
            toggleReceiver(); // Run on page load in case of validation errors (preserves old state)
        });
    </script>
</body>

</html>