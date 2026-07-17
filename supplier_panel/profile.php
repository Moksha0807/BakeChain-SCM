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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Supplier Profile | Supplier Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<style>
.profile-card {
    background: var(--white);
    border: 1px solid var(--sand);
    border-radius: 18px;
    overflow: hidden;
}
.profile-header {
    background: linear-gradient(130deg, var(--burgundy) 0%, #3a0a0e 55%, var(--deep) 100%);
    padding: 30px;
    color: white;
}
.profile-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 800;
    border: 2px solid white;
}
.info-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    font-weight: 800;
    color: var(--muted);
}
.info-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 20px;
}
</style>
</head>
<body>

<?php include '../includes/supplier_sidebar.php'; ?>

<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <h2>Supplier Profile</h2>
            <small>Your partner account information and contact details</small>
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

        <div class="profile-card mb-4">
            <div class="profile-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($supplier['supplier_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h3 class="mb-0 text-white font-weight-900"><?php echo htmlspecialchars($supplier['supplier_name']); ?></h3>
                        <span class="badge mt-2" style="background:#D1FAE5;color:#065F46; font-size:12px;">
                            <i class="bi bi-check-circle-fill"></i> <?php echo ucfirst($supplier['status']); ?> Partner
                        </span>
                    </div>
                </div>
                <a href="dashboard.php" class="btn btn-outline-light rounded-pill btn-sm px-3">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>

            <div class="p-4" style="background: var(--white);">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-label">Contact Person</div>
                        <div class="info-value"><?php echo htmlspecialchars($supplier['contact_person']); ?></div>
                        
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($supplier['email']); ?></div>
                        
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($supplier['phone']); ?></div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-label">Business Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($supplier['address']); ?></div>
                        
                        <div class="info-label">Sourcing System ID</div>
                        <div class="info-value">SUP-<?php echo str_pad($supplier['supplier_id'], 4, '0', STR_PAD_LEFT); ?></div>
                        
                        <div class="info-label">Platform Role</div>
                        <div class="info-value">Authorized Raw Material Supplier</div>
                    </div>
                </div>
            </div>
        </div>

        <?php } ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>