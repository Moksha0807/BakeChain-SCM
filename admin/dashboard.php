<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin')
{
    header("Location: ../auth/login.php");
    exit();
}

$total_suppliers  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM suppliers"))['total'];
$total_materials  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM raw_materials"))['total'];
$total_products   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];
$total_orders     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];
$total_deliveries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM deliveries"))['total'];
$total_batches    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM production_batches"))['total'];
$low_stock        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM raw_materials WHERE current_stock <= minimum_stock"))['total'];
$total_sales      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) AS total FROM orders"))['total'];
$total_inventory  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM inventory"))['total'];
$total_tracking   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tracking_logs"))['total'];

$recent_orders = mysqli_query($conn, "SELECT orders.*, users.name AS customer_name
    FROM orders
    JOIN users ON orders.customer_id = users.user_id
    ORDER BY orders.order_id DESC
    LIMIT 5");

$low_materials = mysqli_query($conn, "SELECT * FROM raw_materials
    WHERE current_stock <= minimum_stock
    LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | BakeChain SCM</title>
<meta name="description" content="BakeChain Admin Dashboard — Supply Chain Overview">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<style>
.hero-banner {
  background-image: url('https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=1400&q=80') !important;
  background-size: cover !important;
  background-position: center !important;
}
.hero-banner::before {
  content: '' !important;
  position: absolute !important;
  inset: 0 !important;
  background: linear-gradient(120deg, rgba(255,248,240,0.93) 0%, rgba(245,235,220,0.82) 55%, rgba(219,196,172,0.45) 100%) !important;
  backdrop-filter: blur(2px) !important;
  z-index: 0 !important;
  border-radius: inherit !important;
  animation: none !important;
  width: auto !important; height: auto !important; top: 0 !important; right: 0 !important;
}
.hero-banner > * { position: relative; z-index: 1; }
</style>
</head>

<body>

<?php include '../includes/admin_sidebar.php'; ?>

<div class="main-content">

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <h1>Supply Chain Dashboard</h1>
      <small>Elegant bakery operations & traceability control</small>
    </div>
    <div class="topbar-right">
      <?php if($low_stock > 0): ?>
        <a href="raw_materials/view_material.php" style="text-decoration:none;">
          <div class="topbar-badge" style="background:#9B1C1C;">
            <i class="bi bi-exclamation-triangle"></i>
            <?php echo $low_stock; ?> Low Stock
          </div>
        </a>
      <?php endif; ?>
      <div class="topbar-badge">
        <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
        <?php echo $_SESSION['name']; ?>
      </div>
    </div>
  </div>

  <!-- Content -->
  <div class="content-body">

    <!-- Hero Banner -->
    <div class="hero-banner" style="grid-template-columns: 1fr auto;">
      <div>
        <div class="hero-eyebrow">
          <i class="bi bi-lightning-charge-fill"></i>
          BakeChain SCM
        </div>
        <h1>
          Smart Bakery
          <span>Supply Chain</span>
        </h1>
        <p>
          Manage ingredients, suppliers, production batches, stock, customer orders,
          deliveries, QR traceability and real-time tracking — all in one premium platform.
        </p>
        <div class="hero-actions">
          <a href="production/create_batch.php" class="btn-hero btn-hero-primary">
            <i class="bi bi-plus-circle"></i> Create Batch
          </a>
          <a href="orders/create_order.php" class="btn-hero btn-hero-ghost">
            <i class="bi bi-cart-plus"></i> New Order
          </a>
          <a href="tracking/tracking_dashboard.php" class="btn-hero btn-hero-ghost">
            <i class="bi bi-geo-alt"></i> Track Item
          </a>
        </div>
      </div>

      <div class="hero-stats">
        <div class="hero-stat-card">
          <div class="num"><?php echo $total_batches; ?></div>
          <div class="label">Production Batches</div>
        </div>
        <div class="hero-stat-card">
          <div class="num"><?php echo $total_orders; ?></div>
          <div class="label">Total Orders</div>
        </div>
        <div class="hero-stat-card">
          <div class="num"><?php echo $total_tracking; ?></div>
          <div class="label">Tracking Logs</div>
        </div>
      </div>
    </div>

    <!-- Revenue Block -->
    <div class="revenue-block">
      <div style="position:relative;z-index:2;">
        <div class="rev-label">Total Revenue Generated</div>
        <div class="rev-amount">₹<?php echo number_format($total_sales, 2); ?></div>
        <div class="rev-sub">From all customer orders across the platform</div>
      </div>
      <div class="rev-right">
        <div class="rev-stat">
          <div class="rs-num"><?php echo $total_deliveries; ?></div>
          <div class="rs-label">Deliveries</div>
        </div>
        <div class="rev-stat">
          <div class="rs-num"><?php echo $total_inventory; ?></div>
          <div class="rs-label">Inventory</div>
        </div>
        <div class="rev-stat">
          <div class="rs-num"><?php echo $total_suppliers; ?></div>
          <div class="rs-label">Suppliers</div>
        </div>
      </div>
    </div>

    <!-- KPI Strip -->
    <div class="kpi-strip">
      <div class="kpi-item">
        <div class="kpi-icon-wrap"><i class="bi bi-truck"></i></div>
        <div class="kpi-value"><?php echo $total_suppliers; ?></div>
        <div class="kpi-label">Suppliers</div>
      </div>
      <div class="kpi-item">
        <div class="kpi-icon-wrap"><i class="bi bi-basket3"></i></div>
        <div class="kpi-value"><?php echo $total_products; ?></div>
        <div class="kpi-label">Products</div>
      </div>
      <div class="kpi-item">
        <div class="kpi-icon-wrap"><i class="bi bi-box-seam"></i></div>
        <div class="kpi-value"><?php echo $total_materials; ?></div>
        <div class="kpi-label">Materials</div>
      </div>
      <div class="kpi-item">
        <div class="kpi-icon-wrap"><i class="bi bi-boxes"></i></div>
        <div class="kpi-value"><?php echo $total_inventory; ?></div>
        <div class="kpi-label">Inventory</div>
      </div>
      <div class="kpi-item">
        <div class="kpi-icon-wrap"><i class="bi bi-geo-alt"></i></div>
        <div class="kpi-value"><?php echo $total_tracking; ?></div>
        <div class="kpi-label">Tracking</div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="section-title">Quick Operations</div>
    <div class="quick-grid">
      <a href="supplier/add_supplier.php" class="quick-action">
        <i class="bi bi-person-plus"></i>
        Add Supplier
      </a>
      <a href="raw_materials/add_material.php" class="quick-action">
        <i class="bi bi-box2-heart"></i>
        Add Material
      </a>
      <a href="products/add_product.php" class="quick-action">
        <i class="bi bi-bag-plus"></i>
        Add Product
      </a>
      <a href="production/create_batch.php" class="quick-action">
        <i class="bi bi-plus-square"></i>
        Create Batch
      </a>
    </div>

    <!-- Data Grid -->
    <div class="data-grid">

      <!-- Recent Orders -->
      <div class="panel-card">
        <div class="panel-card-header">
          <h3><i class="bi bi-receipt"></i> Recent Orders</h3>
          <a href="orders/view_orders.php" class="btn btn-sm btn-primary" style="font-size:12px;padding:6px 14px;border-radius:20px;">View All</a>
        </div>
        <div class="panel-card-body" style="padding:0;">
          <table class="table" style="margin:0;">
            <thead>
              <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while($order = mysqli_fetch_assoc($recent_orders)) { ?>
              <tr>
                <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td style="color:var(--muted);font-size:13px;"><?php echo $order['order_date']; ?></td>
                <td><strong>₹<?php echo $order['total_amount']; ?></strong></td>
                <td>
                  <?php
                    $st = strtolower($order['order_status']);
                    $cls = "status-$st";
                  ?>
                  <span class="status-badge <?php echo $cls; ?>"><?php echo $order['order_status']; ?></span>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Low Stock Alerts -->
      <div class="panel-card">
        <div class="panel-card-header">
          <h3><i class="bi bi-exclamation-triangle"></i> Low Stock Alerts</h3>
          <?php if($low_stock > 0): ?>
            <span class="status-badge status-cancelled"><?php echo $low_stock; ?> items</span>
          <?php endif; ?>
        </div>
        <div class="panel-card-body">
          <?php if(mysqli_num_rows($low_materials) > 0): ?>
            <?php while($mat = mysqli_fetch_assoc($low_materials)): ?>
              <div class="stock-alert">
                <div class="stock-alert-icon">
                  <i class="bi bi-box-seam"></i>
                </div>
                <div>
                  <div class="stock-alert-name"><?php echo htmlspecialchars($mat['material_name']); ?></div>
                  <div class="stock-alert-sub">
                    Current: <?php echo $mat['current_stock']; ?> <?php echo $mat['unit']; ?>
                    &nbsp;·&nbsp;
                    Min: <?php echo $mat['minimum_stock']; ?> <?php echo $mat['unit']; ?>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="stock-alert" style="border-left-color:#34D399;">
              <div class="stock-alert-icon" style="background:rgba(52,211,153,.15);color:#065f46;">
                <i class="bi bi-check-circle"></i>
              </div>
              <div>
                <div class="stock-alert-name">All Clear!</div>
                <div class="stock-alert-sub">All raw materials are above minimum stock levels.</div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>

    <!-- Smart Features -->
    <div class="section-title">Smart SCM Features</div>
    <div class="features-grid">
      <a href="batch_lookup/verify_batch.php" class="feature-card">
        <i class="bi bi-search"></i>
        <h5>Batch Lookup</h5>
        <p>Verify batch details and traceability.</p>
      </a>
      <a href="qr_verification/generate_qr.php" class="feature-card">
        <i class="bi bi-qr-code"></i>
        <h5>QR Generator</h5>
        <p>Generate traceability QR codes.</p>
      </a>
      <a href="tracking/tracking_dashboard.php" class="feature-card">
        <i class="bi bi-geo-alt"></i>
        <h5>Tracking</h5>
        <p>Track product, order and delivery.</p>
      </a>
      <a href="alternate_supplier/suggest_supplier.php" class="feature-card">
        <i class="bi bi-shuffle"></i>
        <h5>Supplier Suggestion</h5>
        <p>Smart supplier recommendations.</p>
      </a>
      <a href="bottleneck_detection/detect_bottleneck.php" class="feature-card">
        <i class="bi bi-exclamation-triangle"></i>
        <h5>Bottleneck Detection</h5>
        <p>Detect and resolve delay points.</p>
      </a>
    </div>

  </div><!-- /content-body -->

</div><!-- /main-content -->

<script>
/* Animate KPI numbers on load */
document.querySelectorAll('.kpi-value, .hero-stat-card .num, .rev-stat .rs-num').forEach(el => {
  const target = parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
  let current = 0;
  const step = Math.ceil(target / 40);
  const timer = setInterval(() => {
    current = Math.min(current + step, target);
    el.textContent = el.textContent.includes('₹')
      ? '₹' + current.toLocaleString('en-IN')
      : current;
    if (current >= target) clearInterval(timer);
  }, 25);
});
</script>

</body>
</html>