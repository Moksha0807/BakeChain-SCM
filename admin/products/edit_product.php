<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT * FROM products WHERE product_id='$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$success = "";

if(isset($_POST['update']))
{
    $product_name = $_POST['product_name'];
    $flavor = $_POST['flavor'];
    $price = $_POST['price'];
    $current_stock = $_POST['current_stock'];
    $status = $_POST['status'];

    $update = "UPDATE products SET
    product_name='$product_name',
    flavor='$flavor',
    price='$price',
    current_stock='$current_stock',
    status='$status'
    WHERE product_id='$id'";

    if(mysqli_query($conn, $update))
    {
        $success = "Product updated successfully!";

        $query = "SELECT * FROM products WHERE product_id='$id'";
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
        <h2>Edit Product</h2>
        <p>Update cookie product information.</p>
    </div>

    <a href="view_product.php" class="btn btn-secondary">Back</a>
</div>

<?php if($success != "") { ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST">

    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control"
        value="<?php echo $row['product_name']; ?>" required>
    </div>

    <div class="mb-3">
        <label>Flavor</label>
        <input type="text" name="flavor" class="form-control"
        value="<?php echo $row['flavor']; ?>" required>
    </div>

    <div class="mb-3">
        <label>Price</label>
        <input type="number" step="0.01" name="price" class="form-control"
        value="<?php echo $row['price']; ?>" required>
    </div>
    
    <div class="mb-3">
        <label>Current Stock</label>
        <input type="number" name="current_stock" class="form-control"
        value="<?php echo $row['current_stock']; ?>" required>
    </div>

    <div class="mb-4">
        <label>Status</label>
        <select name="status" class="form-select" required>
            <option value="available" <?php if($row['status']=="available") echo "selected"; ?>>Available</option>
            <option value="unavailable" <?php if($row['status']=="unavailable") echo "selected"; ?>>Unavailable</option>
        </select>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Update Product</button>
    <a href="view_product.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

<?php include '../../includes/footer.php'; ?>