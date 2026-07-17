<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$query = "SELECT production_batches.*, products.product_name, products.flavor
FROM production_batches
JOIN products ON production_batches.product_id = products.product_id
ORDER BY production_batches.batch_id DESC";

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
.clean-action:hover{ color:black; }
.status-completed{
    color:#176b42;
    font-weight:800;
}
.status-progress{
    color:#b86b00;
    font-weight:800;
}
</style>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Production Management</h2>
        <p>Track product batches, production dates, expiry dates and QR traceability.</p>
    </div>

    <a href="create_batch.php" class="btn btn-primary">+ Create New Batch</a>
</div>

<table class="table table-hover">

<tr>

<th>Batch Code</th>

<th>Product</th>

<th>Flavor</th>

<th>Production Date</th>

<th>Expiry Date</th>

<th>Quantity</th>

<th>Status</th>

<th>Action</th>

</tr>


<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>

<td>

<?php echo $row['batch_code']; ?>

</td>


<td>

<?php echo $row['product_name']; ?>

</td>


<td>

<?php echo $row['flavor']; ?>

</td>


<td>

<?php echo $row['production_date']; ?>

</td>


<td>

<?php echo $row['expiry_date']; ?>

</td>


<td>

<?php echo $row['quantity_produced']; ?>

</td>


<td>

<?php

if(strtolower($row['production_status'])=="completed")
{

echo "<span class='status-completed'> Completed</span>";

}

elseif(strtolower($row['production_status'])=="in production")
{

echo "<span class='status-progress'>In Production</span>";

}

else

{

echo "<span class='status-progress'> ".$row['production_status']."</span>";

}

?>

</td>


<td>

<a

href="batch_details.php?id=<?php echo $row['batch_id']; ?>"

class="clean-action">

View Details

</a>

<a

href="update_batch_status.php?id=<?php echo $row['batch_id']; ?>"

class="clean-action">

Update Status

</a>

</td>

</tr>

<?php } ?>


</table>
</div>
</div>

<?php include '../../includes/footer.php'; ?>