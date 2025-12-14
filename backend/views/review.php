<?php
/**
 * Review Management Page
 */

require_once '../config/database.php';
require_once '../config/auth_helper.php';

require_admin();

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;
$message = '';
$error = '';

if ($action === 'delete' && $id) {
    delete_data('review', ['id_review' => $id]);
    $message = 'Review deleted successfully';
    $action = 'list';
}

$review_list = fetch_all('
    SELECT r.*, p.nama, p.email
    FROM review r
    JOIN pengunjung p ON r.id_pengunjung = p.id_pengunjung
    ORDER BY r.created_at DESC
');

$avg_rating = fetch_one('SELECT AVG(rating) as avg_rating FROM review');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - MangroveTour Admin</title>
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
                <a href="review.php" class="active"><i class="bi bi-star"></i> Reviews</a>
                <a href="revenue_report.php"><i class="bi bi-file-earmark-text"></i> Reports</a>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <a href="../../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10" style="padding: 30px;">
                <h2><i class="bi bi-star"></i> Review Management</h2>

                <?php if ($message): ?>
                    <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Average Rating -->
                <div class="row mt-4 mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 style="color: #667eea;">
                                    <span class="rating"><i class="bi bi-star-fill"></i></span>
                                    <?php echo number_format($avg_rating['avg_rating'] ?? 0, 1); ?> / 5.0
                                </h4>
                                <p style="color: #666; margin: 0;">Average Rating from <?php echo count($review_list); ?> reviews</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="card">
                    <div class="card-header">
                        <h5 style="margin: 0;"><i class="bi bi-chat-dots"></i> All Reviews</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($review_list)): ?>
                            <p style="color: #666; text-align: center; padding: 40px 0;">No reviews yet</p>
                        <?php else: ?>
                            <?php foreach ($review_list as $r): ?>
                                <div class="review-card">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6><?php echo htmlspecialchars($r['nama']); ?></h6>
                                            <p style="color: #666; font-size: 12px; margin: 5px 0;">
                                                <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($r['email']); ?>
                                            </p>
                                            <div style="margin: 5px 0;" class="rating">
                                                <?php for ($i = 0; $i < 5; $i++): ?>
                                                    <?php if ($i < $r['rating']): ?>
                                                        <i class="bi bi-star-fill"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                            <p style="margin: 10px 0; color: #333;"><?php echo htmlspecialchars($r['komentar']); ?></p>
                                            <p style="color: #999; font-size: 12px; margin: 5px 0;">
                                                <?php echo date('d M Y H:i', strtotime($r['created_at'])); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <a href="review.php?action=delete&id=<?php echo $r['id_review']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i> Delete</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
