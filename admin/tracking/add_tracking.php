<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$success = "";

if(isset($_POST['submit']))
{
    $module_name = $_POST['module_name'];
    $reference_id = $_POST['reference_id'];
    $reference_code = $_POST['reference_code'];
    $tracking_status = $_POST['tracking_status'];
    $tracking_message = $_POST['tracking_message'];
    $current_location = $_POST['current_location'];
    $expected_date = $_POST['expected_date'];

    $query = "INSERT INTO tracking_logs
    (module_name, reference_id, reference_code, tracking_status, tracking_message, current_location, expected_date)
    VALUES
    ('$module_name','$reference_id','$reference_code','$tracking_status','$tracking_message','$current_location','$expected_date')";

    if(mysqli_query($conn, $query))
    {
        $success = "Tracking added successfully!";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Add Tracking</h2>
        <p>Add latest location and tracking status.</p>
    </div>

    <a href="current_location.php" class="btn btn-secondary">Current Location</a>
</div>

<?php if($success != "") { ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST">

    <div class="mb-3">
        <label>Module Name</label>
        <select name="module_name" class="form-select" required>
            <option value="">Select Module</option>
            <option value="Raw Materials">Raw Materials</option>
            <option value="Production">Production</option>
            <option value="Inventory">Inventory</option>
            <option value="Orders">Orders</option>
            <option value="Deliveries">Deliveries</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Reference ID</label>
        <input type="number" name="reference_id" class="form-control" placeholder="Example: 1" required>
    </div>

    <div class="mb-3">
        <label>Reference Code</label>
        <input type="text" name="reference_code" class="form-control" placeholder="Example: CHC-BATCH-001 / ORD-001 / DEL-001" required>
    </div>

    <div class="mb-3">
        <label>Tracking Status</label>
        <select name="tracking_status" class="form-select" required>
            <option value="">Select Status</option>
            <option value="Raw Material Received">Raw Material Received</option>
            <option value="Stored in Warehouse">Stored in Warehouse</option>
            <option value="Batch Created">Batch Created</option>
            <option value="In Production">In Production</option>
            <option value="Completed">Completed</option>
            <option value="Stock Updated">Stock Updated</option>
            <option value="Order Confirmed">Order Confirmed</option>
            <option value="Processing">Processing</option>
            <option value="Packed">Packed</option>
            <option value="Shipped">Shipped</option>
            <option value="In Transit">In Transit</option>
            <option value="Delivered">Delivered</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Tracking Message</label>
        <textarea name="tracking_message" class="form-control" rows="3" placeholder="Write tracking message" required></textarea>
    </div>

    <div class="mb-3">
        <label>Current Location</label>
        <select name="current_location" class="form-select" required>
            <option value="">Select Location</option>
            <option value="Supplier Location">Supplier Location</option>
            <option value="Factory Warehouse">Factory Warehouse</option>
            <option value="Production Unit">Production Unit</option>
            <option value="Packaging Unit">Packaging Unit</option>
            <option value="Main Warehouse">Main Warehouse</option>
            <option value="Ahmedabad Delivery Hub">Ahmedabad Delivery Hub</option>
            <option value="Delivery Van">Delivery Van</option>
            <option value="Customer Location">Customer Location</option>
        </select>
    </div>

    <div class="mb-4">
        <label>Expected Date</label>
        <input type="date" name="expected_date" class="form-control" required>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Add Tracking</button>
    <a href="tracking_dashboard.php" class="btn btn-secondary">Back</a>

</form>

</div>
</div>

<?php include '../../includes/footer.php'; ?>