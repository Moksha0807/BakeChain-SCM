<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$query = "SELECT orders.*, users.name AS customer_name
FROM orders
JOIN users ON orders.customer_id = users.user_id
ORDER BY orders.order_id DESC";

$result = mysqli_query($conn, $query);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.clean-action{
    text-decoration:none;
    font-weight:800;
    color:var(--burgundy);
    margin-right:18px;
}
.clean-action:hover{ color:black; }
.clean-delete{ color:#8b1e2d; }
.status-text{ font-weight:800; }
.status-confirmed{ color:#176b42; }
.status-processing{ color:#b86b00; }
.status-completed{ color:#176b42; }
.status-cancelled{ color:#8b1e2d; }
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Order Management</h2>
        <p>Manage customer orders, order amount and processing status.</p>
    </div>

    <a href="create_order.php" class="btn btn-primary">+ Create Order</a>
</div>

<table class="table table-hover">
<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Order Date</th>
    <th>Total Amount</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td>#<?php echo $row['order_id']; ?></td>
    <td><?php echo $row['customer_name']; ?></td>
    <td><?php echo $row['order_date']; ?></td>
    <td>₹<?php echo $row['total_amount']; ?></td>
    <td>
        <?php
        $status = strtolower($row['order_status']);

        if($status == "confirmed"){
            echo "<span class='status-text status-confirmed'>Confirmed</span>";
        }
        elseif($status == "processing"){
            echo "<span class='status-text status-processing'>Processing</span>";
        }
        elseif($status == "completed"){
            echo "<span class='status-text status-completed'>Completed</span>";
        }
        else{
            echo "<span class='status-text status-cancelled'>".$row['order_status']."</span>";
        }
        ?>
    </td>
    <td>
        <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="clean-action">Details</a>
        <a href="update_order_status.php?id=<?php echo $row['order_id']; ?>" class="clean-action">Update Status</a>
    </td>
</tr>
<?php } ?>

</table>

</div>
</div>

<?php include '../../includes/footer.php'; ?>