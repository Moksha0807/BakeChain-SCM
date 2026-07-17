<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$orders = mysqli_query($conn, "SELECT * FROM orders WHERE order_status!='completed'");
$partners = mysqli_query($conn, "SELECT * FROM users WHERE role='delivery_partner' AND status='active'");

$success = "";

if(isset($_POST['submit']))
{
    $order_id = $_POST['order_id'];
    $delivery_partner_id = $_POST['delivery_partner_id'];
    $assigned_date = $_POST['assigned_date'];
    $delivery_date = $_POST['delivery_date'];
    $delivery_status = $_POST['delivery_status'];

    $date1 = new DateTime($assigned_date);
    $date2 = new DateTime($delivery_date);
    $delay_days = $date1->diff($date2)->days;

    $query = "INSERT INTO deliveries
    (order_id, delivery_partner_id, assigned_date, delivery_date, delivery_status, delay_days)
    VALUES
    ('$order_id','$delivery_partner_id','$assigned_date','$delivery_date','$delivery_status','$delay_days')";

    if(mysqli_query($conn, $query))
    {
        $delivery_id = mysqli_insert_id($conn);

        mysqli_query($conn, "INSERT INTO tracking_logs
        (module_name, reference_id, reference_code, tracking_status, tracking_message)
        VALUES
        ('Deliveries','$delivery_id','DEL-$delivery_id','Delivery Assigned','Delivery partner assigned for order ORD-$order_id.')");

        mysqli_query($conn, "UPDATE orders SET order_status='processing' WHERE order_id='$order_id'");

        mysqli_query($conn, "INSERT INTO tracking_logs
        (module_name, reference_id, reference_code, tracking_status, tracking_message)
        VALUES
        ('Orders','$order_id','ORD-$order_id','Processing','Order moved to processing after delivery assignment.')");

        $success = "Delivery assigned successfully!";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Assign Delivery</h2>
        <p>Assign delivery partner to customer order.</p>
    </div>

    <a href="delivery_list.php" class="btn btn-secondary">View Deliveries</a>
</div>

<?php if($success != "") { ?>

<div class="alert alert-success"><?php echo $success; ?></div>

<a href="assign_delivery.php" class="btn btn-primary">Assign Another</a>
<a href="delivery_list.php" class="btn btn-secondary">Go to Delivery List</a>

<?php } else { ?>

<form method="POST">

    <div class="mb-3">
        <label>Select Order</label>
        <select name="order_id" class="form-select" required>
            <option value="">Select Order</option>

            <?php while($o = mysqli_fetch_assoc($orders)) { ?>
                <option value="<?php echo $o['order_id']; ?>">
                    ORD-<?php echo $o['order_id']; ?> | ₹<?php echo $o['total_amount']; ?> | <?php echo $o['order_status']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Select Delivery Partner</label>
        <select name="delivery_partner_id" class="form-select" required>
            <option value="">Select Partner</option>

            <?php while($p = mysqli_fetch_assoc($partners)) { ?>
                <option value="<?php echo $p['user_id']; ?>">
                    <?php echo $p['name']; ?> | <?php echo $p['phone']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Assigned Date</label>
        <input type="date" name="assigned_date" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Delivery Date</label>
        <input type="date" name="delivery_date" class="form-control" required>
    </div>

    <div class="mb-4">
        <label>Delivery Status</label>
        <select name="delivery_status" class="form-select" required>
            <option value="packed">Packed</option>
            <option value="shipped">Shipped</option>
            <option value="out_for_delivery">Out For Delivery</option>
            <option value="delivered">Delivered</option>
        </select>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Assign Delivery</button>
    <a href="delivery_list.php" class="btn btn-secondary">Back</a>

</form>

<?php } ?>

</div>
</div>

<?php include '../../includes/footer.php'; ?>