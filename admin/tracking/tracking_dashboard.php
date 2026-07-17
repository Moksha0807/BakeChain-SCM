<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$total_logs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tracking_logs"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tracking_logs WHERE module_name='Orders'"))['total'];
$total_deliveries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tracking_logs WHERE module_name='Deliveries'"))['total'];
$total_production = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tracking_logs WHERE module_name='Production'"))['total'];

$recent_logs = mysqli_query($conn, "SELECT * FROM tracking_logs ORDER BY tracking_id DESC LIMIT 10");
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.track-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-bottom:30px;
}

.track-card{
    background:var(--burgundy);
    padding:24px;
    border-radius:var(--radius);
    border:1px solid rgba(255,255,255,0.1);
    box-shadow:var(--shadow-sm);
    transition:var(--transition-spring);
}

.track-card:hover{
    transform:translateY(-4px);
    box-shadow:var(--shadow-md);
}

.track-card p{
    color:rgba(255, 255, 255, 0.72) !important;
    font-size:12.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.8px;
    margin:0;
}

.track-card h2{
    color:#FFFFFF !important;
    font-size:32px;
    font-weight:900;
    margin-top:8px;
    margin-bottom:0;
}

.location-text{
    color:#176b42;
    font-weight:800;
}

.clean-action{
    text-decoration:none;
    font-weight:800;
    color:var(--burgundy);
    margin-right:18px;
}
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Tracking Dashboard</h2>
        <p>Monitor current location, status and expected movement across the supply chain.</p>
    </div>

    <a href="add_tracking.php" class="btn btn-primary">+ Add Tracking</a>
</div>

<div class="track-grid">
    <div class="track-card">
        <p>Total Logs</p>
        <h2><?php echo $total_logs; ?></h2>
    </div>

    <div class="track-card">
        <p>Order Tracking</p>
        <h2><?php echo $total_orders; ?></h2>
    </div>

    <div class="track-card">
        <p>Delivery Tracking</p>
        <h2><?php echo $total_deliveries; ?></h2>
    </div>

    <div class="track-card">
        <p>Production Tracking</p>
        <h2><?php echo $total_production; ?></h2>
    </div>
</div>

<h4>Recent Tracking Activity</h4>

<table class="table table-hover">
<tr>
    <th>ID</th>
    <th>Module</th>
    <th>Reference</th>
    <th>Status</th>
    <th>Location</th>
    <th>Expected Date</th>
    <th>Message</th>
    <th>Created At</th>
</tr>

<?php while($row = mysqli_fetch_assoc($recent_logs)) { ?>
<tr>
    <td><?php echo $row['tracking_id']; ?></td>
    <td><?php echo $row['module_name']; ?></td>
    <td><?php echo $row['reference_code']; ?></td>
    <td><?php echo $row['tracking_status']; ?></td>
    <td><span class="location-text"><?php echo $row['current_location']; ?></span></td>
    <td><?php echo $row['expected_date']; ?></td>
    <td><?php echo $row['tracking_message']; ?></td>
    <td><?php echo $row['created_at']; ?></td>
</tr>
<?php } ?>

</table>

<a href="view_tracking.php" class="clean-action">View All Tracking Logs</a>

</div>
</div>

<?php include '../../includes/footer.php'; ?>