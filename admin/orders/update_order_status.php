<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT orders.*, users.name AS customer_name
FROM orders
JOIN users ON orders.customer_id = users.user_id
WHERE orders.order_id='$id'";

$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

$success = "";

if(isset($_POST['update']))
{
    $order_status = $_POST['order_status'];

    $update = "UPDATE orders SET order_status='$order_status' WHERE order_id='$id'";

    if(mysqli_query($conn, $update))
    {
        $message = "Order status updated to $order_status.";

        mysqli_query($conn, "INSERT INTO tracking_logs
        (module_name, reference_id, reference_code, tracking_status, tracking_message)
        VALUES
        ('Orders','$id','ORD-$id','$order_status','$message')");

        $success = "Order status updated successfully!";

        $query = "SELECT orders.*, users.name AS customer_name
        FROM orders
        JOIN users ON orders.customer_id = users.user_id
        WHERE orders.order_id='$id'";

        $result = mysqli_query($conn, $query);
        $order = mysqli_fetch_assoc($result);
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Update Order Status</h2>
        <p>Update customer order progress and automatically create tracking record.</p>
    </div>

    <a href="view_orders.php" class="btn btn-secondary">Back</a>
</div>

<?php if($success != "") { ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST">

    <div class="mb-3">
        <label>Order</label>
        <input type="text" class="form-control"
        value="ORD-<?php echo $order['order_id']; ?> | <?php echo $order['customer_name']; ?>"
        readonly>
    </div>

    <div class="mb-4">
        <label>Order Status</label>
        <select name="order_status" class="form-select" required>
            <option value="confirmed" <?php if($order['order_status']=="confirmed") echo "selected"; ?>>Confirmed</option>
            <option value="processing" <?php if($order['order_status']=="processing") echo "selected"; ?>>Processing</option>
            <option value="packed" <?php if($order['order_status']=="packed") echo "selected"; ?>>Packed</option>
            <option value="completed" <?php if($order['order_status']=="completed") echo "selected"; ?>>Completed</option>
            <option value="cancelled" <?php if($order['order_status']=="cancelled") echo "selected"; ?>>Cancelled</option>
        </select>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update Status</button>
    <a href="view_orders.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

<?php include '../../includes/footer.php'; ?>