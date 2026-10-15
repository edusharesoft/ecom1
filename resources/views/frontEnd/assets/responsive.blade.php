@php
    $generalsetting = \App\Models\GeneralSetting::first();
    $hasNewsTicker = $generalsetting
        && (int) ($generalsetting->news_ticker_enabled ?? 0) === 1
        && trim((string) ($generalsetting->top_headline ?? '')) !== '';
    $mobileContentPaddingTop = $hasNewsTicker ? '134px' : '104px';
@endphp
@media only screen and (min-width:767px) {
    .mobile-menu {
        display: none;
    }
    .mobile-search {
        display: none;
    }
    .mobile-filter-toggle {
        display: none;
    }
}
@media only screen and (min-width:320px) and (max-width:767px) {
    .payment-method-wrappers {
        grid-template-columns: repeat(2, 1fr);
    }
    .filter_sort {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        padding: 0 6px;
        margin-top: 12px;
    }
    .filter_sidebar {
        position: fixed;
        visibility: hidden;
        opacity: 0;
        top: 30px;
        transition: 0.35s all;
        background: #fff;
    }
    .filter_sidebar.active {
        background: #fff;
        z-index: 9999;
        height: 100%;
        width: 97%;
        top: 2px;
        visibility: visible;
        opacity: 1;
        overflow-y: auto;
    }
    .filter_sidebar.active::-webkit-scrollbar-track
    {
    	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    	background-color: #F5F5F5;
    }
    
    .filter_sidebar.active::-webkit-scrollbar
    {
    	width: 6px;
    	background-color: #F5F5F5;
    }
    
    .filter_sidebar.active::-webkit-scrollbar-thumb
    {
    	background-color: #00aef0;
    }

    .filter_btn {
        display: inline-block;
        background: {{$generalsetting->secodery_color}};
        color: #fff;
        width: 55px;
        height: 33px;
        line-height: 33px;
        font-size: 17px;
        border: 1px solid #ddd;
        text-align: center;
        text-transform: capitalize;
        cursor: pointer;
    }
    .filter_close {
        background: #00aef0;
        padding: 10px 15px;
        font-size: 18px;
        color: #fff;
        border-radius: 5px;
        margin: 8px 0;
        cursor: pointer;
        display: block;
    }
    .sidebar-menu {
        display: none;
    }
      li.mobile_home {
        border: 2px solid #ddd;
        margin-top: -35px;
        background: {{$generalsetting->secodery_color}};
        padding-top: 15px;
        border-radius: 50%;
        width: 75px;
        height: 75px;
    }
    
    li.mobile_home a {
        color: #fff;
    }
    
	.scrolltop {
	    display: none !important;
	}
    .hightlight_cont ul {

    padding-left: 5px;
}
    .mobile_hide {
        display:none;
    }
    .desktop_hide {
        display:block;
    }
    .sorting-section {
    margin-top: -15px;
}
    .card-body.cartlist {
    overflow-x: scroll;
}
    .section-title-header .section-title-name {
    font-size: 15px;
}
	#content {
     margin-left: 0;
     padding-top: {{ $mobileContentPaddingTop }};
   }
   .meta_description {
   	display: none;
   }
	.mm-ocd {
    display: none;
   }
    .page-sort {
        padding-right: 6px;
    }
    .showing-data {
        display: none;
    }
    .mobile-filter-toggle {
        display: flex;
        justify-content: center;
        margin: 10px 0;
        align-items: center;
        column-gap: 10px;
        display: none;
    }
.mobile-filter-toggle span {
    font-size: 17px;
    text-transform: uppercase;
    font-weight: 500;
}
    .home-slider-container {
        width: 100%;
        margin-left: 0;
    }
    .feature-products p {
        padding-left: 20px;
    }
