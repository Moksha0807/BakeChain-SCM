<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$query = "SELECT deliveries.*, 
orders.total_amount,
orders.order_status,
users.name AS delivery_partner_name
FROM deliveries
JOIN orders ON deliveries.order_id = orders.order_id
JOIN users ON deliveries.delivery_partner_id = users.user_id
ORDER BY deliveries.delivery_id DESC";

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
.status-delivered{ color:#176b42; font-weight:800; }
.status-shipped{ color:#176b42; font-weight:800; }
.status-packed{ color:#b86b00; font-weight:800; }
.status-delay{ color:#8b1e2d; font-weight:800; }
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Delivery Management</h2>
        <p>Track order dispatch, delivery partner and delivery status.</p>
    </div>

    <a href="assign_delivery.php" class="btn btn-primary">+ Assign Delivery</a>
</div>

<table class="table table-hover">
<tr>
    <th>Delivery ID</th>
    <th>Order ID</th>
    <th>Partner</th>
    <th>Assigned Date</th>
    <th>Delivery Date</th>
    <th>Status</th>
    <th>Delay Days</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td>DEL-<?php echo $row['delivery_id']; ?></td>
    <td>ORD-<?php echo $row['order_id']; ?></td>
    <td><?php echo $row['delivery_partner_name']; ?></td>
    <td><?php echo $row['assigned_date']; ?></td>
    <td><?php echo $row['delivery_date']; ?></td>

    <td>
        <?php
        $status = strtolower($row['delivery_status']);

        if($status == "delivered"){
            echo "<span class='status-delivered'>Delivered</span>";
        }
        elseif($status == "shipped"){
            echo "<span class='status-shipped'>Shipped</span>";
        }
        elseif($status == "packed"){
            echo "<span class='status-packed'>Packed</span>";
        }
        elseif($status == "out_for_delivery" || $status == "out for delivery"){
            echo "<span class='status-shipped'>Out For Delivery</span>";
        }
        else{
            echo "<span class='status-delay'>".ucwords(str_replace('_', ' ', $row['delivery_status']))."</span>";
        }
        ?>
    </td>

    <td>
        <?php
        if($row['delay_days'] > 0){
            echo "<span class='status-delay'>".$row['delay_days']." day(s)</span>";
        } else {
            echo "No Delay";
        }
        ?>
    </td>

    <td>
        <a href="delivery_details.php?id=<?php echo $row['delivery_id']; ?>" class="clean-action">Details</a>
        <a href="update_delivery.php?id=<?php echo $row['delivery_id']; ?>" class="clean-action">Update</a>
    </td>
</tr>
<?php } ?>

</table>

</div>
</div>

<?php include '../../includes/footer.php'; ?>