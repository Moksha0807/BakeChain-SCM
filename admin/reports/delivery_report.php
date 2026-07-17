<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$total_deliveries_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM deliveries")
);
$total_deliveries = $total_deliveries_row['total'];

$delivered_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status='delivered'")
);
$delivered = $delivered_row['total'];

$shipped_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status='shipped'")
);
$shipped = $shipped_row['total'];

$packed_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status='packed'")
);
$packed = $packed_row['total'];

$avg_delay_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT AVG(delay_days) AS avg_delay FROM deliveries")
);
$avg_delay = $avg_delay_row['avg_delay'];

$result = mysqli_query($conn,
"SELECT deliveries.*,
orders.total_amount,
users.name AS delivery_partner_name
FROM deliveries
JOIN orders ON deliveries.order_id = orders.order_id
JOIN users ON deliveries.delivery_partner_id = users.user_id
ORDER BY deliveries.delivery_id DESC"
);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.report-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-bottom:35px;
}

.report-card{
    background:var(--burgundy);
    padding:26px;
    color:white;
}

.report-card p{
    color:#d8c4ac;
    margin-bottom:8px;
}

.report-card h2{
    color:white;
    font-size:34px;
    font-weight:900;
    margin:0;
}

.status-delivered{
    color:#176b42;
    font-weight:800;
}

.status-shipped{
    color:#176b42;
    font-weight:800;
}

.status-packed{
    color:#b86b00;
    font-weight:800;
}

.status-delay{
    color:#8b1e2d;
    font-weight:800;
}
</style>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>
    <h2>Delivery Report</h2>
    <p>Monitor delivery status, delivery partner activity and delay performance.</p>
</div>

</div>


<div class="report-grid">

<div class="report-card">
    <p>Total Deliveries</p>
    <h2><?php echo $total_deliveries; ?></h2>
</div>

<div class="report-card">
    <p>Delivered</p>
    <h2><?php echo $delivered; ?></h2>
</div>

<div class="report-card">
    <p>Shipped</p>
    <h2><?php echo $shipped; ?></h2>
</div>

<div class="report-card">
    <p>Average Delay</p>
    <h2><?php echo $avg_delay ? round($avg_delay,1) : 0; ?> Days</h2>
</div>

</div>


<h4 class="mb-4">Delivery Details</h4>

<table class="table table-hover">

<tr>
    <th>Delivery ID</th>
    <th>Order ID</th>
    <th>Partner</th>
    <th>Assigned Date</th>
    <th>Delivery Date</th>
    <th>Status</th>
    <th>Delay Days</th>
    <th>Amount</th>
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

if($status == "delivered")
{
    echo "<span class='status-delivered'>Delivered</span>";
}
elseif($status == "shipped")
{
    echo "<span class='status-shipped'>Shipped</span>";
}
elseif($status == "packed")
{
    echo "<span class='status-packed'>Packed</span>";
}
elseif($status == "out_for_delivery" || $status == "out for delivery")
{
    echo "<span class='status-shipped'>Out For Delivery</span>";
}
else
{
    echo "<span class='status-delay'>".ucwords(str_replace('_', ' ', $row['delivery_status']))."</span>";
}
?>
</td>

<td>
<?php
if($row['delay_days'] > 0)
{
    echo "<span class='status-delay'>".$row['delay_days']." day(s)</span>";
}
else
{
    echo "No Delay";
}
?>
</td>

<td>₹<?php echo $row['total_amount']; ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>

<?php include '../../includes/footer.php'; ?>