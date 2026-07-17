<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';
include '../../phpqrcode/qrlib.php';

$batches = mysqli_query($conn, "
SELECT *
FROM production_batches
ORDER BY batch_id DESC
");

$success = "";
$error = "";
$batch_code = "";
$qr_web_path = "";

if(isset($_POST['generate']))
{
    $batch_id = $_POST['batch_id'];

    $batch_query = mysqli_query($conn, "
    SELECT production_batches.*, products.product_name, products.flavor, products.price
    FROM production_batches
    JOIN products ON production_batches.product_id = products.product_id
    WHERE production_batches.batch_id='$batch_id'
    ");

    $batch = mysqli_fetch_assoc($batch_query);

    if($batch)
    {
        $batch_code = $batch['batch_code'];

        $location = "Factory Warehouse";

        if(isset($batch['current_location']) && $batch['current_location'] != "")
        {
            $location = $batch['current_location'];
        }

        $qr_text =
"BackChain SCM Batch Details
---------------------------
Batch Code: ".$batch['batch_code']."
Product: ".$batch['product_name']."
Flavor: ".$batch['flavor']."
Price: Rs. ".$batch['price']."
Production Date: ".$batch['production_date']."
Expiry Date: ".$batch['expiry_date']."
Quantity Produced: ".$batch['quantity_produced']."
Current Location: ".$location."
Status: ".$batch['production_status'];

        $qr_folder = "../../assets/qr/";

        if(!is_dir($qr_folder))
        {
            mkdir($qr_folder,0777,true);
        }

        $qr_file_name = $batch_code.".png";
        $qr_file_path = $qr_folder.$qr_file_name;
        $qr_web_path = "../../assets/qr/".$qr_file_name;

        QRcode::png(
            $qr_text,
            $qr_file_path,
            QR_ECLEVEL_H,
            8,
            2
        );

        mysqli_query($conn, "
        UPDATE production_batches
        SET
        qr_code='$qr_file_name',
        qr_data='".mysqli_real_escape_string($conn,$qr_text)."'
        WHERE batch_id='$batch_id'
        ");

        $success="QR Code generated successfully!";
    }
    else
    {
        $error="Batch not found.";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<style>
.qr-box{
    background:white;
    padding:25px;
    border:1px solid var(--sand);
    display:inline-block;
    margin-top:20px;
    margin-bottom:20px;
}

.qr-box img{
    width:250px;
    height:250px;
}
</style>

<div class="content">

<div class="page-card">

<h2>QR Generator</h2>
<p>Generate real QR code with batch details in text format.</p>

<?php if($error!=""){ ?>
<div class="alert alert-danger">
<?php echo $error; ?>
</div>
<?php } ?>

<?php if($success!=""){ ?>

<div class="alert alert-success">
<?php echo $success; ?>
</div>

<h4>Generated QR Code</h4>

<div class="qr-box">
<img src="<?php echo $qr_web_path; ?>?v=<?php echo time(); ?>" alt="QR Code">
</div>

<p>
Scan this QR code to view batch details directly as text on phone.
</p>

<a href="generate_qr.php" class="btn btn-primary">Generate Another</a>

<a href="../batch_lookup/batch_result.php?batch_code=<?php echo $batch_code; ?>" class="btn btn-secondary">
View Batch Result
</a>

<?php } else { ?>

<form method="POST">

<div class="mb-4">
<label>Select Batch</label>

<select name="batch_id" class="form-select" required>
<option value="">Select Batch</option>

<?php while($b=mysqli_fetch_assoc($batches)){ ?>
<option value="<?php echo $b['batch_id']; ?>">
<?php echo $b['batch_code']; ?>
</option>
<?php } ?>

</select>
</div>

<button type="submit" name="generate" class="btn btn-primary">
Generate QR Code
</button>

</form>

<?php } ?>

</div>

</div>

<?php include '../../includes/footer.php'; ?>