.feature-products {
    position: fixed;
    top: 0;
    z-index: 99999;
    background-color: #fff;
    left: -300px;
    width: 300px;
    padding-top: 10px;
    height: 100vh;
    overflow-y: auto;
    transition: all 0.3s ease;
}
.category-breadcrumb {
    justify-content: center;
}
.feature-products.active {
    left: 0;
}

    .mobile-menu.active {
    left: 0;
}
.mobile-menu {
    width: 300px;
    left: -300px;
    position: fixed;
    top: 0;
    z-index: 99999;
    background-color: #fff;
    height: 100vh;
    transition: all 0.3s ease;
    overflow-y: auto;
}
.mobile-menu::-webkit-scrollbar-track
{
	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
	background-color: #F5F5F5;
}

.mobile-menu::-webkit-scrollbar
{
	width: 6px;
	background-color: #F5F5F5;
}

.mobile-menu::-webkit-scrollbar-thumb
{
	background-color: #00aef0;
}

.mobile-search {
    background-color: #fff;
    padding: 15px 16px;
    padding-top: 0;
}
.mobile-search form {
    display: flex;
    border: 1px solid;
    border-radius: 5px;
    background-color: {{$generalsetting->secodery_color}};
    height: 40px;
    overflow: hidden;
    position: relative;
    border-color: hsla(0,0%,100%,.09);
}
.mobile-search form svg {
    height: 16px;
    width: 16px;
    color: #fff;
}
.mobile-search form input {
    text-align: left;
    padding-left: 15px;
    color: #000;
}

