<?php
/**
 * Visitor Management Page
 * Create, Read, Update, Delete visitors
 */

require_once '../config/database.php';
require_once '../config/auth_helper.php';

require_admin();

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize_input($_POST['nama'] ?? '');
    $no_hp = sanitize_input($_POST['no_hp'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');

    if (empty($nama) || empty($no_hp) || empty($email)) {
        $error = 'All fields are required';
    } else if (!validate_email($email)) {
        $error = 'Invalid email format';
    } else if (!validate_phone($no_hp)) {
        $error = 'Invalid phone number';
    } else {
        if ($action === 'edit' && $id) {
            update_data('pengunjung', [
                'nama' => $nama,
                'no_hp' => $no_hp,
                'email' => $email
            ], ['id_pengunjung' => $id]);
            $message = 'Visitor updated successfully';
        } else {
            insert_data('pengunjung', [
                'nama' => $nama,
                'no_hp' => $no_hp,
                'email' => $email
            ]);
            $message = 'Visitor created successfully';
        }
        $action = 'list';
    }
}

if ($action === 'delete' && $id) {
    delete_data('pengunjung', ['id_pengunjung' => $id]);
    $message = 'Visitor deleted successfully';
    $action = 'list';
}

$pengunjung_data = null;
if (($action === 'edit' || $action === 'view') && $id) {
    $pengunjung_data = fetch_one('SELECT * FROM pengunjung WHERE id_pengunjung = ?', [$id]);
    if (!$pengunjung_data) {
        $error = 'Visitor not found';
        $action = 'list';
    }
}

$pengunjung_list = fetch_all('SELECT * FROM pengunjung ORDER BY created_at DESC');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors - MangroveTour Admin</title>
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
                <a href="pengunjung.php" class="active"><i class="bi bi-people"></i> Visitors</a>
                <a href="tiket.php"><i class="bi bi-ticket"></i> Tickets</a>
                <a href="review.php"><i class="bi bi-star"></i> Reviews</a>
                <a href="revenue_report.php"><i class="bi bi-file-earmark-text"></i> Reports</a>
                <hr style="border-color: rgba(255,255,255,0.3);">
                <a href="../../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10" style="padding: 30px;">
                <h2><i class="bi bi-people"></i> Visitor Management</h2>

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
                            <h5 style="margin: 0;"><?php echo $action === 'add' ? 'Add New Visitor' : 'Edit Visitor'; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($pengunjung_data['nama'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($pengunjung_data['no_hp'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($pengunjung_data['email'] ?? ''); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i> Save</button>
                                <a href="pengunjung.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- List -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 style="margin: 0; color: white;">Visitors List</h5>
                            <a href="pengunjung.php?action=add" class="btn btn-sm btn-light"><i class="bi bi-plus"></i> Add Visitor</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pengunjung_list as $p): ?>
                                        <tr>
                                            <td><?php echo $p['id_pengunjung']; ?></td>
                                            <td><?php echo htmlspecialchars($p['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($p['no_hp']); ?></td>
                                            <td><?php echo htmlspecialchars($p['email']); ?></td>
                                            <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                                            <td>
                                                <a href="pengunjung.php?action=edit&id=<?php echo $p['id_pengunjung']; ?>" class="btn btn-sm btn-warning action-btn"><i class="bi bi-pencil"></i></a>
                                                <a href="pengunjung.php?action=delete&id=<?php echo $p['id_pengunjung']; ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></a>
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
