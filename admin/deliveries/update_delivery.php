<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT deliveries.*, users.name AS partner_name
FROM deliveries
JOIN users ON deliveries.delivery_partner_id = users.user_id
WHERE deliveries.delivery_id='$id'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$success = "";

if(isset($_POST['update']))
{
    $delivery_status = $_POST['delivery_status'];
    $delivery_date = $_POST['delivery_date'];

    $date1 = new DateTime($row['assigned_date']);
    $date2 = new DateTime($delivery_date);
    $delay_days = $date1->diff($date2)->days;

    $update = "UPDATE deliveries SET
    delivery_status='$delivery_status',
    delivery_date='$delivery_date',
    delay_days='$delay_days'
    WHERE delivery_id='$id'";

    if(mysqli_query($conn, $update))
    {
        mysqli_query($conn, "INSERT INTO tracking_logs
        (module_name, reference_id, reference_code, tracking_status, tracking_message)
        VALUES
        ('Deliveries','$id','DEL-$id','$delivery_status','Delivery status updated to $delivery_status.')");

        if($delivery_status == "delivered")
        {
            mysqli_query($conn, "UPDATE orders SET order_status='completed' WHERE order_id='".$row['order_id']."'");

            mysqli_query($conn, "INSERT INTO tracking_logs
            (module_name, reference_id, reference_code, tracking_status, tracking_message)
            VALUES
            ('Orders','".$row['order_id']."','ORD-".$row['order_id']."','Completed','Order delivered successfully and marked as completed.')");
        }

        $success = "Delivery status updated successfully!";

        $query = "SELECT deliveries.*, users.name AS partner_name
        FROM deliveries
        JOIN users ON deliveries.delivery_partner_id = users.user_id
        WHERE deliveries.delivery_id='$id'";

        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Update Delivery</h2>
        <p>Update delivery status and create tracking record.</p>
    </div>

    <a href="delivery_list.php" class="btn btn-secondary">Back</a>
</div>

<?php if($success != "") { ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST">

    <div class="mb-3">
        <label>Delivery</label>
        <input type="text" class="form-control"
        value="DEL-<?php echo $row['delivery_id']; ?> | ORD-<?php echo $row['order_id']; ?> | <?php echo $row['partner_name']; ?>"
        readonly>
    </div>

    <div class="mb-3">
        <label>Delivery Date</label>
        <input type="date" name="delivery_date" class="form-control"
        value="<?php echo $row['delivery_date']; ?>" required>
    </div>

    <div class="mb-4">
        <label>Delivery Status</label>
        <select name="delivery_status" class="form-select" required>
            <option value="packed" <?php if($row['delivery_status']=="packed") echo "selected"; ?>>Packed</option>
            <option value="shipped" <?php if($row['delivery_status']=="shipped") echo "selected"; ?>>Shipped</option>
            <option value="out_for_delivery" <?php if($row['delivery_status']=="out_for_delivery") echo "selected"; ?>>Out For Delivery</option>
            <option value="delivered" <?php if($row['delivery_status']=="delivered") echo "selected"; ?>>Delivered</option>
        </select>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update Delivery</button>
    <a href="delivery_list.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

<?php include '../../includes/footer.php'; ?>