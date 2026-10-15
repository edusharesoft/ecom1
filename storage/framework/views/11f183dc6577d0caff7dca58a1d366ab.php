<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
        <title><?php echo $__env->yieldContent('title'); ?></title>
		<?php if(!empty($seo->search_console_verification)): ?>
<?php echo $seo->search_console_verification ?? ''; ?>

<?php endif; ?>
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo e(asset($generalsetting->favicon)); ?>" alt="Super Ecommerce Favicon" />
        <meta name="author" content="Super Ecommerce" />
        <link rel="canonical" href="" />
        <?php echo $__env->yieldPushContent('seo'); ?> 
        <?php echo $__env->yieldPushContent('css'); ?>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
        <link rel="preconnect" href="https://maxcdn.bootstrapcdn.com" crossorigin>
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/bootstrap.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/animate.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/all.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/owl.carousel.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/owl.theme.default.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/mobile-menu.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/select2.min.css')); ?>" />
        <!-- toastr css -->
        <link rel="stylesheet" href="<?php echo e(asset('public/backEnd/')); ?>/assets/css/toastr.min.css" />

        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/wsit-menu.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(url('/style.css')); ?>?v=3">
<link rel="stylesheet" href="<?php echo e(url('/responsive.css')); ?>?v=3">
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/main.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/css/news-ticker.css')); ?>" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" media="print" onload="this.media='all'">
        <noscript><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"></noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
        <meta name="facebook-domain-verification" content="38f1w8335btoklo88dyfl63ba3st2e" />
        <style>
            .float{
            	position:fixed;
            	color:white;
            	width:60px;
            	height:60px;
            	bottom:40px;
            	left:40px;
            	background-color:#25d366;
            	color:#FFF;
            	border-radius:50px;
            	text-align:center;
                font-size:30px;
            	box-shadow: 2px 2px 3px #999;
                z-index:100;
            }
            
            .my-float{
            	margin-top:16px;
            }
            /* Media query to hide the .float class on screens 768px and smaller */
            @media (max-width: 767px) {
                .float {
                    display: none;
                }
            }
        </style>
		<style>
/* ========== Footer V2 — 100% Responsive (colors from General Setting) ========== */
.footer-v2 {
    background-color: <?php echo e(optional($generalsetting)->footer_color ?? '#222222'); ?>;
    color: #e8e8e8;
    font-family: 'Poppins', sans-serif;
    position: relative;
    overflow: hidden;
}

.footer-v2 p, .footer-v2 a, .footer-v2 h5, .footer-v2 h6, .footer-v2 li, .footer-v2 span {
    color: #e8e8e8 !important;
}

/* Top accent line — Primary Color from setting */
.footer-v2__wave {
    height: 4px;
    width: 100%;
    background: linear-gradient(90deg, transparent 0%, <?php echo e(optional($generalsetting)->primary_color ?? '#667eea'); ?> 20%, <?php echo e(optional($generalsetting)->primary_color ?? '#667eea'); ?> 80%, transparent 100%);
    opacity: 0.9;
}

/* Main content — padding responsive (mobile first) */
.footer-v2__main {
    padding: 2rem 1rem 2rem;
    box-sizing: border-box;
}
@media (min-width: 360px) {
    .footer-v2__main { padding-left: 1.25rem; padding-right: 1.25rem; }
}
@media (min-width: 576px) {
    .footer-v2__main { padding: 3rem 1.5rem 2.5rem; }
}
@media (min-width: 992px) {
    .footer-v2__main { padding: 4rem 2rem 3rem; }
}

/* Grid: 1 col mobile → 2 col → 3 col → 4 col desktop (100% responsive) */
.footer-v2__grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}
@media (min-width: 576px) {
    .footer-v2__grid { grid-template-columns: 1fr 1fr; gap: 2.5rem; }
}
@media (min-width: 768px) {
    .footer-v2__grid { grid-template-columns: 1.5fr 1fr 1fr; }
}
@media (min-width: 992px) {
    .footer-v2__grid { grid-template-columns: 2fr 1fr 1fr 1.2fr; gap: 3rem; }
}

/* Brand block */
.footer-v2__brand { }
.footer-v2__logo {
    display: inline-block;
    margin-bottom: 1rem;
}
.footer-v2__logo img {
    height: 48px;
    width: auto;
    filter: brightness(0) invert(1);
}
@media (min-width: 768px) {
    .footer-v2__logo img { height: 52px; }
}
.footer-v2__tagline {
    font-size: 0.9375rem;
    line-height: 1.65;
    opacity: 0.9;
    margin-bottom: 1.5rem;
    max-width: 100%;
}
@media (min-width: 400px) {
    .footer-v2__tagline { max-width: 320px; }
}
.footer-v2__apps {
    margin-top: 1.25rem;
}
.footer-v2__apps-title {
    font-size: 0.8125rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    opacity: 0.95;
}
.footer-v2__app-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.footer-v2__app-badges a {
    display: block;
}
.footer-v2__app-badges img {
    height: 40px;
    width: auto;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.2);
    transition: transform 0.2s, box-shadow 0.2s;
}
.footer-v2__app-badges a:hover img {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

/* Link blocks */
.footer-v2__block { }
.footer-v2__title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1rem;
    position: relative;
    padding-bottom: 0.5rem;
    display: inline-block;
}
.footer-v2__title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 28px;
    height: 2px;
    background-color: <?php echo e(optional($generalsetting)->primary_color ?? '#667eea'); ?>;
    border-radius: 2px;
}
.footer-v2__links {
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-v2__links li {
    margin-bottom: 0.5rem;
}
.footer-v2__links a {
    text-decoration: none;
    font-size: 0.9375rem;
    opacity: 0.85;
    transition: opacity 0.2s, color 0.2s, padding-left 0.2s;
    display: inline-block;
}
.footer-v2__links a:hover {
    opacity: 1;
    color: <?php echo e(optional($generalsetting)->primary_color ?? '#667eea'); ?> !important;
    padding-left: 4px;
}

/* মোবাইলে Useful Link ও Link মেনু কনফ্লিক্ট রোধ — এক কলাম, স্পষ্ট আলাদা */
@media (max-width: 767px) {
    .footer-v2__grid {
        grid-template-columns: 1fr;
        gap: 0;
    }
    .footer-v2__block {
        width: 100%;
        min-width: 0;
        padding: 1rem 0;
        margin: 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .footer-v2__block:last-of-type {
        border-bottom: none;
    }
    .footer-v2__title {
        display: block;
        margin-bottom: 0.75rem;
    }
    .footer-v2__links {
        display: block;
    }
    .footer-v2__links li {
        display: block;
        margin-bottom: 0.5rem;
    }
    .footer-v2__links a {
        display: block;
        padding: 0.35rem 0;
        line-height: 1.4;
        white-space: normal;
        word-break: break-word;
    }
}

/* Newsletter + Social block */
.footer-v2__newsletter { }
.footer-v2__newsletter .footer-v2__title { margin-bottom: 0.75rem; }
.footer-v2__newsletter-desc {
    font-size: 0.8125rem;
    opacity: 0.85;
    margin-bottom: 1rem;
}
.footer-v2__form {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    width: 100%;
    max-width: 100%;
}
@media (min-width: 400px) {
    .footer-v2__form { flex-direction: row; }
}
.footer-v2__form input {
    flex: 1;
    min-width: 0;
    padding: 0.65rem 1rem;
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 10px;
    background: rgba(255,255,255,0.08);
    color: #fff !important;
    font-size: 0.9375rem;
}
.footer-v2__form input::placeholder { color: rgba(255,255,255,0.5); }
.footer-v2__form button {
    padding: 0.65rem 1.25rem;
    border-radius: 10px;
    border: none;
    background-color: <?php echo e(optional($generalsetting)->primary_color ?? '#667eea'); ?>;
    color: #fff !important;
    font-weight: 600;
    font-size: 0.9375rem;
    white-space: nowrap;
    transition: transform 0.2s, opacity 0.2s;
}
.footer-v2__form button:hover {
    transform: scale(1.02);
    opacity: 0.95;
}
.footer-v2__social-title {
    font-size: 0.8125rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    opacity: 0.95;
}
.footer-v2__social-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-v2__social-list a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff !important;
    transition: background 0.2s, transform 0.2s;
}
.footer-v2__social-list a:hover {
    background-color: <?php echo e(optional($generalsetting)->primary_color ?? '#667eea'); ?>;
    transform: translateY(-2px);
}
.footer-v2__social-list i { font-size: 1.1rem; }

/* Bottom bar — Copyright Color from setting */
.footer-v2__bottom {
    background-color: <?php echo e(optional($generalsetting)->copyright_color ?? '#000000'); ?>;
    padding: 1.25rem 1rem;
    border-top: 1px solid rgba(255,255,255,0.08);
}
@media (min-width: 576px) {
    .footer-v2__bottom { padding: 1.25rem 1.5rem; }
}
.footer-v2__copy-wrap {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    text-align: center;
    font-size: 0.875rem;
}
@media (min-width: 768px) {
    .footer-v2__copy-wrap {
        flex-direction: row;
        justify-content: center;
        flex-wrap: wrap;
        text-align: left;
    }
}
.footer-v2__copy-text { margin: 0; }
.footer-v2__copy-sep {
    display: none;
    margin: 0 0.75rem;
    opacity: 0.6;
}
@media (min-width: 768px) {
    .footer-v2__copy-sep { display: inline; }
}
.footer-v2__designer {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.footer-v2__designer-link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.12);
    color: #fff !important;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.8125rem;
    transition: background 0.2s, color 0.2s;
}
.footer-v2__designer-link:hover {
    background: #fff;
    color: #1a1a2e !important;
}
.footer-v2__designer-link img {
    height: 18px;
    width: auto;
    display: block;
}

