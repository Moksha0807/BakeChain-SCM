```php
<?php

include '../../config/database.php';


$total_orders = mysqli_fetch_assoc(

mysqli_query($conn,

"SELECT COUNT(*) AS total FROM orders")

);

$total_orders = $total_orders['total'];



$total_sales = mysqli_fetch_assoc(

mysqli_query($conn,

"SELECT SUM(total_amount) AS total FROM orders")

);

$total_sales = $total_sales['total'];



$recent_orders = mysqli_query($conn,

"SELECT orders.*,
users.name

FROM orders

JOIN users

ON orders.customer_id=users.user_id

ORDER BY order_id DESC

LIMIT 10");

?>


<?php include '../../includes/header.php'; ?>

<?php include '../../includes/admin_sidebar.php'; ?>


<style>

.report-grid{

display:grid;

grid-template-columns:repeat(2,1fr);

gap:25px;

margin-bottom:35px;

}


.report-card{

background:var(--burgundy);

padding:28px;

color:white;

}


.report-card p{

color:#d8c4ac;

margin-bottom:8px;

}


.report-card h2{

color:white;

font-size:40px;

font-weight:900;

margin:0;

}


.status-confirmed{

color:#176b42;

font-weight:800;

}


.status-processing{

color:#b86b00;

font-weight:800;

}


.status-completed{

color:#176b42;

font-weight:800;

}


.status-cancelled{

color:#8b1e2d;

font-weight:800;

}

</style>



<div class="content">

<div class="page-card">


<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>

Sales Report

</h2>

<p>

Monitor orders and revenue.

</p>

</div>

</div>



<div class="report-grid">

<div class="report-card">

<p>

Total Orders

</p>

<h2>

<?php echo $total_orders; ?>

</h2>

</div>



<div class="report-card">

<p>

Total Revenue

</p>

<h2>

₹<?php echo $total_sales ? $total_sales : 0; ?>

</h2>

</div>

</div>




<h4 class="mb-4">

Recent Orders

</h4>



<table class="table table-hover">

<tr>

<th>Order ID</th>

<th>Customer</th>

<th>Date</th>

<th>Amount</th>

<th>Status</th>

</tr>



<?php

while($row=mysqli_fetch_assoc($recent_orders))

{

?>

<tr>

<td>

ORD-<?php echo $row['order_id']; ?>

</td>


<td>

<?php echo $row['name']; ?>

</td>


<td>

<?php echo $row['order_date']; ?>

</td>


<td>

₹<?php echo $row['total_amount']; ?>

</td>


<td>

<?php

$status=strtolower($row['order_status']);

if($status=="confirmed")

{

echo "<span class='status-confirmed'>Confirmed</span>";

}

elseif($status=="processing")

{

echo "<span class='status-processing'>Processing</span>";

}

elseif($status=="completed")

{

echo "<span class='status-completed'>Completed</span>";

}

else

{

echo "<span class='status-cancelled'>".$row['order_status']."</span>";

}

?>

</td>

</tr>

<?php

}

?>


</table>


</div>

</div>


<?php include '../../includes/footer.php'; ?>
```
