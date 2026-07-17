<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon">🏭</div>
    <div class="sidebar-brand-text">
      <h3>BakeChain</h3>
      <span>Production Panel</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Production</div>
    <a href="/cookie_scm/production_panel/dashboard.php"
       class="<?php if($current_page=='dashboard.php') echo 'active'; ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="/cookie_scm/production_panel/create_batch.php"
       class="<?php if($current_page=='create_batch.php') echo 'active'; ?>">
      <i class="bi bi-plus-circle"></i> Create Batch
    </a>
    <a href="/cookie_scm/production_panel/production_history.php"
       class="<?php if($current_page=='production_history.php') echo 'active'; ?>">
      <i class="bi bi-clock-history"></i> Production History
    </a>
    <a href="/cookie_scm/production_panel/verify_batch.php"
       class="<?php if($current_page=='verify_batch.php') echo 'active'; ?>">
      <i class="bi bi-search"></i> Verify Batch
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="/cookie_scm/auth/logout.php">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</div>
<script src="/cookie_scm/assets/js/global.js?v=2.7" defer></script>
