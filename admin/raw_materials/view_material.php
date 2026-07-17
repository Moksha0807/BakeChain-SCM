<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$query = "SELECT * FROM raw_materials ORDER BY material_id DESC";
$result = mysqli_query($conn,$query);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>

.status{
    font-weight:800;
}

.available{
    color:#176b42;
}

.low{
    color:#b86b00;
}

.out{
    color:#8b1e2d;
}

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

.clean-delete:hover{
    color:#4D0E13;
}

.table{
    background:transparent;
}

.table th{
    color:var(--burgundy);
    font-weight:900;
}

.table td{
    vertical-align:middle;
}

</style>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>Raw Materials</h2>

<p>
Manage bakery ingredients and monitor stock availability.
</p>

</div>

<a href="add_material.php" class="btn btn-primary">

+ Add Material

</a>

</div>


<table class="table table-hover">

<tr>

<th>ID</th>

<th>Material</th>

<th>Unit</th>

<th>Current Stock</th>

<th>Minimum Stock</th>

<th>Status</th>

<th>Action</th>

</tr>


<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td>

<?php echo $row['material_id']; ?>

</td>

<td>

<?php echo $row['material_name']; ?>

</td>

<td>

<?php echo strtoupper($row['unit']); ?>

</td>

<td>

<?php echo $row['current_stock']; ?>

</td>

<td>

<?php echo $row['minimum_stock']; ?>

</td>

<td>

<?php

$current=$row['current_stock'];

$minimum=$row['minimum_stock'];

if($current==0)
{
echo "<span class='status out'>Out of Stock</span>";
}

elseif($current<=$minimum)
{
echo "<span class='status low'>Low Stock</span>";
}

else
{
echo "<span class='status available'>Available</span>";
}

?>

</td>

<td>

<a href="edit_material.php?id=<?php echo $row['material_id']; ?>"
class="clean-action">

Edit

</a>


<a href="delete_material.php?id=<?php echo $row['material_id']; ?>"
class="clean-action clean-delete">

Delete

</a>

</td>

</tr>

<?php } ?>


</table>

</div>

</div>

<?php include '../../includes/footer.php'; ?>