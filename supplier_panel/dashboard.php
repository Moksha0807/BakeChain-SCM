<?php
session_start();
include '../config/database.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supplier')
{
    header("Location: ../auth/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$user_q = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($user_q);
$email = $user['email'];
$supplier_q = mysqli_query($conn, "SELECT * FROM suppliers WHERE user_id='$user_id' OR email='$email' LIMIT 1");
$supplier = mysqli_fetch_assoc($supplier_q);
$total_materials = 0;
$fastest_delivery = 0;
$materials = false;
if($supplier)
{
    $supplier_id = $supplier['supplier_id'];
    $total_materials = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM supplier_materials
        WHERE supplier_id='$supplier_id'
    "))['total'];
    $fastest_delivery = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT IFNULL(MIN(delivery_days),0) AS fastest
        FROM supplier_materials
        WHERE supplier_id='$supplier_id'
    "))['fastest'];
    $materials = mysqli_query($conn,"
        SELECT sm.*, rm.material_name, rm.unit, rm.current_stock, rm.minimum_stock
        FROM supplier_materials sm
        JOIN raw_materials rm ON sm.material_id = rm.material_id
        WHERE sm.supplier_id='$supplier_id'
        ORDER BY rm.material_name ASC
    ");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Supplier Dashboard | BakeChain SCM</title>
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
.hero-panel::before {
  content:''; position:absolute; top:-50px; right:-50px;
  width:220px; height:220px; border-radius:50%;
  background:rgba(238,228,218,.06);
}
.hero-panel h1 { color:var(--on-dark); font-size:32px; font-weight:900; margin:0 0 8px; }
.hero-panel p  { color:rgba(238,228,218,.7); font-size:14px; font-weight:500; margin:0; }
.stat-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-bottom: 26px;
}
.stat-card {
  background: var(--white);
  border: 1px solid rgba(216,196,172,.4);
  border-radius: 16px;
  padding: 22px;
  box-shadow: 0 4px 14px rgba(77,14,19,.07);
  transition: .28s;
}
.stat-card:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(77,14,19,.13); }
.stat-icon { width:42px; height:42px; border-radius:11px; background:linear-gradient(135deg,var(--burgundy),#7a1520); display:flex; align-items:center; justify-content:center; color:var(--on-dark); font-size:18px; margin-bottom:12px; }
.stat-value { font-size:30px; font-weight:900; color:var(--burgundy); line-height:1; }
.stat-label { font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.7px; margin-top:4px; }
.qa-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-bottom:26px; }
.qa-card { display:flex; flex-direction:column; align-items:center; text-align:center; gap:11px; padding:22px 16px; background:var(--white); border:1px solid rgba(216,196,172,.4); border-radius:16px; text-decoration:none; color:var(--burgundy); font-weight:700; font-size:14px; transition:.28s; box-shadow:0 4px 12px rgba(77,14,19,.06); }
.qa-card:hover { background:var(--burgundy); color:var(--on-dark); transform:translateY(-5px); box-shadow:0 14px 28px rgba(77,14,19,.2); }
.qa-card i { font-size:26px; }
.panel-card { background:var(--white); border:1px solid rgba(216,196,172,.4); border-radius:18px; overflow:hidden; box-shadow:0 4px 14px rgba(77,14,19,.07); }
.panel-card-header { padding:20px 24px 16px; border-bottom:1px solid rgba(216,196,172,.3); }
.panel-card-header h3 { font-size:16px; font-weight:800; color:var(--burgundy); margin:0; display:flex; align-items:center; gap:8px; }
.notice-box { background:#FEF3C7; border-left:4px solid #FBBF24; border-radius:10px; padding:16px 20px; color:#92400E; font-weight:600; font-size:14px; }
@media(max-width:900px) {
  .stat-grid { grid-template-columns:1fr 1fr; }
  .qa-grid   { grid-template-columns:1fr; }
}
</style>
</head>
<body>
<?php include '../includes/supplier_sidebar.php'; ?>
<div class="main-content">
  <div class="topbar">
    <div class="topbar-left">
      <h2>Supplier Dashboard</h2>
      <small>Manage your supplied materials</small>
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
      <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>! 👋</h1>
      <p>Here's an overview of your materials and supply status.</p>
    </div>
    <?php if(!$supplier): ?>
      <div class="notice-box">
        <i class="bi bi-exclamation-triangle"></i>
        Supplier profile not found. Make sure this supplier email exists in the suppliers table.
      </div>
    <?php else: ?>
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-building"></i></div>
        <div class="stat-value"><?php echo htmlspecialchars($supplier['supplier_name']); ?></div>
        <div class="stat-label">Supplier Name</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
        <div class="stat-value"><?php echo $total_materials; ?></div>
        <div class="stat-label">Total Materials</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-lightning-charge"></i></div>
        <div class="stat-value"><?php echo $fastest_delivery; ?></div>
        <div class="stat-label">Fastest Delivery (Days)</div>
      </div>
    </div>
    <div class="qa-grid">
      <a href="my_materials.php" class="qa-card">
        <i class="bi bi-box-seam"></i>
        My Materials
      </a>
      <a href="profile.php" class="qa-card">
        <i class="bi bi-person-badge"></i>
        Supplier Profile
      </a>
    </div>
    <div class="panel-card">
      <div class="panel-card-header">
        <h3><i class="bi bi-list-check"></i> Your Supplied Materials</h3>
      </div>
      <div style="padding:0;">
        <table class="table" style="margin:0;">
          <thead>
            <tr>
              <th>Material</th>
              <th>Unit</th>
              <th>Price / Unit</th>
              <th>Delivery Days</th>
              <th>Current Stock</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if($materials && mysqli_num_rows($materials) > 0): ?>
              <?php while($row = mysqli_fetch_assoc($materials)): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($row['material_name']); ?></strong></td>
                <td><?php echo strtoupper($row['unit']); ?></td>
                <td>₹<?php echo $row['price_per_unit']; ?></td>
                <td><?php echo $row['delivery_days']; ?> day(s)</td>
                <td><?php echo $row['current_stock']; ?></td>
                <td>
                  <?php if($row['current_stock'] <= $row['minimum_stock']): ?>
                    <span class="badge" style="background:#FEE2E2;color:#991B1B;">Low Stock</span>
                  <?php else: ?>
                    <span class="badge" style="background:#D1FAE5;color:#065F46;">Available</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" style="text-align:center;color:var(--muted);">No materials assigned to this supplier.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>