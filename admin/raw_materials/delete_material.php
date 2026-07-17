<?php


require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT * FROM raw_materials WHERE material_id='$id'";

$result = mysqli_query($conn,$query);

$row = mysqli_fetch_assoc($result);

if(isset($_POST['delete']))
{

    $delete_query = "DELETE FROM raw_materials
                     WHERE material_id='$id'";

    if(mysqli_query($conn,$delete_query))
    {

        echo "

        <script>

        alert('Material deleted successfully.');

        window.location='view_material.php';

        </script>

        ";

    }

}

?>

<?php include '../../includes/header.php'; ?>

<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>Delete Raw Material</h2>

<p>

Are you sure you want to remove this material?

</p>

</div>

<a href="view_material.php"

class="btn btn-secondary">

Back

</a>

</div>


<div style="margin-top:30px;">

<h3>

<?php echo $row['material_name']; ?>

</h3>

<br>

<p>

<b>Unit :</b>

<?php echo strtoupper($row['unit']); ?>

</p>


<p>

<b>Current Stock :</b>

<?php echo $row['current_stock']; ?>

</p>


<p>

<b>Minimum Stock :</b>

<?php echo $row['minimum_stock']; ?>

</p>


<br>

<form method="POST">

<button

type="submit"

name="delete"

class="btn btn-danger">

Delete Material

</button>


<a

href="view_material.php"

class="btn btn-secondary">

Cancel

</a>


</form>

</div>

</div>

</div>

<?php include '../../includes/footer.php'; ?>