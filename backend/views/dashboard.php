<?php
/**
 * Admin Dashboard
 * Main dashboard with statistics and navigation
 */

require_once '../config/database.php';
require_once '../config/auth_helper.php';

require_admin();

$total_visitors = fetch_one('SELECT COUNT(*) as count FROM pengunjung');
$total_tickets = fetch_one('SELECT COUNT(*) as count FROM tiket');
$active_tickets = fetch_one('SELECT COUNT(*) as count FROM tiket WHERE status = "Active"');
$total_reviews = fetch_one('SELECT COUNT(*) as count FROM review');
$total_revenue = fetch_one('SELECT SUM(harga) as total FROM tiket WHERE status = "Used"');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MangroveTour Admin</title>
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
                <a href="dashboard.php" class="active"><i class="bi bi-graph-up"></i> Dashboard</a>
                <a href="pengunjung.php"><i class="bi bi-people"></i> Visitors</a>
                <a href="tiket.php"><i class="bi bi-ticket"></i> Tickets</a>
                <a href="review.php"><i class="bi bi-star"></i> Reviews</a>
                <a href="revenue_report.php"><i class="bi bi-graph-down"></i> Revenue Report</a>
                <a href="financial_report.php"><i class="bi bi-cash-coin"></i> Financial Report</a>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <a href="../../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10" style="padding: 30px;">
                <!-- Top Bar -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h2><i class="bi bi-graph-up"></i> Dashboard</h2>
                        <p style="color: #666;">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                    </div>
                    <div class="col-md-4 text-end">
                        <p style="color: #666;">
                            <i class="bi bi-calendar-event"></i>
                            <?php echo date('d F Y'); ?>
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h4><?php echo $total_visitors['count']; ?></h4>
                            <p>Total Visitors</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h4><?php echo $total_tickets['count']; ?></h4>
                            <p>Total Tickets</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h4><?php echo $active_tickets['count']; ?></h4>
                            <p>Active Tickets</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h4><?php echo $total_reviews['count']; ?></h4>
                            <p>Total Reviews</p>
                        </div>
                    </div>
                </div>

                <!-- Revenue Card -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 style="margin: 0;"><i class="bi bi-cash"></i> Total Revenue</h5>
                            </div>
                            <div class="card-body">
                                <h3 style="color: #667eea; font-weight: 700;">
                                    Rp. <?php echo number_format($total_revenue['total'] ?? 0, 0, ',', '.'); ?>
                                </h3>
                                <p style="color: #666; margin: 0;">From completed ticket validations</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 style="margin: 0;"><i class="bi bi-lightning"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <a href="pengunjung.php?action=add" class="btn btn-primary me-2"><i class="bi bi-plus"></i> Add Visitor</a>
                                <a href="tiket.php?action=add" class="btn btn-success me-2"><i class="bi bi-plus"></i> Create Ticket</a>
                                <a href="revenue_report.php" class="btn btn-info"><i class="bi bi-download"></i> Generate Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
