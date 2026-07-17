<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$success="";

if(isset($_POST['submit']))
{
    $material_name=$_POST['material_name'];
    $unit=$_POST['unit'];
    $current_stock=$_POST['current_stock'];
    $minimum_stock=$_POST['minimum_stock'];

    $query="INSERT INTO raw_materials
    (material_name,unit,current_stock,minimum_stock)

    VALUES

    ('$material_name',
    '$unit',
    '$current_stock',
    '$minimum_stock')";

    if(mysqli_query($conn,$query))
    {
        $success="Material added successfully!";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>Add Raw Material</h2>

<p>
Add bakery ingredients and stock details.
</p>

</div>

<a href="view_material.php" class="btn btn-secondary">

View Materials

</a>

</div>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php } ?>


<form method="POST">

<div class="mb-3">

<label>Material Name</label>

<input
type="text"
name="material_name"
class="form-control"
placeholder="Ex : Flour"
required>

</div>


<div class="mb-3">

<label>Unit</label>

<select
name="unit"
class="form-select"
required>

<option value="">Select Unit</option>

<option value="kg">Kg</option>

<option value="gm">Gram</option>

<option value="liter">Liter</option>

<option value="packet">Packet</option>

<option value="piece">Piece</option>

</select>

</div>


<div class="mb-3">

<label>Current Stock</label>

<input
type="number"
step="0.01"
name="current_stock"
class="form-control"
placeholder="Ex : 500"
required>

</div>


<div class="mb-4">

<label>Minimum Stock</label>

<input
type="number"
step="0.01"
name="minimum_stock"
class="form-control"
placeholder="Ex : 100"
required>

</div>


<button
type="submit"
name="submit"
class="btn btn-primary">

Add Material

</button>


<a
href="view_material.php"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

<?php include '../../includes/footer.php'; ?>