<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT deliveries.*,
orders.total_amount,
orders.order_status,
users.name,
users.phone,
users.email,
users.address
FROM deliveries
JOIN orders ON deliveries.order_id = orders.order_id
JOIN users ON deliveries.delivery_partner_id = users.user_id
WHERE deliveries.delivery_id='$id'";

$result = mysqli_query($conn,$query);
$row = mysqli_fetch_assoc($result);

$tracking = mysqli_query($conn, "SELECT * FROM tracking_logs
WHERE module_name='Deliveries' AND reference_id='$id'
ORDER BY tracking_id ASC");
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.info-card{
    background:#fff8f1;
    border:1px solid var(--sand);
    padding:25px;
    margin-bottom:25px;
}
.timeline{
    border-left:3px solid var(--burgundy);
    padding-left:20px;
    margin-left:10px;
}
.timeline-item{
    margin-bottom:25px;
}
.timeline-status{
    font-weight:800;
    color:var(--burgundy);
}
.timeline-date{
    font-size:13px;
    color:#777;
}
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Delivery Details</h2>
        <p>View delivery information and tracking timeline.</p>
    </div>

    <a href="delivery_list.php" class="btn btn-secondary">Back</a>
</div>

<div class="info-card">
    <p><b>Delivery ID:</b> DEL-<?php echo $row['delivery_id']; ?></p>
    <p><b>Order ID:</b> ORD-<?php echo $row['order_id']; ?></p>
    <p><b>Delivery Partner:</b> <?php echo $row['name']; ?></p>
    <p><b>Phone:</b> <?php echo $row['phone']; ?></p>
    <p><b>Email:</b> <?php echo $row['email']; ?></p>
    <p><b>Address:</b> <?php echo $row['address']; ?></p>
    <p><b>Assigned Date:</b> <?php echo $row['assigned_date']; ?></p>
    <p><b>Delivery Date:</b> <?php echo $row['delivery_date']; ?></p>
    <p><b>Status:</b> <?php echo ucwords(str_replace('_', ' ', $row['delivery_status'])); ?></p>
    <p><b>Delay Days:</b> <?php echo $row['delay_days']; ?></p>
    <p><b>Order Amount:</b> ₹<?php echo $row['total_amount']; ?></p>
</div>

<h4 class="mb-4">Delivery Tracking Timeline</h4>

<div class="timeline">

<?php if(mysqli_num_rows($tracking) > 0) { ?>

    <?php while($track = mysqli_fetch_assoc($tracking)) { ?>
        <div class="timeline-item">
            <div class="timeline-status"><?php echo $track['tracking_status']; ?></div>
            <div><?php echo $track['tracking_message']; ?></div>
            <div class="timeline-date"><?php echo $track['created_at']; ?></div>
        </div>
    <?php } ?>

<?php } else { ?>

    <div class="timeline-item">
        <div class="timeline-status">No Tracking Available</div>
        <div>Tracking logs will appear here.</div>
    </div>

<?php } ?>

</div>

</div>
</div>

<?php include '../../includes/footer.php'; ?>