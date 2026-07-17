<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$products_result = mysqli_query($conn, "SELECT * FROM products WHERE status='available' ORDER BY product_name ASC");
$products = [];
while($p = mysqli_fetch_assoc($products_result)) {
    $products[] = $p;
}

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

// Pre-calculate next batch code per product
$next_codes = [];
foreach ($products as $p) {
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

// Fetch recent batches grouped by product for the sidebar
$recent_batches = [];
$rb = mysqli_query($conn, "SELECT pb.batch_code, pb.production_status, p.product_name
    FROM production_batches pb
    JOIN products p ON pb.product_id = p.product_id
    ORDER BY pb.batch_id DESC LIMIT 10");
while ($row = mysqli_fetch_assoc($rb)) {
    $recent_batches[] = $row;
}

$success = "";
$error   = "";

if (isset($_POST['submit'])) {
    $product_id       = $_POST['product_id'];
    $batch_code       = trim($_POST['batch_code']);
    $production_date  = $_POST['production_date'];
    $expiry_date      = $_POST['expiry_date'];
    $quantity_produced= $_POST['quantity_produced'];
    $production_status= $_POST['production_status'];
    $qr_code          = $batch_code . ".png";

    // Duplicate check
    $check = mysqli_query($conn, "SELECT * FROM production_batches WHERE batch_code='$batch_code'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Batch code <strong>$batch_code</strong> already exists. Please select a product again to get the next available code.";
    } else {
        $query = "INSERT INTO production_batches
        (batch_code, product_id, production_date, expiry_date, quantity_produced, production_status, qr_code)
        VALUES ('$batch_code','$product_id','$production_date','$expiry_date','$quantity_produced','$production_status','$qr_code')";

        if (mysqli_query($conn, $query)) {
            $batch_id = mysqli_insert_id($conn);
            mysqli_query($conn, "UPDATE products SET batch_id='$batch_id', current_stock = current_stock + $quantity_produced WHERE product_id='$product_id'");
            mysqli_query($conn, "INSERT INTO tracking_logs
            (module_name, reference_id, reference_code, tracking_status, tracking_message)
            VALUES ('Production','$batch_id','$batch_code','Batch Created','Production batch has been created successfully.')");
            $success = "Production batch <strong>$batch_code</strong> created successfully!";
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>



<div class="content">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Create Production Batch</h2>
        <p>Select a product — the batch code will be generated automatically.</p>
    </div>
    <a href="production_history.php" class="btn btn-secondary">View History</a>
</div>

<?php if ($error != ""): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success != ""): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <a href="create_batch.php" class="btn btn-primary">Create Another Batch</a>
    <a href="production_history.php" class="btn btn-secondary">View Production History</a>
<?php else: ?>

<div class="page-card">

    <form method="POST" id="batchForm">


            <div class="mb-3">
                <label class="form-label">Select Product</label>
                <select name="product_id" id="productSelect" class="form-select" required onchange="updateBatchCode()">
                    <option value="">Select Product</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?php echo $p['product_id']; ?>"
                                data-code="<?php echo htmlspecialchars($next_codes[$p['product_id']]); ?>">
                            <?php echo htmlspecialchars($p['product_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Batch Code</label>
                <input type="text" id="batchCodeDisplay" class="form-control" placeholder="Select a product to auto-generate" readonly>
                <input type="hidden" name="batch_code" id="batchCodeInput">
            </div>



            <div class="mb-3">
                <label class="form-label fw-bold">Production Date</label>
                <input type="date" name="production_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Quantity Produced</label>
                <input type="number" name="quantity_produced" class="form-control" placeholder="Ex: 100" min="1" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Production Status</label>
                <select name="production_status" class="form-select" required>
                    <option value="created">Created</option>
                    <option value="in production">In Production</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <button type="submit" name="submit" class="btn btn-primary" id="submitBtn" disabled>
                <i class="bi bi-plus-circle"></i> Create Batch
            </button>

        </form>

</div><!-- end page-card -->

<?php endif; ?>

</div><!-- end content -->

<?php include '../../includes/footer.php'; ?>

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