.mobile-search form button {
    background-color: {{$generalsetting->secodery_color}};
    flex: 0 0 60px;
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.mobile-menu-logo {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    padding: 10px 15px;
}
.logo-image img {
    height: 40px;
    width: auto;
}
.mobile-menu-close {
    height: 40px;
    width: 40px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
.mobile-menu-close i {
    font-size: 22px;
}
.first-nav .parent-category {
    display: block;
    line-height: 33px;
}
.first-nav .menu-category-list {
    display: block;
}
.first-nav .menu-category-list .menu-category-link {
    padding: 10px 0;
    display: block;
    padding-left: 20px;
}
.first-nav .parent-category .menu-category-name {
    display: block;
    padding: 10px;
    padding-left: 20px;
    text-transform: capitalize;
    font-weight: 600;
    color: #222;
}
.mobile-menu li.parent-category {
    position: relative;
}
.mobile-menu span.menu-category-toggle {
    position: absolute;
    right: 0px;
    top: 0px;
    display: flex;
    width: 50px;
    height: 50px;
    justify-content: center;
    align-items: center;
}
.mobile-menu span.menu-category-toggle.active i {
    transition: transform 0.3s ease;
}
.mobile-menu span.menu-category-toggle.active i {
    transform: rotate(180deg);
}
.second-nav {
    padding: 0 0px;
    background-color: #fff;
}
.second-nav.active {
    min-height: 15px;
}
.parent-subcategory {
    display: block;
    position: relative;
    padding: 0px;
}
.parent-subcategory .menu-subcategory-name {
    display: block;
    padding: 10px;
    padding-left: 40px;
}
span.menu-subcategory-toggle {
    position: absolute;
    top: 0;
    right: 0;
    display: flex;
    width: 50px;
    height: 50px;
    justify-content: center;
    align-items: center;
}

span.menu-subcategory-toggle.active i {
    transition: transform 0.3s ease;
}
span.menu-subcategory-toggle.active i {
    transform: rotate(180deg);
}
li.childcategory {
    display: block;
}
ul.third-nav {
    padding: 0;
    background-color: #f2f2f2;
}
li.parent-category.active {
    background-color: rgba(0,0,0,.05);
}
li.parent-subcategory.active {
    background-color: #fff;
}

.menu-childcategory-name {
    display: block;
    padding: 10px 0;
    padding-left: 60px;
}
header .toggle {
    margin-top: 0;
    padding-left: 30px;
}
.mobile-menu-social .mobile-social-list .mobile-social-link i {
    color: #c0c0c0;
    font-size: 16px;
}
.mobile-menu-social .mobile-social-list .mobile-social-link {
    border: 2px solid #c0c0c0;
    padding: 5px 13px;
    border-radius: 5px;
    display: block;
}
.mobile-menu-social .mobile-menu-social {
    display: flex;
    column-gap: 5px;
    padding-left: 20px;
}
    .logo-area {
        display: none;
    }
    .menu-area {
        display: none;
    }
    .header-left {
        justify-content: center;
    }
    .header-right {
        display: none;
    }
	.category-item {
	    padding: 5px 0;
	}
	.category-item p {
	    font-weight: 400;
	}
.qty-cart .quantity {
    height: 35px;
    margin-left: 4px;
}
	.quantity .minus,.quantity .plus {
	    height: 35px;
	    line-height: 35px;
	    width: 35px;
	    font-size: 35px;
	}
.d-flex.single_product.col-sm-6 {
    margin-left: 4px;
    margin-top: 10px;
}

	.qty-cart {
	    width: auto !important;
	}
	.cus-order-2 {
	    order: 2;
	}
	.cus-order-1 {
	    order: 1;
	}
	.chheckout-section {
	    padding: 10px 0;
	}
	.cart_details{
	    margin-bottom: 15px;
	}
    .success-img img {
        width: 200px;
    }
	.main_product_inner {
	    grid-template-columns: 1fr 1fr;
	}
	.qty-cart {
		grid-template-columns: 130px auto;
	}
	.quantity .minus {
		width: 40px;
	}
	.quantity .plus {
		width: 40px;
	} 
	.compare_store.mobile-show {
        display: block !important;
        line-height: 42px;
        text-align: center;
    }
    
	.add-to-cart.mobile-fix {
		position: fixed;
		bottom: 64px;
		left: 0;
		right: 0;
		z-index: 999;
		padding: 10px;
		margin: 0 10px;
	}
	.footer-top {
		padding: 30px 0;
	}
	.footer-menu ul li img {
		margin: 0 auto;
		display: block;
	}
	.front-view-flex {
		padding: 10px 0px;
	}
	.front_category_title h1 {
		font-size: 20px;
	}
	.front-view-item {
		margin-bottom: 15px;
	}
	.front-view-title a {
		font-size: 13px;
	}
	.home-page-section-title-box h3 {
		font-size: 20px;
	}
	.category-banner-products {
		padding: 20px 5px;
	}
    .flash-product-section .col-sm-4 {
        padding: 10px 10px !important;
    }
    .flash_all {
        margin-top: 10px;
    }
    .slider-section .offset-sm-3 {
        padding-left: 5px;
    }
	.main-header {
	    display: none;
	}
	.mobile-header {
	    display: block;
	    background-color: #fff;
	}
	.mobile-top {
	    background: #108BC3;
	    padding: 8px 0;
	}
	.mobile-top ul{
		text-align: right;
	}
	.mobile-top ul li a {
	    color: #fff;
	    margin: 0 5px;
	}
	.menu-bar i {
    font-size: 22px;
}
.mobile-logo {
    display: grid;
    grid-template-columns: 20% 60% 20%;
    text-align: center;
    font-size: 16px;
    height: 70px;
    align-items: center;
}
	.fixed-top .mobile-logo {
		margin-bottom: 0px;
	}
.menu-logo img {
    width: auto;
    height: 50px;
    margin-top: 0;
}
.footer-menu ul li a {
    text-align: center;
    text-transform: capitalize;
}
	.main-search.mobile-search {
	    margin: 18px 0;
	    padding: 0 10px;
	}
.menu-bar {
    margin-top: 0;
}

.menu-bag .margin-shopping {
    position: relative;
    width: 30px;
}
.menu-bag .margin-shopping span {
    position: absolute;
    display: inline-block;
    background-color: {{$generalsetting->secodery_color}};
    height: 20px;
    border-radius: 50px;
    padding: 1px 7px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    top: -10px;
    right: 0;
}
.menu-bag .margin-shopping span i.fa-solid.fa-bag-shopping {
    font-size: 22px;
}
.menu-bag {
    margin-top: 0px;
    display: flex;
    justify-content: flex-end;
    margin-right: 7px;
}
	.menu-bag ul li a {
	    margin-right: 15px;
	    position: relative;
	}
	.menu-bag li span {
	    background: #E62E04;
	    font-size: 10px;
	    color: #fff;
	    position: absolute;
	    top: 0;
	    left: 12px;
	    width: 15px;
	    height: 15px;
	    border-radius: 50px;
	    line-height: 15px;
	}

	.slider-item {
	    height: 150px;
	    margin-top: 0px;
	}
	.footer-about p {
	    text-align: center;
	}
	.footer-bottom {
    margin-bottom: 24px;
    padding-bottom: 77px;
}
	.footer-about {
        text-align: center;
        padding: 0 15px;
    }
	/* Footer V2: responsive handled in master; keep legacy .footer_nav for other pages */
	.footer_nav {
	    display: block;
	}
	.section-title-left h4 {
	    font-size: 16px;
	}
	.section-title-right a {
	    font-size: 13px;
	}
	.product-info .name {
	    height: 70px;
	}
	.product-info {
	    padding: 15px 10px;
	}
	.row>* {
	    padding-right: calc(var(--bs-gutter-x) * .3);
	    padding-left: calc(var(--bs-gutter-x) * .3);
	}
	.feature-title ul {
	    text-align: center;
	    overflow-y: scroll;
	}
	.feature-title h4 {
		text-align: center;
	    margin-bottom: 10px;
	}
	.footer-top {
	    padding-bottom: 32px;
	}

	.category-sidebar {
	    position: fixed;
	    z-index: 9999;
	    width: 100%;
	    top: 0;
	    background: #fff;
	    left: 0;
	    visibility: hidden;
	    opacity: 0;
	    transition: 0.35s all;
	}
	.close_filter {
	    position: absolute;
	    top: 0;
	    right: 12px;
	    border: 2px solid #ddd;
	    font-size: 19px;
	    padding: 0px 10px;
	    border-radius: 50px;
	    background: #d3b520;
	    color: #fff;
	}
	.close_filter,.show_filter {
	    display: block;
	}
	.show_filter {
	    display: inline-block;
	    margin-right: 10px;
	    margin-left: 8px;
	}
	.page-title h5 {
   		 font-size: 16px;
	}
	.product-section {
	    margin-top: 0;
	}
	.sort-form select {
	    font-size: 14px;
	}
	.category-sidebar.active {
	    visibility: visible;
	    opacity: 1;
	}
	.auth-section, .checkout-shipping {
      margin-top: 5px;
    }
	.payment-form .gap-3 {
	    gap: 0 !important;
	}
	.modal-view.quick-product {
    	width: 100%;
	}
	.quick-product .short_description,.quick-product .details_short {
	    display: none;
	} 	
	.quick-product-img {
	    width: 20%;
	}
	.quick-product-content {
	    width: 80%;
	}
	.close-modal {
	    left: 50%;
	    top: -17px;
	    transform: translateX(-50%);
	}
	.vcart-section {
	    margin-top: 60px;
	}
	.menu-product{
		display: none;
	}
	.details-wishlist {
        display: none !important;
    }
    a.details-wishlist.compare_store.cursor {
    display: none !important;
}
}
@media only screen and (min-width:767px) and (max-width:991px) {
.menu-product{
	display: none;
}

}
@media only screen and (min-width:992px) and (max-width:1140px) {


}
@media only screen and (min-width:1141px){


}


/* ============================================================
   GLOBAL OVERFLOW & BOX-SIZING FIX
   ============================================================ */
*,
*::before,
*::after {
    box-sizing: border-box;
}

html {
    overflow-x: hidden;
    max-width: 100%;
}

body {
    overflow-x: hidden;
    max-width: 100%;
}

img {
    max-width: 100%;
    height: auto;
}

/* ============================================================
   HEADER — DESKTOP FIXES
   ============================================================ */
@media only screen and (min-width: 992px) {
    header {
        width: 100%;
        max-width: 100%;
        overflow: visible;
    }

    .logo-header {
        grid-template-columns: 18% 60% 22%;
        align-items: center;
    }

    .main-search {
        width: 100%;
    }

    .main-search form {
        width: 100%;
        max-width: 100%;
    }

    .main-search form input {
        width: 88%;
    }

    .main-search form button {
        width: 12%;
    }

    .heder__category {
        grid-template-columns: 24% 8% 10% 44% 14%;
        overflow: hidden;
    }

    li.all__category__list {
        max-width: 100%;
        overflow: hidden;
    }

    .side__bar,
    .side__barsub,
    .side__barchild {
        max-width: 290px;
    }
}

/* ============================================================
   HEADER — TABLET FIXES (768px – 991px)
   ============================================================ */
@media only screen and (min-width: 768px) and (max-width: 991px) {
    .main-header {
        display: block;
    }
    .mobile-header {
        display: none;
    }

    .logo-header {
        grid-template-columns: 22% 52% 26%;
        gap: 8px;
        align-items: center;
    }

    .main-logo {
        height: 45px;
    }

    .main-logo img {
        height: 100%;
        width: auto;
    }

    .main-search form {
        width: 100%;
    }

    .main-search form input {
        width: 85%;
        font-size: 13px;
    }

    .main-search form button {
        width: 15%;
    }

    .heder__category {
        grid-template-columns: 28% 8% 12% 36% 16%;
        font-size: 14px;
    }

    li.all__category__list {
        width: 100%;
        font-size: 15px;
    }

    li.all__category__list i {
        padding-left: 20px;
    }

    .catagory_menu ul li a {
        font-size: 14px;
        margin: 0 5px;
    }

    #content {
        padding-top: {{ $hasNewsTicker ? '150px' : '120px' }};
    }
}

