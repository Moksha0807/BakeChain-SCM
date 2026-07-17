<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$query = "SELECT * FROM suppliers ORDER BY supplier_id DESC";
$result = mysqli_query($conn, $query);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.clean-action{
    text-decoration:none;
    font-weight:900;
    margin-right:14px;
    color:var(--burgundy);
    border-bottom:2px solid transparent;
    padding-bottom:3px;
}

.clean-action:hover{
    color:var(--ink);
    border-bottom:2px solid var(--burgundy);
}

.clean-delete{
    color:#8b1e2d;
}

.clean-delete:hover{
    color:#4D0E13;
    border-bottom:2px solid #8b1e2d;
}

.clean-status{
    font-weight:900;
    color:#8b1e2d;
}

.clean-status.active{
    color:#176b42;
}
</style>

<div class="content">

    <div class="page-card">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Supplier Management</h2>
                <p>Manage supplier records, contact details and active sourcing partners.</p>
            </div>

            <a href="add_supplier.php" class="btn btn-primary">
                + Add Supplier
            </a>
        </div>

        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Supplier Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['supplier_id']; ?></td>
                <td><?php echo $row['supplier_name']; ?></td>
                <td><?php echo $row['contact_person']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['address']; ?></td>

                <td>
                    <?php if(strtolower($row['status']) == "active") { ?>
                        <span class="clean-status active">Active</span>
                    <?php } else { ?>
                        <span class="clean-status">Inactive</span>
                    <?php } ?>
                </td>

                <td>
                    <a href="edit_supplier.php?id=<?php echo $row['supplier_id']; ?>" class="clean-action">
                        Edit
                    </a>

                    <a href="delete_supplier.php?id=<?php echo $row['supplier_id']; ?>" class="clean-action clean-delete">
                        Delete
                    </a>
                </td>
            </tr>
            <?php } ?>

        </table>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>