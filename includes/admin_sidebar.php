<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon">🍪</div>
    <div class="sidebar-brand-text">
      <h3>BakeChain</h3>
      <span>Admin Panel</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Main</div>
    <a href="/cookie_scm/admin/dashboard.php"
       class="<?php if($current_page=='dashboard.php') echo 'active'; ?>">
      <i class="bi bi-house-door"></i> Dashboard
    </a>
    <a href="/cookie_scm/admin/supplier/view_supplier.php"
       class="<?php if($current_page=='view_supplier.php') echo 'active'; ?>">
      <i class="bi bi-truck"></i> Suppliers
    </a>
    <a href="/cookie_scm/admin/raw_materials/view_material.php"
       class="<?php if($current_page=='view_material.php') echo 'active'; ?>">
      <i class="bi bi-box-seam"></i> Raw Materials
    </a>
    <a href="/cookie_scm/admin/products/view_product.php"
       class="<?php if($current_page=='view_product.php') echo 'active'; ?>">
      <i class="bi bi-basket3"></i> Products
    </a>
    <a href="/cookie_scm/admin/production/production_history.php"
       class="<?php if($current_page=='production_history.php') echo 'active'; ?>">
      <i class="bi bi-cpu"></i> Production
    </a>
    <a href="/cookie_scm/admin/inventory/inventory_list.php"
       class="<?php if($current_page=='inventory_list.php') echo 'active'; ?>">
      <i class="bi bi-clipboard-data"></i> Inventory
    </a>
    <a href="/cookie_scm/admin/orders/view_orders.php"
       class="<?php if($current_page=='view_orders.php') echo 'active'; ?>">
      <i class="bi bi-cart-check"></i> Orders
    </a>
    <a href="/cookie_scm/admin/deliveries/delivery_list.php"
       class="<?php if($current_page=='delivery_list.php') echo 'active'; ?>">
      <i class="bi bi-send-check"></i> Deliveries
    </a>
    <a href="/cookie_scm/admin/reports/sales_report.php"
       class="<?php if(in_array($current_page,['sales_report.php','inventory_report.php','delivery_report.php','supplier_report.php'])) echo 'active'; ?>">
      <i class="bi bi-bar-chart-line"></i> Reports
    </a>
    <div class="sidebar-section-label">Smart Tools</div>
    <a href="/cookie_scm/admin/batch_lookup/verify_batch.php"
       class="<?php if(in_array($current_page,['verify_batch.php','batch_result.php'])) echo 'active'; ?>">
      <i class="bi bi-search"></i> Batch Lookup
    </a>
    <a href="/cookie_scm/admin/qr_verification/generate_qr.php"
       class="<?php if(in_array($current_page,['generate_qr.php','qr_result.php'])) echo 'active'; ?>">
      <i class="bi bi-qr-code"></i> QR Generator
    </a>
    <a href="/cookie_scm/admin/tracking/tracking_dashboard.php"
       class="<?php if(in_array($current_page,['tracking_dashboard.php','add_tracking.php','view_tracking.php'])) echo 'active'; ?>">
      <i class="bi bi-geo-alt"></i> Tracking
    </a>
    <a href="/cookie_scm/admin/tracking/current_location.php"
       class="<?php if($current_page=='current_location.php') echo 'active'; ?>">
      <i class="bi bi-pin-map"></i> Current Location
    </a>
    <a href="/cookie_scm/admin/alternate_supplier/suggest_supplier.php"
       class="<?php if($current_page=='suggest_supplier.php') echo 'active'; ?>">
      <i class="bi bi-shuffle"></i> Supplier Suggestion
    </a>
    <a href="/cookie_scm/admin/bottleneck_detection/detect_bottleneck.php"
       class="<?php if($current_page=='detect_bottleneck.php') echo 'active'; ?>">
      <i class="bi bi-exclamation-triangle"></i> Bottleneck Detection
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="/cookie_scm/auth/logout.php">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</div>
<script src="/cookie_scm/assets/js/global.js?v=2.7" defer></script>
<script>
const sidebar = document.getElementById("sidebar");
if(localStorage.getItem("sidebarScroll")){
    sidebar.scrollTop = localStorage.getItem("sidebarScroll");
}
sidebar.addEventListener("scroll", function(){
    localStorage.setItem("sidebarScroll", sidebar.scrollTop);
});
</script>