/* Mobile: space above fixed bottom nav + safe area */
@media (max-width: 768px) {
    .footer-v2__bottom { padding-bottom: 95px; }
    .footer-v2__main { padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right)); }
}

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
    .copyright-wrapper {
        flex-direction: column; /* Stack on mobile */
        gap: 15px;
        text-align: center;
    }
    
    .designer-credit {
        justify-content: center;
    }
}
</style>
        <script>window.dataLayer = window.dataLayer || [];</script>
    </head>
    <body class="gotop">
        <?php $__currentLoopData = $gtm_code ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gtm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $gtm_noscript_id = preg_match('/^GTM-/i', trim($gtm->code)) ? trim($gtm->code) : 'GTM-'.trim($gtm->code); ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($gtm_noscript_id); ?>"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $subtotal = Cart::instance('shopping')->subtotal(); ?>
        <div class="mobile-menu">
                <div class="mobile-menu-logo">
                    <div class="logo-image">
                        <img src="<?php echo e(asset($generalsetting->dark_logo)); ?>" alt="" />
                    </div>
                    <div class="mobile-menu-close">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
                <ul class="first-nav">
                    <?php $__currentLoopData = $menucategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="parent-category">
                        <a href="<?php echo e(url('category/'.$scategory->slug)); ?>" class="menu-category-name">
                            <img src="<?php echo e(asset($scategory->image)); ?>" alt="" class="side_cat_img" />
                            <?php echo e($scategory->name); ?>

                        </a>
                        <?php if($scategory->subcategories->count() > 0): ?>
                        <span class="menu-category-toggle">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                        <?php endif; ?>
                        <ul class="second-nav" style="display: none;">
                            <?php $__currentLoopData = $scategory->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="parent-subcategory">
                                <a href="<?php echo e(url('subcategory/'.$subcategory->slug)); ?>" class="menu-subcategory-name"><?php echo e($subcategory->subcategoryName); ?></a>
                                <?php if($subcategory->childcategories->count() > 0): ?>
                                <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                                <?php endif; ?>
                                <ul class="third-nav" style="display: none;">
                                    <?php $__currentLoopData = $subcategory->childcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $childcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="childcategory"><a href="<?php echo e(url('products/'.$childcat->slug)); ?>" class="menu-childcategory-name"><?php echo e($childcat->childcategoryName); ?></a></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <header id="navbar_top">

        <?php echo $__env->make('frontEnd.layouts.partials.breaking-news-ticker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="mobile-header sticky">
                <div class="mobile-logo">
                    <div class="menu-bar">
                        <a class="toggle">
                            <i class="fa-solid fa-bars"></i>
                        </a>
                    </div>
                    <div class="menu-logo">
                        <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset($generalsetting->dark_logo)); ?>" alt="" /></a>
                    </div>
<div class="menu-bag">
    <a href="<?php echo e(route('customer.checkout')); ?>" class="margin-shopping">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="mobilecart-qty"><?php echo e(Cart::instance('shopping')->count()); ?></span>
    </a>
