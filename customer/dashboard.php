<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM orders
    WHERE customer_id='$customer_id'
"))['total'];

$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT IFNULL(SUM(total_amount),0) AS total FROM orders
    WHERE customer_id='$customer_id'
"))['total'];

$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM orders
    WHERE customer_id='$customer_id'
    AND LOWER(order_status) NOT IN ('completed', 'cancelled')
"))['total'];

$recent_orders = mysqli_query($conn, "
    SELECT * FROM orders
    WHERE customer_id='$customer_id'
    ORDER BY order_id DESC
    LIMIT 8
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Dashboard | BakeChain SCM</title>
<meta name="description" content="BakeChain Customer Dashboard — Track your cookie orders">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
</head>
<body>

<?php include '../includes/customer_sidebar.php'; ?>

<div class="main-content">

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <h2>Customer Dashboard</h2>
      <small>Track your orders and deliveries</small>
    </div>
    <div class="topbar-right">
      <?php if ($pending_orders > 0): ?>
        <div class="topbar-badge">
          <i class="bi bi-clock"></i>
          <?php echo $pending_orders; ?> Pending
        </div>
      <?php endif; ?>
      <div class="topbar-badge">
        <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
        <?php echo htmlspecialchars($_SESSION['name']); ?>
      </div>
    </div>
  </div>

  <div class="content-body">

    <!-- Hero -->
    <div class="hero-panel">
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>! 🍪</h1>
      <p>View your cookie orders, payment summary and delivery tracking from one clean customer panel.</p>
    </div>

    <!-- KPI Row -->
    <div class="kpi-row stagger-children">
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-bag-check"></i></div>
        <div class="kpi-box-value"><?php echo $total_orders; ?></div>
        <div class="kpi-box-label">Total Orders</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-currency-rupee"></i></div>
        <div class="kpi-box-value">₹<?php echo number_format($total_spent, 0); ?></div>
        <div class="kpi-box-label">Total Spent</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="kpi-box-value"><?php echo $pending_orders; ?></div>
        <div class="kpi-box-label">Pending Orders</div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="section-title">Quick Actions</div>
    <div class="qa-grid2 stagger-children">
      <a href="my_orders.php" class="qa-card">
        <i class="bi bi-bag-check"></i>
        <span>View My Orders</span>
      </a>
      <a href="track_order.php" class="qa-card">
        <i class="bi bi-geo-alt"></i>
        <span>Track Order</span>
      </a>
    </div>

    <!-- Recent Orders -->
    <div class="panel-card">
      <div class="panel-card-header">
        <h3><i class="bi bi-receipt"></i> Recent Orders</h3>
        <a href="my_orders.php" class="btn btn-sm btn-primary"
           style="font-size:12px;padding:6px 14px;border-radius:20px;">View All</a>
      </div>
      <div style="padding:0;">
        <table class="table" style="margin:0;">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Order Date</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($recent_orders) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($recent_orders)): ?>
              <tr>
                <td><strong>ORD-<?php echo $row['order_id']; ?></strong></td>
                <td style="color:var(--text-muted);font-size:13px;"><?php echo $row['order_date']; ?></td>
                <td><strong>₹<?php echo $row['total_amount']; ?></strong></td>
                <td>
                  <?php
                    $st = strtolower($row['order_status']);
                    $cls = match($st) {
                        'completed', 'delivered' => 'status-delivered',
                        'pending'   => 'status-pending',
                        'cancelled' => 'status-cancelled',
                        default     => 'status-processing',
                    };
                  ?>
                  <span class="status-badge <?php echo $cls; ?>"><?php echo ucfirst($row['order_status']); ?></span>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:28px;">No orders yet. Place your first cookie order!</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

</body>
</html>