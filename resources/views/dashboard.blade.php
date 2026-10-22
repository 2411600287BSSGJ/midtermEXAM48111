<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking Dashboard</title>
    <!-- Importing a clean, modern font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --bg-color: #F3F4F6;
            --card-bg: #FFFFFF;
            --text-main: #111827;
            --text-muted: #6B7280;
            --success: #10B981;
            --warning: #F59E0B;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
            min-height: 100vh;
        }

        /* Header styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: var(--card-bg);
            padding: 20px 30px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .btn {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--text-main);
            border: 1px solid #D1D5DB;
        }

        .btn-outline:hover {
            background-color: #F9FAFB;
        }

        /* Grid Layout for Cards */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
        }

        /* Individual Card Styling */
        .card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        /* Colored accent bars at the top of cards */
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .card:nth-child(1)::before {
            background-color: var(--primary);
        }

        .card:nth-child(2)::before {
            background-color: var(--success);
        }

        .card:nth-child(3)::before {
            background-color: var(--warning);
        }

        .card h3 {
            margin: 0 0 16px;
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1;
        }

        /* Loading state animation */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .loading {
            animation: pulse 1.5s ease-in-out infinite;
            color: #D1D5DB !important;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Banking Dashboard</h1>
        <div class="nav-links">
            <a href="{{ route('transactions.index') }}" class="btn btn-outline">View All Transactions</a>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ New Transaction</a>
        </div>
    </div>

    <div class="stats">
        <div class="card">
            <h3>Total Accounts</h3>
            <div class="value loading" id="total-accounts">—</div>
        </div>
        <div class="card">
            <h3>Total Money in Bank</h3>
            <div class="value loading" id="total-money">—</div>
        </div>
        <div class="card">
            <h3>Transactions Today</h3>
            <div class="value loading" id="total-transactions">—</div>
        </div>
    </div>

    <script>
        function fetchStats() {
            fetch('/api/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    // Remove loading class
                    document.getElementById('total-accounts').classList.remove('loading');
                    document.getElementById('total-money').classList.remove('loading');
                    document.getElementById('total-transactions').classList.remove('loading');

                    // Update values
                    document.getElementById('total-accounts').textContent = data.total_accounts;
                    document.getElementById('total-money').textContent = '$' + Number(data.total_money).toFixed(2);
                    document.getElementById('total-transactions').textContent = data.total_transactions_today;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }

        // Initial fetch
        fetchStats();

        // Poll every 30 seconds
        setInterval(fetchStats, 30000);
    </script>
</body>

</html>