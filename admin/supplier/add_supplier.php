<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$success = "";

if(isset($_POST['submit']))
{
    $supplier_name = $_POST['supplier_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    $query = "INSERT INTO suppliers 
    (supplier_name, contact_person, phone, email, address, status)
    VALUES 
    ('$supplier_name','$contact_person','$phone','$email','$address','$status')";

    if(mysqli_query($conn, $query))
    {
        $success = "Supplier added successfully!";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">

    <div class="page-card">

        <h2>Add Supplier</h2>
        <p>Create a new supplier record for bakery raw material sourcing.</p>

        <?php if($success!="") { ?>
            <div class="alert alert-success mt-3">
                <?php echo $success; ?>
            </div>

            <a href="add_supplier.php" class="btn btn-primary">Add Another</a>
            <a href="view_supplier.php" class="btn btn-secondary">Go to Supplier List</a>

        <?php } else { ?>

        <form method="POST" class="mt-4">

            <input type="text" name="supplier_name" class="form-control mb-3" placeholder="Supplier Name" required>

            <input type="text" name="contact_person" class="form-control mb-3" placeholder="Contact Person" required>

            <input type="text" name="phone" class="form-control mb-3" placeholder="Phone Number" required>

            <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>

            <textarea name="address" class="form-control mb-3" placeholder="Address" required></textarea>

            <select name="status" class="form-select mb-3" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <button type="submit" name="submit" class="btn btn-primary">Add Supplier</button>
            <a href="view_supplier.php" class="btn btn-secondary">View Suppliers</a>

        </form>

        <?php } ?>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>