<?php
/**
 * Revenue Report - Ticket Sales & Validation
 * 
 * This report tracks COMPLETED TICKET VALIDATIONS
 * Shows when visitors actually used their tickets
 * 
 * Different from: Financial Report (shows payment transactions)
 */

require_once '../config/database.php';
require_once '../config/auth_helper.php';

require_admin();

$period = $_GET['period'] ?? 'monthly';

// Get report data based on period
if ($period === 'daily') {
    $report = fetch_all('
        SELECT DATE(tanggal_berkunjung) as tanggal, COUNT(*) as jumlah, SUM(harga) as total
        FROM tiket
        WHERE status = "Used" AND MONTH(tanggal_berkunjung) = MONTH(NOW())
        GROUP BY DATE(tanggal_berkunjung)
        ORDER BY tanggal DESC
    ');
    $title = 'Daily Revenue (This Month)';
} else if ($period === 'weekly') {
    $report = fetch_all('
        SELECT WEEK(tanggal_berkunjung) as minggu, YEAR(tanggal_berkunjung) as tahun, COUNT(*) as jumlah, SUM(harga) as total
        FROM tiket
        WHERE status = "Used" AND YEAR(tanggal_berkunjung) = YEAR(NOW())
        GROUP BY WEEK(tanggal_berkunjung), YEAR(tanggal_berkunjung)
        ORDER BY tahun DESC, minggu DESC
    ');
    $title = 'Weekly Revenue (This Year)';
} else {
    $report = fetch_all('
        SELECT MONTH(tanggal_berkunjung) as bulan, YEAR(tanggal_berkunjung) as tahun, COUNT(*) as jumlah, SUM(harga) as total
        FROM tiket
        WHERE status = "Used"
        GROUP BY MONTH(tanggal_berkunjung), YEAR(tanggal_berkunjung)
        ORDER BY tahun DESC, bulan DESC
    ');
    $title = 'Monthly Revenue';
}

$total_revenue = fetch_one('SELECT SUM(harga) as total FROM tiket WHERE status = "Used"');
$total_tickets_used = fetch_one('SELECT COUNT(*) as count FROM tiket WHERE status = "Used"');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Report - MangroveTour Admin</title>
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
                <a href="revenue_report.php" class="active"><i class="bi bi-graph-down"></i> Revenue Report</a>
                <a href="financial_report.php"><i class="bi bi-cash-coin"></i> Financial Report</a>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <a href="../../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10" style="padding: 30px;">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h2><i class="bi bi-graph-down"></i> Ticket Sales Revenue Report</h2>
                        <p style="color: #666;"><small>Revenue from completed ticket validations (when visitors actually used their tickets)</small></p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="financial_report.php" class="btn btn-sm btn-info" title="View payment transactions">
                            <i class="bi bi-cash-coin"></i> View Payment Transactions
                        </a>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mt-4 mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 style="color: #999; margin-bottom: 10px;"><i class="bi bi-cash-coin"></i> Total Revenue (All Time)</h6>
                                <h3 style="color: #28a745; font-weight: 700;">
                                    Rp. <?php echo number_format($total_revenue['total'] ?? 0, 0, ',', '.'); ?>
                                </h3>
                                <small style="color: #666;">From validated/used tickets</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 style="color: #999; margin-bottom: 10px;"><i class="bi bi-ticket"></i> Total Tickets Validated</h6>
                                <h3 style="color: #28a745; font-weight: 700;">
                                    <?php echo $total_tickets_used['count']; ?>
                                </h3>
                                <small style="color: #666;">Completed visits</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Period Selection -->
                <div class="card mb-4">
                    <div class="card-body">
                        <p style="margin: 0; margin-bottom: 10px; color: white;"><strong>View by Period:</strong></p>
                        <a href="revenue_report.php?period=daily" class="btn btn-sm <?php echo $period === 'daily' ? 'btn-primary' : 'btn-outline-primary'; ?>">Daily</a>
                        <a href="revenue_report.php?period=weekly" class="btn btn-sm <?php echo $period === 'weekly' ? 'btn-primary' : 'btn-outline-primary'; ?>">Weekly</a>
                        <a href="revenue_report.php?period=monthly" class="btn btn-sm <?php echo $period === 'monthly' ? 'btn-primary' : 'btn-outline-primary'; ?>">Monthly</a>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 style="margin: 0; color: white;"><i class="bi bi-table"></i> <?php echo $title; ?></h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo $period === 'daily' ? 'Visit Date' : ($period === 'weekly' ? 'Week' : 'Month/Year'); ?></th>
                                    <th>Tickets Validated</th>
                                    <th>Revenue Generated (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report as $r): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            if ($period === 'daily') {
                                                echo date('d F Y', strtotime($r['tanggal']));
                                            } else if ($period === 'weekly') {
                                                echo 'Week ' . $r['minggu'] . ' - ' . $r['tahun'];
                                            } else {
                                                $months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                                echo $months[$r['bulan']] . ' ' . $r['tahun'];
                                            }
                                            ?>
                                        </td>
                                        <td><strong><?php echo $r['jumlah']; ?></strong></td>
                                        <td><strong style="color: #28a745;">Rp. <?php echo number_format($r['total'] ?? 0, 0, ',', '.'); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle"></i> <strong>Tip:</strong> This report shows revenue from 
                    <strong>COMPLETED visits</strong> (when tickets were validated). For payment transaction data, 
                    see the <a href="financial_report.php"><strong>Financial Report</strong></a>.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
