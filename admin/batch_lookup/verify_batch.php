<?php 
require_once '../../includes/admin_auth.php';
include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>
    <h2>Batch Lookup</h2>
    <p>Enter batch code to verify product production, expiry, quantity and current location.</p>
</div>

</div>

<?php 
include '../../config/database.php';
$batches = mysqli_query($conn, "SELECT batch_code FROM production_batches ORDER BY batch_code DESC");
?>
<form method="GET" action="batch_result.php">

    <div class="mb-3">
        <label class="form-label">Select Batch Code</label>
        <select name="batch_code" class="form-select" required>
            <option value="">Select Batch Code</option>
            <?php while($row = mysqli_fetch_assoc($batches)) { ?>
                <option value="<?php echo htmlspecialchars($row['batch_code']); ?>">
                    <?php echo htmlspecialchars($row['batch_code']); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Verify Batch</button>

</form>

</div>

</div>

<?php include '../../includes/footer.php'; ?>