<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon">🛒</div>
    <div class="sidebar-brand-text">
      <h3>BakeChain</h3>
      <span>Customer Panel</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Customer</div>
    <a href="/cookie_scm/customer/dashboard.php"
       class="<?php if($current_page=='dashboard.php') echo 'active'; ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="/cookie_scm/customer/my_orders.php"
       class="<?php if($current_page=='my_orders.php') echo 'active'; ?>">
      <i class="bi bi-bag-check"></i> My Orders
    </a>
    <a href="/cookie_scm/customer/track_order.php"
       class="<?php if($current_page=='track_order.php') echo 'active'; ?>">
      <i class="bi bi-geo-alt"></i> Track Order
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="/cookie_scm/auth/logout.php">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</div>
<script src="/cookie_scm/assets/js/global.js?v=2.7" defer></script>
