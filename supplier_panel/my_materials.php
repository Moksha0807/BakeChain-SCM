<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supplier')
{
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($user_query);
$email = $user['email'];

$supplier_query = mysqli_query($conn, "SELECT * FROM suppliers WHERE user_id='$user_id' OR email='$email' LIMIT 1");
$supplier = mysqli_fetch_assoc($supplier_query);

$materials = null;
$total_materials = 0;

if($supplier)
{
    $supplier_id = $supplier['supplier_id'];

    $materials = mysqli_query($conn, "
    SELECT 
        supplier_materials.*,
        raw_materials.material_name,
        raw_materials.unit,
        raw_materials.current_stock,
        raw_materials.minimum_stock
    FROM supplier_materials
    JOIN raw_materials ON supplier_materials.material_id = raw_materials.material_id
    WHERE supplier_materials.supplier_id='$supplier_id'
    ORDER BY raw_materials.material_name ASC
    ");
    $total_materials = mysqli_num_rows($materials);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Supplied Materials | Supplier Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
</head>
<body>

<?php include '../includes/supplier_sidebar.php'; ?>

<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <h2>My Supplied Materials</h2>
            <small>Raw materials assigned to your supplier account</small>
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
        
        <?php if(!$supplier){ ?>
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Supplier profile not found for this login email.
            </div>
        <?php } else { ?>

        <div class="panel-card">
            <div class="panel-card-header d-flex justify-content-between align-items-center">
                <h3><i class="bi bi-box-seam"></i> My Materials (<?php echo $total_materials; ?>)</h3>
                <a href="dashboard.php" class="btn btn-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <div style="padding:0;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Material</th>
                            <th>Unit</th>
                            <th>Price / Unit</th>
                            <th>Delivery Days</th>
                            <th>Current Stock</th>
                            <th>Minimum Stock</th>
                            <th class="pe-4 text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($total_materials > 0){ ?>
                            <?php 
                            mysqli_data_seek($materials, 0);
                            while($row = mysqli_fetch_assoc($materials)){ 
                            ?>
                            <tr>
                                <td class="ps-4"><strong><?php echo htmlspecialchars($row['material_name']); ?></strong></td>
                                <td><span class="badge bg-light text-dark border"><?php echo strtoupper(htmlspecialchars($row['unit'])); ?></span></td>
                                <td><strong>₹<?php echo number_format($row['price_per_unit'], 2); ?></strong></td>
                                <td><?php echo $row['delivery_days']; ?> day(s)</td>
                                <td><?php echo $row['current_stock']; ?></td>
                                <td><?php echo $row['minimum_stock']; ?></td>
                                <td class="pe-4 text-end">
                                    <?php if($row['current_stock'] <= $row['minimum_stock']){ ?>
                                        <span class="badge" style="background:#FEE2E2;color:#991B1B;">Low Stock</span>
                                    <?php } else { ?>
                                        <span class="badge" style="background:#D1FAE5;color:#065F46;">Available</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No materials assigned to this supplier.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php } ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>