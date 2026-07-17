<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$query = "SELECT * FROM products ORDER BY product_id DESC";
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

.clean-action:hover{
    color:black;
}

.clean-delete{
    color:#8b1e2d;
}

.status-available{
    color:#176b42;
    font-weight:800;
}

.status-unavailable{
    color:#8b1e2d;
    font-weight:800;
}
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Product Management</h2>
        <p>Manage cookie products, flavors, pricing and stock availability.</p>
    </div>

    <a href="add_product.php" class="btn btn-primary">+ Add Product</a>
</div>

<table class="table table-hover">
<tr>
    <th>ID</th>
    <th>Product Name</th>
    <th>Flavor</th>
    <th>Price</th>
    <th>Current Stock</th>
    <th>Batch ID</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['product_id']; ?></td>
    <td><?php echo $row['product_name']; ?></td>
    <td><?php echo $row['flavor']; ?></td>
    <td>₹<?php echo $row['price']; ?></td>
    <td><?php echo $row['current_stock']; ?></td>
    <td><?php echo $row['batch_id'] ? $row['batch_id'] : 'N/A'; ?></td>
    <td>
        <?php if(strtolower($row['status']) == "available") { ?>
            <span class="status-available">Available</span>
        <?php } else { ?>
            <span class="status-unavailable">Unavailable</span>
        <?php } ?>
    </td>
    <td>
        <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="clean-action">Edit</a>
        <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="clean-action clean-delete">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</div>
</div>

<?php include '../../includes/footer.php'; ?>