<?php
/**
 * Financial Report - Payment Transaction Analysis
 * Endpoint: backend/views/financial_report.php
 * 
 * This view displays:
 * - Payment transactions from Midtrans
 * - Payment method breakdown
 * - Transaction status tracking
 * - Payment settlement data
 * 
 * Different from: Revenue Report (shows validated ticket usage)
 */

require_once '../config/database.php';
require_once '../config/auth_helper.php';
require_once '../config/midtrans.php';

require_admin();

// Get date range for filtering (default: last 30 days)
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Fetch transaction statistics
$stats = fetch_one("
    SELECT 
        COUNT(*) as total_transactions,
        COUNT(CASE WHEN transaction_status = 'settlement' THEN 1 END) as settled_count,
        COUNT(CASE WHEN transaction_status = 'pending' THEN 1 END) as pending_count,
        COUNT(CASE WHEN transaction_status IN ('expire', 'cancel', 'deny') THEN 1 END) as failed_count,
        SUM(CASE WHEN transaction_status = 'settlement' THEN gross_amount ELSE 0 END) as total_settled,
        SUM(gross_amount) as total_gross
    FROM transactions
    WHERE DATE(created_at) BETWEEN ? AND ?
", [$date_from, $date_to]);

// Fetch all transactions within date range
$transactions = fetch_all("
    SELECT 
        t.*,
        tk.id_tiket,
        p.nama as customer_name,
        p.email as customer_email,
        p.no_hp as customer_phone
    FROM transactions t
    LEFT JOIN tiket tk ON t.id_tiket = tk.id_tiket
    LEFT JOIN pengunjung p ON tk.id_pengunjung = p.id_pengunjung
    WHERE DATE(t.created_at) BETWEEN ? AND ?
    ORDER BY t.created_at DESC
", [$date_from, $date_to]);

// Fetch payment method breakdown
$payment_methods = fetch_all("
    SELECT 
        payment_type,
        COUNT(*) as count,
        SUM(gross_amount) as total
    FROM transactions
    WHERE DATE(created_at) BETWEEN ? AND ? AND transaction_status = 'settlement'
    GROUP BY payment_type
    ORDER BY total DESC
", [$date_from, $date_to]);

// Helper function to format currency
function format_idr($amount) {
    return 'Rp ' . number_format((int)$amount, 0, ',', '.');
}

// Helper function to get status badge color
function get_status_color($status) {
    $colors = [
        'settlement' => 'success',
        'capture' => 'info',
        'pending' => 'warning',
        'expire' => 'secondary',
        'cancel' => 'danger',
        'deny' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

// Handle print request
$is_print = $_GET['print'] ?? false;
if ($is_print) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<html><head><meta charset="UTF-8"><title>Financial Report</title>';
    echo '<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .header { text-align: center; margin-bottom: 30px; }
        @media print { body { margin: 0; } }
    </style></head><body>';
    echo '<div class="header"><h2>Financial Report - MangroveTour</h2>';
    echo '<p>Period: ' . $date_from . ' to ' . $date_to . '</p>';
    echo '<p>Generated: ' . date('Y-m-d H:i:s') . '</p></div>';
    
    // Print stats
    echo '<h3>Summary</h3>';
    echo '<table><tr><td>Total Transactions</td><td>' . $stats['total_transactions'] . '</td></tr>';
    echo '<tr><td>Settled</td><td>' . $stats['settled_count'] . '</td></tr>';
    echo '<tr><td>Pending</td><td>' . $stats['pending_count'] . '</td></tr>';
    echo '<tr><td>Failed</td><td>' . $stats['failed_count'] . '</td></tr>';
    echo '<tr><td><strong>Total Revenue (Settled)</strong></td><td><strong>' . format_idr($stats['total_settled']) . '</strong></td></tr>';
    echo '</table>';
    
    // Print transactions table
    echo '<h3>Transactions</h3>';
    echo '<table>';
    echo '<tr><th>Order ID</th><th>Customer</th><th>Amount</th><th>Payment Type</th><th>Status</th><th>Date</th></tr>';
    foreach ($transactions as $tx) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($tx['order_id']) . '</td>';
        echo '<td>' . htmlspecialchars($tx['customer_name'] ?? 'N/A') . '</td>';
        echo '<td>' . format_idr($tx['gross_amount']) . '</td>';
        echo '<td>' . htmlspecialchars($tx['payment_type'] ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($tx['transaction_status']) . '</td>';
        echo '<td>' . $tx['created_at'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</body></html>';
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report - MangroveTour Admin</title>
    <link href="../../public/assets/css/bootstrap-5.3.8-dist/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/bootstrap-icons-1.13.1/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/admin-style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 style="color: white; margin-bottom: 30px;"><i class="bi bi-leaf"></i> MangroveTour</h4>
                <a href="dashboard.php"><i class="bi bi-graph-up"></i> Dashboard</a>
                <a href="pengunjung.php"><i class="bi bi-people"></i> Visitors</a>
                <a href="tiket.php"><i class="bi bi-ticket"></i> Tickets</a>
                <a href="review.php"><i class="bi bi-star"></i> Reviews</a>
                <a href="revenue_report.php"><i class="bi bi-graph-down"></i> Revenue Report</a>
                <a href="financial_report.php" class="active"><i class="bi bi-cash-coin"></i> Financial Report</a>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <a href="../../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10" style="padding: 30px;">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h2><i class="bi bi-cash-coin"></i> Payment Transaction Report</h2>
                        <p style="color: #666;"><small>Payment data from Midtrans - when customers paid, payment methods, and settlement status</small></p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="revenue_report.php" class="btn btn-sm btn-success me-2" title="View validated ticket revenue">
                            <i class="bi bi-graph-down"></i> View Revenue Report
                        </a>
                        <button onclick="printReport()" class="btn btn-primary">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                            </div>
                            <div class="col-md-4" style="display: flex; align-items: flex-end;">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-check-circle" style="font-size: 32px; color: #28a745;"></i>
                                <p class="card-title mt-2">Payments Settled</p>
                                <h5 style="color: #28a745;">
                                    <?php echo format_idr($stats['total_settled'] ?? 0); ?>
                                </h5>
                                <small class="text-muted"><?php echo $stats['settled_count']; ?> successful payments</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-hourglass-split" style="font-size: 32px; color: #ffc107;"></i>
                                <p class="card-title mt-2">Pending</p>
                                <h5 style="color: #ffc107;">
                                    <?php echo $stats['pending_count']; ?>
                                </h5>
                                <small class="text-muted">Awaiting payment</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-x-circle" style="font-size: 32px; color: #dc3545;"></i>
                                <p class="card-title mt-2">Failed</p>
                                <h5 style="color: #dc3545;">
                                    <?php echo $stats['failed_count']; ?>
                                </h5>
                                <small class="text-muted">Expired or cancelled</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="bi bi-receipt" style="font-size: 32px; color: #0066cc;"></i>
                                <p class="card-title mt-2">Total Transactions</p>
                                <h5 style="color: #0066cc;">
                                    <?php echo $stats['total_transactions']; ?>
                                </h5>
                                <small class="text-muted">All statuses</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Breakdown -->
                <?php if (!empty($payment_methods)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 style="margin: 0;"><i class="bi bi-pie-chart"></i> Payment Methods (Settled)</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Payment Type</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payment_methods as $method): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($method['payment_type'] ?? '-'); ?></td>
                                    <td><?php echo $method['count']; ?></td>
                                    <td><?php echo format_idr($method['total']); ?></td>
                                    <td>
                                        <?php 
                                        $percentage = $stats['total_settled'] > 0 ? ($method['total'] / $stats['total_settled'] * 100) : 0;
                                        echo number_format($percentage, 1) . '%';
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Transactions Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 style="margin: 0; color: white;"><i class="bi bi-table"></i> Transaction Details</h5>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x: auto;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Payment Type</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($transactions)): ?>
                                        <?php foreach ($transactions as $tx): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($tx['order_id']); ?></code></td>
                                            <td><?php echo htmlspecialchars($tx['customer_name'] ?? 'N/A'); ?></td>
                                            <td><strong><?php echo format_idr($tx['gross_amount']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($tx['payment_type'] ?? '-'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo get_status_color($tx['transaction_status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $tx['transaction_status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($tx['created_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No transactions found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle"></i> <strong>About this report:</strong> This shows 
                    <strong>PAYMENT TRANSACTIONS</strong> from Midtrans (when customers paid). To see revenue from 
                    <strong>VALIDATED VISITS</strong> (when tickets were actually used), see the 
                    <a href="revenue_report.php"><strong>Revenue Report</strong></a>.
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printReport() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            const printUrl = `financial_report.php?print=1&date_from=${dateFrom}&date_to=${dateTo}`;
            const printWindow = window.open(printUrl, 'Print', 'width=900,height=600');
            printWindow.addEventListener('load', function() {
                printWindow.print();
            });
        }
    </script>
</body>
</html>
