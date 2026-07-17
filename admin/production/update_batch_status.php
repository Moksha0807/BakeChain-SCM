<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT production_batches.*, products.product_name, products.flavor
FROM production_batches
JOIN products ON production_batches.product_id = products.product_id
WHERE production_batches.batch_id='$id'";

$result = mysqli_query($conn, $query);
$batch = mysqli_fetch_assoc($result);

$success = "";

if(isset($_POST['update']))
{
    $production_status = $_POST['production_status'];

    $update = "UPDATE production_batches SET production_status='$production_status' WHERE batch_id='$id'";

    if(mysqli_query($conn, $update))
    {
        $message = "Batch status updated to $production_status.";

        mysqli_query($conn, "INSERT INTO tracking_logs
        (module_name, reference_id, reference_code, tracking_status, tracking_message)
        VALUES
        ('Production','$id','".$batch['batch_code']."','$production_status','$message')");

        $success = "Batch status updated successfully!";

        $query = "SELECT production_batches.*, products.product_name, products.flavor
        FROM production_batches
        JOIN products ON production_batches.product_id = products.product_id
        WHERE production_batches.batch_id='$id'";

        $result = mysqli_query($conn, $query);
        $batch = mysqli_fetch_assoc($result);
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Update Batch Status</h2>
        <p>Update production batch progress and automatically create tracking record.</p>
    </div>

    <a href="production_history.php" class="btn btn-secondary">Back</a>
</div>

<?php if($success != "") { ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST">

    <div class="mb-3">
        <label>Batch</label>
        <input type="text" class="form-control"
        value="<?php echo $batch['batch_code']; ?> | <?php echo $batch['product_name']; ?> - <?php echo $batch['flavor']; ?>"
        readonly>
    </div>

    <div class="mb-4">
        <label>Production Status</label>
        <select name="production_status" class="form-select" required>
            <option value="created" <?php if($batch['production_status']=="created") echo "selected"; ?>>Created</option>
            <option value="in production" <?php if($batch['production_status']=="in production") echo "selected"; ?>>In Production</option>
            <option value="completed" <?php if($batch['production_status']=="completed") echo "selected"; ?>>Completed</option>
        </select>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update Status</button>
    <a href="production_history.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

<?php include '../../includes/footer.php'; ?>