</div>

                </div>
            </div>

            <div class="mobile-search">
                <form action="<?php echo e(route('search')); ?>">
                    <input type="text" placeholder="Search Product ... " value="" class="msearch_keyword msearch_click" name="keyword" />
                    <button><i data-feather="search"></i></button>
                </form>
                <div class="search_result"></div>
            </div>

            <div class="main-header">
                <!-- header to end -->
                <div class="logo-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="logo-header">
                                    <div class="main-logo">
                                        <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset($generalsetting->dark_logo)); ?>" alt="" /></a>
                                    </div>
                                    <div class="main-search">
                                        <form action="<?php echo e(route('search')); ?>">
                                            <input type="text" placeholder="Search Product..." class="search_keyword search_click" name="keyword" />
                                            <button>
                                                <i data-feather="search"></i>
                                            </button>
                                        </form>
                                        <div class="search_result"></div>
                                    </div>
                                    <div class="header-list-items">
                                        <ul>
                                            <li class="track_btn">
                                                <a href="<?php echo e(route('customer.order_track')); ?>"> <i class="fa fa-truck"></i>Track Order</a>
                                            </li>
                                           

                                            <li class="cart-dialog" id="cart-qty">
                                                <a href="<?php echo e(route('customer.checkout')); ?>">
                                                    <p class="margin-shopping">
                                                        <i class="fa-solid fa-cart-shopping"></i>
                                                        <span><?php echo e(Cart::instance('shopping')->count()); ?></span>
                                                    </p>
                                                </a>
                                                <div class="cshort-summary">
                                                    <ul>
                                                        <?php $__currentLoopData = Cart::instance('shopping')->content(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li>
                                                            <a href=""><img src="<?php echo e(asset($value->options->image)); ?>" alt="" /></a>
                                                        </li>
                                                        <li><a href=""><?php echo e(Str::limit($value->name, 30)); ?></a></li>
                                                        <li>Qty: <?php echo e($value->qty); ?></li>
                                                        <li>
                                                            <p>৳<?php echo e($value->price); ?></p>
                                                            <button class="remove-cart cart_remove" data-id="<?php echo e($value->rowId); ?>"><i data-feather="x"></i></button>
                                                        </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                    <p><strong>সর্বমোট : ৳<?php echo e($subtotal); ?></strong></p>
                                                    <a href="<?php echo e(route('customer.checkout')); ?>" class="go_cart"> অর্ডার করুন </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="menu-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="catagory_menu">
                                    <ul class="heder__category">
                                        <div>
                                            <li class="all__category__list">
                                                <a href="#">ALL CATEGORIES <i class="fa-solid fa-list"></i>
                                                </a>
                                                <?php if(Request::is('/')): ?>
                                                <div></div>
                                                <?php else: ?>
                                                <div class="sidebar-menu side__bar">
                                                    <ul class="hideshow">
                                                        <?php $__currentLoopData = $menucategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li>
                                                                <a href="<?php echo e(route('category', $category->slug)); ?>">
                                                                    <img src="<?php echo e(asset($category->image)); ?>" alt="" />
                                                                    <?php echo e($category->name); ?>

                                                                    <i class="fa-solid fa-chevron-right"></i>
                                                                </a>
                                                                <ul class="sidebar-submenu side__barsub">
                                                                    <?php $__currentLoopData = $category->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <li>
                                                                            <a href="<?php echo e(route('subcategory', $subcategory->slug)); ?>">
                                                                                <?php echo e($subcategory->subcategoryName); ?> <i
                                                                                    class="fa-solid fa-chevron-right"></i> </a>
                                                                            <ul class="sidebar-childmenu side__barchild">
                                                                                <?php $__currentLoopData = $subcategory->childcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $childcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                    <li>
                                                                                        <a href="<?php echo e(route('products', $childcat->slug)); ?>">
                                                                                            <?php echo e($childcat->childcategoryName); ?>

                                                                                        </a>
                                                                                    </li>
                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                            </ul>
                                                                        </li>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </ul>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </div>
                                                <?php endif; ?>
                                           </li> 
                                        </div>


                                        <div> <li><a href="<?php echo e(route('home')); ?>">Home</a></li></div>
                                        <?php if(($generalsetting?->vendor_enabled ?? 1) == 1): ?>
                                        <div><li><a href="<?php echo e(route('sellers')); ?>">Sellers</a></li></div>
                                        <?php endif; ?>
										

                                        <div class="contact__menu"><li><a href="<?php echo e(route('contact')); ?>">Contact</a></li></div>
                                       <div class="right__menu__top">
                                            <?php if(Auth::guard('customer')->user()): ?>
                                            <li class="for_order">
                                                <p>
                                                    <a href="<?php echo e(route('customer.account')); ?>">
                                                        <i class="fa-regular fa-user"></i>
                                                        <?php echo e(Str::limit(Auth::guard('customer')->user()->name,14)); ?>

                                                    </a>
                                                </p>
                                            </li>
                                            <?php elseif(($generalsetting?->vendor_enabled ?? 1) == 1 && Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('vendor')): ?>
                                            <li class="for_order">
                                                <p>
                                                    <a href="<?php echo e(route('vendor.dashboard')); ?>">
                                                        <i class="fa-solid fa-store"></i>
                                                   Vendor Panel
                                                    </a>
                                                </p>
                                            </li>
                                            <?php elseif(($generalsetting?->reseller_enabled ?? 1) == 1 && Auth::guard('admin')->check() && (Auth::guard('admin')->user()->hasRole('reseller') || (isset(Auth::guard('admin')->user()->role) && strtolower(Auth::guard('admin')->user()->role) === 'reseller'))): ?>
                                            <li class="for_order">
                                                <p>
                                                    <a href="<?php echo e(route('reseller.dashboard')); ?>">
                                                        <i class="fa-solid fa-handshake"></i>
                                                Dashboard
                                                    </a>
                                                </p>
                                            </li>
                                            <?php else: ?>
                                            <li class="for_order">
                                                <p>
                                                    <a href="<?php echo e(route('customer.login')); ?>">
                                                        <i class="fa-regular fa-user"></i>
                                                        Login / Sign Up
                                                    </a>
                                                </p>
                                            </li>
                                            <?php endif; ?>
                                       </div>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- main-header end -->
        </header>
        <div id="content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
            <!-- content end -->

<footer class="footer-v2">
    <div class="footer-v2__wave"></div>

    <div class="footer-v2__main">
        <div class="footer-v2__grid">
            <!-- Brand -->
            <div class="footer-v2__brand">
                <a href="<?php echo e(url('/')); ?>" class="footer-v2__logo">
                    <img src="<?php echo e(asset(optional($generalsetting)->white_logo ?? 'public/logo.png')); ?>" alt="<?php echo e(optional($generalsetting)->name ?? 'Logo'); ?>">
                </a>
                <p class="footer-v2__tagline">
                    <?php echo e(optional($generalsetting)->footer_about_text ?? 'আপনার ব্যবসার ডিজিটাল পার্টনার। আমরা বিশ্বাস করি গুণগত মান এবং গ্রাহক সন্তুষ্টিতে। প্রযুক্তির সাথে এগিয়ে চলুন আমাদের সাথে।'); ?>

                </p>
                <div class="footer-v2__apps">
                    <div class="footer-v2__apps-title">Download our app</div>
                    <div class="footer-v2__app-badges">
                        <a href="<?php echo e(optional($generalsetting)->google_play_link ?? '#'); ?>" target="_blank" rel="noopener">
                            <img src="/public/uploads/play.svg" alt="Google Play">
                        </a>
                        <a href="<?php echo e(optional($generalsetting)->app_store_link ?? '#'); ?>" target="_blank" rel="noopener">
                            <img src="/public/uploads/app.png" alt="App Store">
                        </a>
                    </div>
                </div>
            </div>

            <!-- Useful Link -->
            <div class="footer-v2__block">
                <h5 class="footer-v2__title">Useful Link</h5>
                <ul class="footer-v2__links">
                    <li><a href="<?php echo e(route('complaint')); ?>">Complaints</a></li>
                    <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e(route('page', ['slug' => $page->slug])); ?>"><?php echo e($page->name); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <!-- Link -->
            <div class="footer-v2__block">
                <h5 class="footer-v2__title">Link</h5>
                <ul class="footer-v2__links">
                    <?php $__currentLoopData = $pagesright; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e(route('page', ['slug' => $value->slug])); ?>"><?php echo e($value->name); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <!-- Newsletter + Social -->
            <div class="footer-v2__newsletter">
                <h5 class="footer-v2__title">Newsletter</h5>
                <p class="footer-v2__newsletter-desc">Subscribe for offers and updates.</p>
                <form action="<?php echo e(route('frontend.newsletter.subscribe')); ?>" method="POST" class="footer-v2__form">
                    <?php echo csrf_field(); ?>
                    <input type="email" name="email" placeholder="Your email..." required>
                    <button type="submit"><i class="fas fa-paper-plane"></i> Subscribe</button>
                </form>
                <div class="footer-v2__social-title">Follow Us</div>
                <ul class="footer-v2__social-list">
                    <?php $__currentLoopData = $socialicons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e($value->link); ?>" target="_blank" rel="noopener" aria-label="Social"><i class="<?php echo e($value->icon); ?>"></i></a>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-v2__bottom">
        <div class="footer-v2__copy-wrap">
            <span class="footer-v2__copy-text">&copy; <?php echo e(date('Y')); ?> <strong><?php echo e(optional($generalsetting)->name ?? config('app.name')); ?></strong>. All rights reserved</span>
            <span class="footer-v2__copy-sep">|</span>
            <span class="footer-v2__designer">
                Designed by <a href="https://shataj.com" target="_blank" rel="noopener" class="footer-v2__designer-link"><img src="/public/uploads/logo.png" alt="">Shataj Soft </a>
            </span>
        </div>
    </div>
</footer>

        
        <a href="javascript:void(0)" class="floating-cart-widget" id="floatingCartBtn" title="কার্ট দেখুন">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="floating-cart-badge mobilecart-qty"><?php echo e(Cart::instance('shopping')->count()); ?></span>
        </a>

        
        <div id="sidebarCartOverlay" class="sidebar-cart-overlay" onclick="closeSidebarCart()"></div>
        <div id="sidebarCartDrawer" class="sidebar-cart-drawer">
            <div id="sidebarCartContent">
                
            </div>
        </div>

<div class="mobile_bottom_nav">
    <div class="nav_container">
        <a href="javascript:void(0)" class="nav_item toggle">
            <div class="icon_box">
                <i class="fa-solid fa-bars"></i>
            </div>
            <span class="nav_text">Category</span>
        </a>

        <a href="<?php echo e(route('customer.order_track')); ?>" class="nav_item <?php echo e(Route::is('customer.order_track') ? 'active' : ''); ?>">
            <div class="icon_box">
                <i class="fa fa-truck"></i>
            </div>
            <span class="nav_text">Tracking</span>
        </a>

        <div class="nav_item home_wrapper">
            <a href="<?php echo e(route('home')); ?>" class="home_fab <?php echo e(Route::is('home') ? 'active' : ''); ?>">
                <i class="fa-solid fa-house"></i>
            </a>
        </div>

        <a href="<?php echo e(route('customer.checkout')); ?>" class="nav_item <?php echo e(Route::is('customer.checkout') ? 'active' : ''); ?>">
            <div class="icon_box">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart_badge mobilecart-qty"><?php echo e(Cart::instance('shopping')->count()); ?></span>
            </div>
            <span class="nav_text">Cart</span>
        </a>

        <?php if(Auth::guard('customer')->user()): ?>
            <a href="<?php echo e(route('customer.account')); ?>" class="nav_item <?php echo e(Route::is('customer.account') ? 'active' : ''); ?>">
                <div class="icon_box">
                    <i class="fa-solid fa-user"></i>
                </div>
                <span class="nav_text">Account</span>
            </a>
        <?php elseif(($generalsetting?->vendor_enabled ?? 1) == 1 && Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('vendor')): ?>
            <a href="<?php echo e(route('vendor.dashboard')); ?>" class="nav_item">
                <div class="icon_box">
                    <i class="fa-solid fa-store"></i>
                </div>
                <span class="nav_text">Vendor</span>
            </a>
        <?php elseif(($generalsetting?->reseller_enabled ?? 1) == 1 && Auth::guard('admin')->check() && (Auth::guard('admin')->user()->hasRole('reseller') || (isset(Auth::guard('admin')->user()->role) && strtolower(Auth::guard('admin')->user()->role) === 'reseller'))): ?>
            <a href="<?php echo e(route('reseller.dashboard')); ?>" class="nav_item">
                <div class="icon_box">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <span class="nav_text">Reseller</span>
            </a>
        <?php else: ?>
            <a href="<?php echo e(route('customer.login')); ?>" class="nav_item <?php echo e(Route::is('customer.login') ? 'active' : ''); ?>">
                <div class="icon_box">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </div>
                <span class="nav_text">Login</span>
            </a>
        <?php endif; ?>
    </div>
</div>
<style>
/* --- Mobile Bottom Navigation Styles --- */
.mobile_bottom_nav {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #ffffff;
    box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    padding: 10px 0;
    border-radius: 20px 20px 0 0; /* উপরের কোনা গুলো একটু গোল হবে */
    display: none; /* ডেস্কটপে হাইড থাকবে */
}

/* শুধুমাত্র মোবাইলে দেখানোর জন্য */
@media (max-width: 768px) {
    .mobile_bottom_nav {
        display: block;
    }
}

.nav_container {
    display: flex;
    justify-content: space-around;
    align-items: flex-end; /* আইটেমগুলো নিচে সমান থাকবে */
    position: relative;
    padding: 0 10px;
}

/* সাধারণ মেনু আইটেম */
.nav_item {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #6c757d; /* ডিফল্ট কালার */
    font-size: 12px;
    transition: all 0.3s ease;
    width: 20%;
}

.icon_box {
    position: relative;
    font-size: 20px;
    margin-bottom: 4px;
    transition: transform 0.2s;
}

.nav_text {
    font-weight: 500;
}

/* হোভার এবং একটিভ কালার */
.nav_item:hover, .nav_item.active {
    color: #FF6600; /* আপনার ব্র্যান্ড কালার এখানে দিন */
}

.nav_item.active .icon_box {
    transform: translateY(-3px); /* একটিভ হলে একটু উপরে উঠবে */
}

/* --- Center Floating Home Button --- */
.home_wrapper {
    position: relative;
    bottom: 25px; /* স্বাভাবিকের চেয়ে উপরে থাকবে */
}

.home_fab {
    width: 60px;
    height: 60px;
    background: <?php echo e($generalsetting->primary_color); ?>; /* ব্র্যান্ড কালার */
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    font-size: 24px;
    box-shadow: 0 8px 15px rgba(255, 102, 0, 0.4);
    border: 4px solid #fff; /* সাদা বর্ডার */
    transition: transform 0.3s ease;
}

.home_fab:hover {
    transform: scale(1.1); /* হোভারে বড় হবে */
    color: #fff;
}

/* --- Cart Badge Style --- */
.cart_badge {
    position: absolute;
    top: -8px;
    right: -10px;
    background: #ff0000;
    color: #fff;
    font-size: 10px;
    font-weight: bold;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 2px solid #fff;
}
</style>

<?php echo $__env->make('frontEnd.layouts.partials.gemini_customer_chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<style>
.floating-cart-widget {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    width: 52px;
    height: 70px;
    background: <?php echo e(optional($generalsetting)->primary_color ?? '#007bff'); ?>;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px 0 0 12px;
    box-shadow: -3px 0 15px rgba(0,123,255,0.4);
    z-index: 9998;
    text-decoration: none;
    transition: all 0.3s ease;
}
.floating-cart-widget:hover { color: #fff; width: 56px; }
.floating-cart-widget i { font-size: 24px; }
.floating-cart-badge {
    position: absolute;
    top: -6px;
    left: 50%;
    transform: translateX(-50%);
    min-width: 22px;
    height: 22px;
    background: #fff;
    color: <?php echo e(optional($generalsetting)->primary_color ?? '#007bff'); ?>;
    font-size: 11px;
    font-weight: bold;
    border-radius: 50%;
    border: 2px solid <?php echo e(optional($generalsetting)->primary_color ?? '#007bff'); ?>;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
}
@media (max-width: 768px) {
    .floating-cart-widget { top: 35%; width: 48px; height: 60px; z-index: 9999; }
    .floating-cart-widget i { font-size: 20px; }
    .floating-cart-badge { min-width: 20px; height: 20px; font-size: 10px; }
}
.sidebar-cart-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 10010;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
}
.sidebar-cart-overlay.active { opacity: 1; visibility: visible; }
.sidebar-cart-drawer {
    position: fixed;
    top: 0; right: 0;
    width: 380px;
    max-width: 95vw;
    height: 100%;
    height: 100dvh;
    background: #fff;
    z-index: 10011;
    transform: translateX(100%);
    transition: transform 0.35s ease;
    box-shadow: -5px 0 25px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.sidebar-cart-drawer.active { transform: translateX(0); }
#sidebarCartContent { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
.sidebar-cart-header {
    background: <?php echo e(optional($generalsetting)->primary_color ?? '#007bff'); ?>;
    color: #fff;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}
.sidebar-cart-close { background: transparent; border: none; color: #fff; font-size: 22px; cursor: pointer; padding: 4px; line-height: 1; }
.sidebar-cart-title { font-size: 20px; font-weight: 700; margin: 0; flex: 1; }
.sidebar-cart-body { flex: 0 1 auto; min-height: 0; overflow-y: auto; padding: 16px; background: #f8f9fa; }
.sidebar-cart-item { display: flex; gap: 12px; padding: 12px; background: #fff; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
.sidebar-cart-item-img { width: 70px; height: 85px; flex-shrink: 0; border-radius: 6px; overflow: hidden; }
.sidebar-cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
.sidebar-cart-item-details { flex: 1; min-width: 0; position: relative; }
.sidebar-cart-item-title { font-weight: 600; color: #222; text-decoration: none; display: block; margin-bottom: 4px; font-size: 14px; line-height: 1.3; }
.sidebar-cart-item-title:hover { color: <?php echo e(optional($generalsetting)->primary_color ?? '#007bff'); ?>; }
.sidebar-cart-item-price { font-size: 13px; color: #444; margin: 0 0 4px 0; }
.sidebar-cart-item-savings { font-size: 12px; color: #28a745; font-weight: 500; margin: 0 0 8px 0; }
.sidebar-cart-item-remove { position: absolute; bottom: 0; right: 0; background: none; border: none; color: <?php echo e(optional($generalsetting)->primary_color ?? '#007bff'); ?>; cursor: pointer; padding: 4px; font-size: 14px; }
.sidebar-cart-qty { display: flex; align-items: center; margin: 8px 0 6px 0; width: fit-content; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; }
.sidebar-qty-btn { width: 28px; height: 28px; border: none; background: #f0f0f0; color: #333; font-size: 18px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
.sidebar-qty-btn:hover { background: <?php echo e(optional($generalsetting)->primary_color ?? '#007bff'); ?>; color: #fff; }
.sidebar-qty-num { min-width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; background: #fff; }
.sidebar-cart-empty { text-align: center; padding: 40px 20px; color: #888; }
.sidebar-cart-empty i { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
.sidebar-cart-footer { padding: 16px 20px; border-top: 1px solid #eee; background: #fff; flex-shrink: 0; }
.sidebar-cart-total { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 12px; }
.sidebar-cart-total-label { font-size: 14px; color: #666; }
.sidebar-cart-total-amount { font-size: 20px; font-weight: 700; color: #222; }
.sidebar-cart-checkout-btn {
    display: block; width: 100%; padding: 14px 24px;
    background: <?php echo e(optional($generalsetting)->primary_color ?? '#007bff'); ?>;
    color: #fff !important; text-align: center; font-weight: 600; font-size: 16px;
    border-radius: 6px; text-decoration: none; transition: opacity 0.2s;
}
.sidebar-cart-checkout-btn:hover { opacity: 0.9; color: #fff !important; }
@media (max-width: 768px) {
    .sidebar-cart-drawer { width: 100%; max-width: 100%; }
    .sidebar-cart-item-img { width: 60px; height: 72px; }
    .sidebar-qty-btn { width: 36px; height: 36px; }
    .sidebar-qty-num { min-width: 36px; height: 36px; }
}

/* Fly to cart animation */
.fly-to-cart-img {
    position: fixed;
    z-index: 99999;
    pointer-events: none;
    border-radius: 8px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.35);
    object-fit: cover;
    border: 2px solid #fff;
}
@keyframes cartBump {
    0% { transform: scale(1); }
    40% { transform: scale(1.25); }
    70% { transform: scale(0.95); }
    100% { transform: scale(1); }
}
.cart-bump-animate {
    animation: cartBump 0.45s ease-out;
}
.floating-cart-widget, .menu-bag a, .mobile_bottom_nav .nav_item .icon_box {
    transform-origin: center center;
}
</style>

<script>
/* Sidebar Cart - খোলা/বন্ধ ও রিফ্রেশ */
function openSidebarCart() {
    document.getElementById("sidebarCartOverlay").classList.add("active");
    document.getElementById("sidebarCartDrawer").classList.add("active");
    document.body.style.overflow = "hidden";
    sidebarCartRefresh();
}
function closeSidebarCart() {
    document.getElementById("sidebarCartOverlay").classList.remove("active");
    document.getElementById("sidebarCartDrawer").classList.remove("active");
    document.body.style.overflow = "";
}
function sidebarCartRefresh() {
    $.get("<?php echo e(route('cart.sidebar')); ?>", function(html) {
        $("#sidebarCartContent").html(html);
        if (typeof feather !== "undefined") feather.replace();
    });
}
document.getElementById("floatingCartBtn")?.addEventListener("click", function(e) {
    e.preventDefault();
    openSidebarCart();
});
document.getElementById("sidebarCartOverlay")?.addEventListener("click", closeSidebarCart);
</script>


        <!-- /. fixed sidebar -->

        <div id="custom-modal"></div>
        <div id="page-overlay"></div>
        <div id="loading"><div class="custom-loader"></div></div>
        <script>document.addEventListener('DOMContentLoaded',function(){var e=document.getElementById('loading');if(e)e.style.display='none';});</script>

        <script src="<?php echo e(asset('public/frontEnd/js/jquery-3.6.3.min.js')); ?>"></script>
        <script>
            $(function() {
                $("#loading").hide();
                $(window).on("load", function() { $("#loading").hide(); });
                setTimeout(function() { $("#loading").hide(); }, 3000);
            });
        </script>
        <script src="<?php echo e(asset('public/frontEnd/js/bootstrap.min.js')); ?>" defer></script>
        <script src="<?php echo e(asset('public/frontEnd/js/owl.carousel.min.js')); ?>" defer></script>
        <script src="<?php echo e(asset('public/frontEnd/js/mobile-menu.js')); ?>" defer></script>
        <script src="<?php echo e(asset('public/frontEnd/js/wsit-menu.js')); ?>" defer></script>
        <script src="<?php echo e(asset('public/frontEnd/js/mobile-menu-init.js')); ?>" defer></script>
        <script src="<?php echo e(asset('public/frontEnd/js/wow.min.js')); ?>" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof WOW !== 'undefined') { new WOW().init(); }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>

        <!-- feather icon -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof feather !== 'undefined') { feather.replace(); }
            });
        </script>
        <script src="<?php echo e(asset('public/backEnd/')); ?>/assets/js/toastr.min.js" defer></script>
        <?php echo Toastr::message(); ?> <?php echo $__env->yieldPushContent('script'); ?>
		
		
		<script>
    $(document).ready(function() {
        $(".main_slider").owlCarousel({
            items: 1,
            loop: true,
            dots: false,
            autoplay: true,
            nav: true,
            autoplayHoverPause: false,
            margin: 0,
            mouseDrag: true,
            smartSpeed: 8000,
            autoplayTimeout: 3000,
            animateOut: "fadeOutDown",
            animateIn: "slideInDown",

            navText: ["<i class='fa-solid fa-angle-left'></i>",
                "<i class='fa-solid fa-angle-right'></i>"
            ],
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".hotdeals-slider").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: true,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: true,
                    loop: false,
                },
            },
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".category-slider").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    nav: true,
                },
                600: {
                    items: 5,
                    nav: false,
                },
                1000: {
                    items: 8,
                    nav: true,
                    loop: false,
                },
            },
        });

        $(".product_slider").owlCarousel({
            margin: 15,
            items: 6,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                600: {
                    items: 5,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: false,
                },
            },
        });
		$(".customer-review").owlCarousel({
            margin: 8,
            items: 6,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                600: {
                    items: 3,
                    nav: false,
                },
                1000: {
                    items: 5,
                    nav: false,
                },
            },
        });
    });
</script>
		
        <script>
            $(".quick_view").on("click", function () {
                var id = $(this).data("id");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "<?php echo e(route('quickview')); ?>",
                        success: function (data) {
                            if (data) {
                                $("#custom-modal").html(data);
                                $("#custom-modal").show();
                                $("#loading").hide();
                                $("#page-overlay").show();
                            }
                        },
                    });
                }
            });
        </script>
        <!-- quick view end -->
        <!-- cart js start -->
        <script>
            function runFlyToCart($sourceEl, onComplete) {
                var $flyImg;
                if ($sourceEl && $sourceEl.closest && $sourceEl.closest('.variant-modal-content').length)
                    $flyImg = $sourceEl.closest('.variant-modal-content').find('.variant-modal-img img').first();
                if (!$flyImg || !$flyImg.length)
                    $flyImg = $sourceEl.closest('.product_item, .wist_item, .search-item, .quick-product, .product-section, .main-details-page').find('.pro_img img, .quick-product-img img, .details_slider img, .block__pic, .dimage_item img').first();
                if (!$flyImg || !$flyImg.length) $flyImg = $('.details_slider img, .block__pic, #details_slider_main img').first();
                if (!$flyImg || !$flyImg.length) { if (typeof onComplete === 'function') onComplete(); return; }
                var rect = $flyImg[0].getBoundingClientRect();
                var $clone = $flyImg.clone().addClass('fly-to-cart-img').css({
                    position: 'fixed', width: 90, height: 110,
                    left: rect.left, top: rect.top, margin: 0, padding: 0, zIndex: 99999
                }).appendTo('body');
                var $target = $('#floatingCartBtn, .floating-cart-widget').first();
                if (!$target.length || !$target.is(':visible')) $target = $('.mobile_bottom_nav .cart_badge').closest('a').first();
                if (!$target.length) $target = $('.menu-bag a').first();
                var destRect = $target.length && $target.is(':visible') ? $target[0].getBoundingClientRect() : { left: $(window).width() - 60, top: $(window).height() / 2 - 40 };
                var endW = 36, endH = 44;
                var endLeft = destRect.left + ($target.length ? (destRect.width || 0) / 2 - endW / 2 : 0);
                var endTop = destRect.top + ($target.length ? (destRect.height || 0) / 2 - endH / 2 : 0);
                var midLeft = (rect.left + endLeft) / 2 - 20;
                var midTop = Math.min(rect.top, endTop) - 100;
                $clone.animate({ left: midLeft, top: midTop, width: 70, height: 85, opacity: 1 }, 300, 'swing', function() {
                    $(this).animate({ left: endLeft, top: endTop, width: endW, height: endH, opacity: 0.6 }, 350, 'swing', function() {
                        $clone.remove();
                        if ($target && $target.length) { $target.addClass('cart-bump-animate'); setTimeout(function() { $target.removeClass('cart-bump-animate'); }, 450); }
                        if (typeof onComplete === 'function') onComplete();
                    });
                });
            }
            $(document).on("click", ".addcartbutton", function (e) {
                var $btn = $(this);
                var id = $btn.data("id");
                var checkout = $btn.data("checkout");
                var qty = 1;
                if (id) {
                    e.preventDefault();
                    $.ajax({
                        cache: "false",
                        type: "GET",
                        url: "<?php echo e(url('add-to-cart')); ?>/" + id + "/" + qty,
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                toastr.success('Success', 'Product add to cart successfully');
                                cart_count();
                                mobile_cart();
                                if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                                runFlyToCart($btn, function() { if (typeof openSidebarCart === "function") openSidebarCart(); });
                            }
                        },
                    });
                }
                if(checkout){
                    window.location.href = '<?php echo e(route('customer.checkout')); ?>'; 
                }
            });
            $(document).on("click", ".cart_store", function (e) {
                var $btn = $(this);
                var $form = $btn.closest('form');
                if (!$form.length) return;
                var id = $btn.data("id") || $form.find("input[name=id]").val();
                if (!id) return;
                e.preventDefault();
                $form.addClass('cart-ajax-submit');
                $.ajax({
                    type: "POST",
                    data: $form.serialize(),
                    url: $form.attr('action'),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    dataType: "json",
                    success: function (data) {
                        if (data && data.success) {
                            toastr.success('Success', 'Product add to cart successfully');
                            cart_count();
                            mobile_cart();
                            if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                            var isOrderNow = $btn.is('[name="order_now"]') || $btn.hasClass('order_now_btn');
                            if (isOrderNow) {
                                window.location.href = '<?php echo e(route('customer.checkout')); ?>';
                                return;
                            }
                            runFlyToCart($btn, function() { if (typeof openSidebarCart === "function") openSidebarCart(); });
                        } else {
                            toastr.error(data && data.message ? data.message : 'Failed');
                        }
                    },
                    error: function(xhr) {
                        try {
                            var d = xhr.responseJSON;
                            if (d && !d.success) {
                                toastr.error(d.message || 'Failed');
                                return;
                            }
                        } catch(e) {}
                        $form.submit();
                    },
                    complete: function() { $form.removeClass('cart-ajax-submit'); }
                });
            });

            $(document).on("click", ".cart_remove", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "<?php echo e(route('cart.remove')); ?>",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                cart_count();
                                mobile_cart();
                                cart_summary();
                                if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                            }
                        },
                    });
                }
            });

            $(document).on("click", ".cart_increment", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "<?php echo e(route('cart.increment')); ?>",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                cart_count();
                                mobile_cart();
                                if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                            }
                        },
                    });
                }
            });

            $(document).on("click", ".cart_decrement", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "<?php echo e(route('cart.decrement')); ?>",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                cart_count();
                                mobile_cart();
                                if (typeof sidebarCartRefresh === "function") sidebarCartRefresh();
                            }
                        },
                    });
                }
            });

            function cart_count() {
                $.ajax({
                    type: "GET",
                    url: "<?php echo e(route('cart.count')); ?>",
                    success: function (data) {
                        if (data) {
                            $("#cart-qty").html(data);
                        } else {
                            $("#cart-qty").empty();
                        }
                    },
                });
            }
            function mobile_cart() {
                $.ajax({
                    type: "GET",
                    url: "<?php echo e(route('mobile.cart.count')); ?>",
                    success: function (data) {
                        if (data) {
                            $(".mobilecart-qty").html(data);
                        } else {
                            $(".mobilecart-qty").empty();
                        }
                    },
                });
            }
            function cart_summary() {
                $.ajax({
                    type: "GET",
                    url: "<?php echo e(route('shipping.charge')); ?>",
                    dataType: "html",
                    success: function (response) {
                        $(".cart-summary").html(response);
                    },
                });
            }
        </script>
        <!-- cart js end -->
        <script>
            $(".search_click").on("keyup change", function () {
                var keyword = $(".search_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "<?php echo e(route('livesearch')); ?>",
                    success: function (products) {
                        if (products) {
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
            $(".msearch_click").on("keyup change", function () {
                var keyword = $(".msearch_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "<?php echo e(route('livesearch')); ?>",
                    success: function (products) {
                        if (products) {
                            $("#loading").hide();
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
        </script>
        <!-- search js start -->
        <script></script>
        <script></script>
        <script>
            $(".district").on("change", function () {
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    data: { id: id },
                    url: "<?php echo e(route('districts')); ?>",
                    success: function (res) {
                        if (res) {
                            $(".area").empty();
                            $(".area").append('<option value="">Select..</option>');
                            $.each(res, function (key, value) {
                                $(".area").append('<option value="' + key + '" >' + value + "</option>");
                            });
                        } else {
                            $(".area").empty();
                        }
                    },
                });
            });
        </script>
        <script>
            $(".toggle").on("click", function () {
                $("#page-overlay").show();
                $(".mobile-menu").addClass("active");
            });

            $("#page-overlay").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
                $(".feature-products").removeClass("active");
            });

            $(".mobile-menu-close").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
            });

            $(".mobile-filter-toggle").on("click", function () {
                $("#page-overlay").show();
                $(".feature-products").addClass("active");
            });
        </script>
        <script>
            $(document).ready(function () {
                $(".parent-category").each(function () {
                    const menuCatToggle = $(this).find(".menu-category-toggle");
                    const secondNav = $(this).find(".second-nav");

                    menuCatToggle.on("click", function () {
                        menuCatToggle.toggleClass("active");
                        secondNav.slideToggle("fast");
                        $(this).closest(".parent-category").toggleClass("active");
                    });
                });
                $(".parent-subcategory").each(function () {
                    const menuSubcatToggle = $(this).find(".menu-subcategory-toggle");
                    const thirdNav = $(this).find(".third-nav");

                    menuSubcatToggle.on("click", function () {
                        menuSubcatToggle.toggleClass("active");
                        thirdNav.slideToggle("fast");
                        $(this).closest(".parent-subcategory").toggleClass("active");
                    });
                });
            });
        </script>

        <script>
            var menu = new MmenuLight(document.querySelector("#menu"), "all");

            var navigator = menu.navigation({
                selectedClass: "Selected",
                slidingSubmenus: true,
                // theme: 'dark',
                title: "ক্যাটাগরি",
            });

            var drawer = menu.offcanvas({
                // position: 'left'
            });

            //  Open the menu.
            document.querySelector('a[href="#menu"]').addEventListener("click", (evnt) => {
                evnt.preventDefault();
                drawer.open();
            });
        </script>

        <script>
            // document.addEventListener("DOMContentLoaded", function () {
            //     window.addEventListener("scroll", function () {
            //         if (window.scrollY > 200) {
            //             document.getElementById("navbar_top").classList.add("fixed-top");
            //         } else {
            //             document.getElementById("navbar_top").classList.remove("fixed-top");
            //             document.body.style.paddingTop = "0";
            //         }
            //     });
            // });
            /*=== Main Menu Fixed === */
            // document.addEventListener("DOMContentLoaded", function () {
            //     window.addEventListener("scroll", function () {
            //         if (window.scrollY > 0) {
            //             document.getElementById("m_navbar_top").classList.add("fixed-top");
            //             // add padding top to show content behind navbar
            //             navbar_height = document.querySelector(".navbar").offsetHeight;
            //             document.body.style.paddingTop = navbar_height + "px";
            //         } else {
            //             document.getElementById("m_navbar_top").classList.remove("fixed-top");
            //             // remove padding top from body
            //             document.body.style.paddingTop = "0";
            //         }
            //     });
            // });
            /*=== Main Menu Fixed === */

            $(window).scroll(function () {
                if ($(this).scrollTop() > 50) {
                    $(".scrolltop:hidden").stop(true, true).fadeIn();
                } else {
                    $(".scrolltop").stop(true, true).fadeOut();
                }
            });
            $(function () {
                $(".scroll").click(function () {
                    $("html,body").animate({ scrollTop: $(".gotop").offset().top }, "1000");
                    return false;
                });
            });
        </script>
        <script>
            $(".filter_btn").click(function(){
               $(".filter_sidebar").addClass('active');
               $("body").css("overflow-y", "hidden");
            })
            $(".filter_close").click(function(){
               $(".filter_sidebar").removeClass('active');
               $("body").css("overflow-y", "auto");
            })
        </script>
        
        
        <?php
    $popup = App\Models\Popup::where('status', 1)->latest()->first();
?>

<?php if($popup): ?>
<?php
    $isSimpleImagePopup = empty(trim($popup->description ?? '')) && empty(trim($popup->btn_text ?? '')) && empty(trim($popup->offer_end_text ?? ''));
?>
<div class="modal fade" id="popShopModal" tabindex="-1" aria-hidden="true" style="z-index: 10000;">
    <div class="modal-dialog modal-dialog-centered popup-modal-compact <?php echo e($isSimpleImagePopup ? 'popup-simple-fit' : ''); ?>">
        <div class="modal-content ps-content <?php echo e($isSimpleImagePopup ? 'popup-simple-image' : ''); ?>">
            <button type="button" class="ps-close" data-bs-dismiss="modal" aria-label="বন্ধ">&times;</button>
            
            <?php if($isSimpleImagePopup): ?>
                
                <a href="<?php echo e(!empty(trim($popup->link ?? '')) ? $popup->link : 'javascript:void(0)'); ?>" <?php echo e(!empty(trim($popup->link ?? '')) ? 'target="_blank"' : ''); ?> class="popup-simple-link">
                    <img src="<?php echo e(url('public/'.$popup->image)); ?>" alt="<?php echo e($popup->title); ?>" class="popup-simple-img">
                </a>
            <?php else: ?>
                
                <div class="ps-layout">
                    <div class="ps-text-section">
                        <h3 class="ps-brand"><?php echo e($popup->title); ?></h3>
                        <div class="ps-headline">
                            <p><?php echo nl2br(e($popup->description)); ?></p>
                        </div>
                        <?php if($popup->offer_end_text): ?>
                        <p class="ps-deadline"><?php echo e($popup->offer_end_text); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo e($popup->link ?? '#'); ?>" class="ps-btn">
                            <?php echo e($popup->btn_text ?? 'Shop the Sale'); ?>

                        </a>
                        <div class="ps-footer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                              <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                            </svg>
                            <span>POWERED BY <strong><?php echo e($generalsetting->name ?? 'CommerceGurus'); ?></strong></span>
                        </div>
                    </div>
                    <div class="ps-image-section">
                        <img src="<?php echo e(url('public/'.$popup->image)); ?>" alt="Offer Image">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    #popShopModal .modal-dialog.popup-modal-compact:not(.popup-simple-fit) {
        max-width: min(620px, 92vw) !important;
        margin-left: auto;
        margin-right: auto;
    }
    /* শুধু ইমেজ পপআপ: বক্স টানা না রেখে ছবির আসল লেআউটে ফিট — সাদা স্ট্রাইপ কমে */
    #popShopModal .modal-dialog.popup-modal-compact.popup-simple-fit {
        width: fit-content !important;
        max-width: min(560px, calc(100vw - 24px)) !important;
        margin-left: auto;
        margin-right: auto;
    }

    #popShopModal .modal-content.ps-content.popup-simple-image {
        background-color: transparent !important;
        box-shadow: none !important;
    }
    /* শুধু ইমেজ পপআপ: সাদা ফ্রেম + ছায়া (overflow hidden স্ট্রোক কাটে না) */
    #popShopModal .modal-content.popup-simple-image .popup-simple-link {
        display: block;
        padding: 5px;
        background: rgba(255, 255, 255, 0.96);
        border-radius: 16px;
        box-shadow:
            0 0 0 1px rgba(255, 255, 255, 0.55),
            0 12px 40px rgba(0, 0, 0, 0.24);
        overflow: visible;
        line-height: 0;
    }

    #popShopModal .modal-content.ps-content {
        border: none !important;
        outline: none !important;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.14);
    }
    #popShopModal .modal-content.ps-content:focus,
    #popShopModal .modal-content.ps-content:focus-visible {
        outline: none !important;
    }

    .ps-content {
        border: none !important;
        border-radius: 12px;
        overflow: hidden;
        background-color: #fff;
        max-width: 100%;
        margin: 0 auto;
    }

    .ps-layout {
        display: flex;
        flex-direction: row;
        min-height: 340px;
    }

    .ps-text-section {
        width: 50%;
        padding: 34px 28px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
        position: relative;
    }

    .ps-brand {
        color: #b93a3a;
        font-family: Georgia, 'Times New Roman', serif;
        font-weight: 700;
        font-size: 32px;
        margin-bottom: 12px;
        line-height: 1;
    }

    .ps-headline {
        color: #222;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.35;
        margin-bottom: 12px;
    }

    .ps-headline span, .ps-headline p {
        font-weight: 400;
        font-size: 14px;
        color: #555;
        margin-top: 10px;
    }

    .ps-deadline {
        color: #888;
        font-size: 13px;
        margin-bottom: 22px;
    }

    .ps-btn {
        background-color: #2c3e50;
        color: #fff !important;
        text-decoration: none;
        padding: 12px 22px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        border-radius: 2px;
        display: block;
        width: 100%;
        transition: 0.3s;
        border: none;
    }
    .ps-btn:hover {
        background-color: #000;
    }

    .ps-footer {
        margin-top: 28px;
        font-size: 9px;
        color: #aaa;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
    }
    .ps-footer strong { color: #333; }

    .ps-image-section {
        width: 50%;
        position: relative;
        background: #ececec;
        padding: 5px;
        box-sizing: border-box;
    }
    .ps-image-section img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 2px;
        border: 3px solid rgba(255, 255, 255, 0.96);
        box-sizing: border-box;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.08);
    }

    .popup-simple-image {
        padding: 0;
        width: fit-content;
        max-width: min(560px, calc(100vw - 24px));
        margin: 0 auto;
        border-radius: 16px !important;
        overflow: visible !important;
        border: none !important;
        background: transparent;
    }
    .popup-simple-link {
        display: block;
        line-height: 0;
        text-decoration: none;
        border: none;
        outline: none;
        border-radius: inherit;
    }
    .popup-simple-link:focus,
    .popup-simple-link:focus-visible {
        outline: none !important;
    }
    .popup-simple-link[href="javascript:void(0)"] {
        cursor: default;
    }
    .popup-simple-img {
        width: auto;
        max-width: min(560px, calc(100vw - 24px));
        height: auto;
        max-height: min(80vh, 600px);
        object-fit: contain;
        display: block;
        border-radius: 12px !important;
        vertical-align: top;
        border: none;
        outline: none;
    }

    .ps-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #fff;
        border: none !important;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 22px;
        line-height: 30px;
        color: #333;
        cursor: pointer;
        z-index: 1050;
        box-shadow: none;
        transition: 0.2s;
        outline: none !important;
    }
    .ps-close:hover {
        color: #b93a3a;
        transform: scale(1.06);
        background: rgba(255, 255, 255, 0.95);
    }
    .ps-close:focus-visible {
        outline: 2px solid rgba(185, 58, 58, 0.35) !important;
        outline-offset: 2px;
    }

    @media (max-width: 768px) {
        #popShopModal .modal-dialog.popup-modal-compact:not(.popup-simple-fit) {
            max-width: min(540px, 94vw) !important;
            margin: 10px auto;
        }
        #popShopModal .modal-dialog.popup-simple-fit {
            max-width: calc(100vw - 20px) !important;
        }
        .popup-simple-image {
            max-width: calc(100vw - 20px);
        }
        .popup-simple-img {
            max-width: calc(100vw - 20px);
            max-height: min(78vh, 520px);
        }
        .ps-layout {
            flex-direction: column-reverse;
            min-height: 0;
        }
        .ps-text-section { width: 100%; padding: 24px 20px; }
        .ps-image-section { width: 100%; height: 200px; }
        .ps-brand { font-size: 26px; }
        .popup-simple-img {
            max-height: min(72vh, 440px);
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ৩ ঘন্টা পর পর দেখাবে
        const hoursToWait = 3;  
        // সাইটে ঢোকার ২ সেকেন্ড পর দেখাবে
        const delayInSeconds = 2; 

        const timeLimit = hoursToWait * 60 * 60 * 1000;
        const lastShown = localStorage.getItem('popupLastShown');
        const now = new Date().getTime();

        if (!lastShown || (now - lastShown > timeLimit)) {
            setTimeout(function() {
                // Try opening with jQuery (Standard for Laravel themes)
                if (typeof jQuery != 'undefined') {
                    $('#popShopModal').modal('show');
                } 
                // Try opening with Bootstrap 5
                else if (typeof bootstrap != 'undefined') {
                    var myModal = new bootstrap.Modal(document.getElementById('popShopModal'));
                    myModal.show();
                }

                localStorage.setItem('popupLastShown', now);
            }, delayInSeconds * 1000);
        }
    });
