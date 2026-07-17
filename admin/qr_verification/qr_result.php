<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$batch_code = "";

if(isset($_GET['batch_code']))
{
    $batch_code = $_GET['batch_code'];
}
elseif(isset($_POST['batch_code']))
{
    $batch_code = $_POST['batch_code'];
}

// Fetch all batch codes for the dropdown
$all_batches = mysqli_query($conn, "SELECT batch_code FROM production_batches ORDER BY batch_code DESC");

$batch = null;

if($batch_code != "")
{
    $query = "SELECT production_batches.*, products.product_name, products.flavor, products.price
    FROM production_batches
    JOIN products ON production_batches.product_id = products.product_id
    WHERE production_batches.batch_code='$batch_code'";

    $result = mysqli_query($conn, $query);
    $batch = mysqli_fetch_assoc($result);
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.verify-box{
    background:#fff8f1;
    border:1px solid var(--sand);
    padding:28px;
    margin-bottom:28px;
}

.detail-card{
    background:#fff8f1;
    border:1px solid var(--sand);
    padding:28px;
    margin-top:25px;
}

.detail-row{
    display:grid;
    grid-template-columns:220px 1fr;
    padding:12px 0;
    border-bottom:1px solid #e6d6c5;
}

.detail-row:last-child{
    border-bottom:none;
}

.detail-label{
    font-weight:900;
    color:var(--burgundy);
}

.detail-value{
    color:#4a3a36;
    font-weight:500;
}

.status-completed{
    color:#176b42;
    font-weight:800;
}

.status-progress{
    color:#b86b00;
    font-weight:800;
}

.not-found{
    background:#fff8f1;
    border:1px solid var(--sand);
    padding:28px;
    margin-top:25px;
}
</style>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>QR Verification Result</h2>
        <p>Verify product batch details using QR batch code.</p>
    </div>

    <a href="generate_qr.php" class="btn btn-secondary">Back to QR Generator</a>
</div>

<div class="verify-box">
    <form method="POST" action="qr_result.php">
        <div class="row">
            <div class="col-md-9">
                <label class="mb-1">Select Batch Code</label>
                <select name="batch_code" class="form-select" required>
                    <option value="">Select Batch Code</option>
                    <?php 
                    mysqli_data_seek($all_batches, 0);
                    while($br = mysqli_fetch_assoc($all_batches)) { ?>
                        <option value="<?php echo htmlspecialchars($br['batch_code']); ?>" <?php if($br['batch_code'] == $batch_code) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($br['batch_code']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Verify QR</button>
            </div>
        </div>
    </form>
</div>

<?php if($batch_code != "" && $batch) { ?>

<div class="detail-card">

    <div class="detail-row">
        <div class="detail-label">Batch Code</div>
        <div class="detail-value"><?php echo $batch['batch_code']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Product</div>
        <div class="detail-value"><?php echo $batch['product_name']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Flavor</div>
        <div class="detail-value"><?php echo $batch['flavor']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Price</div>
        <div class="detail-value">₹<?php echo $batch['price']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Production Date</div>
        <div class="detail-value"><?php echo $batch['production_date']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Expiry Date</div>
        <div class="detail-value"><?php echo $batch['expiry_date']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Quantity Produced</div>
        <div class="detail-value"><?php echo $batch['quantity_produced']; ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Current Location</div>
        <div class="detail-value">
            <?php
            if(isset($batch['current_location']) && $batch['current_location'] != "")
            {
                echo $batch['current_location'];
            }
            else
            {
                echo "Factory Warehouse";
            }
            ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value">
            <?php
            if(strtolower($batch['production_status']) == "completed")
            {
                echo "<span class='status-completed'>Completed</span>";
            }
            else
            {
                echo "<span class='status-progress'>".$batch['production_status']."</span>";
            }
            ?>
        </div>
    </div>

</div>

<?php } elseif($batch_code != "") { ?>

<div class="not-found">
    <h4>Invalid QR / Batch Not Found</h4>
    <p>No batch found with code: <b><?php echo $batch_code; ?></b></p>
</div>

<?php } ?>

</div>

</div>

<?php include '../../includes/footer.php'; ?>