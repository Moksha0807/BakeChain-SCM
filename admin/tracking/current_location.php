<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$query = "SELECT t1.*
FROM tracking_logs t1
INNER JOIN (
    SELECT reference_code, MAX(tracking_id) AS latest_id
    FROM tracking_logs
    GROUP BY reference_code
) t2 ON t1.tracking_id = t2.latest_id
ORDER BY t1.tracking_id DESC";

$result = mysqli_query($conn, $query);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.status-text{
    font-weight:800;
    color:#176b42;
}

.location-text{
    font-weight:800;
    color:var(--burgundy);
}

.clean-action{
    text-decoration:none;
    font-weight:800;
    color:var(--burgundy);
}
</style>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Current Location</h2>
        <p>View latest location and status of tracked batches, orders and deliveries.</p>
    </div>

    <a href="tracking_dashboard.php" class="btn btn-secondary">Tracking Dashboard</a>
</div>

<table class="table table-hover">

<tr>
    <th>Reference Code</th>
    <th>Module</th>
    <th>Current Location</th>
    <th>Current Status</th>
    <th>Expected Date</th>
    <th>Last Updated</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
    <td><?php echo $row['reference_code']; ?></td>
    <td><?php echo $row['module_name']; ?></td>
    <td><span class="location-text"><?php echo $row['current_location']; ?></span></td>
    <td><span class="status-text"><?php echo $row['tracking_status']; ?></span></td>
    <td><?php echo $row['expected_date']; ?></td>
    <td><?php echo $row['created_at']; ?></td>
</tr>

<?php } ?>

</table>

</div>

</div>

<?php include '../../includes/footer.php'; ?>