</script>
<?php endif; ?>

        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

<?php if(session('show_order_limit_modal')): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ডাইনামিক হোয়াটসঅ্যাপ নাম্বার (ডাটাবেস থেকে)
        var whatsappNumber = "<?php echo e($contact->whatsapp ?? $contact->hotline ?? '8801700000000'); ?>"; 
        
        Swal.fire({
            title: '', 
            html: `
                <div class="custom-modal-content">
                    <div class="modal-header-custom">
                        <div class="header-left">
                            <i class="fas fa-exclamation-triangle header-icon"></i>
                            <span>Duplicate Order Detective Alert</span>
                        </div>
                        <i class="fas fa-times close-icon" onclick="Swal.close()"></i>
                    </div>

                    <div class="modal-body-custom">
                        <p>
                            <img src="https://img.icons8.com/emoji/48/000000/warning-emoji.png" style="width: 20px; vertical-align: text-bottom;"> 
                            <b>সতর্কতা!</b> আপনি ইতিমধ্যে এই পণ্যটির জন্য অর্ডার দিয়েছেন। নির্দিষ্ট সময়ের মধ্যে একই পণ্যের পুনরায় অর্ডার দেওয়া অনুমোদিত নয়। 
                            👉 আপনি যদি সত্যিই আবার অর্ডার করতে চান, তাহলে নিচে দেওয়া WhatsApp নম্বরে যোগাযোগ করুন:
                        </p>
                    </div>

                    <div class="modal-footer-custom">
                        <a href="https://wa.me/${whatsappNumber}?text=আমি একই পণ্য পুনরায় অর্ডার করতে চাই, অনুগ্রহ করে সাহায্য করুন।" target="_blank" class="btn-whatsapp-custom">
                            <i class="fab fa-whatsapp"></i> CONTACT ON WHATSAPP
                        </a>
                        <button onclick="Swal.close()" class="btn-close-custom">Close</button>
                    </div>
                </div>
            `,
            showConfirmButton: false, // ডিফল্ট বাটন বন্ধ রাখা হয়েছে
            background: 'transparent', // ডিফল্ট ব্যাকগ্রাউন্ড রিমুভ
            customClass: {
                popup: 'swal-no-padding'
            },
            allowOutsideClick: false
        });
    });
