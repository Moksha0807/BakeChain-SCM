<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT * FROM products WHERE product_id='$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$message = "";

if(isset($_POST['delete']))
{
    $check_query = "SELECT COUNT(*) AS total FROM production_batches WHERE product_id='$id'";
    $check_result = mysqli_query($conn, $check_query);
    $check = mysqli_fetch_assoc($check_result);

    if($check['total'] > 0)
    {
        mysqli_query($conn, "UPDATE products SET status='unavailable' WHERE product_id='$id'");
        $message = "This product is linked with production batches, so it has been marked as Unavailable instead of deleting.";
    }
    else
    {
        mysqli_query($conn, "DELETE FROM products WHERE product_id='$id'");

        echo "<script>
            alert('Product deleted successfully.');
            window.location='view_product.php';
        </script>";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Delete Product</h2>
        <p>Confirm product removal from the product catalog.</p>
    </div>

    <a href="view_product.php" class="btn btn-secondary">Back</a>
</div>

<?php if($message != "") { ?>

    <div class="alert alert-warning">
        <?php echo $message; ?>
    </div>

    <a href="view_product.php" class="btn btn-secondary">Back to Product List</a>

<?php } else { ?>

    <div class="alert alert-danger">
        Are you sure you want to delete product:
        <strong><?php echo $row['product_name']; ?></strong>?
    </div>

    <p><b>Flavor:</b> <?php echo $row['flavor']; ?></p>
    <p><b>Price:</b> ₹<?php echo $row['price']; ?></p>
    <p><b>Current Stock:</b> <?php echo $row['current_stock']; ?></p>

    <form method="POST">
        <button type="submit" name="delete" class="btn btn-danger">Delete Product</button>
        <a href="view_product.php" class="btn btn-secondary">Cancel</a>
    </form>

<?php } ?>

</div>
</div>

<?php include '../../includes/footer.php'; ?>