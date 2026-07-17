<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'production_manager')
{
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$error = "";

// Build prefix map per product (based on product name keywords)
function getBatchPrefix($product_name) {
    $name = strtolower($product_name);
    if (str_contains($name, 'chocolate')) return 'CHC';
    if (str_contains($name, 'oatmeal'))   return 'OAT';
    if (str_contains($name, 'butter'))    return 'BTC';
    // Generic fallback: first 3 letters of first word, uppercased
    $words = explode(' ', trim($product_name));
    return strtoupper(substr($words[0], 0, 3));
}

// Fetch products into an array
$products_result = mysqli_query($conn, "SELECT * FROM products ORDER BY product_name ASC");
$products_list = [];
while($p = mysqli_fetch_assoc($products_result)) {
    $products_list[] = $p;
}

// Pre-calculate next batch code per product
$next_codes = [];
foreach ($products_list as $p) {
    $pid    = $p['product_id'];
    $prefix = getBatchPrefix($p['product_name']);
    // Find highest numbered batch for this prefix
    $res    = mysqli_query($conn, "SELECT batch_code FROM production_batches WHERE batch_code LIKE '$prefix-BATCH-%' ORDER BY batch_code DESC");
    $next   = 1;
    if ($row = mysqli_fetch_assoc($res)) {
        preg_match('/-(\d+)$/', $row['batch_code'], $m);
        if (!empty($m[1])) $next = (int)$m[1] + 1;
    }
    $next_codes[$pid] = $prefix . '-BATCH-' . str_pad($next, 3, '0', STR_PAD_LEFT);
}

if(isset($_POST['create_batch']))
{
    $batch_code = trim($_POST['batch_code']);
    $product_id = $_POST['product_id'];
    $production_date = $_POST['production_date'];
    $expiry_date = $_POST['expiry_date'];
    $quantity_produced = $_POST['quantity_produced'];
    $production_status = $_POST['production_status'];

    $check = mysqli_query($conn, "SELECT * FROM production_batches WHERE batch_code='$batch_code'");

    if(mysqli_num_rows($check) > 0)
    {
        $error = "Batch code already exists. Please select a product again to get the next available code.";
    }
    else
    {
        $qr_code = $batch_code . ".png";

        $insert = mysqli_query($conn, "
        INSERT INTO production_batches
        (batch_code, product_id, production_date, expiry_date, quantity_produced, production_status, qr_code)
        VALUES
        ('$batch_code', '$product_id', '$production_date', '$expiry_date', '$quantity_produced', '$production_status', '$qr_code')
        ");

        if($insert)
        {
            $batch_id = mysqli_insert_id($conn);
            mysqli_query($conn, "UPDATE products SET batch_id='$batch_id', current_stock = current_stock + $quantity_produced WHERE product_id='$product_id'");
            mysqli_query($conn, "INSERT INTO tracking_logs
            (module_name, reference_id, reference_code, tracking_status, tracking_message)
            VALUES ('Production','$batch_id','$batch_code','Batch Created','Production batch has been created successfully.')");
            $message = "Production batch created successfully.";
        }
        else
        {
            $error = "Failed to create batch.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Batch | Production Panel</title>
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
            <h2>Create Production Batch</h2>
            <small>Create a new bakery production batch & update finished product stock</small>
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

        <div class="panel-card">
            <div class="panel-card-header">
                <h3><i class="bi bi-plus-circle"></i> Batch Creation Form</h3>
            </div>
            <div class="p-4" style="background: var(--white);">
                <form method="POST" id="batchForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Select Product</label>
                            <select name="product_id" id="productSelect" class="form-select form-select-lg" required onchange="updateBatchCode()">
                                <option value="">Select Product</option>
                                <?php foreach ($products_list as $product) { ?>
                                    <option value="<?php echo $product['product_id']; ?>">
                                        <?php echo htmlspecialchars($product['product_name']); ?> | <?php echo htmlspecialchars($product['flavor']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Batch Code</label>
                            <input type="text" id="batchCodeDisplay" class="form-control form-control-lg bg-light" placeholder="Select a product to auto-generate" readonly>
                            <input type="hidden" name="batch_code" id="batchCodeInput">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Production Date</label>
                            <input type="date" name="production_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+3 months')); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Quantity Produced</label>
                            <input type="number" name="quantity_produced" min="1" class="form-control" placeholder="Example: 100" required>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Production Status</label>
                            <select name="production_status" class="form-select" required>
                                <option value="created">Created</option>
                                <option value="in production">In Production</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="create_batch" id="submitBtn" class="btn btn-primary px-4 py-2" disabled>
                            <i class="bi bi-check-circle-fill"></i> Create Batch
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary px-4 py-2">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const nextCodes = <?php echo json_encode($next_codes); ?>;

function updateBatchCode() {
    const sel      = document.getElementById('productSelect');
    const pid      = sel.value;
    const display  = document.getElementById('batchCodeDisplay');
    const input    = document.getElementById('batchCodeInput');
    const btn      = document.getElementById('submitBtn');

    if (pid && nextCodes[pid]) {
        const code = nextCodes[pid];
        display.value = code;
        input.value   = code;
        btn.disabled  = false;
    } else {
        display.value = '';
        input.value   = '';
        btn.disabled  = true;
    }
}
</script>
</body>
</html>