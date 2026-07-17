<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'delivery_partner')
{
    header("Location: ../auth/login.php");
    exit();
}

$partner_id = $_SESSION['user_id'];
$message = "";
$error = "";
$delivery_id = "";

if(isset($_GET['id']))
{
    $delivery_id = $_GET['id'];
}

if(isset($_POST['update_delivery']))
{
    $delivery_id = $_POST['delivery_id'];
    $delivery_status = mysqli_real_escape_string($conn, trim($_POST['delivery_status']));
    $delivery_date = $_POST['delivery_date'];
    $delay_days = $_POST['delay_days'];

    if($delivery_status == "")
    {
        $error = "Please select delivery status.";
    }
    else
    {
        // Fetch order_id before update
        $del_query = mysqli_query($conn, "SELECT order_id FROM deliveries WHERE delivery_id='$delivery_id'");
        $del_row = mysqli_fetch_assoc($del_query);
        $order_id = $del_row ? $del_row['order_id'] : 0;

        $update = mysqli_query($conn, "
        UPDATE deliveries
        SET 
            delivery_status='$delivery_status',
            delivery_date='$delivery_date',
            delay_days='$delay_days'
        WHERE delivery_id='$delivery_id'
        ");

        if(!$update)
        {
            $error = "SQL Error: " . mysqli_error($conn);
        }
        else
        {
            // Insert tracking log for delivery
            mysqli_query($conn, "INSERT INTO tracking_logs
            (module_name, reference_id, reference_code, tracking_status, tracking_message)
            VALUES
            ('Deliveries','$delivery_id','DEL-$delivery_id','$delivery_status','Delivery status updated to $delivery_status.')");

            if($delivery_status == "delivered" && $order_id > 0)
            {
                mysqli_query($conn, "UPDATE orders SET order_status='completed' WHERE order_id='$order_id'");

                mysqli_query($conn, "INSERT INTO tracking_logs
                (module_name, reference_id, reference_code, tracking_status, tracking_message)
                VALUES
                ('Orders','$order_id','ORD-$order_id','Completed','Order delivered successfully and marked as completed.')");
            }

            $message = "Delivery updated successfully.";
        }
    }
}

$deliveries = mysqli_query($conn, "
SELECT *
FROM deliveries
WHERE delivery_partner_id='$partner_id'
AND delivery_status != 'delivered'
ORDER BY delivery_id DESC
");

$selected_delivery = null;

if($delivery_id != "")
{
    $selected_query = mysqli_query($conn, "
    SELECT *
    FROM deliveries
    WHERE delivery_id='$delivery_id'
    AND delivery_partner_id='$partner_id'
    AND delivery_status != 'delivered'
    LIMIT 1
    ");

    if(mysqli_num_rows($selected_query) > 0)
    {
        $selected_delivery = mysqli_fetch_assoc($selected_query);
    }
    else
    {
        $error = "Selected delivery not found for this delivery partner.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Delivery | BakeChain SCM</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<style>
.info-preview {
    background: var(--cream);
    border: 1px solid var(--sand);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<?php include '../includes/delivery_sidebar.php'; ?>

<div class="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <h2>Update Delivery Status</h2>
            <small>Update delivery progression stages & delay logs</small>
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
        
        <?php if($message != ""){ ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if($error != ""){ ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="panel-card mb-4">
            <div class="panel-card-header d-flex justify-content-between align-items-center">
                <h3><i class="bi bi-search"></i> Select Delivery Assignment</h3>
                <a href="assigned_deliveries.php" class="btn btn-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
            
            <div class="p-4" style="background: var(--white);">
                <form method="GET" class="row align-items-end g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Select Active Delivery</label>
                        <select name="id" class="form-select form-select-lg" onchange="this.form.submit()" required>
                            <option value="">— Select Delivery Assignment —</option>
                            <?php 
                            mysqli_data_seek($deliveries, 0);
                            while($row = mysqli_fetch_assoc($deliveries)){ 
                            ?>
                                <option value="<?php echo $row['delivery_id']; ?>" <?php if($delivery_id == $row['delivery_id']) echo "selected"; ?>>
                                    DEL-<?php echo $row['delivery_id']; ?> | ORD-<?php echo $row['order_id']; ?> | Status: <?php echo $row['delivery_status'] == "" ? "Not Updated" : ucwords(str_replace('_', ' ', $row['delivery_status'])); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <?php if($selected_delivery){ ?>
        <div class="panel-card">
            <div class="panel-card-header">
                <h3><i class="bi bi-pencil-square"></i> Update Form — DEL-<?php echo $selected_delivery['delivery_id']; ?></h3>
            </div>
            
            <div class="p-4" style="background: var(--white);">
                <form method="POST">
                    <input type="hidden" name="delivery_id" value="<?php echo $selected_delivery['delivery_id']; ?>">

                    <?php
                    // Define status progression
                    $status_order = ['packed', 'shipped', 'out_for_delivery', 'delivered'];
                    $current_status = $selected_delivery['delivery_status'];
                    $current_index = array_search($current_status, $status_order);

                    $status_labels = [
                        'packed'           => 'Packed',
                        'shipped'          => 'Shipped',
                        'out_for_delivery' => 'Out For Delivery',
                        'delivered'        => 'Delivered',
                    ];
                    ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Delivery Status Progression</label>
                            <?php if($current_status == 'delivered'): ?>
                                <div class="alert alert-success py-2">
                                    ✅ Already Delivered. No changes allowed.
                                </div>
                                <input type="hidden" name="delivery_status" value="delivered">
                            <?php else: ?>
                                <select name="delivery_status" class="form-select form-select-lg" required>
                                    <option value="">Select Next Status</option>
                                    <?php
                                    foreach($status_order as $index => $status):
                                        if($current_index === false || $index > $current_index):
                                    ?>
                                        <option value="<?php echo $status; ?>">
                                            <?php echo $status_labels[$status]; ?>
                                        </option>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </select>
                                <div class="form-text mt-2 text-muted">
                                    Current Status: <span class="badge bg-secondary"><?php echo $status_labels[$current_status] ?? ucwords(str_replace('_',' ',$current_status)); ?></span> (Progression is forward-only)
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Delivery Date</label>
                            <input type="date" name="delivery_date" class="form-control form-control-lg" value="<?php echo $selected_delivery['delivery_date']; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Delay Days</label>
                            <input type="number" name="delay_days" min="0" class="form-control form-control-lg" value="<?php echo $selected_delivery['delay_days']; ?>" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="update_delivery" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-check-circle-fill"></i> Save Delivery Status
                        </button>
                        <a href="assigned_deliveries.php" class="btn btn-secondary px-4 py-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php } ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>