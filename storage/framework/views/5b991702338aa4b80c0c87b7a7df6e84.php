<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />

    <title><?php echo $__env->yieldContent('title'); ?><?php if(isset($generalsetting) && $generalsetting): ?> - <?php echo e($generalsetting->name); ?><?php endif; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo e(asset(isset($generalsetting->favicon) ? $generalsetting->favicon : 'public/backEnd/assets/images/favicon.ico')); ?>" />

    <!-- Bootstrap css -->
    <link href="<?php echo e(asset('public/backEnd/')); ?>/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="<?php echo e(asset('public/backEnd/')); ?>/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="<?php echo e(asset('public/backEnd/')); ?>/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- toastr css -->
    <link rel="stylesheet" href="<?php echo e(asset('public/backEnd/')); ?>/assets/css/toastr.min.css" />
    <!-- SweetAlert2 - ডেমো মুড পপআপের জন্য -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <!-- custom css -->
    <link href="<?php echo e(asset('public/backEnd/')); ?>/assets/css/custom.css" rel="stylesheet" type="text/css" />
    <!-- Multibranch-style panel theme (same design as the Multi-Branch SaaS platform) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" />
    <?php echo $__env->make('partials.mb.panel-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- Head js -->
    <?php echo $__env->yieldContent('css'); ?>
    <script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/head.js"></script>
  </head>

  <?php
    $mbAdmin = Auth::guard('admin')->user();
    $mbInitial = mb_strtoupper(mb_substr((string) ($mbAdmin->name ?? 'A'), 0, 1));
    $mbLogo = isset($generalsetting->dark_logo) && $generalsetting->dark_logo ? $generalsetting->dark_logo : null;
  ?>
  <!-- body start -->
  <body class="panel-ui" data-layout-mode="default" data-theme="light" data-layout-width="fluid" data-topbar-color="light" data-menu-position="fixed" data-leftbar-color="light" data-leftbar-size="default" data-sidebar-user="false">

    <!-- ========== Sidebar (multibranch style) ========== -->
    <aside class="mg-sidebar" id="sidebar">
      <div class="brand">
        <?php if($mbLogo): ?>
          <img src="<?php echo e(asset($mbLogo)); ?>" alt="" class="logo-img">
        <?php else: ?>
          <span class="logo-ic" style="background:linear-gradient(135deg,#f59e0b,#ef4444)"><i class="fa-solid fa-crown"></i></span>
        <?php endif; ?>
        <span class="t"><b>Admin Panel</b><small><?php echo e($generalsetting->name ?? config('app.name')); ?></small></span>
      </div>
      <nav data-fa-menu>
            <ul id="side-menu" class="lm">
<?php echo $__env->make('backEnd.layouts.partials.admin_menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </ul>
      </nav>
      <?php echo $__env->make('partials.mb.feather-to-fa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </aside>
    <div class="sb-backdrop" onclick="document.getElementById('sidebar').classList.remove('show')"></div>

    <div class="mg-main">
      <!-- ========== Topbar ========== -->
      <div class="mg-topbar">
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="burger" id="sbToggle" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
          <span class="page-title d-none d-sm-inline"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></span>
        </div>
        <div class="tb-right">
          <?php if(isset($demoMode) && $demoMode): ?>
          <div class="d-none d-lg-flex"><span class="badge bg-warning text-dark px-2 py-1" title=".env থেকে DEMO_MODE=true সেট করা আছে"><i class="fa-regular fa-eye me-1"></i>ডেমো</span></div>
          <?php endif; ?>
          <a href="<?php echo e(route('home')); ?>" target="_blank" class="text-dark d-none d-md-flex gap-2 small fw-semibold" title="Visit Site"><i class="fa-solid fa-globe text-info"></i> Visit Site</a>
          <div class="dropdown">
            <a href="#" class="tb-icon" data-bs-toggle="dropdown" aria-expanded="false" title="New orders">
              <i class="fa-regular fa-bell"></i>
              <?php if(!empty($neworder)): ?><span class="badge rounded-pill bg-danger"><?php echo e($neworder); ?></span><?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-end p-0" style="width:320px">
              <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <strong>Orders</strong>
                <a href="<?php echo e(route('admin.orders', ['slug' => 'pending'])); ?>" class="small">View All</a>
              </div>
              <div style="max-height:320px;overflow:auto">
                <?php $__empty_1 = true; $__currentLoopData = ($pendingorder ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $porder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <a href="<?php echo e(route('admin.orders', ['slug' => 'pending'])); ?>" class="dropdown-item d-flex align-items-center gap-2 py-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;background:#e0f2fe;color:#0369a1"><i class="fa-solid fa-bag-shopping"></i></span>
                    <span class="min-w-0"><span class="d-block text-truncate"><?php echo e($porder->customer ? $porder->customer->name : 'Guest'); ?></span><small class="text-muted">Invoice : <?php echo e($porder->invoice_id); ?></small></span>
                  </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <div class="text-center text-muted small py-4">No new orders</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="dropdown">
            <a href="#" class="tb-user" data-bs-toggle="dropdown" aria-expanded="false">
              <?php if(!empty($mbAdmin->image)): ?>
                <img src="<?php echo e(asset($mbAdmin->image)); ?>" alt="" class="avatar">
              <?php else: ?>
                <span class="avatar" style="background:linear-gradient(135deg,#f59e0b,#ef4444)"><?php echo e($mbInitial); ?></span>
              <?php endif; ?>
              <span class="nm"><?php echo e($mbAdmin->name ?? ''); ?><small>Admin</small></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
              <a href="<?php echo e(url('admin/dashboard')); ?>" class="dropdown-item"><i class="fa-solid fa-gauge-high me-2 text-primary"></i>Dashboard</a>
              <?php if(Route::has('change_password')): ?>
              <a href="<?php echo e(route('change_password')); ?>" class="dropdown-item"><i class="fa-solid fa-key me-2 text-warning"></i>Change Password</a>
              <?php endif; ?>
              <div class="dropdown-divider"></div>
              <a href="<?php echo e(route('logout')); ?>" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
            </div>
          </div>
          <form method="POST" action="<?php echo e(route('logout')); ?>" class="p-0"><?php echo csrf_field(); ?><button type="submit" class="tb-logout" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></button></form>
        </div>
      </div>
      <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;"><?php echo csrf_field(); ?></form>

      <div class="mg-content">
        <div class="content-page">
          <div class="content">
            <?php echo $__env->yieldContent('content'); ?>
          </div>
        </div>
      </div>
    </div>
    <!-- END main -->

    <!-- Right Sidebar -->
    <div class="right-bar">
      <div data-simplebar class="h-100">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-bordered nav-justified" role="tablist">
          <li class="nav-item">
            <a class="nav-link py-2" data-bs-toggle="tab" href="#chat-tab" role="tab">
              <i class="mdi mdi-message-text d-block font-22 my-1"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-2" data-bs-toggle="tab" href="#tasks-tab" role="tab">
              <i class="mdi mdi-format-list-checkbox d-block font-22 my-1"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-2 active" data-bs-toggle="tab" href="#settings-tab" role="tab">
              <i class="mdi mdi-cog-outline d-block font-22 my-1"></i>
            </a>
          </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content pt-0">
          <div class="tab-pane" id="chat-tab" role="tabpanel">
            <form class="search-bar p-3">
              <div class="position-relative">
                <input type="text" class="form-control" placeholder="Search..." />
                <span class="mdi mdi-magnify"></span>
              </div>
            </form>
          </div>

          <div class="tab-pane" id="tasks-tab" role="tabpanel">
            <h6 class="fw-medium p-3 m-0 text-uppercase">Working Tasks</h6>
          </div>
          <div class="tab-pane active" id="settings-tab" role="tabpanel">
            <h6 class="fw-medium px-3 m-0 py-2 font-13 text-uppercase bg-light">
              <span class="d-block py-1">Theme Settings</span>
            </h6>

            <div class="p-3">
              <div class="alert alert-warning" role="alert"><strong>Customize </strong> the overall color scheme, sidebar menu, etc.</div>

              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Color Scheme</h6>
              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="layout-color" value="light" id="light-mode-check" checked />
                <label class="form-check-label" for="light-mode-check">Light Mode</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="layout-color" value="dark" id="dark-mode-check" />
                <label class="form-check-label" for="dark-mode-check">Dark Mode</label>
              </div>

              <!-- Width -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Width</h6>
              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="layout-width" value="fluid" id="fluid-check" checked />
                <label class="form-check-label" for="fluid-check">Fluid</label>
              </div>
              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="layout-width" value="boxed" id="boxed-check" />
                <label class="form-check-label" for="boxed-check">Boxed</label>
              </div>

              <!-- Menu positions -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Menus (Leftsidebar and Topbar) Positon</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="menu-position" value="fixed" id="fixed-check" checked />
                <label class="form-check-label" for="fixed-check">Fixed</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="menu-position" value="scrollable" id="scrollable-check" />
                <label class="form-check-label" for="scrollable-check">Scrollable</label>
              </div>

              <!-- Left Sidebar-->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Left Sidebar Color</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-color" value="light" id="light-check" />
                <label class="form-check-label" for="light-check">Light</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-color" value="dark" id="dark-check" checked />
                <label class="form-check-label" for="dark-check">Dark</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-color" value="brand" id="brand-check" />
                <label class="form-check-label" for="brand-check">Brand</label>
              </div>

              <div class="form-check form-switch mb-3">
                <input type="checkbox" class="form-check-input" name="leftbar-color" value="gradient" id="gradient-check" />
                <label class="form-check-label" for="gradient-check">Gradient</label>
              </div>

              <!-- size -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Left Sidebar Size</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-size" value="default" id="default-size-check" checked />
                <label class="form-check-label" for="default-size-check">Default</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-size" value="condensed" id="condensed-check" />
                <label class="form-check-label" for="condensed-check">Condensed <small>(Extra Small size)</small></label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="leftbar-size" value="compact" id="compact-check" />
                <label class="form-check-label" for="compact-check">Compact <small>(Small size)</small></label>
              </div>

              <!-- User info -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Sidebar User Info</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="sidebar-user" value="fixed" id="sidebaruser-check" />
                <label class="form-check-label" for="sidebaruser-check">Enable</label>
              </div>

              <!-- Topbar -->
              <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Topbar</h6>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="topbar-color" value="dark" id="darktopbar-check" checked />
                <label class="form-check-label" for="darktopbar-check">Dark</label>
              </div>

              <div class="form-check form-switch mb-1">
                <input type="checkbox" class="form-check-input" name="topbar-color" value="light" id="lighttopbar-check" />
                <label class="form-check-label" for="lighttopbar-check">Light</label>
              </div>

              <div class="d-grid mt-4">
                <button class="btn btn-primary" id="resetBtn">Reset to Default</button>
                <a href="https://1.envato.market/uboldadmin" class="btn btn-danger mt-3" target="_blank"><i class="mdi mdi-basket me-1"></i> Purchase Now</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- end slimscroll-menu-->
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/app.min.js"></script>
    
    
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        (function () {
            function initFeather() {
                if (typeof feather !== 'undefined' && typeof feather.replace === 'function') {
                    try {
                        feather.replace();
                    } catch (e) {
                        console.warn('Feather replace error:', e);
                    }
                }
            }
            function scheduleFeatherPasses() {
                initFeather();
                setTimeout(initFeather, 80);
                setTimeout(initFeather, 250);
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', scheduleFeatherPasses);
            } else {
                scheduleFeatherPasses();
            }
            window.addEventListener('load', function () {
                initFeather();
            });
            if (typeof jQuery !== 'undefined') {
                jQuery(function () {
                    scheduleFeatherPasses();
                });
                jQuery(document).on(
                    'shown.bs.collapse hidden.bs.collapse',
                    '[data-bs-toggle="collapse"]',
                    function () {
                        setTimeout(initFeather, 50);
                    }
                );
            }
        })();
    </script>
    <script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/toastr.min.js"></script>
    <script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php echo Toastr::message(); ?>

	<script>
<?php if(Session::has('success')): ?>
    toastr.success(<?php echo json_encode(Session::get('success'), 15, 512) ?>);
<?php endif; ?>
<?php if(Session::has('error') && !Session::has('demo_mode_blocked')): ?>
    toastr.error(<?php echo json_encode(Session::get('error'), 15, 512) ?>);
<?php endif; ?>
<?php if(Session::has('info')): ?>
    toastr.info(<?php echo json_encode(Session::get('info'), 15, 512) ?>);
<?php endif; ?>
<?php if(Session::has('warning')): ?>
    toastr.warning(<?php echo json_encode(Session::get('warning'), 15, 512) ?>);
<?php endif; ?>
<?php if(Session::has('demo_mode_blocked')): ?>
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: '<strong style="font-size:1.4rem;color:#2c3e50;">ডেমো মুড সক্রিয়</strong>',
            html: '<div style="text-align:center;padding:10px 0;"><div style="width:70px;height:70px;margin:0 auto 15px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fe-eye" style="font-size:32px;color:#fff;"></i></div><p style="font-size:1rem;color:#5a6c7d;margin-bottom:8px;line-height:1.6;">অ্যাডমিন প্যানেল থেকে কোন ডাটা পরিবর্তন বা সংযোজন করা যাবে না।</p><p style="font-size:0.9rem;color:#95a5a6;margin:0;">কাস্টমার সাইটে অর্ডার, ট্রাকিং ও অন্যান্য সেবা স্বাভাবিকভাবে কাজ করবে।</p></div>',
            confirmButtonText: 'বুঝেছি',
            confirmButtonColor: '#667eea',
            customClass: { popup: 'demo-mode-popup', confirmButton: 'demo-mode-btn' },
            width: '420px',
            backdrop: 'rgba(0,0,0,0.5)',
        });
    } else {
        toastr.info("ডেমো মুড চালু আছে। অ্যাডমিন প্যানেল থেকে কোন পরিবর্তন করা যাবে না।");
    }
<?php endif; ?>
</script>
    <style>
    .demo-mode-popup { border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
    .demo-mode-btn { padding: 10px 28px; font-weight: 600; border-radius: 8px; }
    </style>
    <script>
    function showDemoModeAlert(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: '<strong style="font-size:1.4rem;color:#2c3e50;">ডেমো মুড সক্রিয়</strong>',
                html: '<div style="text-align:center;padding:10px 0;"><div style="width:70px;height:70px;margin:0 auto 15px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="fe-eye" style="font-size:32px;color:#fff;"></i></div><p style="font-size:1rem;color:#5a6c7d;margin-bottom:8px;line-height:1.6;">' + (msg || 'অ্যাডমিন প্যানেল থেকে কোন ডাটা পরিবর্তন বা সংযোজন করা যাবে না।') + '</p><p style="font-size:0.9rem;color:#95a5a6;margin:0;">কাস্টমার সাইটে অর্ডার, ট্রাকিং ও অন্যান্য সেবা স্বাভাবিকভাবে কাজ করবে।</p></div>',
                confirmButtonText: 'বুঝেছি',
                confirmButtonColor: '#667eea',
                customClass: { popup: 'demo-mode-popup', confirmButton: 'demo-mode-btn' },
                width: '420px',
                backdrop: 'rgba(0,0,0,0.5)',
            });
        }
    }
    $(document).ajaxComplete(function(event, xhr, settings) {
        if (xhr.status === 403) {
            try {
                var data = typeof xhr.responseJSON !== 'undefined' ? xhr.responseJSON : JSON.parse(xhr.responseText || '{}');
                if (data.demo_mode && typeof Swal !== 'undefined') {
                    showDemoModeAlert(data.message || '');
                }
            } catch (e) {}
        }
    });
    </script>
    <script type="text/javascript">
      $(document).on('click', '.delete-confirm', function (event) {
        event.preventDefault();
        var form = $(this).closest("form");
        <?php if(isset($demoMode) && $demoMode): ?>
        showDemoModeAlert();
        return;
        <?php endif; ?>
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
          }).then(function(result) {
            if (result.isConfirmed) { form.submit(); }
          });
        } else {
          if (confirm('Are you sure you want to delete this record?')) { form.submit(); }
        }
      });
      $(document).on('click', '.change-confirm', function (event) {
        event.preventDefault();
        var form = $(this).closest("form");
        <?php if(isset($demoMode) && $demoMode): ?>
        showDemoModeAlert();
        return;
        <?php endif; ?>
        swal({
          title: `Are you sure you want to change this record?`,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            form.submit();
          }
        });
      });
      <?php if(isset($demoMode) && $demoMode): ?>
      $(document).on('submit', 'form', function(e) {
        var action = (this.action || '').toLowerCase();
        if (action.indexOf('logout') !== -1) return;
        var method = ($(this).find('input[name="_method"]').val() || $(this).attr('method') || 'get').toLowerCase();
        if (method === 'get') return;
        e.preventDefault();
        showDemoModeAlert();
        return false;
      });
      document.addEventListener('click', function(e) {
        var el = e.target.closest ? e.target.closest('a[href*="destroy"], a[href*="bulk_destroy"], a[href*="/delete"], a.order_delete') : null;
        if (el && el.href && el.href.indexOf('#') !== 0) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          showDemoModeAlert();
          return false;
        }
      }, true);
      <?php endif; ?>
    </script>
    <!--patho courier-->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.pathaocity').change(function() {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo e(url('admin/pathao-city')); ?>?city_id=" + id,
                        success: function(res) {
                            if (res && res.data && res.data.data) {
                                $(".pathaozone").empty();
                                $(".pathaozone").append('<option value="">Select..</option>');
                                $.each(res.data.data, function(index, zone) {
                                    $(".pathaozone").append('<option value="' + zone.zone_id + '">' + zone.zone_name + '</option>');
                                    $('.pathaozone').trigger("chosen:updated");
                                });
                            } else {
                                 $(".pathaoarea").empty();
                                $(".pathaozone").empty();
                            }
                        }
                    });
                } else {
                     $(".pathaoarea").empty();
                    $(".pathaozone").empty();
                }
            });
        });
    </script>
    <script type="text/javascript"> 
        $(document).ready(function() {
            $('.pathaozone').change(function() {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo e(url('admin/pathao-zone')); ?>?zone_id=" + id,
                        success: function(res) {
                            if (res && res.data && res.data.data) {
                                $(".pathaoarea").empty();
                                $(".pathaoarea").append('<option value="">Select..</option>');
                                $.each(res.data.data, function(index, area) {
                                    $(".pathaoarea").append('<option value="' + area.area_id + '">' + area.area_name + '</option>');
                                    $('.pathaoarea').trigger("chosen:updated");
                                });
                            } else {
                                $(".pathaoarea").empty();
                            }
                        }
                    });
                } else {
                    $(".pathaoarea").empty();
                }
            });
        });
    </script>
    
    <style>
        .content-page .form-switch .form-check-input[type="checkbox"] {
            cursor: pointer;
            margin-top: 0.2em;
        }
        .content-page .card-body .form-switch + .text-muted,
        .content-page .card-body label.form-check-label ~ .text-muted {
            margin-top: -0.15rem;
        }
    </style>
    <script>
        (function () {
            function applyAdminKillSwitches(root) {
                var container = root && root.nodeType === 1 ? root : document;
                if (!container.querySelector || !document.querySelector('.content-page')) return;
                var nodes = container.querySelectorAll('.content-page input[type="checkbox"]');
                for (var i = 0; i < nodes.length; i++) {
                    var cb = nodes[i];
                    if (cb.getAttribute('data-no-bs-switch') === 'true' || cb.classList.contains('no-bs-switch')) continue;
                    if (cb.closest && (cb.closest('#settings-tab') || cb.closest('.right-bar'))) continue;
                    if (cb.closest && cb.closest('table')) continue;
                    if (cb.closest && cb.closest('label.switch')) continue;
                    if (cb.closest && cb.closest('.switch')) continue;
                    if (cb.matches('.toggle-checkbox')) continue;

                    var wrap = cb.closest('.form-check');
                    if (wrap && !wrap.classList.contains('form-switch')) {
                        wrap.classList.add('form-switch');
                        cb.setAttribute('role', 'switch');
                    }
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () { applyAdminKillSwitches(document); });
            } else {
                applyAdminKillSwitches(document);
            }
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ajaxComplete(function () { applyAdminKillSwitches(document); });
            }
            window.adminApplyKillSwitches = applyAdminKillSwitches;
        })();
    </script>
    <?php if(auth()->guard('admin')->check()): ?>
        <?php echo $__env->make('backEnd.layouts.partials.admin_order_live_notify', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('backEnd.layouts.partials.gemini_admin_chatbot', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    <?php echo $__env->make('partials.mb.panel-toggle', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldContent('script'); ?>
  </body>
</html>
<?php /**PATH D:\laragon\www\ecom1\resources\views/backEnd/layouts/master.blade.php ENDPATH**/ ?>