<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$low_materials = mysqli_query($conn, "
SELECT * FROM raw_materials
WHERE current_stock <= minimum_stock
");

$low_inventory = mysqli_query($conn, "
SELECT inventory.*, products.product_name
FROM inventory
JOIN products ON inventory.product_id = products.product_id
WHERE inventory.stock_quantity <= 20
");

$delayed_deliveries = mysqli_query($conn, "
SELECT deliveries.*, orders.order_id
FROM deliveries
JOIN orders ON deliveries.order_id = orders.order_id
WHERE deliveries.delay_days > 0
");
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.issue-card{
    background:#fff8f1;
    border-left:6px solid var(--burgundy);
    padding:22px;
    margin-bottom:18px;
}

.issue-card h5{
    color:var(--burgundy);
    font-weight:900;
}

.issue-card p{
    margin:0;
}

.good-card{
    background:#fff8f1;
    border-left:6px solid #176b42;
    padding:22px;
}

.good-card h5{
    color:#176b42;
    font-weight:900;
}
</style>

<div class="content">
<div class="page-card">

<h2>Supply Chain Bottleneck Detection</h2>
<p>Automatically detects stock issues and delivery delays.</p>

<br>

<?php
$found = false;
?>

<?php while($row = mysqli_fetch_assoc($low_materials)) { 
$found = true;
?>
<div class="issue-card">
    <h5>Low Raw Material Stock</h5>
    <p>
        <?php echo $row['material_name']; ?> stock is low.
        Current: <b><?php echo $row['current_stock']; ?> <?php echo $row['unit']; ?></b>,
        Minimum Required: <b><?php echo $row['minimum_stock']; ?> <?php echo $row['unit']; ?></b>.
    </p>
</div>
<?php } ?>

<?php while($row = mysqli_fetch_assoc($low_inventory)) { 
$found = true;
?>
<div class="issue-card">
    <h5>Low Inventory Stock</h5>
    <p>
        <?php echo $row['product_name']; ?> has only
        <b><?php echo $row['stock_quantity']; ?></b> units available.
    </p>
</div>
<?php } ?>

<?php while($row = mysqli_fetch_assoc($delayed_deliveries)) { 
$found = true;
?>
<div class="issue-card">
    <h5>Delayed Delivery</h5>
    <p>
        Delivery <b>DEL-<?php echo $row['delivery_id']; ?></b>
        for Order <b>ORD-<?php echo $row['order_id']; ?></b>
        is delayed by <b><?php echo $row['delay_days']; ?> day(s)</b>.
    </p>
</div>
<?php } ?>

<?php if(!$found) { ?>
<div class="good-card">
    <h5>No Bottlenecks Found</h5>
    <p>All supply chain operations are running smoothly.</p>
</div>
<?php } ?>

</div>
</div>

<?php include '../../includes/footer.php'; ?>