<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT inventory.*, products.product_name, products.flavor
FROM inventory
JOIN products ON inventory.product_id = products.product_id
WHERE inventory.inventory_id='$id'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$success = "";

if(isset($_POST['update']))
{
    $stock_quantity = $_POST['stock_quantity'];
    $location = $_POST['location'];

    if($stock_quantity == 0)
    {
        $status = "Out of Stock";
    }
    elseif($stock_quantity <= 20)
    {
        $status = "Low Stock";
    }
    else
    {
        $status = "Available";
    }

    $update = "UPDATE inventory SET
    stock_quantity='$stock_quantity',
    location='$location',
    status='$status'
    WHERE inventory_id='$id'";

    if(mysqli_query($conn, $update))
    {
        mysqli_query($conn, "UPDATE products 
        SET current_stock='$stock_quantity', status='".strtolower($status)."'
        WHERE product_id='".$row['product_id']."'");

        mysqli_query($conn, "INSERT INTO tracking_logs
        (module_name, reference_id, reference_code, tracking_status, tracking_message)
        VALUES
        ('Inventory', '$id', '".$row['product_name']."', 'Stock Updated', 'Inventory stock updated to $stock_quantity at $location.')");

        $success = "Inventory stock updated successfully!";

        $query = "SELECT inventory.*, products.product_name, products.flavor
        FROM inventory
        JOIN products ON inventory.product_id = products.product_id
        WHERE inventory.inventory_id='$id'";

        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Update Inventory Stock</h2>
        <p>Update finished product stock and automatically create tracking record.</p>
    </div>

    <a href="inventory_list.php" class="btn btn-secondary">Back</a>
</div>

<?php if($success != "") { ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST">

    <div class="mb-3">
        <label>Product</label>
        <input type="text" class="form-control"
        value="<?php echo $row['product_name']; ?> - <?php echo $row['flavor']; ?>" readonly>
    </div>

    <div class="mb-3">
        <label>Stock Quantity</label>
        <input type="number" name="stock_quantity" class="form-control"
        value="<?php echo $row['stock_quantity']; ?>" required>
    </div>

    <div class="mb-4">
        <label>Location</label>
        <input type="text" name="location" class="form-control"
        value="<?php echo $row['location']; ?>" required>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update Stock</button>
    <a href="inventory_list.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

<?php include '../../includes/footer.php'; ?>