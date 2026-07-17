<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT production_batches.*, products.product_name, products.flavor, products.price
FROM production_batches
JOIN products ON production_batches.product_id = products.product_id
WHERE production_batches.batch_id='$id'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$tracking = mysqli_query($conn, "SELECT * FROM tracking_logs
WHERE module_name='Production' AND reference_id='$id'
ORDER BY tracking_id ASC");
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.detail-box{
    background:#fff8f1;
    border:1px solid var(--sand);
    padding:22px;
    margin-bottom:20px;
}

.detail-box p{
    margin-bottom:12px;
}

.timeline-item{
    border-left:4px solid var(--burgundy);
    padding:14px 18px;
    background:#fff8f1;
    margin-bottom:14px;
}

.timeline-item strong{
    color:var(--burgundy);
}
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Batch Details</h2>
        <p>View complete production batch information and tracking activity.</p>
    </div>

    <a href="production_history.php" class="btn btn-secondary">Back</a>
</div>

<div class="detail-box">
    <p><b>Batch Code:</b> <?php echo $row['batch_code']; ?></p>
    <p><b>Product:</b> <?php echo $row['product_name']; ?></p>
    <p><b>Flavor:</b> <?php echo $row['flavor']; ?></p>
    <p><b>Price:</b> ₹<?php echo $row['price']; ?></p>
    <p><b>Production Date:</b> <?php echo $row['production_date']; ?></p>
    <p><b>Expiry Date:</b> <?php echo $row['expiry_date']; ?></p>
    <p><b>Quantity Produced:</b> <?php echo $row['quantity_produced']; ?></p>
    <p><b>Status:</b> <?php echo $row['production_status']; ?></p>
    <p><b>QR Code:</b> <?php echo $row['qr_code']; ?></p>
</div>

<h4>Production Tracking</h4>

<?php if(mysqli_num_rows($tracking) > 0) { ?>
    <?php while($t = mysqli_fetch_assoc($tracking)) { ?>
        <div class="timeline-item">
            <strong><?php echo $t['tracking_status']; ?></strong><br>
            <?php echo $t['tracking_message']; ?><br>
            <small><?php echo $t['created_at']; ?></small>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="timeline-item">
        <strong>No tracking record found.</strong><br>
        Tracking will appear here once batch activity is added.
    </div>
<?php } ?>

</div>
</div>

<?php include '../../includes/footer.php'; ?>