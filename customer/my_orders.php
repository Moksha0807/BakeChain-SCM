<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

$orders = mysqli_query($conn, "
    SELECT * FROM orders
    WHERE customer_id='$customer_id'
    ORDER BY order_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders | BakeChain SCM</title>
<meta name="description" content="View all your BakeChain cookie orders and their delivery status.">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
</head>
<body>

<?php include '../includes/customer_sidebar.php'; ?>

<div class="main-content">

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <h2>My Orders</h2>
      <small>Your complete cookie order history</small>
    </div>
    <div class="topbar-right">
      <div class="topbar-badge">
        <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
        <?php echo htmlspecialchars($_SESSION['name']); ?>
      </div>
    </div>
  </div>

  <div class="content-body">


    <?php if (mysqli_num_rows($orders) > 0): ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:20px;margin-bottom:26px;">
        <?php while ($row = mysqli_fetch_assoc($orders)): ?>
          <?php
            $status = strtolower($row['order_status']);
            $badge_class = match($status) {
                'completed', 'delivered' => 'status-delivered',
                'cancelled' => 'status-cancelled',
                default     => 'status-pending',
            };
          ?>
          <div class="order-card" style="padding:24px;border-radius:16px;">

            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;">
              <div>
                <div style="font-size:22px;font-weight:900;color:var(--burgundy);">
                  ORD-<?php echo $row['order_id']; ?>
                </div>
                <div style="font-size:13px;color:var(--text-muted);font-weight:600;margin-top:2px;">
                  <?php echo $row['order_date']; ?>
                </div>
              </div>
              <span class="status-badge <?php echo $badge_class; ?>">
                <?php echo ucfirst($row['order_status']); ?>
              </span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px;">
              <div class="info-box" style="border-radius:10px;">
                <div style="font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;">
                  Total Amount
                </div>
                <div style="font-size:22px;font-weight:900;color:var(--burgundy);margin-top:4px;">
                  ₹<?php echo $row['total_amount']; ?>
                </div>
              </div>
              <div class="info-box" style="border-radius:10px;">
                <div style="font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;">
                  Order Status
                </div>
                <div style="font-size:16px;font-weight:900;color:var(--burgundy);margin-top:4px;">
                  <?php echo ucfirst($row['order_status']); ?>
                </div>
              </div>
            </div>

            <div style="display:flex;gap:10px;">
              <a href="track_order.php?order_id=<?php echo $row['order_id']; ?>"
                 class="btn btn-primary" style="flex:1;justify-content:center;">
                <i class="bi bi-geo-alt"></i> Track
              </a>
              <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-house"></i>
              </a>
            </div>

          </div>
        <?php endwhile; ?>
      </div>

    <?php else: ?>
      <div class="empty" style="text-align:center;border-radius:16px;padding:50px;">
        <div style="font-size:52px;margin-bottom:14px;">🍪</div>
        <h3 style="color:var(--burgundy);font-size:26px;margin-bottom:8px;">No Orders Yet</h3>
        <p style="color:var(--text-muted);">You have not placed any cookie orders yet.</p>
        <a href="dashboard.php" class="btn btn-primary" style="margin-top:14px;">
          <i class="bi bi-house"></i> Back to Dashboard
        </a>
      </div>
    <?php endif; ?>

  </div>
</div>

</body>
</html>