<?php

require_once '../../includes/admin_auth.php';
include '../../config/database.php';

$customers = mysqli_query($conn, "SELECT * FROM users WHERE role='customer'");
$products = mysqli_query($conn, "SELECT * FROM products WHERE status='available'");

$success = "";

if(isset($_POST['submit']))
{
    $customer_id = $_POST['customer_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $product_query = "SELECT * FROM products WHERE product_id='$product_id'";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);

    $price = $product['price'];
    $total_amount = $price * $quantity;
    $order_date = date("Y-m-d");
    $order_status = "confirmed";

    $order_query = "INSERT INTO orders
    (customer_id, order_date, total_amount, order_status)
    VALUES
    ('$customer_id','$order_date','$total_amount','$order_status')";

    if(mysqli_query($conn, $order_query))
    {
        $order_id = mysqli_insert_id($conn);

        mysqli_query($conn, "INSERT INTO order_items
        (order_id, product_id, quantity, price)
        VALUES
        ('$order_id','$product_id','$quantity','$price')");

        mysqli_query($conn, "UPDATE products
        SET current_stock = current_stock - $quantity
        WHERE product_id='$product_id'");

        mysqli_query($conn, "INSERT INTO tracking_logs
        (module_name, reference_id, reference_code, tracking_status, tracking_message)
        VALUES
        ('Orders','$order_id','ORD-$order_id','Order Confirmed','Customer order has been confirmed successfully.')");

        $success = "Order created successfully!";
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<div class="content">
<div class="page-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Create Order</h2>
        <p>Create customer order and automatically add tracking record.</p>
    </div>

    <a href="view_orders.php" class="btn btn-secondary">View Orders</a>
</div>

<?php if($success != "") { ?>
    <div class="alert alert-success"><?php echo $success; ?></div>

    <a href="create_order.php" class="btn btn-primary">Create Another</a>
    <a href="view_orders.php" class="btn btn-secondary">Go to Order List</a>
<?php } else { ?>

<form method="POST">

    <div class="mb-3">
        <label>Select Customer</label>
        <select name="customer_id" class="form-select" required>
            <option value="">Select Customer</option>
            <?php while($c = mysqli_fetch_assoc($customers)) { ?>
                <option value="<?php echo $c['user_id']; ?>">
                    <?php echo $c['name']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Select Product</label>
        <select name="product_id" class="form-select" required>
            <option value="">Select Product</option>
            <?php while($p = mysqli_fetch_assoc($products)) { ?>
                <option value="<?php echo $p['product_id']; ?>">
                    <?php echo $p['product_name']; ?> - <?php echo $p['flavor']; ?> - ₹<?php echo $p['price']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-4">
        <label>Quantity</label>
        <input type="number" name="quantity" class="form-control" placeholder="Enter Quantity" required>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Create Order</button>
    <a href="view_orders.php" class="btn btn-secondary">Back</a>

</form>

<?php } ?>

</div>
</div>

<?php include '../../includes/footer.php'; ?>