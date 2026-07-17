<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$total_suppliers_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM suppliers")
);
$total_suppliers = $total_suppliers_row['total'];

$active_suppliers_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM suppliers WHERE status='Active' OR status='active'")
);
$active_suppliers = $active_suppliers_row['total'];

$inactive_suppliers_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM suppliers WHERE status='Inactive' OR status='inactive'")
);
$inactive_suppliers = $inactive_suppliers_row['total'];

$suppliers = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY supplier_id DESC");
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.report-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
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
    font-size:36px;
    font-weight:900;
    margin:0;
}

.status-active{
    color:#176b42;
    font-weight:800;
}

.status-inactive{
    color:#8b1e2d;
    font-weight:800;
}
</style>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Supplier Report</h2>
        <p>Monitor supplier count, activity status and supplier contact details.</p>
    </div>
</div>

<div class="report-grid">

    <div class="report-card">
        <p>Total Suppliers</p>
        <h2><?php echo $total_suppliers; ?></h2>
    </div>

    <div class="report-card">
        <p>Active Suppliers</p>
        <h2><?php echo $active_suppliers; ?></h2>
    </div>

    <div class="report-card">
        <p>Inactive Suppliers</p>
        <h2><?php echo $inactive_suppliers; ?></h2>
    </div>

</div>

<h4 class="mb-4">Supplier Details</h4>

<table class="table table-hover">

<tr>
    <th>Supplier ID</th>
    <th>Supplier Name</th>
    <th>Contact Person</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Address</th>
    <th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($suppliers)) { ?>

<tr>

<td>SUP-<?php echo $row['supplier_id']; ?></td>

<td><?php echo $row['supplier_name']; ?></td>

<td><?php echo $row['contact_person']; ?></td>

<td><?php echo $row['phone']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['address']; ?></td>

<td>
<?php
if(strtolower($row['status']) == "active")
{
    echo "<span class='status-active'>Active</span>";
}
else
{
    echo "<span class='status-inactive'>Inactive</span>";
}
?>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<?php include '../../includes/footer.php'; ?>