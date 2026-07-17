/**
 * BakeChain SCM — Global JavaScript
 * Handles: Back Button injection, micro-animations, UI enhancements
 */

(function () {
  'use strict';

  /* ─── 1. SMART PAGE HEADER BANNER & SMALL BACK BUTTON ──────────────── */
  const NO_BACK_PAGES = [
    'dashboard.php',
    'login.php',
    'register.php',
    'logout.php',
  ];

  const currentFile = window.location.pathname.split('/').pop().split('?')[0];
  const isNoBack    = NO_BACK_PAGES.some(p => currentFile === p);

  // Determine correct panel dashboard URL
  let dashboardUrl = '/cookie_scm/admin/dashboard.php';
  const pathName = window.location.pathname;
  if (pathName.includes('/production_panel/')) {
    dashboardUrl = '/cookie_scm/production_panel/dashboard.php';
  } else if (pathName.includes('/delivery_panel/')) {
    dashboardUrl = '/cookie_scm/delivery_panel/dashboard.php';
  } else if (pathName.includes('/supplier_panel/')) {
    dashboardUrl = '/cookie_scm/supplier_panel/dashboard.php';
  } else if (pathName.includes('/customer/')) {
    dashboardUrl = '/cookie_scm/customer/dashboard.php';
  }

  // Determine if this is a create/add/verify/update page and process has succeeded
  const isSuccess = !!document.querySelector('.success, .alert-success, .success-box');
  const isCreateOrAction = currentFile.includes('create') || 
                           currentFile.includes('add') || 
                           currentFile.includes('verify') || 
                           currentFile.includes('suggest') || 
                           currentFile.includes('update') ||
                           currentFile.includes('stock');

  // Set the back button destination to always redirect directly to the panel dashboard URL to avoid history loops
  let backUrl = dashboardUrl;

  // Find any existing manual back buttons on the page and hide them to avoid duplication
  const manualBacks = document.querySelectorAll(
    'a[href*="dashboard.php"], a[href*="history.php"], a[href*="view_"], a[href*="list.php"], a.back-btn, .back-btn, a.btn-light, .btn-secondary, a[style*="background:#4D0E13"], .bk-back-btn'
  );
  manualBacks.forEach(btn => {
    if (btn.classList.contains('bk-back-btn')) {
      btn.remove();
      return;
    }
    const text = btn.innerText.toLowerCase().trim();
    if (text === 'back' || text === 'cancel' || text === '← back' || btn.classList.contains('back-btn')) {
      btn.style.display = 'none';
    }
  });

  // Dynamically inject the page-header-banner on inner pages
  if (!isNoBack) {
    const titleEl = document.querySelector('.content h1, .content h2, .main-content h1, .main-content h2, .card h1, .page-card h2');
    if (titleEl && 
        !titleEl.closest('.kpi-card') && 
        !titleEl.closest('.panel-card') && 
        !titleEl.closest('.sidebar-brand') &&
        !titleEl.closest('.kpi-box')) {

      const parentFlex = titleEl.closest('.d-flex, .page-head, [style*="justify-content:space-between"]');
      let descEl = titleEl.nextElementSibling;
      let descText = '';
      if (descEl && descEl.tagName === 'P') {
        descText = descEl.innerText;
      }

      // Contextual Unsplash image URL matching the role
      let bgImg = 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=1200&q=80'; // default
      const pathLower = pathName.toLowerCase();
      if (pathLower.includes('/production') || pathLower.includes('/batch')) {
        bgImg = 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=1200&q=80';
      } else if (pathLower.includes('/inventory') || pathLower.includes('/raw_materials') || pathLower.includes('/material')) {
        bgImg = 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80';
      } else if (pathLower.includes('/orders') || pathLower.includes('/sales')) {
        bgImg = 'https://images.unsplash.com/photo-1511018556340-d16986a1c194?auto=format&fit=crop&w=1200&q=80';
      } else if (pathLower.includes('/deliveries') || pathLower.includes('/tracking') || pathLower.includes('/delivery')) {
        bgImg = 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1200&q=80';
      } else if (pathLower.includes('/supplier')) {
        bgImg = 'https://images.unsplash.com/photo-1595079676339-1534801ad6cf?auto=format&fit=crop&w=1200&q=80';
      } else if (pathLower.includes('/qr') || pathLower.includes('/verify')) {
        bgImg = 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80';
      }

      // Create the banner element — no background image, uses CSS gradient instead
      const banner = document.createElement('div');
      banner.className = 'page-header-banner';

      const contentWrap = document.createElement('div');
      contentWrap.className = 'banner-content';

      const eyebrow = document.createElement('span');
      eyebrow.className = 'banner-eyebrow';
      eyebrow.innerHTML = '<i class="bi bi-cookie"></i> BakeChain SCM';
      contentWrap.appendChild(eyebrow);

      const titleRow = document.createElement('div');
      titleRow.style.display = 'flex';
      titleRow.style.alignItems = 'center';
      titleRow.style.gap = '12px';

      const titleTextEl = document.createElement(titleEl.tagName);
      titleTextEl.className = 'banner-title';
      titleTextEl.style.margin = '0';
      titleTextEl.innerText = titleEl.innerText;
      titleRow.appendChild(titleTextEl);

      contentWrap.appendChild(titleRow);

      if (descText) {
        const desc = document.createElement('p');
        desc.className = 'banner-subtitle';
        desc.innerText = descText;
        contentWrap.appendChild(desc);
        descEl.remove();
      }

      banner.appendChild(contentWrap);

      // Back button in the right side corner of the banner
      const backBtn = document.createElement('a');
      backBtn.className = 'bk-back-btn-small';
      backBtn.href = backUrl;
      backBtn.title = 'Go Back';
      backBtn.innerHTML = '<i class="bi bi-arrow-left"></i> Back';

      const actionWrap = document.createElement('div');
      actionWrap.className = 'banner-actions';
      actionWrap.appendChild(backBtn);

      // If parentFlex had other action buttons (like "View History" or "Create New"), add them next to the back button
      if (parentFlex) {
        const btns = parentFlex.querySelectorAll('a:not(.bk-back-btn):not(.back-btn):not([href*="dashboard.php"]), button');
        const filteredBtns = Array.from(btns).filter(btn => {
          const btnText = btn.innerText.toLowerCase().trim();
          return btnText !== 'back' && btnText !== 'cancel';
        });
        filteredBtns.forEach(btn => {
          actionWrap.appendChild(btn.cloneNode(true));
        });
      }

      banner.appendChild(actionWrap);

      // Replace the parent flex header or the title itself
      if (parentFlex) {
        parentFlex.replaceWith(banner);
      } else {
        titleEl.replaceWith(banner);
      }
    }
  }

  /* ─── 2. INTERSECTION OBSERVER — fade-in cards on scroll ──────────── */
  const fadeTargets = document.querySelectorAll(
    '.kpi-card, .kpi-item, .kpi-box, .stat-card, .panel-card, .page-card, .card, .feature-card, .qa-card, .quick-action, .order-card, .suggestion-card'
  );

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('bk-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.08 }
    );
    fadeTargets.forEach(el => {
      el.classList.add('bk-hidden');
      observer.observe(el);
    });
  } else {
    // Fallback: just show everything
    fadeTargets.forEach(el => el.classList.add('bk-visible'));
  }

  /* ─── 3. RIPPLE EFFECT on buttons ──────────────────────────────────── */
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn, .btn-login, .btn-register, .btn-hero, .btn-hero-primary, .btn-hero-ghost, button[type="submit"]');
    if (!btn) return;
    const circle = document.createElement('span');
    circle.className = 'bk-ripple';
    const rect = btn.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    circle.style.cssText = `
      width:${size}px; height:${size}px;
      left:${e.clientX - rect.left - size / 2}px;
      top:${e.clientY - rect.top - size / 2}px;
    `;
    btn.style.position = btn.style.position || 'relative';
    btn.style.overflow = 'hidden';
    btn.appendChild(circle);
    setTimeout(() => circle.remove(), 600);
  });

  /* ─── 4. COUNTER ANIMATION for KPI numbers ─────────────────────────── */
  function animateCounters() {
    const els = document.querySelectorAll('.kpi-value, .kpi-box-value, .stat-value, .hero-stat-card .num, .rev-stat .rs-num');
    els.forEach(el => {
      const raw    = el.textContent.trim();
      const hasRs  = raw.includes('₹');
      const num    = parseInt(raw.replace(/[^0-9]/g, ''), 10) || 0;
      if (num === 0) return;
      let cur = 0;
      const step  = Math.max(1, Math.ceil(num / 50));
      const timer = setInterval(() => {
        cur = Math.min(cur + step, num);
        el.textContent = hasRs ? '₹' + cur.toLocaleString('en-IN') : cur;
        if (cur >= num) clearInterval(timer);
      }, 20);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', animateCounters);
  } else {
    animateCounters();
  }

})();