</script>

<style>
    /* পপ-আপ কন্টেইনার রিসেট */
    .swal-no-padding {
        padding: 0 !important;
        background: none !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    /* মেইন বক্স ডিজাইন */
    .custom-modal-content {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        font-family: 'Arial', sans-serif;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        max-width: 500px;
        margin: 0 auto;
    }

    /* লাল হেডার (ছবির মতো হুবহু) */
    .modal-header-custom {
        background-color: #b91c1c; /* গাঢ় লাল */
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        font-size: 18px;
        font-weight: bold;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-icon {
        color: #facc15; /* হলুদ আইকন */
        font-size: 20px;
    }

    .close-icon {
        cursor: pointer;
        opacity: 0.8;
        font-size: 20px;
        transition: 0.2s;
    }
    .close-icon:hover {
        opacity: 1;
    }

    /* বডি টেক্সট */
    .modal-body-custom {
        padding: 30px 25px;
        text-align: left;
        font-size: 15px;
        line-height: 1.6;
        color: #4b5563;
    }

    /* ফুটার এবং বাটন */
    .modal-footer-custom {
        padding: 0 25px 30px 25px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    /* হোয়াটসঅ্যাপ বাটন (সবুজ) */
    .btn-whatsapp-custom {
        background-color: #10b981;
        color: white !important;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: bold;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: background 0.3s;
        border: none;
    }
    .btn-whatsapp-custom:hover {
        background-color: #059669;
    }

    /* ক্লোজ বাটন (লাল) */
    .btn-close-custom {
        background-color: #dc2626;
        color: white;
        padding: 10px 30px;
        border-radius: 50px;
        font-weight: bold;
        font-size: 14px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: background 0.3s;
    }
    .btn-close-custom:hover {
        background-color: #b91c1c;
    }

    /* রেস্পন্সিভ ডিজাইন */
    @media (max-width: 450px) {
        .modal-footer-custom {
            flex-direction: column;
        }
        .btn-whatsapp-custom, .btn-close-custom {
            width: 100%;
            justify-content: center;
        }
    }
</style>
<?php endif; ?>

<?php $snxAccent = optional($generalsetting)->primary_color ?? '#e94560'; ?>

<style>
#snx-popup {
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 99999;
    width: 320px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-left: 4px solid <?php echo e($snxAccent); ?>;
    transform: translateX(-380px);
    opacity: 0;
    transition: transform 0.45s cubic-bezier(.34,1.56,.64,1), opacity 0.35s ease;
    pointer-events: none;
}
#snx-popup.snx-show {
    transform: translateX(0);
    opacity: 1;
    pointer-events: auto;
}
#snx-popup .snx-img {
    width: 58px;
    height: 58px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid #f0f0f0;
}
#snx-popup .snx-body { flex: 1; min-width: 0; }
#snx-popup .snx-title {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
#snx-popup .snx-title span { color: <?php echo e($snxAccent); ?>; }
#snx-popup .snx-product {
    font-size: 12px;
    color: #475569;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
