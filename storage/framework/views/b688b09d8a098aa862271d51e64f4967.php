<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo e($generalsetting->name); ?></title>
        <link rel="shortcut icon" href="<?php echo e(asset($generalsetting->favicon)); ?>" type="image/x-icon" />
        <!-- fot awesome -->
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/campaign/css')); ?>/all.css" />
        <!-- core css -->
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/campaign/css')); ?>/bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/campaign/css')); ?>/animate.css" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/campaign/css')); ?>/owl.theme.default.css" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/campaign/css')); ?>/owl.carousel.min.css" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/campaign/css')); ?>/select2.min.css" />
        <!-- common css -->
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/campaign/css')); ?>/style.css" />
        <link rel="stylesheet" href="<?php echo e(asset('public/frontEnd/campaign/css')); ?>/responsive.css" />
        <!-- ========== DataLayer Initialization ========== -->
        <?php
            $camp_name      = strip_tags($campaign_data->name ?? '');
            $camp_slug      = $campaign_data->slug ?? '';
            $camp_id        = (string) $campaign_data->id;
            $_firstProd     = $products->first();
            $camp_value     = $_firstProd ? (float) $_firstProd->new_price : 0.0;
            $camp_products  = $products->map(function($p) {
                return [
                    'id'        => (string) $p->id,
                    'name'      => strip_tags($p->name ?? ''),
                    'price'     => (float)  $p->new_price,
                    'old_price' => (float)  $p->old_price,
                ];
            })->values();
            $_camp_idx      = 0;
            $camp_items_gtm = $products->map(function($p) use (&$_camp_idx) {
                return [
                    'item_id'   => (string) $p->id,
                    'item_name' => strip_tags($p->name ?? ''),
                    'price'     => (float)  $p->new_price,
                    'index'     => $_camp_idx++,
                    'quantity'  => 1,
                ];
            })->values();
        ?>
        <script>
            window.dataLayer = window.dataLayer || [];
            window._campaignData = {
                id:          <?php echo e(json_encode($camp_id)); ?>,
                name:        <?php echo e(json_encode($camp_name)); ?>,
                slug:        <?php echo e(json_encode($camp_slug)); ?>,
                currency:    'BDT',
                fb_event_id: <?php echo e(json_encode($fb_view_content_event_id)); ?>

            };
            window._campaignProducts = <?php echo json_encode($camp_products); ?>;
            window._campaignVariants = <?php echo json_encode($campaignVariants ?? [], 15, 512) ?>;
            window._singleCampaignProductId = <?php echo json_encode($products->isNotEmpty() ? (string) $products->first()->id : null, 15, 512) ?>;
            dataLayer.push({
                event:         'campaign_page_loaded',
                page_type:     'campaign_landing',
                campaign_id:   <?php echo e(json_encode($camp_id)); ?>,
                campaign_name: <?php echo e(json_encode($camp_name)); ?>,
                currency:      'BDT',
                value:         <?php echo e($camp_value); ?>,
                ecommerce: {
                    currency: 'BDT',
                    items:    <?php echo json_encode($camp_items_gtm); ?>

                }
            });
        </script>
        <!-- ========== Google Tag Manager ========== -->
        <?php $__currentLoopData = $gtm_code ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gtm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $gtm_container_id = preg_match('/^GTM-/i', trim($gtm->code))
                ? trim($gtm->code)
                : 'GTM-' . trim($gtm->code);
        ?>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo e($gtm_container_id); ?>');</script>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <!-- ========== End Google Tag Manager ========== -->

        <meta name="app-url" content="<?php echo e(route('campaign',$campaign_data->slug)); ?>" />
        <meta name="robots" content="index, follow" />
        <meta name="description" content="<?php echo e($campaign_data->description); ?>" />
        <meta name="keywords" content="<?php echo e($campaign_data->slug); ?>" />

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product" />
        <meta name="twitter:site" content="<?php echo e($campaign_data->name); ?>" />
        <meta name="twitter:title" content="<?php echo e($campaign_data->name); ?>" />
        <meta name="twitter:description" content="<?php echo e($campaign_data->description); ?>" />
        <meta name="twitter:creator" content="<?php echo e($generalsetting->name); ?>" />
        <meta property="og:url" content="<?php echo e(route('campaign',$campaign_data->slug)); ?>" />
        <meta name="twitter:image" content="<?php echo e(asset($campaign_data->image_one)); ?>" />

        <!-- Open Graph data -->
        <meta property="og:title" content="<?php echo e($campaign_data->name); ?>" />
        <meta property="og:type" content="product" />
        <meta property="og:url" content="<?php echo e(route('campaign',$campaign_data->slug)); ?>" />
        <meta property="og:image" content="<?php echo e(asset($campaign_data->image_one)); ?>" />
        <meta property="og:description" content="<?php echo e($campaign_data->description); ?>" />
        <meta property="og:site_name" content="<?php echo e($campaign_data->name); ?>" />

        <!-- ========== Facebook Pixel (single init) ========== -->
        <?php if(isset($pixels) && $pixels->count() > 0): ?>
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
            (window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            <?php $__currentLoopData = $pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pixel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            fbq('init', '<?php echo e($pixel->code); ?>');
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            fbq('track', 'PageView', {}, {eventID: <?php echo e(json_encode('pv_camp'.$campaign_data->id.'_'.time())); ?>});
            fbq('track', 'ViewContent', {
                content_name: <?php echo e(json_encode($camp_name)); ?>,
                content_ids:  <?php echo json_encode($products->pluck('id')->map(fn($id) => (string)$id)->values()->toArray()); ?>,
                content_type: 'product',
                value:        <?php echo e($camp_value); ?>,
                currency:     'BDT',
                num_items:    <?php echo e($products->count()); ?>

            }, {eventID: <?php echo e(json_encode($fb_view_content_event_id)); ?>});
        </script>
        <?php $__currentLoopData = $pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pixel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=<?php echo e($pixel->code); ?>&ev=PageView&noscript=1" />
        </noscript>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <!-- ========== End Facebook Pixel ========== -->

        <!-- ========== TikTok Pixel ========== -->
        <?php if(isset($tiktok_pixels) && $tiktok_pixels->count() > 0): ?>
        <script>
            !function (w, d, t) {
                w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
                ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];
                ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
                for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
                ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};
                ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
                    ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};
                    var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;
                    var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
            }(window, document, 'ttq');
            <?php $__currentLoopData = $tiktok_pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tiktok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            ttq.load('<?php echo e($tiktok->code); ?>');
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ttq.page();
            ttq.track('ViewContent', {
                content_name: <?php echo e(json_encode($camp_name)); ?>,
                content_id:   <?php echo e(json_encode($camp_id)); ?>,
                content_type: 'product',
                value:        <?php echo e($camp_value); ?>,
                currency:     'BDT',
                quantity:     1
            });
        </script>
        <?php endif; ?>
        <!-- ========== End TikTok Pixel ========== -->
        <style>
            /* Style for selected product card */
            .product-card.selected {
                border: 2px solid #198754;
                box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.35);
            }
            .campaign-product-select {
                position: relative;
                cursor: pointer;
            }
            .campaign-product-radio {
                position: absolute;
                opacity: 0;
                width: 0;
                height: 0;
                pointer-events: none;
            }
            .countdown-container {
                text-align: center;
            }
            .counter-card {
                border: 2px dotted white; /* Dotted border */
                border-radius: 15px; /* Rounded corners */
                padding: 5px; /* Padding for the card */
                background-color: transparent; /* Slightly transparent white background */
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
                text-align: center; /* Center the text within each card */
               
            }
            .counter-card div{
                font-size: 1.2em;
                font-weight:bolder;
                color:white;
            }
            
            
            .counter-card span {
                display: block; /* Make the span block-level for better spacing */
                font-size: 0.8em; /* Font size for labels */
                color:orange;
            }
            @keyframes colorAnimation {
                0% {
                    color: pink; /* Start with pink */
                }
                33% {
                    color: green; /* Transition to green */
                }
                66% {
                    color: red; /* Transition to red */
                }
                100% {
                    color: pink; /* Return to pink */
                }
            }
            
            .animated-heading {
                font-size: 2em; /* Adjust font size as needed */
                font-weight: bold; /* Make the heading bold */
                animation: colorAnimation 3s linear infinite; /* Apply the animation */
                
               
            }
            .form_inn{
                padding:10px;
            }
            @media (max-width: 992px) {
                .campro_inn,.cont_inner,.cont_num ,.discount_inn{
                    padding: 10px!important; /* Add 10px padding for tablet and smaller devices */
                    width: 100%;
                }
                .discount_inn{
                    margin:10px 0 0 0;
                }
                .campro_inn h2{
                    font-size:20px;
                }
            }

        </style>
        <style>
            .button-3d {
                position: relative;
                overflow: hidden;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
        
           
            
        
            .button-3d:hover {
                transform: scale(1.05);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }
        
           
        
        </style>
        <style>
            .button-animated-border {
                position: relative;
                overflow: hidden;
                border: 3px solid white; /* Initial border */
                border-radius: 10px; /* Optional: for rounded corners */
                transition: color 0.3s ease; /* Transition for text color */
                animation: border-animation 3s linear infinite; /* Animation */
            }
        
            
        
            @keyframes border-animation {
                0% {
                    border-color: white; /* Transparent at start */
                    transform: scale(0.95); /* Initial scale */
                }
                25% {
                    border-color: yellow; /* Fill with white */
                    transform: scale(1); /* Slightly grow */
                }
                50% {
                    border-color: white; /* Transparent in middle */
                    transform: scale(0.95); /* Back to original scale */
                }
                75% {
                    border-color: yellow; /* Fill with white again */
                    transform: scale(1); /* Slightly grow again */
                }
                100% {
                    border-color: white; /* Transparent at end */
                    transform: scale(0.95); /* Back to original scale */
                }
            }
        
            .button-animated-border:hover {
                color: #fff; /* Change text color on hover */
            }
        </style>

<?php echo $generalsetting->header_code; ?>

    </head>

    <body>
        <!-- ========== GTM noscript ========== -->
        <?php $__currentLoopData = $gtm_code ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gtm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $gtm_noscript_id = preg_match('/^GTM-/i', trim($gtm->code)) ? trim($gtm->code) : 'GTM-'.trim($gtm->code); ?>
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($gtm_noscript_id); ?>"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <!-- ========== TikTok Pixel noscript ========== -->
        <?php if(isset($tiktok_pixels) && $tiktok_pixels->count() > 0): ?>
        <?php $__currentLoopData = $tiktok_pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tiktok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <noscript><img height="1" width="1" style="display:none" alt=""
            src="https://analytics.tiktok.com/i18n/pixel/events.js?sdkid=<?php echo e($tiktok->code); ?>&noscript=1" /></noscript>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

         <?php
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal=str_replace(',','',$subtotal);
            $subtotal=str_replace('.00', '',$subtotal);
            $shipping = Session::get('shipping')?Session::get('shipping'):0;
        ?>
        <section style="background-image: radial-gradient(at center center, #139525 28%, #0E320F 79%)">
            <div class="container py-2 py-md-4">
                <div class="row gy-2">
                    <div class="col-md-7">
                        <h4 class="text-light text-center py-2 py-md-4 fw-bolder"><?php echo $campaign_data->top_title_1; ?> <span class="text-warning"> <?php echo $campaign_data->top_title_2; ?></span> </h4>
                    </div>
                     <div class="col-md-5">
                        <div class="countdown-container">
                            <div class="countdown" id="countdown">
                                <div class="row g-1">
                                    <div class="col-3">
                                       <div class="counter-card">
                                            <div id="days"></div>
                                            <span>Days</span>
                                        </div> 
                                    </div>
                                    <div class="col-3">
                                        <div class="counter-card">
                                            <div id="hours"></div>
                                            <span>Hours</span>
                                        </div>                                        
                                    </div>
                                    <div class="col-3">
                                        <div class="counter-card">
                                            <div id="minutes"></div>
                                            <span>Minutes</span>
                                        </div>                                    
                                    </div>
                                    <div class="col-3">
                                        <div class="counter-card">
                                            <div id="seconds"></div>
                                            <span>Seconds</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2 py-md-4">
                <div class="py-2 py-md-4  rounded" style="border:2px dashed green">
                    <h2 class="animated-heading text-center"><?php echo $campaign_data->heading_1; ?></h2>
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2 py-md-4">
                <div class="row gy-2">
                    <?php if($campaign_data->image_one): ?>
                    <div class="col-sm-6">
                        <img class="img-fluid shadow" src="<?php echo e(asset($campaign_data->image_one)); ?>" >
                    </div>
                    <?php endif; ?>
                    <?php if($campaign_data->image_two): ?>
                    <div class="col-sm-6">
                        <img class="img-fluid shadow" src="<?php echo e(asset($campaign_data->image_two)); ?>" >
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2 py-md-4">
                <div class="row gy-2">
                    <?php if($campaign_data->feature_1): ?>
                    <div class="col-sm-6">
                       <div class="py-2 py-md-4  rounded" style="border:1px dashed green">
                            <h2 class="text-center"><?php echo $campaign_data->feature_1; ?></h2>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($campaign_data->feature_2): ?>
                    <div class="col-sm-6">
                       <div class="py-2 py-md-4  rounded" style="border:1px dashed green">
                            <h2 class="text-center"><?php echo $campaign_data->feature_2; ?></h2>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2">
                <div class="py-2 py-md-4  rounded" style="border:2px dashed green">
                    <h2 class="animated-heading text-center"><?php echo $campaign_data->heading_2; ?></h2>
                </div>
            </div>
        </section>
        <section>
            <div class="container py-2 ">
                <div class="py-2 py-md-4  rounded" style="border:2px dashed green">
                    <h2 class="animated-heading text-center"><?php echo $campaign_data->heading_3; ?></h2>
                </div>
            </div>
        </section>
        
        <?php if($campaign_data->video!=null): ?>
        <section class="camp_video_sec">
            <div class="container">
            
                <div class="row justify-content-center gy-2 gy-md-4">
                    <div class="col-md-8">
                        <h2 class="p-2 py-md-3 rounded text-center" style="background-color:black;border:green 2px solid;color:white;font-weight:bolder">প্রডাক্টের "ভিডিও দেখুন"</h2>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="camp_vid rounded" style="border:5px solid red">
                            <iframe width="100%" height="480" 
                            src="https://www.youtube.com/embed/<?php echo e($campaign_data->video); ?>" 
                            title="<?php echo e($campaign_data->banner_title); ?>" frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen=""></iframe>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="ord_btn">
                            <a href="#order_form" class="cam_order_now" id="cam_order_now"> অর্ডার করতে ক্লিক করুন <i class="fa-solid fa-hand-point-right"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <section class="py-2 py-md-4" style="background: linear-gradient(to bottom, #FAF4B3, #ECC7CF);">
            <div class="container my-2 my-md-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h2 class="text-center p-2 p-md-4 rounded" style="background-color:#FBEFF7;border:2px dashed #F1ACE7">আমাদের থেকে বিস্তারিত জানতে এই নাম্বারে কল করুন <?php echo e($contact->phone); ?></h2>
                        <div class="row justify-content-center my-2 my-md-4 gy-2">
                            <div class="col-md-6 custom_btn">
                                <div class="shadow-lg">
                                    <a href="tel:<?php echo e($contact->phone); ?>" 
                                    class="btn btn-danger btn-lg d-block py-md-3 fs-2 fw-bolder button-3d button-animated-border" >
                                        <i class="fa-solid fa-phone"></i> আমাদের কল করুন </a>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                            <div class="shadow-lg">
                                <a href="https://wa.me/<?php echo e($contact->whatsapp); ?>" 
                                class="btn btn-success btn-lg d-block py-md-3 fs-2 text-light fw-bolder button-3d button-animated-border">
                                    <i class="fa-brands fa-whatsapp"></i> হোয়াটসঅ্যাপ  
                                    </a>
                             </div>
                                
                            </div>
                        </div>
                        
                        <h2 class="text-center p-2 p-md-4 rounded" style="background-color:#FBEFF7;border:2px dashed #F1ACE7"><?php echo $campaign_data->heading_4; ?></h2>
                    
                    </div>
                </div>
            </div>
        </section>

        <?php if(optional($campaign_data)->short_description && strlen($campaign_data->short_description) > 15 || 
    optional($campaign_data)->description && strlen($campaign_data->description) > 15): ?>
        <section class="rules_sec">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h2>বিস্তারিত</h2>
                                <?php echo $campaign_data->short_description; ?>

                                <br>
                                <br>
                                <?php echo $campaign_data->description; ?> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="campro_inn">
                            <div class="campro_head">
                                <h2><?php echo e($campaign_data->name); ?></h2>
                            </div>

                            <div class="campro_img_slider owl-carousel">
                                <?php if($campaign_data->image_one): ?>
                               <div class="campro_img_item">
                                   <img src="<?php echo e(asset($campaign_data->image_one)); ?>" alt="">
                               </div> 
                               <?php endif; ?>
                                <?php if($campaign_data->image_two): ?>
                               <div class="campro_img_item">
                                   <img src="<?php echo e(asset($campaign_data->image_two)); ?>" alt="">
                               </div> 
                               <?php endif; ?>
                                <?php if($campaign_data->image_three): ?>
                               <div class="campro_img_item">
                                   <img src="<?php echo e(asset($campaign_data->image_three)); ?>" alt="">
                               </div>
                               <?php endif; ?>
                            </div>
                            <div class="col-sm-12">
                                <div class="ord_btn">
                                    <a href="#order_form" class="cam_order_now" id="cam_order_now"> অর্ডার করতে ক্লিক করুন <i class="fa-solid fa-hand-point-right"></i> </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>


        <section>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="rev_inn">
                            
                            <h2 class="campaign_offer"><?php echo e($campaign_data->review); ?></h2>
                            
                            <div class="review_slider owl-carousel">
                            <?php $__currentLoopData = $campaign_data->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="review_item">
                                <img src="<?php echo e(asset($value->image)); ?>" alt="">
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                           </div>
                            <div class="col-sm-12">
                                <div class="ord_btn">
                                    <a href="#order_form" class="cam_order_now" id="cam_order_now"> অর্ডার করতে ক্লিক করুন <i class="fa-solid fa-hand-point-right"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <section class="form_sec">
        <div class="container">
           <div class="row">
             <div class="col-sm-12">
                <div class="form_inn">
                    <div class="col-sm-12">
                        <div class="row">
                <div class="col-sm-12">
                    <h2 class="campaign_offer">অফারটি সীমিত সময়ের জন্য, তাই অফার শেষ হওয়ার আগেই অর্ডার করুন</h2>
                    <?php if($campaign_data->note): ?>
                    <p class="my-1 text-center">
                        <?php echo $campaign_data->note; ?>

                    </p>
                    <?php endif; ?>
                </div>
                
            </div>
            <div class="row order_by">
                <?php if($products->isEmpty()): ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center fw-bold mb-3" role="alert">
                        প্রোডাক্ট এড নেই
                    </div>
                </div>
                <?php else: ?>
                <div class="col-lg-7 cust-order-1">
                    <div class="cart_details">
                        <?php if($products->count()>1): ?>
                        <div class="card mb-2 ">
                          <div class="card-header">
                                <h5 class="potro_font">একটি পণ্য সিলেক্ট করুনণ </h5>
                            </div>  
                             <div class="card-body">
                                <div class="row g-2">
                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $cardVariantColors = $product->variantPrices->pluck('color')->unique('id')->filter()->map(function ($c) {
                                                $label = $c->getDisplayName() ?? $c->colorName ?? $c->color_name ?? null;
                                                if (empty($label) && !empty($c->color)) {
                                                    $label = $c->color;
                                                }
                                                return ['id' => (int) $c->id, 'name' => $label ?: ('Color #'.$c->id), 'hex' => $c->color ?? null];
                                            })->values();
                                            $cardVariantSizes = $product->variantPrices->pluck('size')->unique('id')->filter()->map(function ($s) {
                                                $label = $s->sizeName ?? $s->size_name ?? $s->name ?? null;
                                                return ['id' => (int) $s->id, 'name' => $label ?: ('Size #'.$s->id)];
                                            })->values();
                                        ?>
                                        <div class="col-md-3 col-6">
                                            <div class="campaign-product-select border shadow"
                                                data-product-id="<?php echo e($product->id); ?>"
                                                data-variants="<?php echo e(htmlspecialchars(json_encode(['colors' => $cardVariantColors, 'sizes' => $cardVariantSizes]), ENT_QUOTES, 'UTF-8')); ?>">
                                                <input type="radio"
                                                    class="campaign-product-radio"
                                                    name="product"
                                                    id="product_<?php echo e($product->id); ?>"
                                                    value="<?php echo e($product->id); ?>"
                                                    <?php echo e($loop->first ? 'checked' : ''); ?>>
                                                <label for="product_<?php echo e($product->id); ?>" class="card shadow-sm product-card mb-0 w-100 <?php echo e($loop->first ? 'selected' : ''); ?>">
                                                    <img src="<?php echo e(asset(optional($product->image)->image ?? 'public/uploads/default.webp')); ?>" class="card-img-top" alt="<?php echo e($product->name); ?>" style="height: 100px; object-fit: cover;">
                                                    <div class="card-body p-1 text-center">
                                                        <div class="card-title"><?php echo e(Str::limit($product->name, 20)); ?></div>
                                                        <div class="card-text mb-1">৳<?php echo e($product->new_price); ?> <del>৳<?php echo e($product->old_price); ?></del></div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                             </div>
                        </div>
                        <?php endif; ?>
                        <?php
                            $showCampaignVariantCard = $products->contains(function ($prod) {
                                return $prod->variantPrices->whereNotNull('color_id')->isNotEmpty()
                                    || $prod->variantPrices->whereNotNull('size_id')->isNotEmpty();
                            });
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="potro_font">পণ্যের বিবরণ </h5>
                            </div>
                            <div class="card-body p-2 p-md-3">
                                <?php if($showCampaignVariantCard): ?>
                                <div id="campaign-variant-box" class="mb-3">
                                    <p class="fw-bold mb-2 potro_font">কালার ও সাইজ বাছুন</p>
                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $panelColors = $product->variantPrices->pluck('color')->unique('id')->filter();
                                            $panelSizes  = $product->variantPrices->pluck('size')->unique('id')->filter();
                                            $panelHasVariants = $panelColors->isNotEmpty() || $panelSizes->isNotEmpty();
                                        ?>
                                        <div class="campaign-product-variant-panel border rounded p-2 p-md-3 bg-light"
                                            id="campaign-variants-<?php echo e($product->id); ?>"
                                            data-product-id="<?php echo e($product->id); ?>"
                                            style="<?php echo e($loop->first ? '' : 'display:none;'); ?>">
                                            <?php if($panelHasVariants): ?>
                                            <div class="row g-2">
                                                <?php if($panelColors->isNotEmpty()): ?>
                                                <div class="col-md-6 campaign-panel-color">
                                                    <label class="form-label mb-1">কালার</label>
                                                    <select class="form-select form-select-lg campaign-pick-color" data-product-id="<?php echo e($product->id); ?>">
                                                        <option value="">কালার সিলেক্ট করুন</option>
                                                        <?php $__currentLoopData = $panelColors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campColor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($campColor->id); ?>">
                                                                <?php echo e($campColor->getDisplayName() ?? $campColor->colorName ?? $campColor->color ?? ('Color #'.$campColor->id)); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                                <?php endif; ?>
                                                <?php if($panelSizes->isNotEmpty()): ?>
                                                <div class="col-md-6 campaign-panel-size">
                                                    <label class="form-label mb-1">সাইজ</label>
                                                    <select class="form-select form-select-lg campaign-pick-size" data-product-id="<?php echo e($product->id); ?>">
                                                        <option value="">সাইজ সিলেক্ট করুন</option>
                                                        <?php $__currentLoopData = $panelSizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campSize): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($campSize->id); ?>">
                                                                <?php echo e($campSize->sizeName ?? $campSize->name ?? ('Size #'.$campSize->id)); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-0 small text-muted mt-2">কালার/সাইজ পরিবর্তন করলে দাম অটোমেটিক আপডেট হবে।</p>
                                            <?php else: ?>
                                            <p class="mb-0 small text-muted">এই পণ্যের জন্য কালার/সাইজ অপশন নেই।</p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php endif; ?>
                                <div id="campaign-cartlist" class="cartlist table-responsive">
                                    <?php echo $__env->make('frontEnd.layouts.ajax.campaign-cart-table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 cus-order-2">
                    <div class="checkout-shipping" id="order_form">
                        <form action="<?php echo e(route('customer.ordersave')); ?>" method="POST" data-parsley-validate="">
                        <?php echo csrf_field(); ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="potro_font">আপনার ইনফরমেশন দিন  </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="name">আপনার নাম লিখুন * </label>
                                            <input type="text" id="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e(old('name')); ?>" placeholder="নাম" required>
                                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="phone">আপনার মোবাইল লিখুন *</label>
                                            <input type="number" minlength="11" id="number" maxlength="11" pattern="0[0-9]+" title="please enter number only and 0 must first character" title="Please enter an 11-digit number." id="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="+৮৮ বাদে ১১ সংখ্যা "  required>
                                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="address">আপনার ঠিকানা লিখুন   *</label>
                                            <input type="address" id="address" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="জেলা, থানা, গ্রাম " name="address" value="<?php echo e(old('address')); ?>"  required>
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($message); ?></strong>
                                                </span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    
                                    <?php if($requires_shipping ?? true): ?>
                                        <div class="col-sm-12">
                                            <div class="form-group mb-3">
                                                <label for="cmp_division">বিভাগ *</label>
                                                <select name="division_id" id="cmp_division" class="form-control <?php $__errorArgs = ['division_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                                    <option value="">বিভাগ নির্বাচন করুন</option>
                                                    <?php $__currentLoopData = $divisions ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($d->id); ?>" <?php if(old('division_id') == $d->id): echo 'selected'; endif; ?>><?php echo e($d->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group mb-3">
                                                <label for="cmp_district">জেলা *</label>
                                                <select name="district_id" id="cmp_district" class="form-control <?php $__errorArgs = ['district_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required disabled>
                                                    <option value="">আগে বিভাগ সিলেক্ট করুন</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group mb-3">
                                                <label for="cmp_upazila">উপজেলা / থানা *</label>
                                                <select name="upazila_id" id="cmp_upazila" class="form-control <?php $__errorArgs = ['upazila_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required disabled>
                                                    <option value="">আগে জেলা সিলেক্ট করুন</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="division_id" value="">
                                        <input type="hidden" name="district_id" value="">
                                        <input type="hidden" name="upazila_id" value="">
                                    <?php endif; ?>

                                    
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label>পেমেন্ট মেথড নির্বাচন করুন *</label>
                                            <?php if($hasAdvance ?? false): ?>
                                                <div class="alert alert-warning py-2 small mb-2">এই অর্ডারে অগ্রিম পেমেন্ট প্রয়োজন, অনলাইন পেমেন্ট সিলেক্ট করুন।</div>
                                            <?php endif; ?>
                                            <div class="cmp-pay-list">
                                                <?php if(!($hasDigital ?? false) && !($hasAdvance ?? false)): ?>
                                                    <label class="cmp-pay"><input type="radio" name="payment_method" value="cod" <?php if(old('payment_method', 'cod') === 'cod'): echo 'checked'; endif; ?>>
                                                        <span class="cmp-pay-ic" style="color:#16a34a"><i class="fas fa-truck"></i></span>
                                                        <span class="cmp-pay-t"><strong>Cash On Delivery</strong><small>পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন</small></span></label>
                                                <?php endif; ?>
                                                <?php if($shatajpay ?? null): ?>
                                                    <label class="cmp-pay"><input type="radio" name="payment_method" value="shatajpay" <?php if(old('payment_method') === 'shatajpay'): echo 'checked'; endif; ?>>
                                                        <img src="<?php echo e(asset('public/frontEnd/images/shatajpay.png')); ?>" class="cmp-pay-logo" alt="" onerror="this.style.display='none'">
                                                        <span class="cmp-pay-t"><strong>Shataj Pay</strong><small>কার্ড ও মোবাইল ব্যাংকিং পেমেন্ট</small></span></label>
                                                <?php endif; ?>
                                                <?php if($bkash_gateway ?? null): ?>
                                                    <label class="cmp-pay"><input type="radio" name="payment_method" value="bkash" <?php if(old('payment_method') === 'bkash'): echo 'checked'; endif; ?>>
                                                        <img src="<?php echo e(asset('public/frontEnd/images/bkash.svg')); ?>" class="cmp-pay-logo" alt="" onerror="this.style.display='none'">
                                                        <span class="cmp-pay-t"><strong>bKash Payment</strong><small>বিকাশ অ্যাপ বা গেটওয়ে দ্বারা পেমেন্ট</small></span></label>
                                                <?php endif; ?>
                                                <?php if($shurjopay_gateway ?? null): ?>
                                                    <label class="cmp-pay"><input type="radio" name="payment_method" value="shurjopay" <?php if(old('payment_method') === 'shurjopay'): echo 'checked'; endif; ?>>
                                                        <img src="<?php echo e(asset('public/frontEnd/images/shurjoPay.png')); ?>" class="cmp-pay-logo" alt="" onerror="this.style.display='none'">
                                                        <span class="cmp-pay-t"><strong>Online Payment</strong><small>ShurjoPay (Card/Mobile Banking)</small></span></label>
                                                <?php endif; ?>
                                                <?php if($uddoktapay_gateway ?? null): ?>
                                                    <label class="cmp-pay"><input type="radio" name="payment_method" value="uddoktapay" <?php if(old('payment_method') === 'uddoktapay'): echo 'checked'; endif; ?>>
                                                        <img src="<?php echo e(asset('public/frontEnd/images/uddokta.png')); ?>" class="cmp-pay-logo" alt="" onerror="this.style.display='none'">
                                                        <span class="cmp-pay-t"><strong>UddoktaPay</strong><small>মোবাইল ব্যাংকিং পেমেন্ট গেটওয়ে</small></span></label>
                                                <?php endif; ?>
                                                <?php if($aamarpay_gateway ?? null): ?>
                                                    <label class="cmp-pay"><input type="radio" name="payment_method" value="aamarpay" <?php if(old('payment_method') === 'aamarpay'): echo 'checked'; endif; ?>>
                                                        <img src="<?php echo e(asset('public/frontEnd/images/aamarpay.png')); ?>" class="cmp-pay-logo" alt="" onerror="this.style.display='none'">
                                                        <span class="cmp-pay-t"><strong>aamarPay</strong><small>কার্ড ও মোবাইল ব্যাংকিং পেমেন্ট</small></span></label>
                                                <?php endif; ?>
                                                <?php $__currentLoopData = $manual_gateways ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <label class="cmp-pay"><input type="radio" name="payment_method" value="manual_<?php echo e($mg->id); ?>" <?php if(old('payment_method') === 'manual_'.$mg->id): echo 'checked'; endif; ?>>
                                                        <?php if($mg->logo_asset_url): ?><img src="<?php echo e($mg->logo_asset_url); ?>" class="cmp-pay-logo" alt=""><?php else: ?><span class="cmp-pay-ic" style="color:#2563eb"><i class="fas fa-money-check-alt"></i></span><?php endif; ?>
                                                        <span class="cmp-pay-t"><strong><?php echo e($mg->title); ?></strong><small>ম্যানুয়াল পেমেন্ট — নির্দেশনা অনুযায়ী টাকা পাঠিয়ে ট্রানজেকশন আইডি দিন</small></span></label>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                            <div id="cmp-manual-fields" class="mt-2 p-2 rounded border border-warning bg-light" style="display:none">
                                                <div id="cmp-manual-inst" class="small text-secondary mb-2" style="white-space:pre-wrap"></div>
                                                <input type="text" name="manual_trx_id" id="cmp_manual_trx" class="form-control mb-2" value="<?php echo e(old('manual_trx_id')); ?>" maxlength="55" placeholder="ট্রানজেকশন আইডি / রেফারেন্স *">
                                                <input type="text" name="manual_sender_number" class="form-control" value="<?php echo e(old('manual_sender_number')); ?>" maxlength="55" placeholder="যে নম্বর থেকে পাঠিয়েছেন (ঐচ্ছিক)">
                                            </div>
                                            <div id="cmp-pay-error" class="text-danger small fw-bold mt-1" style="display:none">অনুগ্রহ করে একটি পেমেন্ট মেথড সিলেক্ট করুন।</div>
                                            <?php if($errors->any()): ?>
                                                <div class="text-danger small mt-2"><?php echo e($errors->first()); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <script>
                                        window.CMP_MANUAL_GATEWAYS = <?php echo json_encode(($manual_gateways ?? collect())->map(fn ($g) => ['code' => 'manual_' . $g->id, 'instructions' => (string) ($g->instructions ?? '')])->values()->all(), 512) ?>;
                                    </script>
                                    <style>
                                        .cmp-pay-list { display: flex; flex-direction: column; gap: 8px; }
                                        .cmp-pay { display: flex; align-items: center; gap: 10px; border: 1px solid #dfe3ea; border-radius: 8px; padding: 9px 12px; cursor: pointer; margin: 0; background: #fff; transition: .15s; }
                                        .cmp-pay:hover { border-color: #8b87e0; }
                                        .cmp-pay input { width: 18px; height: 18px; flex-shrink: 0; margin: 0; }
                                        .cmp-pay-logo { height: 28px; max-width: 70px; object-fit: contain; }
                                        .cmp-pay-ic { font-size: 22px; width: 32px; text-align: center; }
                                        .cmp-pay-t { display: flex; flex-direction: column; line-height: 1.3; }
                                        .cmp-pay-t small { color: #6b7280; font-size: 12px; }
                                        .cmp-pay.is-on { border-color: #6366f1; background: #f5f3ff; box-shadow: 0 0 0 2px rgba(99, 102, 241, .15); }
                                    </style>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="order_place" type="submit">অর্ডার কন্ফার্ম করুন </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- card end -->
                    </form>
                    </div>
                    <?php if($campaign_data->billing_details): ?>
                    <p class="my-1 text-center">
                        <?php echo $campaign_data->billing_details; ?>

                    </p>
                    <?php endif; ?>
                </div>
                <!-- col end -->
                
            <!-- col end -->
            </div>
            <?php endif; ?>
                    </div>
                </div>

             </div>
            </div>
        </div>
    </section>

        <script src="<?php echo e(asset('public/frontEnd/campaign/js')); ?>/jquery-2.1.4.min.js"></script>
        <script src="<?php echo e(asset('public/frontEnd/campaign/js')); ?>/all.js"></script>
        <script src="<?php echo e(asset('public/frontEnd/campaign/js')); ?>/bootstrap.min.js"></script>
        <script src="<?php echo e(asset('public/frontEnd/campaign/js')); ?>/owl.carousel.min.js"></script>
        <script src="<?php echo e(asset('public/frontEnd/campaign/js')); ?>/select2.min.js"></script>
        <script src="<?php echo e(asset('public/frontEnd/campaign/js')); ?>/script.js"></script>
        <!-- bootstrap js -->
        <script>
            $(document).ready(function () {
                $(".owl-carousel").owlCarousel({
                    margin: 15,
                    loop: true,
                    dots: false,
                    autoplay: true,
                    autoplayTimeout: 6000,
                    autoplayHoverPause: true,
                    items: 1,
                    });
                $('.owl-nav').remove();
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.select2').select2();
            });
        </script>
        <script>
            // ⭐ বিভাগ → জেলা → উপজেলা (চেকআউট পেজের একই AJAX), জেলা বাছাই করলে ডেলিভারি চার্জ আপডেট
            function cmpShipping(id) {
                $.ajax({
                    type: "GET",
                    data: { id: id, campaign: 1 },
                    url: "<?php echo e(route('shipping.charge')); ?>",
                    dataType: "html",
                    success: function(response){
                        $('#campaign-cartlist').html(response);
                    }
                });
            }
            $('#cmp_division').on('change', function () {
                var divId = $(this).val();
                $('#cmp_district').prop('disabled', !divId).html(divId ? '<option value="">লোড হচ্ছে...</option>' : '<option value="">আগে বিভাগ সিলেক্ট করুন</option>');
                $('#cmp_upazila').prop('disabled', true).html('<option value="">আগে জেলা সিলেক্ট করুন</option>');
                if (!divId) return;
                $.get('<?php echo e(url('/ajax/delivery/districts')); ?>/' + divId, function (res) {
                    var opts = '<option value="">জেলা নির্বাচন করুন</option>';
                    (res.data || []).forEach(function (r) {
                        opts += '<option value="' + r.id + '">' + r.name + ' (৳' + r.delivery_charge + ')</option>';
                    });
                    $('#cmp_district').html(opts).prop('disabled', false);
                }).fail(function () { $('#cmp_district').html('<option value="">লোড ব্যর্থ</option>'); });
            });
            $('#cmp_district').on('change', function () {
                var distId = $(this).val();
                $('#cmp_upazila').prop('disabled', !distId).html(distId ? '<option value="">লোড হচ্ছে...</option>' : '<option value="">আগে জেলা সিলেক্ট করুন</option>');
                if (!distId) return;
                cmpShipping(distId);
                $.get('<?php echo e(url('/ajax/delivery/upazilas')); ?>/' + distId, function (res) {
                    var opts = '<option value="">উপজেলা নির্বাচন করুন</option>';
                    (res.data || []).forEach(function (r) { opts += '<option value="' + r.id + '">' + r.name + '</option>'; });
                    $('#cmp_upazila').html(opts).prop('disabled', false);
                }).fail(function () { $('#cmp_upazila').html('<option value="">লোড ব্যর্থ</option>'); });
            });
            if ($('#cmp_division').val()) { $('#cmp_division').trigger('change'); }

            // ⭐ পেমেন্ট মেথড: সিলেক্টেড স্টাইল + ম্যানুয়াল পেমেন্টের নির্দেশনা
            function cmpSyncPay() {
                var v = $('input[name="payment_method"]:checked').val() || '';
                $('.cmp-pay').removeClass('is-on');
                $('input[name="payment_method"]:checked').closest('.cmp-pay').addClass('is-on');
                if (v.indexOf('manual_') === 0) {
                    var inst = '';
                    (window.CMP_MANUAL_GATEWAYS || []).forEach(function (g) { if (g.code === v) inst = g.instructions || ''; });
                    $('#cmp-manual-inst').text(inst);
                    $('#cmp-manual-fields').show();
                    $('#cmp_manual_trx').prop('required', true);
                } else {
                    $('#cmp-manual-fields').hide();
                    $('#cmp_manual_trx').prop('required', false);
                }
            }
            if (!$('input[name="payment_method"]:checked').length) {
                $('input[name="payment_method"]:first').prop('checked', true);
            }
            $(document).on('change', 'input[name="payment_method"]', function () { $('#cmp-pay-error').hide(); cmpSyncPay(); });
            cmpSyncPay();
            $('form[action="<?php echo e(route("customer.ordersave")); ?>"]').on('submit', function (e) {
                if ($('input[name="payment_method"]').length && !$('input[name="payment_method"]:checked').length) {
                    e.preventDefault();
                    $('#cmp-pay-error').show();
                    return false;
                }
            });
        </script>
           <script>
            $(document).on("click", ".cart_remove", function () {
                var id = $(this).data("id");
                var useCampaign = $(this).data("campaign");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id, campaign: useCampaign ? 1 : undefined },
                        url: "<?php echo e(route('cart.remove')); ?>",
                        success: function (data) {
                            if (data) {
                                $("#campaign-cartlist").html(data);
                                $("#loading").hide();
                                if (typeof cart_count === 'function') cart_count();
                                if (typeof mobile_cart === 'function') mobile_cart();
                                if (typeof cart_summary === 'function') cart_summary();
                            }
                        },
                    });
                }
            });
            $(document).on("click", ".cart_increment", function () {
                var id = $(this).data("id");
                var useCampaign = $(this).data("campaign");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id, campaign: useCampaign ? 1 : undefined },
                        url: "<?php echo e(route('cart.increment')); ?>",
                        success: function (data) {
                            if (data) {
                                $("#campaign-cartlist").html(data);
                                $("#loading").hide();
                                if (typeof cart_count === 'function') cart_count();
                                if (typeof mobile_cart === 'function') mobile_cart();
                            }
                        },
                    });
                }
            });

            $(document).on("click", ".cart_decrement", function () {
                var id = $(this).data("id");
                var useCampaign = $(this).data("campaign");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id, campaign: useCampaign ? 1 : undefined },
                        url: "<?php echo e(route('cart.decrement')); ?>",
                        success: function (data) {
                            if (data) {
                                $("#campaign-cartlist").html(data);
                                $("#loading").hide();
                                if (typeof cart_count === 'function') cart_count();
                                if (typeof mobile_cart === 'function') mobile_cart();
                            }
                        },
                    });
                }
            });

        </script>
        <script>
            $('.review_slider').owlCarousel({   
                dots: false,
                arrow: false,
                autoplay: true,
                loop: true,
                margin: 10,
                smartSpeed: 1000,
                mouseDrag: true,
                touchDrag: true,
                items: 6,
                responsiveClass: true,
                responsive: {
                    300: {
                        items: 1,
                    },
                    480: {
                        items: 2,
                    },
                    768: {
                        items: 5,
                    },
                    1170: {
                        items: 5,
                    },
                }
            });
        </script>

        <script>
            $('.campro_img_slider').owlCarousel({   
                dots: false,
                arrow: false,
                autoplay: true,
                loop: true,
                margin: 10,
                smartSpeed: 1000,
                mouseDrag: true,
                touchDrag: true,
                items: 3,
                responsiveClass: true,
                responsive: {
                    300: {
                        items: 1,
                    },
                    480: {
                        items: 2,
                    },
                    768: {
                        items: 3,
                    },
                    1170: {
                        items: 3,
                    },
                }
            });
        </script>
        <script>
            function getCurrentCampaignProductId() {
                const checked = document.querySelector('input[name="product"]:checked');
                if (checked) return checked.value;
                return window._singleCampaignProductId || null;
            }

            function showCampaignVariantPanel(productId) {
                productId = String(productId);
                $('.campaign-product-variant-panel').hide();
                const panel = document.getElementById('campaign-variants-' + productId);
                if (panel) {
                    panel.style.display = '';
                }
            }

            function getCampaignVariantValues(productId) {
                const panel = document.getElementById('campaign-variants-' + productId);
                if (!panel) {
                    return { colorId: '', sizeId: '' };
                }
                const colorEl = panel.querySelector('.campaign-pick-color');
                const sizeEl  = panel.querySelector('.campaign-pick-size');
                return {
                    colorId: colorEl ? colorEl.value : '',
                    sizeId:  sizeEl ? sizeEl.value : ''
                };
            }

            function highlightCampaignProduct(productId) {
                productId = String(productId);
                document.querySelectorAll('.product-card').forEach(function (card) {
                    card.classList.remove('selected');
                });
                const label = document.querySelector('label[for="product_' + productId + '"]');
                if (label) {
                    label.classList.add('selected');
                }
            }

            function trackCampaignAddToCart(productId, prodPrice, prodName) {
                dataLayer.push({'ecommerce': null});
                dataLayer.push({
                    'event': 'add_to_cart',
                    'ecommerce': {
                        'currency': 'BDT',
                        'value': prodPrice,
                        'items': [{
                            'item_id':   String(productId),
                            'item_name': prodName,
                            'price':     prodPrice,
                            'quantity':  1
                        }]
                    }
                });

                if (typeof fbq !== 'undefined') {
                    fbq('track', 'AddToCart', {
                        content_ids:  [String(productId)],
                        content_name: prodName,
                        content_type: 'product',
                        value:        prodPrice,
                        currency:     'BDT'
                    }, {eventID: 'atc_' + productId + '_' + Math.floor(Date.now()/1000)});
                }

                if (typeof ttq !== 'undefined') {
                    ttq.track('AddToCart', {
                        content_id:   String(productId),
                        content_name: prodName,
                        content_type: 'product',
                        value:        prodPrice,
                        currency:     'BDT',
                        quantity:     1
                    });
                }
            }

            function requestCampaignCart(productId, colorId, sizeId, trackAdd) {
                if (!productId) return;

                $.ajax({
                    type: "GET",
                    cache: false,
                    dataType: "html",
                    data: {
                        id: productId,
                        color_id: colorId || '',
                        size_id: sizeId || '',
                        campaign: '1'
                    },
                    url: "<?php echo e(route('cart.changeProduct')); ?>",
                    success: function (data) {
                        if (data && String(data).indexOf('<table') !== -1) {
                            $("#campaign-cartlist").html(data);
                        }

                        if (trackAdd) {
                            var selProd = window._campaignProducts
                                ? window._campaignProducts.find(function (p) { return p.id === String(productId); })
                                : null;
                            var prodPrice = selProd ? selProd.price : 0;
                            var prodName  = selProd ? selProd.name  : '';
                            var priceText = $('#campaign-cartlist tbody tr:first td:last').text().replace(/[^0-9.]/g, '');
                            if (priceText) {
                                prodPrice = parseFloat(priceText) || prodPrice;
                            }
                            trackCampaignAddToCart(productId, prodPrice, prodName);
                        }
                    }
                });
            }

            function updateCart(productId, trackAdd) {
                productId = String(productId);

                const $radio = $('#product_' + productId);
                if ($radio.length) {
                    $radio.prop('checked', true);
                }

                highlightCampaignProduct(productId);
                showCampaignVariantPanel(productId);
                const variantVals = getCampaignVariantValues(productId);
                requestCampaignCart(productId, variantVals.colorId, variantVals.sizeId, !!trackAdd);
            }

            $(document).on('change', 'input.campaign-product-radio', function () {
                updateCart($(this).val(), true);
            });

            $(document).on('click', '.campaign-product-select', function (e) {
                const $radio = $(this).find('input.campaign-product-radio');
                if (!$radio.length) return;
                if (!$radio.prop('checked')) {
                    $radio.prop('checked', true).trigger('change');
                } else if (!$(e.target).is('input.campaign-product-radio')) {
                    updateCart($radio.val(), false);
                }
            });

            $(document).on('change', '.campaign-pick-color, .campaign-pick-size', function () {
                const productId = String($(this).data('product-id') || getCurrentCampaignProductId());
                if (!productId) return;
                const variantVals = getCampaignVariantValues(productId);
                requestCampaignCart(productId, variantVals.colorId, variantVals.sizeId, false);
            });

            $(document).ready(function () {
                const firstInput = document.querySelector('input.campaign-product-radio:checked')
                    || document.querySelector('input.campaign-product-radio');
                const productId = firstInput ? firstInput.value : window._singleCampaignProductId;
                if (productId) {
                    highlightCampaignProduct(productId);
                    showCampaignVariantPanel(productId);
                }
            });
        </script>
        <script>
            <?php if($campaign_data->deadline): ?>
            // Set the deadline from the campaign data
            const deadline = new Date("<?php echo e($campaign_data->deadline); ?>").getTime();
        
            // Update the countdown every 1 second
            const x = setInterval(function() {
                // Get current date and time
                const now = new Date().getTime();
        
                // Calculate the distance between now and the deadline
                const distance = deadline - now;
        
                // Time calculations for days, hours, minutes and seconds
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
                // Display the result in the respective elements
                document.getElementById("days").innerHTML = days;
                document.getElementById("hours").innerHTML = hours;
                document.getElementById("minutes").innerHTML = minutes;
                document.getElementById("seconds").innerHTML = seconds;
        
                // If the countdown is over, write some text
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("countdown").innerHTML = "EXPIRED";
                }
            }, 1000);
            <?php else: ?>
            document.getElementById("countdown").style.display = "none";
            <?php endif; ?>
        </script>
        <script>
            // ========== GTM — view_item_list (সব প্রোডাক্ট) ==========
            dataLayer.push({'ecommerce': null});
            dataLayer.push({
                'event': 'view_item_list',
                'ecommerce': {
                    'currency': 'BDT',
                    'items': window._campaignProducts
                        ? window._campaignProducts.map(function(p, i) {
                            return {item_id: p.id, item_name: p.name, price: p.price, index: i, quantity: 1};
                          })
                        : []
                }
            });

            $(document).ready(function() {
                // ========== InitiateCheckout + Lead — Order Form Submit ==========
                $('form[action="<?php echo e(route("customer.ordersave")); ?>"]').on('submit', function() {
                    var subtotalVal   = parseFloat($('#net_total strong').text().replace(/[^0-9.]/g, '')) || 0;
                    var contentIds    = window._campaignProducts ? window._campaignProducts.map(function(p){ return p.id; }) : [];
                    var icEventId     = 'ic_camp<?php echo e($campaign_data->id); ?>_' + Math.floor(Date.now()/1000);
                    var leadEventId   = 'lead_camp<?php echo e($campaign_data->id); ?>_' + Math.floor(Date.now()/1000);
                    var campItems     = window._campaignProducts
                        ? window._campaignProducts.map(function(p, i){
                            return {item_id: p.id, item_name: p.name, price: p.price, index: i, quantity: 1};
                          })
                        : [];

                    // GTM — begin_checkout
                    dataLayer.push({'ecommerce': null});
                    dataLayer.push({
                        'event': 'begin_checkout',
                        'ecommerce': {
                            'currency': 'BDT',
                            'value':    subtotalVal,
                            'items':    campItems
                        }
                    });

                    // Facebook Pixel — InitiateCheckout + Lead
                    if (typeof fbq !== 'undefined') {
                        fbq('track', 'InitiateCheckout', {
                            content_ids:  contentIds,
                            content_type: 'product',
                            value:        subtotalVal,
                            currency:     'BDT',
                            num_items:    contentIds.length
                        }, {eventID: icEventId});
                        fbq('track', 'Lead', {
                            value:        subtotalVal,
                            currency:     'BDT',
                            content_name: <?php echo e(json_encode($camp_name)); ?>

                        }, {eventID: leadEventId});
                    }

                    // TikTok Pixel — InitiateCheckout
                    if (typeof ttq !== 'undefined') {
                        ttq.track('InitiateCheckout', {
                            content_ids:  contentIds,
                            content_type: 'product',
                            value:        subtotalVal,
                            currency:     'BDT',
                            quantity:     contentIds.length
                        });
                    }
                });

                // ========== Order Now Button Click ==========
                $('.cam_order_now').on('click', function() {
                    dataLayer.push({
                        event:         'click_order_now_button',
                        campaign_id:   <?php echo e(json_encode($camp_id)); ?>,
                        campaign_name: <?php echo e(json_encode($camp_name)); ?>

                    });
                });
            });
        </script>
    </body>
</html>
<?php /**PATH D:\laragon\www\ecom1\resources\views/frontEnd/layouts/pages/campaign/campaign.blade.php ENDPATH**/ ?>