<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$supplier_query = "SELECT * FROM suppliers WHERE supplier_id='$id'";
$supplier_result = mysqli_query($conn, $supplier_query);
$supplier = mysqli_fetch_assoc($supplier_result);

$message = "";

if(isset($_POST['delete']))
{
    $check_query = "SELECT COUNT(*) AS total FROM supplier_materials WHERE supplier_id='$id'";
    $check_result = mysqli_query($conn, $check_query);
    $check = mysqli_fetch_assoc($check_result);

    if($check['total'] > 0)
    {
        mysqli_query($conn, "UPDATE suppliers SET status='Inactive' WHERE supplier_id='$id'");
        $message = "This supplier is linked with raw materials, so it has been marked as Inactive instead of deleting.";
    }
    else
    {
        mysqli_query($conn, "DELETE FROM suppliers WHERE supplier_id='$id'");
        echo "<script>
            alert('Supplier deleted successfully!');
            window.location.href='view_supplier.php';
        </script>";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">

    <div class="page-card">

        <h2>Delete Supplier</h2>
        <p>Confirm supplier removal from the system.</p>

        <?php if($message!="") { ?>
            <div class="alert alert-warning mt-3">
                <?php echo $message; ?>
            </div>

            <a href="view_supplier.php" class="btn btn-secondary">Back to Supplier List</a>

        <?php } else { ?>

            <div class="alert alert-danger mt-4">
                Are you sure you want to delete supplier:
                <strong><?php echo $supplier['supplier_name']; ?></strong>?
            </div>

            <form method="POST">
                <button type="submit" name="delete" class="btn btn-danger">Yes, Delete</button>
                <a href="view_supplier.php" class="btn btn-secondary">Cancel</a>
            </form>

        <?php } ?>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>