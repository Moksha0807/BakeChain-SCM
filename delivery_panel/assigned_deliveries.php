<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'delivery_partner')
{
    header("Location: ../auth/login.php");
    exit();
}

$partner_id = $_SESSION['user_id'];

$deliveries = mysqli_query($conn, "
SELECT deliveries.*, orders.total_amount
FROM deliveries
JOIN orders ON deliveries.order_id = orders.order_id
WHERE deliveries.delivery_partner_id='$partner_id'
AND deliveries.delivery_status != 'delivered'
ORDER BY deliveries.delivery_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assigned Deliveries | BakeChain SCM</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
</head>
<body>

<?php include '../includes/delivery_sidebar.php'; ?>

<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <h2>Assigned Deliveries</h2>
            <small>View and manage all active delivery assignments</small>
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
                <h3><i class="bi bi-truck"></i> Active Deliveries</h3>
                <a href="dashboard.php" class="btn btn-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <div style="padding:0;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Delivery ID</th>
                            <th>Order ID</th>
                            <th>Assigned Date</th>
                            <th>Delivery Date</th>
                            <th>Status</th>
                            <th>Delay</th>
                            <th>Amount</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($deliveries) > 0){ ?>
                            <?php while($row = mysqli_fetch_assoc($deliveries)){ ?>
                            <tr>
                                <td class="ps-4"><strong>DEL-<?php echo $row['delivery_id']; ?></strong></td>
                                <td><strong>ORD-<?php echo $row['order_id']; ?></strong></td>
                                <td><?php echo $row['assigned_date']; ?></td>
                                <td><?php echo $row['delivery_date']; ?></td>
                                <td>
                                    <?php
                                    $status = strtolower($row['delivery_status']);
                                    if($status == "delivered") {
                                        echo "<span class='badge' style='background:#D1FAE5;color:#065F46;'>Delivered</span>";
                                    } elseif($status == "out_for_delivery" || $status == "out for delivery") {
                                        echo "<span class='badge' style='background:#DBEAFE;color:#1E40AF;'>Out For Delivery</span>";
                                    } elseif($status == "shipped") {
                                        echo "<span class='badge' style='background:#FEF3C7;color:#92400E;'>Shipped</span>";
                                    } elseif($status == "packed") {
                                        echo "<span class='badge' style='background:#F3F4F6;color:#374151;'>Packed</span>";
                                    } else {
                                        echo "<span class='badge' style='background:#E5E7EB;color:#4B5563;'>Not Updated</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if($row['delay_days'] > 0){ ?>
                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-exclamation-circle-fill"></i> <?php echo $row['delay_days']; ?> day(s)</span>
                                    <?php } else { ?>
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> No Delay</span>
                                    <?php } ?>
                                </td>
                                <td><strong>₹<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                                <td class="pe-4 text-end">
                                    <a href="update_delivery.php?id=<?php echo $row['delivery_id']; ?>" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill">
                                        <i class="bi bi-pencil-square"></i> Update Status
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-truck fs-1 d-block mb-2"></i>
                                    No pending deliveries assigned to you.
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