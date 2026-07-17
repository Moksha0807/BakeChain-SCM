<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'production_manager')
{
    header("Location: ../auth/login.php");
    exit();
}

$batches = mysqli_query($conn, "
SELECT production_batches.*, products.product_name, products.flavor
FROM production_batches
JOIN products ON production_batches.product_id = products.product_id
ORDER BY production_batches.batch_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Production History | Production Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
</head>
<body>

<?php include '../includes/production_sidebar.php'; ?>

<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <h2>Production History</h2>
            <small>Monitor and update status of all created batches</small>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
                <?php echo htmlspecialchars($_SESSION['name']); ?>
            </div>
        </div>
    </div>

    <!-- Content Body -->
    <div class="content-body">
        
        <div class="panel-card">
            <div class="panel-card-header d-flex justify-content-between align-items-center">
                <h3><i class="bi bi-clock-history"></i> All Production Batches</h3>
                <a href="create_batch.php" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-plus-circle"></i> Create New Batch
                </a>
            </div>
            
            <div style="padding:0;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Batch Code</th>
                            <th>Product Name</th>
                            <th>Flavor</th>
                            <th>Production Date</th>
                            <th>Expiry Date</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($batches) > 0) { ?>
                            <?php while($row = mysqli_fetch_assoc($batches)) { ?>
                            <tr>
                                <td class="ps-4"><strong><?php echo htmlspecialchars($row['batch_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['flavor']); ?></td>
                                <td><?php echo $row['production_date']; ?></td>
                                <td><?php echo $row['expiry_date']; ?></td>
                                <td><?php echo $row['quantity_produced']; ?></td>
                                <td>
                                    <?php
                                    $status = strtolower($row['production_status']);
                                    if($status == "completed") {
                                        echo "<span class='badge' style='background:#D1FAE5;color:#065F46;'>Completed</span>";
                                    } elseif($status == "in production") {
                                        echo "<span class='badge' style='background:#DBEAFE;color:#1E40AF;'>In Production</span>";
                                    } else {
                                        echo "<span class='badge' style='background:#F3F4F6;color:#374151;'>".htmlspecialchars($row['production_status'])."</span>";
                                    }
                                    ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="update_batch_status.php?id=<?php echo $row['batch_id']; ?>" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill">
                                        <i class="bi bi-pencil-square"></i> Update Status
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No production batches found.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>