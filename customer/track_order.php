<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id      = $_SESSION['user_id'];
$selected_order_id = "";
$order_data        = null;
$delivery_data     = null;
$error             = "";

$orders = mysqli_query($conn, "
    SELECT * FROM orders
    WHERE customer_id='$customer_id'
    ORDER BY order_id DESC
");

if (isset($_GET['order_id']))  { $selected_order_id = $_GET['order_id']; }
if (isset($_POST['track']))    { $selected_order_id = $_POST['order_id']; }

if ($selected_order_id != "") {
    $oq = mysqli_query($conn, "
        SELECT * FROM orders
        WHERE order_id='$selected_order_id'
        AND customer_id='$customer_id'
        LIMIT 1
    ");
    if (mysqli_num_rows($oq) > 0) {
        $order_data = mysqli_fetch_assoc($oq);
        $dq = mysqli_query($conn, "
            SELECT * FROM deliveries
            WHERE order_id='$selected_order_id'
            LIMIT 1
        ");
        if (mysqli_num_rows($dq) > 0) {
            $delivery_data = mysqli_fetch_assoc($dq);
        } else {
            $error = "Delivery has not been assigned for this order yet.";
        }
    } else {
        $error = "Invalid order selected.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Track Order | BakeChain SCM</title>
<meta name="description" content="Track your BakeChain cookie order delivery status in real time.">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<style>
/* Timeline specific */
.timeline { margin-top:16px; }
.t-step {
  display:flex; gap:14px; margin-bottom:18px;
  animation:fadeUp .4s ease both;
}
.t-step:nth-child(1){animation-delay:.04s;} .t-step:nth-child(2){animation-delay:.08s;}
.t-step:nth-child(3){animation-delay:.12s;} .t-step:nth-child(4){animation-delay:.16s;}
.t-step:nth-child(5){animation-delay:.20s;}
.t-circle {
  width:38px; height:38px; border-radius:50%; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-weight:900; font-size:15px;
  background:var(--surface-3); color:var(--text-muted);
  border:2px solid var(--border);
  transition:var(--transition);
}
.t-step.done  .t-circle { background:#176B42; color:#fff; border-color:#176B42; }
.t-step.active .t-circle { background:var(--burgundy); color:#fff; border-color:var(--burgundy); }
.t-body h4 { margin:0; font-size:15px; font-weight:900; color:var(--text); }
.t-body p  { margin:3px 0 0; font-size:13px; color:var(--text-muted); font-weight:600; }
.t-step.done  .t-body h4 { color:#176B42; }
.t-step.active .t-body h4 { color:var(--burgundy); }
</style>
</head>
<body>

<?php include '../includes/customer_sidebar.php'; ?>

<div class="main-content">

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <h2>Track Order</h2>
      <small>Real-time delivery tracking</small>
    </div>
    <div class="topbar-right">
      <div class="topbar-badge">
        <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
        <?php echo htmlspecialchars($_SESSION['name']); ?>
      </div>
    </div>
  </div>

  <div class="content-body">


    <!-- Order selector -->
    <div class="page-card" style="border-radius:16px;margin-bottom:22px;">
      <h3 style="font-size:18px;font-weight:900;color:var(--burgundy);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-search"></i> Select Order to Track
      </h3>
      <form method="POST">
        <div class="input-wrap" style="margin-bottom:14px;">
          <i class="bi bi-bag-check input-icon"></i>
          <select name="order_id" required>
            <option value="">— Select an Order —</option>
            <?php
              // Need a fresh query since we may have consumed $orders above
              $orders2 = mysqli_query($conn, "SELECT * FROM orders WHERE customer_id='$customer_id' ORDER BY order_id DESC");
              while ($row = mysqli_fetch_assoc($orders2)):
            ?>
              <option value="<?php echo $row['order_id']; ?>"
                <?php if ($selected_order_id == $row['order_id']) echo 'selected'; ?>>
                ORD-<?php echo $row['order_id']; ?> &nbsp;|&nbsp;
                ₹<?php echo $row['total_amount']; ?> &nbsp;|&nbsp;
                <?php echo ucfirst($row['order_status']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <button type="submit" name="track" class="btn btn-primary">
          <i class="bi bi-geo-alt"></i> Track Order
        </button>
      </form>

      <?php if ($error != ""): ?>
        <div class="error-box" style="margin-top:16px;"><?php echo $error; ?></div>
      <?php endif; ?>
    </div>

    <!-- Tracking Result -->
    <?php if ($order_data): ?>
      <?php
        $delivery_status = $delivery_data
          ? strtolower($delivery_data['delivery_status'])
          : strtolower($order_data['order_status']);

        $confirmed_class = 'done';
        $packed_class    = '';
        $shipped_class   = '';
        $out_class       = '';
        $delivered_class = '';

        if ($delivery_status == 'packed')                          { $packed_class = 'active'; }
        elseif ($delivery_status == 'shipped')                     { $packed_class = 'done'; $shipped_class = 'active'; }
        elseif (in_array($delivery_status, ['out_for_delivery','out for delivery'])) {
          $packed_class = 'done'; $shipped_class = 'done'; $out_class = 'active';
        } elseif ($delivery_status == 'delivered' || strtolower($order_data['order_status']) == 'completed') {
          $packed_class = 'done'; $shipped_class = 'done'; $out_class = 'done'; $delivered_class = 'done';
        } else {
          $packed_class = 'active';
        }
      ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="tracking-layout">

        <!-- Timeline -->
        <div class="page-card" style="border-radius:16px;">
          <h3 style="font-size:18px;font-weight:900;color:var(--burgundy);margin-bottom:4px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-map"></i> Tracking Timeline
          </h3>
          <div class="timeline">
            <div class="t-step <?php echo $confirmed_class; ?>">
              <div class="t-circle"><i class="bi bi-check-lg"></i></div>
              <div class="t-body">
                <h4>Order Confirmed</h4>
                <p>Your order has been placed successfully.</p>
              </div>
            </div>
            <div class="t-step <?php echo $packed_class; ?>">
              <div class="t-circle">2</div>
              <div class="t-body">
                <h4>Packed</h4>
                <p>Your order is being packed for dispatch.</p>
              </div>
            </div>
            <div class="t-step <?php echo $shipped_class; ?>">
              <div class="t-circle">3</div>
              <div class="t-body">
                <h4>Shipped</h4>
                <p>Your order has left the bakery unit.</p>
              </div>
            </div>
            <div class="t-step <?php echo $out_class; ?>">
              <div class="t-circle">4</div>
              <div class="t-body">
                <h4>Out For Delivery</h4>
                <p>Your order is on the way to you!</p>
              </div>
            </div>
            <div class="t-step <?php echo $delivered_class; ?>">
              <div class="t-circle"><i class="bi bi-house-check"></i></div>
              <div class="t-body">
                <h4>Delivered</h4>
                <p>Your order has been delivered successfully.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Details -->
        <div class="page-card" style="border-radius:16px;">
          <h3 style="font-size:18px;font-weight:900;color:var(--burgundy);margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-receipt"></i> Order Details
          </h3>
          <div class="info-box" style="border-radius:10px;margin-bottom:10px;">
            <div style="font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;">Order ID</div>
            <div style="font-size:20px;font-weight:900;color:var(--burgundy);margin-top:3px;">ORD-<?php echo $order_data['order_id']; ?></div>
          </div>
          <div class="info-box" style="border-radius:10px;margin-bottom:10px;">
            <div style="font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;">Order Date</div>
            <div style="font-weight:800;margin-top:3px;"><?php echo $order_data['order_date']; ?></div>
          </div>
          <div class="info-box" style="border-radius:10px;margin-bottom:10px;">
            <div style="font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;">Total Amount</div>
            <div style="font-size:22px;font-weight:900;color:var(--burgundy);margin-top:3px;">₹<?php echo $order_data['total_amount']; ?></div>
          </div>
          <?php if ($delivery_data): ?>
          <div class="info-box" style="border-radius:10px;margin-bottom:10px;">
            <div style="font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;">Delivery Status</div>
            <div style="margin-top:5px;">
              <span class="status-badge <?php echo $delivery_status == 'delivered' ? 'status-delivered' : 'status-pending'; ?>">
                <?php echo ucwords(str_replace('_', ' ', $delivery_data['delivery_status'])); ?>
              </span>
            </div>
          </div>
          <div class="info-box" style="border-radius:10px;margin-bottom:10px;">
            <div style="font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;">Delivery Date</div>
            <div style="font-weight:800;margin-top:3px;"><?php echo $delivery_data['delivery_date'] ?? '—'; ?></div>
          </div>
          <?php if ($delivery_data['delay_days'] > 0): ?>
          <div class="info-box" style="border-radius:10px;border-left:3px solid var(--warning);">
            <div style="font-size:11px;font-weight:900;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;">Delay</div>
            <div style="font-weight:900;color:var(--warning);margin-top:3px;"><?php echo $delivery_data['delay_days']; ?> day(s)</div>
          </div>
          <?php endif; ?>
          <?php endif; ?>
        </div>

      </div>

      <style>
        @media(max-width:900px) { .tracking-layout { grid-template-columns:1fr; } }
      </style>

    <?php endif; ?>

  </div>
</div>

</body>
</html>