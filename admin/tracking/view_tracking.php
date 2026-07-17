<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$where = "";

if(isset($_GET['search']) && $_GET['search'] != "")
{
    $search = $_GET['search'];

    $where = "WHERE 
    module_name LIKE '%$search%' OR 
    reference_code LIKE '%$search%' OR
    tracking_status LIKE '%$search%' OR
    current_location LIKE '%$search%'";
}

$query = "SELECT * FROM tracking_logs $where ORDER BY tracking_id DESC";
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

.location-text{
    color:#176b42;
    font-weight:800;
}
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Tracking Logs</h2>
        <p>Search and view complete product, order, delivery and production tracking history.</p>
    </div>

    <a href="add_tracking.php" class="btn btn-primary">+ Add Tracking</a>
</div>

<form method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-8">
            <input type="text" name="search" class="form-control"
            placeholder="Search by ORD-1, DEL-1, Production, Warehouse, Delivered"
            value="<?php if(isset($_GET['search'])) echo $_GET['search']; ?>">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>

        <div class="col-md-2">
            <a href="view_tracking.php" class="btn btn-secondary w-100">Reset</a>
        </div>
    </div>
</form>

<table class="table table-hover">
<tr>
    <th>ID</th>
    <th>Module</th>
    <th>Reference ID</th>
    <th>Reference Code</th>
    <th>Status</th>
    <th>Message</th>
    <th>Current Location</th>
    <th>Expected Date</th>
    <th>Created At</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['tracking_id']; ?></td>
    <td><?php echo $row['module_name']; ?></td>
    <td><?php echo $row['reference_id']; ?></td>
    <td><?php echo $row['reference_code']; ?></td>
    <td><?php echo $row['tracking_status']; ?></td>
    <td><?php echo $row['tracking_message']; ?></td>
    <td><span class="location-text"><?php echo $row['current_location']; ?></span></td>
    <td><?php echo $row['expected_date']; ?></td>
    <td><?php echo $row['created_at']; ?></td>
</tr>
<?php } ?>

</table>

<a href="tracking_dashboard.php" class="clean-action">Back to Tracking Dashboard</a>

</div>
</div>

<?php include '../../includes/footer.php'; ?>