<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$materials = mysqli_query($conn, "SELECT * FROM raw_materials ORDER BY material_name ASC");

$suggestions = null;
$selected_material = "";

if(isset($_POST['suggest']))
{
    $material_id = $_POST['material_id'];
    $selected_material = $material_id;

    $suggestions = mysqli_query($conn, "
        SELECT 
            supplier_materials.*,
            suppliers.supplier_name,
            suppliers.contact_person,
            suppliers.phone,
            suppliers.email,
            suppliers.address,
            suppliers.status,
            raw_materials.material_name,
            raw_materials.unit
        FROM supplier_materials
        JOIN suppliers ON supplier_materials.supplier_id = suppliers.supplier_id
        JOIN raw_materials ON supplier_materials.material_id = raw_materials.material_id
        WHERE supplier_materials.material_id = '$material_id'
        AND (suppliers.status = 'active' OR suppliers.status = 'Active')
        ORDER BY supplier_materials.price_per_unit ASC, supplier_materials.delivery_days ASC
    ");
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.suggestion-card{
    background:#fff8f1;
    border:1px solid var(--sand);
    padding:24px;
    margin-bottom:20px;
}

.best-card{
    border-left:7px solid var(--burgundy);
}

.price-text{
    color:var(--burgundy);
    font-weight:900;
}

.fast-text{
    color:#176b42;
    font-weight:900;
}

.clean-label{
    color:var(--burgundy);
    font-weight:900;
}
</style>

<div class="content">

<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>
    <h2>Smart Alternate Supplier Suggestion</h2>
    <p>Select a raw material and get the best available supplier based on price and delivery speed.</p>
</div>

<a href="../dashboard.php" class="btn btn-secondary">Back</a>

</div>

<form method="POST">

    <div class="mb-4">
        <label>Select Raw Material</label>

        <select name="material_id" class="form-select" required>
            <option value="">Select Raw Material</option>

            <?php while($m = mysqli_fetch_assoc($materials)) { ?>
                <option value="<?php echo $m['material_id']; ?>"
                    <?php if($selected_material == $m['material_id']) echo "selected"; ?>>
                    <?php echo $m['material_name']; ?>
                </option>
            <?php } ?>

        </select>
    </div>

    <button type="submit" name="suggest" class="btn btn-primary">
        Suggest Supplier
    </button>

</form>

<?php if($suggestions !== null) { ?>

<br><br>

<h4>Suggested Suppliers</h4>

<?php if(mysqli_num_rows($suggestions) > 0) { ?>

    <?php 
    $rank = 1;
    while($row = mysqli_fetch_assoc($suggestions)) { 
    ?>

        <div class="suggestion-card <?php if($rank == 1) echo 'best-card'; ?>">

            <?php if($rank == 1) { ?>
                <h5>Best Recommended Supplier</h5>
            <?php } else { ?>
                <h5>Alternative Supplier <?php echo $rank; ?></h5>
            <?php } ?>

            <p>
                <span class="clean-label">Supplier:</span>
                <?php echo $row['supplier_name']; ?>
            </p>

            <p>
                <span class="clean-label">Material:</span>
                <?php echo $row['material_name']; ?>
            </p>

            <p>
                <span class="clean-label">Price:</span>
                <span class="price-text">₹<?php echo $row['price_per_unit']; ?> / <?php echo strtoupper($row['unit']); ?></span>
            </p>

            <p>
                <span class="clean-label">Delivery Time:</span>
                <span class="fast-text"><?php echo $row['delivery_days']; ?> day(s)</span>
            </p>

            <p>
                <span class="clean-label">Contact Person:</span>
                <?php echo $row['contact_person']; ?>
            </p>

            <p>
                <span class="clean-label">Phone:</span>
                <?php echo $row['phone']; ?>
            </p>

            <p>
                <span class="clean-label">Email:</span>
                <?php echo $row['email']; ?>
            </p>

            <p>
                <span class="clean-label">Address:</span>
                <?php echo $row['address']; ?>
            </p>

        </div>

    <?php 
    $rank++;
    } 
    ?>

<?php } else { ?>

    <div class="suggestion-card">
        <h5>No Supplier Found</h5>
        <p>No active supplier is available for this raw material.</p>
    </div>

<?php } ?>

<?php } ?>

</div>

</div>

<?php include '../../includes/footer.php'; ?>