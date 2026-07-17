<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$order_query = "SELECT orders.*, users.name AS customer_name, users.email, users.phone, users.address
FROM orders
JOIN users ON orders.customer_id = users.user_id
WHERE orders.order_id='$id'";

$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

$item_query = "SELECT order_items.*, products.product_name, products.flavor
FROM order_items
JOIN products ON order_items.product_id = products.product_id
WHERE order_items.order_id='$id'";

$items = mysqli_query($conn, $item_query);

$tracking = mysqli_query($conn, "SELECT * FROM tracking_logs
WHERE module_name='Orders' AND reference_id='$id'
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
        <h2>Order Details</h2>
        <p>View customer order information, ordered products and tracking journey.</p>
    </div>

    <a href="view_orders.php" class="btn btn-secondary">Back</a>
</div>

<div class="detail-box">
    <p><b>Order ID:</b> #<?php echo $order['order_id']; ?></p>
    <p><b>Customer:</b> <?php echo $order['customer_name']; ?></p>
    <p><b>Email:</b> <?php echo $order['email']; ?></p>
    <p><b>Phone:</b> <?php echo $order['phone']; ?></p>
    <p><b>Address:</b> <?php echo $order['address']; ?></p>
    <p><b>Order Date:</b> <?php echo $order['order_date']; ?></p>
    <p><b>Total Amount:</b> ₹<?php echo $order['total_amount']; ?></p>
    <p><b>Status:</b> <?php echo $order['order_status']; ?></p>
</div>

<h4>Ordered Products</h4>

<table class="table table-hover">
<tr>
    <th>Product</th>
    <th>Flavor</th>
    <th>Quantity</th>
    <th>Price</th>
    <th>Total</th>
</tr>

<?php while($item = mysqli_fetch_assoc($items)) { ?>
<tr>
    <td><?php echo $item['product_name']; ?></td>
    <td><?php echo $item['flavor']; ?></td>
    <td><?php echo $item['quantity']; ?></td>
    <td>₹<?php echo $item['price']; ?></td>
    <td>₹<?php echo $item['quantity'] * $item['price']; ?></td>
</tr>
<?php } ?>

</table>

<h4 class="mt-4">Order Tracking</h4>

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
        Tracking will appear here once order activity is added.
    </div>
<?php } ?>

</div>
</div>

<?php include '../../includes/footer.php'; ?>