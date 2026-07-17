<?php


require_once '../../includes/admin_auth.php';
include '../../config/database.php';


$total_items_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM inventory")
);

$total_items = $total_items_row['total'];


$total_stock_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(stock_quantity) AS total FROM inventory")
);

$total_stock = $total_stock_row['total'];


$low_stock_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM inventory WHERE stock_quantity > 0 AND stock_quantity <= 20")
);

$low_stock = $low_stock_row['total'];


$out_stock_row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM inventory WHERE stock_quantity = 0")
);

$out_stock = $out_stock_row['total'];


$result = mysqli_query($conn,
"SELECT inventory.*, products.product_name, products.flavor, products.price
FROM inventory
JOIN products
ON inventory.product_id = products.product_id
ORDER BY inventory.inventory_id DESC"
);

?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>

.report-grid{

display:grid;

grid-template-columns:repeat(4,1fr);

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

<h2>

Inventory Report

</h2>

<p>

Monitor finished product stock and warehouse availability.

</p>

</div>

</div>



<div class="report-grid">

<div class="report-card">

<p>Total Inventory Items</p>

<h2><?php echo $total_items; ?></h2>

</div>


<div class="report-card">

<p>Total Stock Quantity</p>

<h2><?php echo $total_stock ? $total_stock : 0; ?></h2>

</div>


<div class="report-card">

<p>Low Stock Items</p>

<h2><?php echo $low_stock; ?></h2>

</div>


<div class="report-card">

<p>Out of Stock Items</p>

<h2><?php echo $out_stock; ?></h2>

</div>

</div>



<h4 class="mb-4">

Inventory Details

</h4>


<table class="table table-hover">

<tr>

<th>Inventory ID</th>

<th>Product</th>

<th>Flavor</th>

<th>Price</th>

<th>Stock</th>

<th>Location</th>

<th>Status</th>

<th>Last Updated</th>

</tr>


<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>

<td>INV-<?php echo $row['inventory_id']; ?></td>

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

</tr>

<?php } ?>

</table>


</div>

</div>


<?php include '../../includes/footer.php'; ?>