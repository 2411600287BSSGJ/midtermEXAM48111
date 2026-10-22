<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
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
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            background-color: var(--primary);
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.2s;
            margin-left: 10px;
        }

        .nav-links a:hover {
            background-color: var(--primary-hover);
        }

        .nav-links a.secondary {
            background-color: transparent;
            color: var(--text-main);
            border: 1px solid #d1d5db;
        }

        .nav-links a.secondary:hover {
            background-color: #e5e7eb;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .table-container {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th,
        td {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background-color: #f9fafb;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 600;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f9fafb;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-deposit {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-withdrawal {
            background: #fee2e2;
            color: #991b1b;
        }

        .amount-deposit {
            color: #059669;
            font-weight: 600;
        }

        .amount-withdrawal {
            color: #dc2626;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Transaction History</h1>
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="secondary">← Dashboard</a>
                <a href="{{ route('transactions.create') }}">+ New Transaction</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>#{{ $tx->id }}</td>
                            <td><strong>{{ $tx->account->account_number }}</strong></td>
                            <td>
                                <span class="badge badge-{{ $tx->type }}">
                                    {{ $tx->type }}
                                </span>
                            </td>
                            <td class="{{ $tx->type === 'deposit' ? 'amount-deposit' : 'amount-withdrawal' }}">
                                {{ $tx->type === 'deposit' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                            </td>
                            <td style="color: var(--text-muted);">{{ $tx->notes ?? '—' }}</td>
                            <td style="color: var(--text-muted);">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>