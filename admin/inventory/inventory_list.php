<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$query = "SELECT inventory.*, products.product_name, products.flavor, products.price
FROM inventory
JOIN products ON inventory.product_id = products.product_id
ORDER BY inventory.inventory_id DESC";

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

.status-available{
    color:#176b42;
    font-weight:800;
}

.status-low{
    color:#b86b00;
    font-weight:800;
}

.status-out{
    color:#8b1e2d;
    font-weight:800;
}
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Inventory Management</h2>
        <p>Track finished cookie product stock and warehouse availability.</p>
    </div>
</div>

<table class="table table-hover">
<tr>
    <th>ID</th>
    <th>Product</th>
    <th>Flavor</th>
    <th>Price</th>
    <th>Stock Quantity</th>
    <th>Location</th>
    <th>Status</th>
    <th>Last Updated</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['inventory_id']; ?></td>
    <td><?php echo $row['product_name']; ?></td>
    <td><?php echo $row['flavor']; ?></td>
    <td>₹<?php echo $row['price']; ?></td>
    <td><?php echo $row['stock_quantity']; ?></td>
    <td><?php echo $row['location']; ?></td>
    <td>
        <?php
        if($row['stock_quantity'] == 0)
        {
            echo "<span class='status-out'>Out of Stock</span>";
        }
        elseif($row['stock_quantity'] <= 20)
        {
            echo "<span class='status-low'>Low Stock</span>";
        }
        else
        {
            echo "<span class='status-available'>Available</span>";
        }
        ?>
    </td>
    <td><?php echo $row['last_updated']; ?></td>
    <td>
        <a href="stock_update.php?id=<?php echo $row['inventory_id']; ?>" class="clean-action">
            Update Stock
        </a>
    </td>
</tr>
<?php } ?>

</table>

</div>
</div>

<?php include '../../includes/footer.php'; ?>