<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'production_manager')
{
    header("Location: ../auth/login.php");
    exit();
}

$batch = null;
$error = "";
$batches = mysqli_query($conn, "SELECT batch_code FROM production_batches ORDER BY batch_code DESC");

if(isset($_POST['verify']))
{
    $batch_code = trim($_POST['batch_code']);

    $query = mysqli_query($conn, "
    SELECT production_batches.*, products.product_name, products.flavor
    FROM production_batches
    JOIN products ON production_batches.product_id = products.product_id
    WHERE production_batches.batch_code='$batch_code'
    LIMIT 1
    ");

    if(mysqli_num_rows($query) > 0)
    {
        $batch = mysqli_fetch_assoc($query);
    }
    else
    {
        $error = "No batch found with this batch code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Batch | Production Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<style>
.info-box {
    background: var(--cream);
    border: 1px solid var(--sand);
    border-radius: 12px;
    padding: 16px 20px;
    height: 100%;
}
.info-box small {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    font-weight: 800;
    color: var(--muted);
    margin-bottom: 4px;
}
.info-box p {
    font-size: 18px;
    font-weight: 900;
    color: var(--burgundy);
    margin: 0;
}
</style>
</head>
<body>

<?php include '../includes/production_sidebar.php'; ?>

<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <h2>Verify Batch</h2>
            <small>Trace batch details, product components, and dates</small>
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
        
        <?php if($error != ""){ ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="panel-card mb-4">
            <div class="panel-card-header d-flex justify-content-between align-items-center">
                <h3><i class="bi bi-search"></i> Select Batch to Verify</h3>
                <a href="dashboard.php" class="btn btn-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <div class="p-4" style="background: var(--white);">
                <form method="POST" class="row align-items-end g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Batch Code</label>
                        <select name="batch_code" class="form-select form-select-lg" required>
                            <option value="">— Select Batch Code —</option>
                            <?php 
                            mysqli_data_seek($batches, 0);
                            while($row = mysqli_fetch_assoc($batches)) { 
                            ?>
                                <option value="<?php echo htmlspecialchars($row['batch_code']); ?>" <?php if(isset($batch_code) && $batch_code == $row['batch_code']) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($row['batch_code']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" name="verify" class="btn btn-primary btn-lg w-100 py-2">
                            <i class="bi bi-shield-check"></i> Verify Batch Details
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if($batch) { ?>
        <div class="panel-card">
            <div class="panel-card-header bg-success-subtle text-success">
                <h3 class="text-success"><i class="bi bi-check-circle-fill"></i> Batch Verified Successfully</h3>
            </div>
            
            <div class="p-4" style="background: var(--white);">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box">
                            <small>Batch Code</small>
                            <p><?php echo htmlspecialchars($batch['batch_code']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box">
                            <small>Product Name</small>
                            <p><?php echo htmlspecialchars($batch['product_name']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box">
                            <small>Flavor</small>
                            <p><?php echo htmlspecialchars($batch['flavor']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box">
                            <small>Quantity Produced</small>
                            <p><?php echo htmlspecialchars($batch['quantity_produced']); ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        <div class="info-box">
                            <small>Production Date</small>
                            <p><?php echo htmlspecialchars($batch['production_date']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="info-box">
                            <small>Expiry Date</small>
                            <p><?php echo htmlspecialchars($batch['expiry_date']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <div class="info-box">
                            <small>Batch Status</small>
                            <p>
                                <?php
                                $status = strtolower($batch['production_status']);
                                if($status == "completed") {
                                    echo "<span class='text-success'><i class='bi bi-check-circle-fill'></i> Completed</span>";
                                } elseif($status == "in production") {
                                    echo "<span class='text-primary'><i class='bi bi-gear-fill spin'></i> In Production</span>";
                                } else {
                                    echo "<span class='text-secondary'>".ucwords($status)."</span>";
                                }
                                ?>
                            </p>
                        </div>
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