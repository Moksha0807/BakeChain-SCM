<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'production_manager')
{
    header("Location: ../auth/login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT production_batches.*, products.product_name, products.flavor
FROM production_batches
JOIN products ON production_batches.product_id = products.product_id
WHERE production_batches.batch_id='$id'";

$result = mysqli_query($conn, $query);
$batch = mysqli_fetch_assoc($result);

if (!$batch) {
    header("Location: production_history.php");
    exit();
}

$success = "";
$error = "";

if(isset($_POST['update']))
{
    $production_status = mysqli_real_escape_string($conn, $_POST['production_status']);

    $update = "UPDATE production_batches SET production_status='$production_status' WHERE batch_id='$id'";

    if(mysqli_query($conn, $update))
    {
        $message = "Batch status updated to $production_status.";

        mysqli_query($conn, "INSERT INTO tracking_logs
        (module_name, reference_id, reference_code, tracking_status, tracking_message)
        VALUES
        ('Production','$id','".$batch['batch_code']."','$production_status','$message')");

        $success = "Batch status updated successfully!";

        // Refresh data
        $result = mysqli_query($conn, $query);
        $batch = mysqli_fetch_assoc($result);
    }
    else
    {
        $error = "Failed to update batch status.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Batch Status | Production Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
</head>
<body>

<?php include '../includes/production_sidebar.php'; ?>

<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <h2>Update Batch Status</h2>
            <small>Change status for batch <?php echo htmlspecialchars($batch['batch_code']); ?></small>
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
        
        <?php if($success != ""){ ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if($error != ""){ ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="panel-card">
            <div class="panel-card-header d-flex justify-content-between align-items-center">
                <h3><i class="bi bi-pencil-square"></i> Batch Status Form</h3>
                <a href="production_history.php" class="btn btn-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Back to History
                </a>
            </div>
            
            <div class="p-4" style="background: var(--white);">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Batch Details</label>
                            <input type="text" class="form-control form-control-lg bg-light" 
                                   value="<?php echo htmlspecialchars($batch['batch_code']); ?> | <?php echo htmlspecialchars($batch['product_name']); ?> (<?php echo htmlspecialchars($batch['flavor']); ?>)" 
                                   readonly>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Production Status</label>
                            <select name="production_status" class="form-select form-select-lg" required>
                                <option value="created" <?php if(strtolower($batch['production_status']) == "created") echo "selected"; ?>>Created</option>
                                <option value="in production" <?php if(strtolower($batch['production_status']) == "in production") echo "selected"; ?>>In Production</option>
                                <option value="completed" <?php if(strtolower($batch['production_status']) == "completed") echo "selected"; ?>>Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="update" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-check-circle-fill"></i> Update Status
                        </button>
                        <a href="production_history.php" class="btn btn-secondary px-4 py-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