#snx-popup .snx-time {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
#snx-popup .snx-close {
    position: absolute;
    top: 8px;
    right: 10px;
    font-size: 16px;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
    background: none;
    border: none;
    padding: 0;
}
#snx-popup .snx-close:hover { color: <?php echo e($snxAccent); ?>; }
#snx-popup .snx-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 10px;
    color: #64748b;
    background: #f1f5f9;
    border-radius: 20px;
    padding: 2px 7px;
    margin-top: 4px;
}
#snx-popup .snx-dot {
    width: 7px; height: 7px;
    background: #22c55e;
    border-radius: 50%;
    display: inline-block;
    animation: snx-pulse 1.5s infinite;
}
@keyframes snx-pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(1.4); }
}
@media (max-width: 480px) {
    #snx-popup { width: calc(100vw - 32px); left: 16px; bottom: 16px; }
}
</style>

<div id="snx-popup" role="alert" aria-live="polite">
    <button class="snx-close" id="snx-close-btn" aria-label="Close">✕</button>
    <img id="snx-img" class="snx-img" src="" alt="Product">
    <div class="snx-body">
        <div class="snx-title"><span id="snx-name"></span> just purchased</div>
        <div class="snx-product" id="snx-product"></div>
        <div class="snx-time">
            <span class="snx-dot"></span>
            <span id="snx-time"></span>
        </div>
        <div class="snx-badge">🛡️ Verified Order</div>
    </div>
