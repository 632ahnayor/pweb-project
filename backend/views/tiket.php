<?php
/**
 * Ticket Management Page
 */

require_once '../config/database.php';
require_once '../config/auth_helper.php';

require_operator();

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pengunjung = $_POST['id_pengunjung'] ?? null;
    $tanggal_berkunjung = $_POST['tanggal_berkunjung'] ?? null;
    $status = $_POST['status'] ?? 'Active';
    $harga = $_POST['harga'] ?? 50000;

    if (empty($id_pengunjung) || empty($tanggal_berkunjung)) {
        $error = 'All fields are required';
    } else {
        if ($action === 'edit' && $id) {
            require_admin();
            update_data('tiket', [
                'tanggal_berkunjung' => $tanggal_berkunjung,
                'status' => $status,
                'harga' => $harga
            ], ['id_tiket' => $id]);
            $message = 'Ticket updated successfully';
        } else {
            insert_data('tiket', [
                'id_pengunjung' => $id_pengunjung,
                'tanggal_berkunjung' => $tanggal_berkunjung,
                'status' => 'Active',
                'harga' => $harga
            ]);
            $message = 'Ticket created successfully';
        }
        $action = 'list';
    }
}

if ($action === 'delete' && $id) {
    require_admin();
    delete_data('tiket', ['id_tiket' => $id]);
    $message = 'Ticket deleted successfully';
    $action = 'list';
}

$tiket_data = null;
if (($action === 'edit' || $action === 'view') && $id) {
    $tiket_data = fetch_one('SELECT * FROM tiket WHERE id_tiket = ?', [$id]);
    if (!$tiket_data) {
        $error = 'Ticket not found';
        $action = 'list';
    }
}

$tiket_list = fetch_all('
    SELECT t.*, p.nama, p.email
    FROM tiket t
    JOIN pengunjung p ON t.id_pengunjung = p.id_pengunjung
    ORDER BY t.created_at DESC
');

$pengunjung_list = fetch_all('SELECT id_pengunjung, nama FROM pengunjung ORDER BY nama');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets - MangroveTour Admin</title>
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
                <a href="tiket.php" class="active"><i class="bi bi-ticket"></i> Tickets</a>
                <a href="review.php"><i class="bi bi-star"></i> Reviews</a>
                <a href="revenue_report.php"><i class="bi bi-file-earmark-text"></i> Reports</a>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <a href="../../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10" style="padding: 30px;">
                <h2><i class="bi bi-ticket"></i> Ticket Management</h2>

                <?php if ($message): ?>
                    <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($action === 'add' || $action === 'edit'): ?>
                    <!-- Form -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 style="margin: 0;"><?php echo $action === 'add' ? 'Create New Ticket' : 'Edit Ticket'; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="id_pengunjung" class="form-label">Visitor</label>
                                    <select class="form-control" id="id_pengunjung" name="id_pengunjung" required>
                                        <option value="">-- Select Visitor --</option>
                                        <?php foreach ($pengunjung_list as $p): ?>
                                            <option value="<?php echo $p['id_pengunjung']; ?>" <?php echo ($tiket_data['id_pengunjung'] ?? null) == $p['id_pengunjung'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($p['nama']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_berkunjung" class="form-label">Visit Date</label>
                                    <input type="date" class="form-control" id="tanggal_berkunjung" name="tanggal_berkunjung" value="<?php echo htmlspecialchars($tiket_data['tanggal_berkunjung'] ?? date('Y-m-d')); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Price (Rp)</label>
                                    <input type="number" class="form-control" id="harga" name="harga" value="<?php echo htmlspecialchars($tiket_data['harga'] ?? 50000); ?>" required>
                                </div>
                                <?php if ($action === 'edit'): ?>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="Active" <?php echo ($tiket_data['status'] ?? 'Active') === 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Used" <?php echo ($tiket_data['status'] ?? 'Active') === 'Used' ? 'selected' : ''; ?>>Used</option>
                                            <option value="Expired" <?php echo ($tiket_data['status'] ?? 'Active') === 'Expired' ? 'selected' : ''; ?>>Expired</option>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i> Save</button>
                                <a href="tiket.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- List -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 style="margin: 0; color: white;">Tickets List</h5>
                            <a href="tiket.php?action=add" class="btn btn-sm btn-light"><i class="bi bi-plus"></i> Create Ticket</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Visitor</th>
                                        <th>Visit Date</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tiket_list as $t): ?>
                                        <tr>
                                            <td><?php echo $t['id_tiket']; ?></td>
                                            <td><?php echo htmlspecialchars($t['nama']); ?></td>
                                            <td><?php echo date('d M Y', strtotime($t['tanggal_berkunjung'])); ?></td>
                                            <td>Rp. <?php echo number_format($t['harga'], 0, ',', '.'); ?></td>
                                            <td>
                                                <?php if ($t['status'] === 'Active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php elseif ($t['status'] === 'Used'): ?>
                                                    <span class="badge bg-primary">Used</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Expired</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($t['created_at'])); ?></td>
                                            <td>
                                                <a href="tiket.php?action=edit&id=<?php echo $t['id_tiket']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                                <?php if (has_role('Admin')): ?>
                                                    <a href="tiket.php?action=delete&id=<?php echo $t['id_tiket']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