/* ============================================================
   SLIDER SECTION — RESPONSIVE
   ============================================================ */
@media only screen and (max-width: 991px) {
    .col-sm-3.hidetosm {
        display: none;
    }

    .col-sm-9 {
        width: 100%;
        flex: 0 0 100%;
        max-width: 100%;
    }

    .home-slider-container {
        width: 100%;
        margin-left: 0;
        padding: 0;
    }

    .slider-item {
        width: 100%;
        height: auto;
        max-height: 350px;
        overflow: hidden;
    }

    .slider-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

@media only screen and (max-width: 767px) {
    .slider-item {
        height: 180px;
    }

    .slider-item img {
        object-fit: cover;
    }
}

/* ============================================================
   CONTAINER — PREVENT OVERFLOW
   ============================================================ */
.container {
    max-width: 1200px;
    width: 100%;
    padding-left: 15px;
    padding-right: 15px;
    margin-left: auto;
    margin-right: auto;
}

@media only screen and (max-width: 767px) {
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }

    .row {
        margin-left: -5px;
        margin-right: -5px;
    }

    .row > * {
        padding-left: 5px;
        padding-right: 5px;
    }
}

/* ============================================================
   NEWS TICKER — RESPONSIVE
   ============================================================ */
@media only screen and (max-width: 767px) {
    .news-ticker-bar {
        height: 30px;
    }

    .news-ticker-label {
        font-size: 9px;
        padding: 0 8px;
        letter-spacing: 0.5px;
    }

    .news-ticker-item {
        font-size: 11px;
        padding-right: 40px;
    }

    .news-ticker-item::after {
        margin-left: 40px;
    }
}

/* ============================================================
   PRODUCT DETAILS — STICKY VIDEO
   ============================================================ */
@media only screen and (max-width: 767px) {
    section.pro_details_area .product-video-sticky-wrap {
        position: static;
        top: auto;
    }
}

/* ============================================================
   PREVENT HORIZONTAL SCROLL
   ============================================================ */
section,
.homeproduct,
.slider-section,
.brand-section,
.vendor-shops-section,
.blog-home-section,
.footer-v2 {
    max-width: 100%;
    overflow-x: hidden;
}

section.pro_details_area {
    overflow: visible;
}