</div>

<script>
(function(){
    var popup      = document.getElementById('snx-popup');
    if (!popup) return;

    var imgEl      = document.getElementById('snx-img');
    var nameEl     = document.getElementById('snx-name');
    var productEl  = document.getElementById('snx-product');
    var timeEl     = document.getElementById('snx-time');
    var closeBtn   = document.getElementById('snx-close-btn');

    var notifications = [];
    var currentIndex  = 0;
    var hideTimer, nextTimer, fetchTimer;
    var userClosed    = false;
    var SHOW_DURATION = 5000;
    var INT_MIN       = 8000;
    var INT_MAX       = 15000;

    closeBtn.addEventListener('click', function(){
        hidePopup();
        userClosed = true;
        clearTimeout(fetchTimer);
        fetchTimer = setTimeout(function(){ userClosed = false; showNext(); }, 120000);
    });

    function showPopup(item) {
        imgEl.src        = item.image || '<?php echo e(asset("public/frontEnd/images/default-product.png")); ?>';
        imgEl.onerror    = function(){ this.src = '<?php echo e(asset("public/frontEnd/images/default-product.png")); ?>'; };
        nameEl.textContent    = item.name;
        productEl.textContent = item.product_name;
        timeEl.textContent    = item.time;

        productEl.onclick = function(){
            if (item.product_url && item.product_url !== '#') {
                window.location.href = item.product_url;
            }
        };
        productEl.style.cursor = item.product_url && item.product_url !== '#' ? 'pointer' : 'default';

        popup.classList.add('snx-show');
        clearTimeout(hideTimer);
        hideTimer = setTimeout(hidePopup, SHOW_DURATION);
    }

    function hidePopup() {
        popup.classList.remove('snx-show');
    }

    function showNext() {
        if (userClosed || notifications.length === 0) return;
        var item = notifications[currentIndex % notifications.length];
        currentIndex++;
        showPopup(item);
        clearTimeout(nextTimer);
        var delay = INT_MIN + Math.floor(Math.random() * (INT_MAX - INT_MIN));
        nextTimer = setTimeout(showNext, delay);
    }

    function fetchAndStart() {
        fetch('<?php echo e(route("sales.notifications")); ?>')
            .then(function(r){ return r.json(); })
            .then(function(data){
                if (!data.enabled) return;
                var items = data.items || data;
                if (!Array.isArray(items) || items.length === 0) return;

                if (data.display_duration) SHOW_DURATION = data.display_duration;
                if (data.interval_min)     INT_MIN       = data.interval_min;
                if (data.interval_max)     INT_MAX       = data.interval_max;

                notifications = items.sort(function(){ return Math.random() - 0.5; });
                currentIndex  = 0;
                clearTimeout(nextTimer);
                nextTimer = setTimeout(showNext, 3000);
            })
            .catch(function(){});
    }

    function scheduleFetch() {
        if ('requestIdleCallback' in window) {
            requestIdleCallback(fetchAndStart, { timeout: 10000 });
        } else {
            setTimeout(fetchAndStart, 8000);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleFetch);
    } else {
        scheduleFetch();
    }

    setInterval(fetchAndStart, 300000);
})();
</script>

<?php echo $__env->make('frontEnd.layouts.partials.deferred-tracking', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </body>
</html>
<?php /**PATH D:\laragon\www\ecom1\resources\views/frontEnd/layouts/master.blade.php ENDPATH**/ ?>