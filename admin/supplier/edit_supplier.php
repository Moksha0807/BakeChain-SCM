<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$id = $_GET['id'];

$query = "SELECT * FROM suppliers WHERE supplier_id='$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$success = "";

if(isset($_POST['update']))
{
    $supplier_name = $_POST['supplier_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    $update = "UPDATE suppliers SET
    supplier_name='$supplier_name',
    contact_person='$contact_person',
    phone='$phone',
    email='$email',
    address='$address',
    status='$status'
    WHERE supplier_id='$id'";

    if(mysqli_query($conn, $update))
    {
        $success = "Supplier updated successfully!";
        $query = "SELECT * FROM suppliers WHERE supplier_id='$id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">

    <div class="page-card">

        <h2>Edit Supplier</h2>
        <p>Update supplier information and sourcing status.</p>

        <?php if($success!="") { ?>
            <div class="alert alert-success mt-3">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <form method="POST" class="mt-4">

            <input type="text" name="supplier_name" class="form-control mb-3"
            value="<?php echo $row['supplier_name']; ?>" required>

            <input type="text" name="contact_person" class="form-control mb-3"
            value="<?php echo $row['contact_person']; ?>" required>

            <input type="text" name="phone" class="form-control mb-3"
            value="<?php echo $row['phone']; ?>" required>

            <input type="email" name="email" class="form-control mb-3"
            value="<?php echo $row['email']; ?>" required>

            <textarea name="address" class="form-control mb-3" required><?php echo $row['address']; ?></textarea>

            <select name="status" class="form-select mb-3" required>
                <option value="Active" <?php if($row['status']=="Active") echo "selected"; ?>>Active</option>
                <option value="Inactive" <?php if($row['status']=="Inactive") echo "selected"; ?>>Inactive</option>
            </select>

            <button type="submit" name="update" class="btn btn-primary">Update Supplier</button>
            <a href="view_supplier.php" class="btn btn-secondary">Back to List</a>

        </form>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>