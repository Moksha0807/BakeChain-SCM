<?php


require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id=$_GET['id'];

$query="SELECT * FROM raw_materials WHERE material_id='$id'";

$result=mysqli_query($conn,$query);

$row=mysqli_fetch_assoc($result);

$success="";

if(isset($_POST['update']))
{

    $material_name=$_POST['material_name'];

    $unit=$_POST['unit'];

    $current_stock=$_POST['current_stock'];

    $minimum_stock=$_POST['minimum_stock'];

    $update="UPDATE raw_materials SET

    material_name='$material_name',

    unit='$unit',

    current_stock='$current_stock',

    minimum_stock='$minimum_stock'

    WHERE material_id='$id'";

    if(mysqli_query($conn,$update))
    {

        $success="Material updated successfully!";

        $query="SELECT * FROM raw_materials WHERE material_id='$id'";

        $result=mysqli_query($conn,$query);

        $row=mysqli_fetch_assoc($result);

    }

}

?>

<?php include '../../includes/header.php'; ?>

<?php include '../../includes/admin_sidebar.php'; ?>


<div class="content">

<div class="page-card">


<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>Edit Raw Material</h2>

<p>

Update bakery ingredient information.

</p>

</div>


<a href="view_material.php"

class="btn btn-secondary">

Back

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

value="<?php echo $row['material_name']; ?>"

required>

</div>



<div class="mb-3">

<label>Unit</label>

<select

name="unit"

class="form-select"

required>

<option value="kg"

<?php if($row['unit']=="kg") echo "selected"; ?>>

Kg

</option>

<option value="gm"

<?php if($row['unit']=="gm") echo "selected"; ?>>

Gram

</option>

<option value="liter"

<?php if($row['unit']=="liter") echo "selected"; ?>>

Liter

</option>

<option value="packet"

<?php if($row['unit']=="packet") echo "selected"; ?>>

Packet

</option>

<option value="piece"

<?php if($row['unit']=="piece") echo "selected"; ?>>

Piece

</option>

</select>

</div>



<div class="mb-3">

<label>Current Stock</label>

<input

type="number"

step="0.01"

name="current_stock"

class="form-control"

value="<?php echo $row['current_stock']; ?>"

required>

</div>



<div class="mb-4">

<label>Minimum Stock</label>

<input

type="number"

step="0.01"

name="minimum_stock"

class="form-control"

value="<?php echo $row['minimum_stock']; ?>"

required>

</div>



<button

type="submit"

name="update"

class="btn btn-primary">

Update Material

</button>


<a

href="view_material.php"

class="btn btn-secondary">

Cancel

</a>


</form>

</div>

</div>


<?php include '../../includes/footer.php'; ?>