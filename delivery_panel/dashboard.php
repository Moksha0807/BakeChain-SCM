<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'delivery_partner')
{
    header("Location: ../auth/login.php");
    exit();
}

$partner_id = $_SESSION['user_id'];

$total_deliveries = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM deliveries
WHERE delivery_partner_id='$partner_id'
"))['total'];

$completed = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM deliveries
WHERE delivery_partner_id='$partner_id'
AND delivery_status='delivered'
"))['total'];

$pending = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM deliveries
WHERE delivery_partner_id='$partner_id'
AND delivery_status!='delivered'
"))['total'];

$total_delay = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT IFNULL(SUM(delay_days),0) total
FROM deliveries
WHERE delivery_partner_id='$partner_id'
"))['total'];

$recent = mysqli_query($conn,"
SELECT *
FROM deliveries
WHERE delivery_partner_id='$partner_id'
ORDER BY delivery_id DESC
LIMIT 8
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Delivery Dashboard | BakeChain SCM</title>
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

.qa-grid2 { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-bottom:26px; }
.qa-card { display:flex; flex-direction:column; align-items:center; text-align:center; gap:11px; padding:22px 16px; background:var(--white); border:1px solid rgba(216,196,172,.4); border-radius:16px; text-decoration:none; color:var(--burgundy); font-weight:700; font-size:14px; transition:.28s; box-shadow:0 4px 12px rgba(77,14,19,.06); }
.qa-card:hover { background:var(--burgundy); color:var(--on-dark); transform:translateY(-5px); box-shadow:0 14px 28px rgba(77,14,19,.2); }
.qa-card i { font-size:26px; }

.panel-card { background:var(--white); border:1px solid rgba(216,196,172,.4); border-radius:18px; overflow:hidden; box-shadow:0 4px 14px rgba(77,14,19,.07); }
.panel-card-header { padding:20px 24px 16px; border-bottom:1px solid rgba(216,196,172,.3); }
.panel-card-header h3 { font-size:16px; font-weight:800; color:var(--burgundy); margin:0; display:flex; align-items:center; gap:8px; }

@media(max-width:900px) {
  .kpi-row  { grid-template-columns:repeat(2,1fr); }
  .qa-grid2 { grid-template-columns:1fr; }
}
</style>
</head>
<body>

<?php include '../includes/delivery_sidebar.php'; ?>

<div class="main-content">

  <div class="topbar">
    <div class="topbar-left">
      <h2>Delivery Dashboard</h2>
      <small>Manage and track your assigned deliveries</small>
    </div>
    <div class="topbar-right">
      <?php if($pending > 0): ?>
        <div class="topbar-badge" style="background:#92400E;">
          <i class="bi bi-clock"></i>
          <?php echo $pending; ?> Pending
        </div>
      <?php endif; ?>
      <div class="topbar-badge">
        <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['name'],0,1)); ?></div>
        <?php echo $_SESSION['name']; ?>
      </div>
    </div>
  </div>

  <div class="content-body">

    <div class="hero-panel">
      <h1>Delivery Partner Dashboard 🚀</h1>
      <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>. Manage assigned deliveries and update delivery status.</p>
    </div>

    <div class="kpi-row">
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-truck"></i></div>
        <div class="kpi-box-value"><?php echo $total_deliveries; ?></div>
        <div class="kpi-box-label">Total Deliveries</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-check-circle"></i></div>
        <div class="kpi-box-value"><?php echo $completed; ?></div>
        <div class="kpi-box-label">Completed</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-clock-history"></i></div>
        <div class="kpi-box-value"><?php echo $pending; ?></div>
        <div class="kpi-box-label">Pending</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-box-icon"><i class="bi bi-exclamation-circle"></i></div>
        <div class="kpi-box-value"><?php echo $total_delay; ?></div>
        <div class="kpi-box-label">Total Delay Days</div>
      </div>
    </div>

    <div class="qa-grid2">
      <a href="assigned_deliveries.php" class="qa-card">
        <i class="bi bi-truck"></i>
        Assigned Deliveries
      </a>
      <a href="update_delivery.php" class="qa-card">
        <i class="bi bi-pencil-square"></i>
        Update Delivery
      </a>
    </div>

    <div class="panel-card">
      <div class="panel-card-header">
        <h3><i class="bi bi-list-check"></i> Recent Deliveries</h3>
      </div>
      <div style="padding:0;">
        <table class="table" style="margin:0;">
          <thead>
            <tr>
              <th>Delivery ID</th>
              <th>Order ID</th>
              <th>Status</th>
              <th>Assigned Date</th>
              <th>Delivery Date</th>
              <th>Delay</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($recent)): ?>
            <tr>
              <td><strong>DEL-<?php echo $row['delivery_id']; ?></strong></td>
              <td>ORD-<?php echo $row['order_id']; ?></td>
              <td>
                <?php if($row['delivery_status'] == 'delivered'): ?>
                  <span class="badge" style="background:#D1FAE5;color:#065F46;">Delivered</span>
                <?php else: ?>
                  <span class="badge" style="background:#FEF3C7;color:#92400E;"><?php echo ucwords(str_replace('_',' ',$row['delivery_status'])); ?></span>
                <?php endif; ?>
              </td>
              <td style="color:var(--muted);font-size:13px;"><?php echo $row['assigned_date']; ?></td>
              <td style="color:var(--muted);font-size:13px;"><?php echo $row['delivery_date']; ?></td>
              <td><?php echo $row['delay_days']; ?> Day(s)</td>
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