<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$success = "";

if(isset($_POST['submit']))
{
    $product_name = $_POST['product_name'];
    $flavor = $_POST['flavor'];
    $price = $_POST['price'];
    $current_stock = $_POST['current_stock'];
    $status = $_POST['status'];

    $query = "INSERT INTO products
    (product_name, flavor, price, current_stock, batch_id, status)
    VALUES
    ('$product_name', '$flavor', '$price', '$current_stock', NULL, '$status')";

    if(mysqli_query($conn, $query))
    {
        $success = "Product added successfully!";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Add Product</h2>
        <p>Add new cookie product with flavor, price and stock details.</p>
    </div>

    <a href="view_product.php" class="btn btn-secondary">View Products</a>
</div>

<?php if($success != "") { ?>
    <div class="alert alert-success"><?php echo $success; ?></div>

    <a href="add_product.php" class="btn btn-primary">Add Another</a>
    <a href="view_product.php" class="btn btn-secondary">Go to Product List</a>
<?php } else { ?>

<form method="POST">

    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control" placeholder="Ex: Chocolate Chip Cookie" required>
    </div>

    <div class="mb-3">
        <label>Flavor</label>
        <input type="text" name="flavor" class="form-control" placeholder="Ex: Chocolate Chip" required>
    </div>

    <div class="mb-3">
        <label>Price</label>
        <input type="number" step="0.01" name="price" class="form-control" placeholder="Ex: 120" required>
    </div>

    <div class="mb-3">
        <label>Current Stock</label>
        <input type="number" name="current_stock" class="form-control" placeholder="Ex: 150" required>
    </div>

    <div class="mb-4">
        <label>Status</label>
        <select name="status" class="form-select" required>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
        </select>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Add Product</button>
    <a href="view_product.php" class="btn btn-secondary">Back</a>

</form>

<?php } ?>

</div>
</div>

<?php include '../../includes/footer.php'; ?>