<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'production_manager')
{
    header("Location: ../auth/login.php");
    exit();
}
$total_batches = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) AS total FROM production_batches
"))['total'];
$completed_batches = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) AS total FROM production_batches
WHERE LOWER(production_status)='completed'
"))['total'];
$in_production = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) AS total FROM production_batches
WHERE LOWER(production_status)='in production'
"))['total'];
$total_quantity = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT IFNULL(SUM(quantity_produced),0) AS total FROM production_batches
"))['total'];
$batches = mysqli_query($conn, "
SELECT production_batches.*, products.product_name, products.flavor
FROM production_batches
JOIN products ON production_batches.product_id = products.product_id
ORDER BY production_batches.batch_id DESC
LIMIT 8
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Production Dashboard | BakeChain SCM</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<style>
.hero-panel {
  background: linear-gradient(130deg, var(--burgundy) 0%, #3a0a0e 55%, var(--deep) 100%);
  border-radius: 20px;
  padding: 38px 44px;
  margin-bottom: 26px;
  position: relative;
  overflow: hidden;
}
.hero-panel::before { content:''; position:absolute; top:-50px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(238,228,218,.06); }
.hero-panel h1 { color:var(--on-dark); font-size:32px; font-weight:900; margin:0 0 8px; }
.hero-panel p  { color:rgba(238,228,218,.7); font-size:14px; font-weight:500; margin:0; }
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:26px; }
.kpi-box { background:var(--white); border:1px solid rgba(216,196,172,.4); border-radius:16px; padding:22px; box-shadow:0 4px 14px rgba(77,14,19,.07); transition:.28s; }
.kpi-box:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(77,14,19,.13); }
.kpi-box-icon { width:42px; height:42px; border-radius:11px; background:linear-gradient(135deg,var(--burgundy),#7a1520); display:flex; align-items:center; justify-content:center; color:var(--on-dark); font-size:18px; margin-bottom:12px; }
.kpi-box-value { font-size:30px; font-weight:900; color:var(--burgundy); line-height:1; }
.kpi-box-label { font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.7px; margin-top:4px; }
.qa-grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:26px; }
.qa-card { display:flex; flex-direction:column; align-items:center; text-align:center; gap:11px; padding:22px 16px; background:var(--white); border:1px solid rgba(216,196,172,.4); border-radius:16px; text-decoration:none; color:var(--burgundy); font-weight:700; font-size:14px; transition:.28s; box-shadow:0 4px 12px rgba(77,14,19,.06); }
.qa-card:hover { background:var(--burgundy); color:var(--on-dark); transform:translateY(-5px); box-shadow:0 14px 28px rgba(77,14,19,.2); }
.qa-card i { font-size:26px; }
.panel-card { background:var(--white); border:1px solid rgba(216,196,172,.4); border-radius:18px; overflow:hidden; box-shadow:0 4px 14px rgba(77,14,19,.07); }
.panel-card-header { padding:20px 24px 16px; border-bottom:1px solid rgba(216,196,172,.3); }
.panel-card-header h3 { font-size:16px; font-weight:800; color:var(--burgundy); margin:0; display:flex; align-items:center; gap:8px; }
@media(max-width:900px) {
  .kpi-row   { grid-template-columns:repeat(2,1fr); }
  .qa-grid3  { grid-template-columns:1fr; }
}
</style>
</head>
<body>
<?php include '../includes/production_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div class="topbar-left">
      <h2>Production Dashboard</h2>
      <small>Monitor batches and production operations</small>
    </div>
    <div class="topbar-right">
      <div class="topbar-badge">
        <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['name'],0,1)); ?></div>
        <?php echo $_SESSION['name']; ?>
      </div>
    </div>
  </div>
  <div class="content-body">
    <div class="hero-panel">
      <h1>Production Manager Dashboard 🏭</h1>
      <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>. Manage batches, monitor status and verify traceability.</p>
    </div>
    <div class="kpi-row">
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-boxes"></i></div>
        <div class="kpi-box-value"><?php echo $total_batches; ?></div>
        <div class="kpi-box-label">Total Batches</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-check-circle"></i></div>
        <div class="kpi-box-value"><?php echo $completed_batches; ?></div>
        <div class="kpi-box-label">Completed</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-gear"></i></div>
        <div class="kpi-box-value"><?php echo $in_production; ?></div>
        <div class="kpi-box-label">In Production</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-basket3"></i></div>
        <div class="kpi-box-value"><?php echo $total_quantity; ?></div>
        <div class="kpi-box-label">Total Quantity</div>
      </div>
    </div>
    <div class="qa-grid3">
      <a href="create_batch.php" class="qa-card">
        <i class="bi bi-plus-circle"></i>
        Create Batch
      </a>
      <a href="production_history.php" class="qa-card">
        <i class="bi bi-clock-history"></i>
        Production History
      </a>
      <a href="verify_batch.php" class="qa-card">
        <i class="bi bi-search"></i>
        Verify Batch
      </a>
    </div>
    <div class="panel-card">
      <div class="panel-card-header">
        <h3><i class="bi bi-list-check"></i> Recent Production Batches</h3>
      </div>
      <div style="padding:0;">
        <table class="table" style="margin:0;">
          <thead>
            <tr>
              <th>Batch Code</th>
              <th>Product</th>
              <th>Flavor</th>
              <th>Production Date</th>
              <th>Expiry Date</th>
              <th>Quantity</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($batches)): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($row['batch_code']); ?></strong></td>
              <td><?php echo htmlspecialchars($row['product_name']); ?></td>
              <td><?php echo htmlspecialchars($row['flavor']); ?></td>
              <td style="color:var(--muted);font-size:13px;"><?php echo $row['production_date']; ?></td>
              <td style="color:var(--muted);font-size:13px;"><?php echo $row['expiry_date']; ?></td>
              <td><?php echo $row['quantity_produced']; ?></td>
              <td>
                <?php
                  $st = strtolower($row['production_status']);
                  if($st == 'completed') {
                    echo "<span class='badge' style='background:#D1FAE5;color:#065F46;'>Completed</span>";
                  } elseif($st == 'in production') {
                    echo "<span class='badge' style='background:#DBEAFE;color:#1E40AF;'>In Production</span>";
                  } else {
                    echo "<span class='badge' style='background:#F3F4F6;color:#374151;'>".$row['production_status']."</span>";
                  }
                ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>