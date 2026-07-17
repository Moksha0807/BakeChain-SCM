<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cookie_scm/assets/css/global.css?v=2.7" rel="stylesheet">
<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon">🚚</div>
    <div class="sidebar-brand-text">
      <h3>BakeChain</h3>
      <span>Supplier Panel</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-section-label">Supplier</div>
    <a href="/cookie_scm/supplier_panel/dashboard.php"
       class="<?php if($current_page=='dashboard.php') echo 'active'; ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="/cookie_scm/supplier_panel/my_materials.php"
       class="<?php if($current_page=='my_materials.php') echo 'active'; ?>">
      <i class="bi bi-box-seam"></i> My Materials
    </a>
    <a href="/cookie_scm/supplier_panel/profile.php"
       class="<?php if($current_page=='profile.php') echo 'active'; ?>">
      <i class="bi bi-person-badge"></i> Supplier Profile
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="/cookie_scm/auth/logout.php">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</div>
<script src="/cookie_scm/assets/js/global.js?v=2.7" defer></script>