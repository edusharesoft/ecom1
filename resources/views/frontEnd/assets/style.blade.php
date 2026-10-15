@php
    $generalsetting = \App\Models\GeneralSetting::first();
    $hasNewsTicker = $generalsetting
        && (int) ($generalsetting->news_ticker_enabled ?? 0) === 1
        && trim((string) ($generalsetting->top_headline ?? '')) !== '';
    $contentPaddingTop = $hasNewsTicker ? '156px' : '122px';
@endphp
@import url("https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800;900&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap");
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

body {
    font-family: "Roboto", sans-serif;
    font-size: 14px;
    overflow-x: hidden;
    line-height: 1.5;
    background: #ffffff;
}

/* ===== Customer Account Card ===== */
.account-card{
    background:#ffffff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 4px 18px rgba(0,0,0,0.06);
}

/* ===== Account Title ===== */
.account-title{
    color:#000;
    font-size:18px;
    font-weight:600;
    margin-bottom:8px;
}

/* divider line */
.account-divider{
    width:60px;
    height:3px;
    background:#0d6efd;
    border-radius:5px;
    margin-bottom:20px;
}

/* ===== Form ===== */
.account-card label{
    font-weight:600;
    font-size:14px;
    color:#333;
}

.account-card .form-control,
.account-card select{
    border:1px solid #ddd;
    height:42px;
    border-radius:8px;
    padding:8px 12px;
}

.account-card .form-control:focus{
    border-color:#0d6efd;
    box-shadow:none;
}

/* ===== Submit Button ===== */
.account-card .submit-btn{
    background:#0d6efd;
    border:none;
    padding:10px 28px;
    border-radius:25px;
    font-weight:600;
    font-size:14px;
    transition:.3s;
}

.account-card .submit-btn:hover{
    background:#084298;
}

 /* নতুন ড্যাশবোর্ড স্টাইল শুরু */
    :root{
        --blue:#0d6efd;
        --orange:#ff6a00;
        --dark:#1f1f2e;
        --bg:#f4f6fb;
        --card:#ffffff;
        --text:#1d1d1f;
        --muted:#7a7a7a;
        --radius:14px;
    }
    
    /* *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif;}  <-- এই লাইনটি পুরনো CSS-কে ওভাররাইড করতে পারে, তাই এখানে সতর্ক থাকুন। */
    body{background:var(--bg);color:var(--text);}
    
    /* ===== Layout ===== */
.dashboard{
    display:flex;
    max-width:1200px;     /* 👈 মাঝখানের width */
    margin:0 auto;        /* 👈 বাম–ডান gap auto */
}


    
    /* ===== Sidebar ===== */
    .sidebar{
        width:260px;
        background:#fff;
        padding:22px;
        border-right:1px solid #eee;
    }
    .profile{text-align:center;margin-bottom:24px;}
    .profile img{
        width:72px;height:72px;border-radius:50%;
        margin-bottom:10px;
    }
    .profile h4{font-size:15px;font-weight:600;}
    .profile p{font-size:12px;color:var(--muted);}
    
    .menu a{
        display:flex;align-items:center;gap:10px;
        padding:10px 14px;
        font-size:14px;
        color:#444;
        text-decoration:none;
        border-radius:10px;
        margin-bottom:6px;
    }
    .menu a i{width:18px;}
    .menu a.active,
    .menu a:hover{
        background:#fff0e6;
        color:#ff7a00;
    }
    
    .logout{
        margin-top:20px;
        background:var(--blue);
        color:#fff;
        text-align:center;
        padding:10px;
        border-radius:22px;
        text-decoration:none;
        display:block;
        font-size:14px;
    }
    
    /* ===== Main ===== */
    .main{flex:1;padding:26px;}
    
    /* ===== Top Cards ===== */
    .top-grid{
        display:grid;
        grid-template-columns:2.2fr 1fr;
        gap:20px;
        margin-bottom:20px;
    }
    
    .wallet{
        background:var(--dark);
        color:#fff;
        border-radius:var(--radius);
        padding:22px;
        position:relative;
        overflow:hidden;
    }
    .wallet::after{
        content:'';
        position:absolute;
        right:-40px;bottom:-40px;
        width:160px;height:160px;
        background:rgba(255,255,255,.05);
        border-radius:50%;
    }
    .wallet h5{font-size:13px;opacity:.7;}
    .wallet h2{margin:8px 0 4px;}
    .wallet small{opacity:.6;}
    .wallet button{
        margin-top:16px;
        border:1px solid #fff;
        background:transparent;
        color:#fff;
        padding:8px 18px;
        border-radius:20px;
        cursor:pointer;
        font-size:13px;
    }
    
    .right-cards{
        display:grid;
        grid-template-rows:1fr 1fr;
        gap:20px;
    }
    .blue-card,.orange-card{
        color:#fff;
        border-radius:var(--radius);
        padding:20px;
    }
    .blue-card{background:var(--blue);}
    .orange-card{background:var(--orange);}
    .blue-card h6,.orange-card h6{font-size:13px;opacity:.85;}
    .blue-card h3,.orange-card h3{margin:8px 0;}
    
    /* ===== Middle ===== */
.middle{
    display:flex;
    gap:20px;
}
.middle > *{
    flex:1;
}

    
    .stats{
        background:#fff;
        border-radius:var(--radius);
        padding:20px;
    }
    .stat-item{
        display:flex;align-items:center;gap:14px;
        margin-bottom:16px;
    }
    .stat-item:last-child{margin-bottom:0;}
    .stat-icon{
        width:38px;height:38px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;
        color:#fff;font-size:14px;
    }
    .red{background:#e74c3c;}
    .blue{background:#3498db;}
    .green{background:#2ecc71;}
    .stat-item h4{font-size:18px;}
    .stat-item p{font-size:12px;color:var(--muted);}
    
    .card{
        background:#fff;
        border-radius:var(--radius);
        padding:20px;
    }
    .card h5{font-size:15px;margin-bottom:8px;}
    .card p{font-size:13px;color:var(--muted);}
    .card button{
        margin-top:14px;
        background:var(--blue);
        border:none;
        color:#fff;
        padding:8px 18px;
        border-radius:20px;
        font-size:13px;
        cursor:pointer;
    }
    
    /* ===== Wishlist ===== */
    .wishlist{
        background:#fff;
        border-radius:var(--radius);
        padding:40px;
        text-align:center;
    }
    .wishlist img{max-width:220px;width:100%;}
    .wishlist p{margin-top:12px;font-size:14px;color:var(--muted);}
    
    /* ===== Responsive ===== */
    @media(max-width:1024px){
        .top-grid{grid-template-columns:1fr;}
        .middle{grid-template-columns:1fr;}
    }
    @media(max-width:768px){
        .dashboard{flex-direction:column;}
        .sidebar{width:100%;border-right:none;border-bottom:1px solid #eee;}
        .main{padding:16px;}
    }
    /* প্রোডাক্ট কার্ডের বাটন কন্টেইনার */
    .product_item .pro_btn{
        display:flex;
        align-items:stretch;
        gap:6px;                 /* দুই বাটনের মাঝে ছোট গ্যাপ */
        margin-top:6px;
        width:100%;
    }

    /* প্রো_btn এর সরাসরি সন্তান form / anchor */
    .product_item .pro_btn > form,
    .product_item .pro_btn > a{
        margin:0;
    }

    /* বাম দিকের বড় “অর্ডার করুন” বাটন – পুরো জায়গা নেবে */
    .product_item .pro_btn > form:first-child,
    .product_item .pro_btn > a.order-btn-link{
        flex:1 1 auto;
    }

    /* ডান পাশের ছোট কার্ট বাটন – ফিক্সড width */
    .product_item .pro_btn > form:last-child,
    .product_item .pro_btn > a.cart-icon-link{
        flex:0 0 44px;
        max-width:44px;
    }

    /* অর্ডার বাটনের ডিজাইন */
    .product_item .order-btn,
    .product_item .order-btn-link{
        display:flex;
        justify-content:center;
        align-items:center;
        width:100%;
        height:40px;
        padding:8px 12px;
        background:#d32f2f;
        color:#fff !important;
        border-radius:4px;
        font-size:14px;
        font-weight:600;
        cursor:pointer;
        font-family:"Potro Sans Bangla",sans-serif;
        transition:all .2s ease;
        text-align:center;
    }
    .product_item .order-btn:hover,
    .product_item .order-btn-link:hover{
        background:#b71c1c;
        border-color:#b71c1c;
        color:#fff !important;
    }

    /* কার্ট আইকন বাটন */
    .product_item .cart-icon-btn,
    .product_item .cart-icon-link{
        display:flex;
        justify-content:center;
        align-items:center;
        width:100%;
        height:40px;
        background:#fff;
        border-radius:4px;
        cursor:pointer;
        transition:all .2s ease;
    }
    .product_item .cart-icon-btn i,
    .product_item .cart-icon-link i{
        font-size:18px;
        color:#fff;
    }
    .product_item .cart-icon-btn:hover,
    .product_item .cart-icon-link:hover{
        background:#d32f2f;
        border-color:#d32f2f;
    }
    .product_item .cart-icon-btn:hover i,
    .product_item .cart-icon-link:hover i{
        color:#fff;
    }

    /* আগের গ্লোবাল প্রো_btn বাটন স্টাইল সামান্য ওভাররাইড */
    .product_item .pro_btn button{
        width:100%;
        border-radius:4px;
        background: {{$generalsetting->primary_color}};
    }
/*==== COMMON CSS START ====*/
@font-face {
    font-family: "Potro Sans Bangla";
    src: url("../fonts/Potro-Sans-Bangla-Regular.ttf");
    src: url("../fonts/Potro-Sans-Bangla-Regular.ttf?#iefix") format("embedded-opentype"), url("../fonts/Potro-Sans-Bangla-Regular.ttf") format("truetype");
}

@font-face {
    font-family: "Alinur Banglaborno";
    src: url("../fonts/Li-Alinur-Banglaborno-Unicode.ttf");
    src: url("../fonts/Li-Alinur-Banglaborno-Unicode.ttf?#iefix") format("embedded-opentype"), url("../fonts/Li-Alinur-Banglaborno-Unicode.ttf") format("truetype");
}

p {
    text-align: left;
    margin: 0;
    color: #000;
    padding: 0;
}

.alinur {
    font-family: "Alinur Banglaborno", sans-serif;
}

button,
button:focus,
button:active {
    outline: none !important;
    box-shadow: 0 0 !important;
}

form input::placeholder {
    font-size: 15px;
    color: #888;
    text-align: left !important;
    float: left !important;
}

ul,
ol {
    margin: 0;
    padding: 0;
}

li {
    display: inline-block;
    list-style: none;
}

a,
a:hover,
a,
a:hover,
a:focus {
    color: {{$generalsetting->primary_color}};
    text-decoration: none;
    outline-offset: 0;
    outline: 0;
}

button,
button:focus,
button:active {
    text-decoration: none;
    border: 0;
    outline: 0;
}

.form-control,
.form-control:focus,
input,
input:focus {
    outline: 0;
    border: 0;
    box-shadow: 0 0 !important;
}

.parsley-errors-list {
    color: #ff0018;
}

.parsley-error {
    border: 1px solid #ff0018 !important;
}

img {
    max-width: 100%;
    height: 100%;
}

button {
    padding: 0;
    border: 0;
}

h1,
h2,
h3,
h4,
h5,
h6 {
    font-family: "Poppins", sans-serif;
    margin: 0;
}

.potro_font {
    font-family: Potro Sans Bangla;
}

svg {
    height: 16px;
    width: 16px;
}

.cursor {
    cursor: pointer;
}

.float-left {
    float: left;
}

.float-right {
    float: right;
}

.container_97 {
    max-width: 1200px;
}

.container {
    max-width: 1200px;
}

footer {
    padding: 0 0;
    background: {{$generalsetting->footer_color}};
}

.footer-widgets {
    background-color: #7538aa;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding-top: 30px;
}

.footer-copyright {
    background-color: rgba(22, 112, 191, 0.98);
    padding: 15px 0 15px;
}

.footer-copy-text {
    color: hsla(0, 0%, 100%, 0.5);
}

.container_top {
    width: 975px;
    margin: 0 auto;
}

/*==== COMMON CSS END ====  */

/*==== HEADER CSS START ====  */
header {
    position: fixed;
    box-shadow: 0px 0px 10px #ddd;
    top: 0;
    width: 100%;
    z-index: 9999;
}

.header-top {
    background: rgba(0, 119, 204, 0.97);
    display: flex;
    height: 50px;
    align-items: center;
}

.header-left span {
    margin-right: 15px;
    color: #fff;
    text-transform: uppercase;
    font-size: 13px;
    font-weight: 500;
}

.header-left span a {
    color: #fff;
}

.header-left ul li a {
    margin: 0 5px;
}

.header-left ul {
    display: inline-block;
}

.header-right {
    text-align: right;
}

.header-right span ul {
    display: flex;
    justify-content: flex-end;
    column-gap: 4px;
}

.header-right a {
    color: #ffffffcc;
    border: 2px solid;
    font-size: 16px;
    border-radius: 5px;
    transition: 0.3s all;
    border-color: hsla(0, 0%, 100%, 0.5);
    height: 30px;
    width: 32px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.header-right a:hover {
    background: blue;
    border: 1px solid blue;
}

.Newsletter a {
    border: none;
    width: auto;
    margin-right: 8px;
}

.Newsletter a:hover {
    background: none;
    border: none;
}

li.Newsletter i {
    margin-right: 2px !important;
    font-size: 10px;
}

.header-left {
    font-size: 15px;
    height: 100%;
    display: flex;
    align-items: center;
}

.logo-area {
    padding: 8px 0;
}

.sticky.is-sticky {
    background: #222;
    z-index: 9999;
    border-bottom: 1px solid #ddd;
}

.logo-header {
    display: grid;
    grid-template-columns: 20% 58% 22%;
    grid-gap: 0;
}

/*search*/
.main-search {
    margin: 8px 0;
    position: relative;
}
.search_result {
    position: relative;
}
.search_product {
    position: absolute;
    width: 100%;
    background: #fff;
    z-index: 999999;
    border: 1px solid #ddd;
    top: 0;
}

.search_product img {
    width: 50px;
    height: 50px;
    margin-top: 6px;
    border-radius: 50px;
}

.search_product li {
    width: 100%;
    display: grid;
    grid-template-columns: 65px auto;
    grid-gap: 15px;
    padding: 8px 15px;
    border-bottom: 1px solid #ddd;
    transition: 0.35s all;
}

.search_product ul li:hover {
    background: #f1f1f1;
}

.search_content .price {
    color: #b74135;
    font-weight: 600;
}

.mobile-show {
    display: none !important;
}

.mobile-header {
    display: none;
}

.mobile-categories ul li {
    display: block;
    z-index: 99999;
    width: 100%;
    height: auto;
}

.mobile-categories {
    position: fixed;
    z-index: 99999;
    background: #fff;
    top: 0;
    width: 85%;
    transition: 0.35s all;
    left: -100px;
    visibility: hidden;
    opacity: 0;
    height: 100%;
}

.mobile-categories.active {
    left: 0;
    visibility: visible;
    opacity: 1;
}

.mobile-search input {
    width: 86% !important;
    text-align: center;
}

.mobile-search button {
    width: 13% !important;
}

.main-search form {
    border: 1px solid;
    height: 39px;
    background: #f7f7f7;
    border-color: {{$generalsetting->primary_color}};
    width: 100%;
    margin: 0 auto;
    border-radius: 5px;
    overflow: hidden;
}

.main-search form input {
    height: 100%;
    padding: 0 5px;
    font-size: 12px !important;
    background: #ffffff;
    width: 90%;
    float: left;
}

.main-search form button {
    height: 100%;
    outline: 0;
    background: {{$generalsetting->primary_color}};
    width: 10%;
    float: left;
}

.main-search form button svg {
    height: 20px;
    width: 35px;
    color: #fff;
}

.mobile-nav li a {
    text-transform: capitalize;
    padding: 8px 15px;
    display: block;
    border-bottom: 1px solid #ddd;
}

.mobile-menu .nav li button.active {
    border-radius: 0;
    color: #ff0018;
}

.main-search.mobile-search {
    margin: 0;
    padding: 0;
}

.main-search.mobile-search form {
    border: 1px solid #ddd;
}

.mobile-menu .nav li {
    width: 50%;
    float: left;
}

.mobile-menu .nav li button {
    margin: 0;
    padding: 12px 0;
    display: block;
    width: 100%;
    color: #222;
}

.nice-select {
    height: 41px !important;
    line-height: 41px !important;
}

.nice-select.open .list {
    z-index: 99999 !important;
}

.nice-select .option {
    display: block !important;
}

/*search end*/
.header-list-items {
    text-align: end;
    margin: 5px 0;
}

.header-list-items ul {
    margin-top: 7px;
}

.header-list-items ul li {
    text-align: center;
    padding: 0;
    margin-left: 12px;
    font-weight: 600;
}

.header-list-items ul li:first-child {
    margin-left: 0;
}

.for_order a i {
    font-size: 16px !important;
}

.header-list-items ul li a {
    display: inline-block;
}

.header-list-items ul li p {
    display: inline-block;
    text-align: center;
    position: relative;
    font-size: 15px;
    font-weight: normal;
}

.margin-shopping {
    margin-right: 10px;
}

.header-list-items ul li i {
    font-size: 20px;
}

.cart-svg {
    height: 22px;
    width: 22px;
}

.header-list-items ul li span {
    position: absolute;
    top: -8px;
    right: -12px;
    background: {{$generalsetting->primary_color}};
    color: #fff;
    height: 18px;
    width: 18px;
    line-height: 18px;
    font-size: 12px;
    border-radius: 50px;
}

.login-dialog {
    position: relative;
}

.login-box {
    position: absolute;
    top: 45px;
    width: 260px;
    padding: 15px;
    background: #fff;
    z-index: 9;
    box-shadow: 0 0 20px rgb(0 0 0 / 16%);
    left: 50%;
    transform: translateX(-50%);
    border-radius: 5px;
    visibility: hidden;
    opacity: 0;
    transition: 0.35s all;
}

.login-box:after {
    content: " ";
    bottom: 100%;
    left: 50%;
    border: solid transparent;
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-color: rgba(255, 255, 255, 0);
    border-bottom-color: #ffffff;
    border-width: 8px;
    margin-left: -8px;
}

.login-dialog:hover .login-box {
    top: 27px;
    visibility: visible;
    opacity: 1;
}

.login-box .form-control {
    margin: 10px 0;
    background-color: #fff !important;
    height: 40px;
    border: 1px solid #ddd;
}

.login-menu li {
    display: block;
    text-align: left !important;
    margin: 8px 0;
}

.login-menu li a {
    display: block;
    color: #222;
}

.forget-link {
    margin-bottom: 12px;
    margin-top: 4px;
    color: #2c1c1c;
    text-transform: uppercase;
    font-weight: 500;
    display: block;
    font-size: 12px;
    text-align: center;
}

.submit-btn {
    background: {{$generalsetting->primary_color}} !important;
    display: block;
    width: 100%;
    border: 0;
    border-radius: 50px;
    padding: 11px 0;
    text-transform: uppercase;
    margin: 6px 0;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
}

.register-now a {
    border: 1px solid #ddd;
    width: 100%;
    padding: 6px 0;
    border-radius: 5px;
    transition: 0.35s all;
}

.register-now a:hover {
    background: #ff0018;
    color: #fff;
}

#cart-qty {
    position: relative;
}

.cshort-summary {
    position: absolute;
    top: 55px;
    width: 390px;
    right: 0;
    max-height: 500px;
    z-index: 999;
    background: #fff;
    padding: 10px;
    box-shadow: 0px 0px 5px 1px #ddd;
    border-radius: 5px;
    opacity: 0;
    visibility: hidden;
    transition: 0.35s all;
}

.go_cart,
.go_cart:hover {
    background: {{$generalsetting->primary_color}};
    width: 100%;
    color: #fff;
    border-radius: 5px;
    padding: 10px 0;
    margin-top: 5px;
    display: block;
    text-align: center;
}

#cart-qty:hover .cshort-summary {
    top: 45px;
    opacity: 1;
    visibility: visible;
}
.cshort-summary ul li p {
    font-size: 15px;
    font-weight: 600;
    display: block;
    text-align: left;
}
.cshort-summary img {
    width: 45px;
    height: 45px;
    border: 1px solid #ddd;
    border-radius: 50px;
    padding: 5px;
}

.cshort-summary ul {
    display: grid;
    grid-template-columns: 15% 45% 20% 20%;
    margin: 5px 0;
}

.cshort-summary .cart_remove {
    margin-left: 5px;
}

.cshort-summary ul li {
    display: block;
    text-align: left;
    margin: 5px 0;
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
}

.cshort-summary .remove-cart,
.cshort-summary .remove-cart:focus {
    height: 20px;
    width: 20px;
    line-height: 20px;
}

.menu_view_all,
.menu_view_all:hover {
    position: absolute !important;
    bottom: 0;
    right: 0;
    border: none !important;
    color: #fff !important;
    background: #ff0018 !important;
    z-index: 99999999;
    display: inline-block !important;
    height: auto !important;
}

.menu-area {
    background: {{$generalsetting->primary_color}};
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}

.categories {
    position: relative;
    z-index: 99;
}

.categories ul li img {
    width: 16px;
}

.categories p {
    padding: 10px 10px;
    background: #ff0018;
    font-weight: 600;
    text-transform: uppercase;
    color: #fff;
    letter-spacing: 1px;
    cursor: pointer;
}

.categories .side-category {
    transition: 0.35s all;
    line-height: 1.8;
    text-align: left;
}

.categories .side-category li {
    position: relative;
    border-right: 1px solid #ddd;

}

.categories .side-category li a {
    position: relative;
    margin-right: 10px;
}

.categories .side-category li a i {
    position: absolute;
    right: 0;
}

.categories .sub-category li {
    display: block;
    position: relative;
}

.categories ul li a {
    display: block;
    padding: 10px 10px;
    color: #000;
    transition: 0.35s all;
    text-transform: uppercase !important;
}

.side-category li i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: 10px;
    font-size: 12px;
    color: #999;
}

.categories ul li a:hover {
    color: {{$generalsetting->primary_color}};
}

.sub-category {
    left: 0px;
    top: 100%;
    visibility: hidden;
    opacity: 0;
    min-width: 200px;
    transition: 0.35s all;
    position: absolute;
    background-color: #fff;
    text-align: left;
}

.categories ul li:hover>.sub-category {
    visibility: visible;
    opacity: 1;
}

.categories ul li a {
    position: relative;
    text-transform: capitalize;
}

.categories ul li img {
    width: 16px;
    height: 16px;
    margin-right: 3px;
}

.catagory_menu ul li a {
    color: #fff;
    margin: 0 8px;
    display: block;
    text-transform: capitalize;
    font-size: 16px;
}

.catagory_menu {
    padding: 0px;
}



.cat_bar i {
    padding-right: 5px;
    font-size: 16px;
}

.main-menu {
    text-align: right;
}

.main-menu ul li a {
    text-transform: uppercase;
    margin-left: 18px;
    margin-top: 10px;
    display: inline-block;
    font-weight: 500;
    color: #777;
    position: relative;
}

.main-menu ul li a:after {
    content: "";
    width: 0;
    height: 2px;
    left: 0;
    transition: 0.35s;
    bottom: 0;
    background: #ff0018;
    position: absolute;
}

.main-menu ul li a:hover:after {
    width: 100%;
}

.fixed-top {
    top: -170px;
    transform: translateY(170px);
    transition: transform 2s;
    background: #fff;
    box-shadow: 0px 0px 10px #c3c3c3;
    z-index: 999;
}

.main-logo {
    height: 55px;
    text-align: left;
}

.main-logo a {
    display: block;
    width: 100%;
    height: 100%;
}

.main-logo img {
    width: auto;
    height: 100%;
    margin-top: 0;
}

/*==== HEADER CSS END ====  */





/*==== CATEGORY SECTION CSS START ====  */
section.slider-section {
    margin-bottom: 10px;
    margin-top: 10px;
    overflow: hidden;
}

.home-slider-container {
    padding: 0;
    padding-left: 0px;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.homeproduct {
    padding-bottom: 0px;
    padding-top: 0px;
    background: #ffffff;
    margin-bottom: 10px;
    padding: 10px 0;
}

.category-section.section-padding {
    margin-bottom: 20px;
}

.section-title {
    padding: 20px 0;
}

.section-title h2 {
    font-size: 16px;
    font-weight: 600;
}

.front-category ul li a {
    color: #555;
    padding: 5px 8px;
    display: inline-block;
    border: 1px solid #ff0018;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 600;
    transition: 0.35s all;
    margin-right: 5px;
}

.front-category ul li a:hover {
    background: #ff0018;
    color: #fff;
    border-color: #ff0018;
}

.feature-btn {
    text-align: center;
    margin-top: 25px;
}

.feature-btn a {
    border: 1px solid #ddd;
    padding: 8px 25px;
    transition: 0.35s all;
    border-radius: 5px;
}

.feature-btn a:hover {
    background: #ff0018;
    color: #fff;
}

.sidebar_item {
    margin-bottom: 20px;
}

.sidebar_item h2.accordion-header button.accordion-button {
    background-color: {{$generalsetting->primary_color}};
    color: #fff;
    text-transform: uppercase;
    font-size: 14px;
    font-weight: 600;
    border-radius: 0;
    padding: 10px 20px;
}

.sidebar_item .accordion-header .accordion-button::after {
    content: '\f078';
    font-family: 'FontAwesome';
    background: none;
    font-size: 17px;
}

.accordion-item:last-of-type .accordion-button.collapsed {
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}

.accordion-item:last-of-type .accordion-collapse {
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}

.accordion-item .accordion-collapse {
    border-radius: 0;
}

.accordion-item {
    border-radius: 0;
}

/*==== CATEGORY SECTION CSS END ====  */

/*==== SLIDER CSS START ====  */
.main-slider {
    position: relative;
    z-index: -1;
}

.slider-section .offset-sm-3 {
    padding-left: 0;
}

.main-slider .owl-nav button span {
    font-size: 30px;
}

.main-slider .owl-nav button {
    height: 40px;
    width: 35px;
    background: #fff !important;
    transition: 0.35s all;
}

.main-slider .owl-nav button:hover {
    background: #ff0018 !important;
    color: #fff;
}

.main-slider .owl-prev {
    position: absolute;
    left: -10px;
    visibility: hidden;
    opacity: 0;
    top: 50%;
    transform: translateY(-50%);
    border-radius: 5px;
    transition: 0.35s all;
}

.main-slider .owl-next {
    position: absolute;
    right: -10px;
    visibility: hidden;
    opacity: 0;
    top: 50%;
    transform: translateY(-50%);
    border-radius: 5px;
    transition: 0.35s all;
}

.main-slider:hover.main-slider .owl-prev {
    left: 10px;
    visibility: visible;
    opacity: 1;
}

.main-slider:hover.main-slider .owl-next {
    right: 10px;
    visibility: visible;
    opacity: 1;
}

.slider-item {
    width: 100%;
    height: 400px;
    overflow: hidden;
}

.slider-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/*==== SLIDER CSS END ====  */

/*==== FRONT CATEGORY  CSS START ====  */
.hot-deals-section {
    padding: 25px 0;
}

.hot-deals-inner {
    background-color: #ffefcf;
    padding: 5px 5px 20px 5px;
}

.hot-deals-img img {
    width: 100px;
}

.hot-deals-product {
    border: 1px solid #e80a0a;
}

.deals-discount {
    position: absolute;
    top: 3px;
    right: 3px;
    color: #fff;
    padding: 0 10px;
}

.discount-wrapper {
    position: relative;
}

.discount-wrapper img {
    width: 45px !important;
}

.discount-wrapper span {
    position: absolute;
    top: 6px;
    left: 8px;
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.discount-wrapper span:last-child {
    top: 21px;
    left: 10px;
    font-size: 11px;
}

.hot-deals-btn {
    text-align: right;
}

.hot-deals-btn a {
    color: #ef4523;
    font-weight: 900;
    margin-top: 15px;
    display: inline-block;
    margin-right: 12px;
    font-size: 16px;
}

.hotdeals-slider-one {
    margin-bottom: 15px;
}

.hotdeal_price {
    position: absolute;
    background-color: #0089cf;
    right: 1px;
    bottom: 10px;
    padding-left: 10px;
    padding-right: 10px;
    color: white;
    font-weight: bold;
    border-top-left-radius: 50px;
    border-bottom-left-radius: 50px;
}

.custom_paginate {
    margin-top: 35px;
}

.custom_paginate .pagination {
    display: flex;
    justify-content: center;
}

/*==== FRONT CATEGORY  CSS END ====  */

/*==== FOOTER  CSS START ====  */

.section-title-right ul li a {
    text-transform: capitalize;
    margin-left: 10px;
    cursor: pointer;
}

.footer-menu ul li a:hover {
    color: #f1ffe7;
    margin-left: 2px;
}

.footer-menu ul li a {
    color: #959595;
    margin: 8px 0;
    display: block;
    transition: 0.35s all;
}

.footer-top {
    padding: 50px 0;
    
}

.footer-about {
    text-align: center;
}

.footer-about p {
    text-align: center;
    margin: 5px 0;
    color: #ffffff;
    font-size: 16px;
}

.footer-about h3 {
    font-size: 16px;
    font-weight: bold;
    color: #ffffff;
    margin: 10px 0;
}

.footer-about ul li a {
    display: block;
    height: 35px;
    line-height: 35px;
    width: 35px;
    border-radius: 50px;
    margin: 0 2px;
    text-align: center;
    color: #ffffff;
}

.footer-menu ul li a:hover {
    color: #ff0018;
    margin-left: 2px !important;
}

.footer-about ul li a .feather {
    color: #fff;
}

.footer-about {
    text-align: center;
    padding-right: 70px;
}

.footer-about a {
    display: block;
    color: #ffffff;
}

.footer-about a img {
    height: auto;
    width: 110px;
    object-fit: contain;
}

.footer-menu .title {
    text-transform: uppercase;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 10px;
    font-size: 15px;
}

.footer-menu ul li {
    display: block;
}

.footer-menu ul li a {
    color: #fff;
    margin: 8px 0 !important;
    display: block;
}

.footer-bottom {
    background: {{$generalsetting->copyright_color}};
    padding: 10px 0;
}

.footer-hotlint {
    color: #f1ffe7;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

.copyright p {
    text-align: center;
    color: #a0a0a0;
}

/*==== FOOTER CSS END ====  */

/*==== FOOTER FIXED MENU START ====  */
.footer_nav {
    display: none;
    text-align: center;
    padding: 10px 0;
    position: fixed;
    bottom: 0;
    width: 100%;
    background: #fff;
    z-index: 99;
    border-top: 1px solid #ddd;
}

.footer_nav ul {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
}

.footer_nav ul li a span {
    display: block;
}

.footer_nav ul li {
    position: relative;
}

.footer_nav ul li i {
    font-size: 16px;
}

.item_count {
    position: absolute;
    top: -5px;
    right: 25px;
    background: #ff0018;
    width: 15px;
    height: 15px;
    font-size: 12px;
    color: #fff;
    line-height: 15px;
    border-radius: 50px;
}

/*==== FOOTER FIXED MENU END ====  */

/*==== DETAILS CSS START ====  */
.product-section {
    padding: 30px 0;
    background: #fff;
    margin-bottom: 10px;
    margin-top: 10px;
}

.product-cart .name {
    font-size: 22px;
    font-weight: 600;
    text-transform: capitalize;
}

.details-price {
    font-size: 24px;
    font-weight: 600;
    color: {{$generalsetting->primary_color}};
    margin: 10px 0;
}

.product-code p {
    display: inline-block;
    background: {{$generalsetting->primary_color}};
    color: #fff;
    padding: 0px 10px;
    border-top: 15px solid transparent;
    border-bottom: 15px solid transparent;
    border-right: 15px solid #fff;
    line-height: 0;
    margin-bottom: 10px;
}

.details-price del {
    color: #bbb;
    margin: 5px 0;
    font-size: 19px;
}

.qty-cart .quantity {
    position: relative;
    border: 1px solid #222;
    height: 40px;
    overflow: hidden;
    width: 130px;
    margin-top: 10px;
}

.qty-cart {
    width: auto;
    display: flex;
    align-items: center;
    column-gap: 20px;
}

.quantity input {
    position: relative;
    text-align: center;
    font-size: 16px;
    height: 100%;
    width: 100%;
    pointer-events: none;
    font-weight: 500;
}

.quantity .minus {
    position: absolute;
    left: 0;
    bottom: 0;
    z-index: 1;
    height: 40px;
    line-height: 40px;
    width: 40px;
    border-right: 1px solid #222;
    text-align: center;
    font-size: 40px;
    cursor: pointer;
}

.quantity .plus {
    position: absolute;
    right: 0;
    bottom: 0;
    z-index: 1;
    height: 40px;
    line-height: 40px;
    width: 40px;
    border-left: 1px solid #222;
    text-align: center;
    font-size: 26px;
    cursor: pointer;
}

.order_now_btn {
    font-size: 18px;
    color: #fff;
    background-color: {{$generalsetting->primary_color}};
    border: 1px solid #000000;
    border-radius: 3px;
    width: 50%;
    margin-left: 5px;
    font-family: "Potro Sans Bangla";
    height: 45px;
    margin-top: 10px;
    border-radius: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 5px;
}

.order_now_btn:hover,
.order_now_btn:active {
    color: white !important;
    background-color: {{$generalsetting->primary_color}} !important;
    border-color: {{$generalsetting->primary_color}} !important;
    outline: 0;
    color: #fff !important;
}

.add_cart_btn {
    color: #fff;
    background-color: {{$generalsetting->primary_color}};
    border: 1px solid {{$generalsetting->primary_color}};
    border-radius: 0;
    width: 50%;
    height: 45px;
    margin-top: 10px;
    border-radius: 5px;
}

.call_now_btn {
    height: 45px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.call_now_btn i {
    margin-right: 10px;
}

.add_cart_btn:hover,
.add_cart_btn:active {
    color: #fff !important;
    background-color: #087bce !important;
    border-color: #087bce !important;
}

.features {
    border: 1px solid #ddd;
    border-top: 4px solid #000000;
    padding: 10px 10px 20px 10px;
}

.features .icon {
    width: 35px;
    font-size: 23px;
    height: 45px;
}

.features .text {
    font-size: 15px;
}

.feature-products {
    border-right: 1px solid #e8e8e8;
    height: 100%;
    padding-top: 32px;
}

.feature-products-wrapper {
    padding: 0px 10px;
}

.feature-products p {
    margin-bottom: 0;
    font-size: 18px;
    text-align: left;
    padding: 1px 0;
    font-family: "Jost", sans-serif;
    font-weight: 600;
    padding: 8px 0;
    position: relative;
}

.feature-products p::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    width: 40px;
    background-color: #ddd;
}

.feature-products tr {
    height: auto;
    padding: 0 10px;
    display: block;
    margin-top: 10px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.feature-products .img {
    width: 55px;
}

.feature-products .title {
    font-size: 14px;
    padding-bottom: 5px;
}

.delivery_details tr td {
    color: #000000 !important;
}

.delivery_details .potro_font {
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1px;
    padding-left: 0;
}

.related-title {
    margin-bottom: 15px;
}

.related-title h5 {
    font-weight: 600;
    font-size: 18px;
}

.tab-description li {
    display: block;
    position: relative;
    margin-left: 15px;
    margin: 5px 0;
    font-size: 15px;
}

.tab-content {
    padding: 30px 15px;
    margin-top: 20px;
    background-color: #fff;
    border-radius: 5px;
    margin-left: 12px;
}

.details-action-box .section-head {
    border-bottom: 1px solid #ddd;
    padding: 0 0 10px 0;
    display: flex;
}

.details-action-box .section-head .title {
    text-align: left;
    flex: 1 1 auto;
}

.section-head h2 {
    font-size: 1.2rem;
    font-weight: 600;
    padding: 10px 0;
    text-align: left;
    color: #000;
}

.details-action-box .section-head .action {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 0 0 auto;
}

.details-action-box .section-head .action .details-action-btn {
    padding: 5px 20px;
    background-color: #fff;
    border: 2px solid {{$generalsetting->primary_color}};
    border-radius: 5px;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: {{$generalsetting->primary_color}};
    cursor: pointer;
}

.review-card {
    background: #f1f1f1;
    padding: 15px;
    border-radius: 5px;
    position: relative;
    margin: 10px 0;
}

.review_star {
    color: #FF7E22;
    margin-bottom: 7px;
}

.details-action-box .empty-content {
    padding: 50px 0;
    text-align: center;
}

.details-action-box .empty-content i {
    font-size: 3rem;
    height: 80px;
    width: 80px;
    text-align: center;
    line-height: 80px;
    background-color: #2748f652;
    border-radius: 50%;
    color: {{$generalsetting->primary_color}};
}

p.empty-text {
    text-align: center;
}

.insert-review a {
    background: red;
    width: 300px;
    text-align: center;
    padding: 5px 9px;
    color: white;
    font-weight: 500;
}

/*===============*/
.rating {
    unicode-bidi: bidi-override;
    direction: rtl;
    text-align: left;
}

.rating>label {
    display: inline-block;
    position: relative;
    width: 28px;
    font-weight: 600;
    color: #009e60;
}

.rating label {
    font-size: 20px !important;
    cursor: pointer !important;
}

textarea#message-text {
    border: 2px solid;
    border-color: #009e60;
    max-width: 450px;
}

.rating>label.active:before,
.rating>label.active~label:before,
.rating>label:hover:before,
.rating>label:hover~label:before {
    content: "\2605";
    position: absolute;
    color: #009e60;
}

.rating input {
    display: none;
}

button.details-review-button {
    min-width: 126px;
    background: #0f821d;
    margin-top: 10px;
    padding: 7px;
    color: white;
    font-size: 14px;
    text-align: center;
    border-radius: 3px;
}

/*==== DETAILS CSS END ====  */

/*====  CATEGORY CSS START ====  */
.page_title p {
    color: #000000;
    font-weight: 600;
    font-size: 18px;
}

.page_title {
    margin-bottom: 25px;
}

.cust_according_body ul li {
    display: block;
}

.cust_according_body ul li a {
    display: block;
    padding: 5px 16px;
    font-size: 14px;
    font-weight: 600;
    color: #444;
}

.cust_according_body ul li:hover a {
    background-color: #548ef038;
}

.subcategory-filter-label {
    display: flex;
    column-gap: 10px;
    padding: 5px 10px;
    cursor: pointer;
    padding: 5px 16px;
    font-size: 14px;
    font-weight: 600;
}
.subcategory-filter-list p {
    color: #555;
}
.subcategory-filter-label:hover {
    background-color: #548ef038;
}

.subcategory-filter-label input {
    position: relative;
    border: 2px solid {{$generalsetting->primary_color}};
    border-radius: 2px;
    background: none;
    cursor: pointer;
    line-height: 0;
    margin: 0 .6em 0 0;
    outline: 0;
    padding: 0 !important;
    vertical-align: text-top;
    height: 20px;
    width: 20px;
    -webkit-appearance: none;
    opacity: .5;
    margin-top: 3px;
}

.subcategory-filter-label input[type=checkbox]:checked {
    background-color: #ff0018;
    opacity: 1;
}

.subcategory-filter-label input[type=checkbox]:before {
    content: '';
    position: absolute;
    right: 50%;
    top: 50%;
    width: 6px;
    height: 11px;
    border: solid #FFF;
    border-width: 0 2px 2px 0;
    margin: -1px -2px 0 0px;
    transform: rotate(45deg) translate(-50%, -50%);
    z-index: 2;
}

.filter-price-inputs {
    display: flex;
    justify-content: space-between;
}

p.min-price input {
    width: 70px;
}

p.max-price input {
    width: 70px;
}

.ui-slider-horizontal .ui-slider-range {
    background-color: {{$generalsetting->primary_color}};
}

.ui-state-default,
.ui-widget-content .ui-state-default,
.ui-widget-header .ui-state-default,
.ui-button,
html .ui-button.ui-state-disabled:hover,
html .ui-button.ui-state-disabled:active {
    border-radius: 50px;
}

.cust_according_body {
    padding: 10px 0;
}

.slider-box {
    padding: 10px 25px;
}
/*====  CATEGORY CSS END ====  */

/*====  QUICK VIEW CSS START ====  */
#page-overlay {
    display: none;
    position: fixed;
    height: 100%;
    width: 100%;
    background: rgba(0, 0, 0, 0.5);
    left: 0;
    top: 0;
    z-index: 9999;
}

#custom-modal {
    display: none;
}

.modal-view {
    position: fixed;
    width: 1000px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    z-index: 99999;
    padding: 30px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.close-modal {
    position: absolute;
    right: -10px;
    top: -13px;
    background: #ddd;
    height: 35px;
    width: 35px;
    border-radius: 50px;
    font-size: 18px;
}

.quick-view-inner {
    overflow: hidden;
}

.quick-product .name {
    text-transform: capitalize;
    font-size: 25px;
    font-weight: 500;
    color: #444;
    margin-bottom: 5px;
}

.quick-product-img {
    width: 40%;
    float: left;
}

.quick-product-content {
    width: 60%;
    float: left;
    padding: 0 20px;
}

/*====  QUICK VIEW CSS END ====  */

.sec_title {
    margin-bottom: 10px;
}

.section-title-header {
    border-bottom: 1px solid #ececec;
    padding-bottom: 10px;
}

.section-title-header .section-title-name {
    font-size: 20px;
    font-weight: 600;
    font-family: "Lato", sans-serif;
    padding-bottom: 10px;
    position: relative;
    bottom: -2px;
    text-transform: capitalize;
}

li.see_more_btn {
    text-align: end;
}

li.recent_pro {
    font-size: 15px;
}

.sec_title i {
    font-size: 10px;
    margin-left: 5px;
}

/*====  LOADING SPINNER CSS END ====  */
#loading {
    position: fixed;
    left: 50%;
    top: 0;
    z-index: 9999;
    display: none;
    background: rgba(255, 255, 255, 0.5);
    height: 100%;
    width: 100%;
    transform: translate(-50%);
    text-align: center;
}

.custom-loader {
    width: 50px;
    height: 50px;
    --c: radial-gradient(farthest-side, #ff0018 92%, #0000);
    background: var(--c) 50% 0, var(--c) 50% 100%, var(--c) 100% 50%, var(--c) 0 50%;
    background-size: 12px 12px;
    background-repeat: no-repeat;
    animation: s7 0.5s infinite;
    position: fixed;
    top: 50%;
    left: 50%;
}

@keyframes s7 {
    to {
        transform: rotate(0.5turn);
    }
}

/*====   LOADING SPINNER CSS END ====  */

/*====   WISHLIST MODAL CSS START ====  */
#custom-modal .title {
    font-weight: 600;
    font-size: 20px;
    color: #555;
    text-align: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 12px;
}

.wishlist-modal .quantity {
    font-weight: 500;
    color: #777;
}

.wishlist-modal {
    margin-top: 25px;
}

.wishlist-modal .name {
    text-align: left;
    margin: 0;
}

.wishlist-modal .price {
    color: #ff0018;
    font-size: 26px;
    font-weight: 600;
    margin: 12px 0;
}

.continue-confirm {
    display: grid;
    grid-template-columns: auto auto;
    grid-gap: 15px;
    margin-top: 15px;
}

.continue-btn {
    background: #ddd;
    border-radius: 5px;
    padding: 10px 0px;
    text-transform: uppercase;
    color: #666;
    font-weight: 600;
    transition: 0.35s all;
}

.confirm-btn {
    background: #ff0018;
    border-radius: 5px;
    padding: 10px 0px;
    text-transform: uppercase;
    color: #fff;
    font-weight: 600;
    display: block;
    text-align: center;
    cursor: pointer;
    transition: 0.35s all;
}

.continue-btn:hover,
.confirm-btn:hover {
    background: #ff0018;
    color: #fff;
}

/*====   WISHLIST MODAL CSS END ====  */

/*====   WISHLIST CSS START ====  */
.vcart-inner {
    background: #f5f7f9;
    padding: 20px 25px;
    border-radius: 5px;
}

.vcart-content img {
    width: 50px;
    height: 50px;
    text-align: center;
    border-radius: 50px;
    background: #fff;
    padding: 1px;
}

.cart_name {
    max-width: 185px;
}

.cart-title h4 {
    font-weight: 600;
    padding: 5px 10px;
    text-align: center;
}

.vcart-content table th {
    color: #666;
}

.vcart-content table td {
    font-size: 15px;
}

.remove-cart, .remove-cart:focus {
    background: #e20c15;
    border-radius: 50px;
    height: 30px;
    width: 30px;
    text-align: center;
    outline: 0;
}

.remove-cart .feather {
    color: #fff;
}

.wcart-btn,
.wcart-btn:focus {
    background: #ff0018;
    color: #fff;
    height: 30px;
    width: 30px;
    border-radius: 50px;
    outline: 0;
}

/*==== WISHLIST CSS END ====  */

/*====  CART CSS START ====  */
.vcart-qty .quantity {
    height: 35px;
    width: 85px;
    margin-top: 0 !important;
}

.vcart-qty .quantity .minus {
    left: 0;
    width: 25px;
    font-size: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.vcart-qty .quantity .plus {
    right: 0;
    height: 33px;
    line-height: 33px;
    width: 25px;
    font-size: 20px;
}

.cart-summary {
    background: #f5f7f9;
    padding: 15px;
    border-radius: 5px;
}

.cart-summary h5 {
    text-transform: uppercase;
    font-weight: 600;
    font-size: 15px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}
.cshort-summary p strong {
    font-size: 15px;
    display: block;
    margin: 8px 0;
}

.cart-summary table td {
    padding: 10px 0 !important;
    text-transform: capitalize;
    font-size: 15px;
}

.cart-summary table td:last-child {
    text-align: right;
}

.coupon-form {
    margin-top: 25px;
}

.coupon-form form {
    display: inline-block;
    width: 300px;
}

.coupon-form form input {
    width: 80%;
    float: left;
    height: 38px;
    border: 1px solid #ddd;
    border-radius: 5px 0px 0px 5px;
    padding: 0 10px;
    text-transform: capitalize;
}

.coupon-form form input {
    width: 80%;
    float: left;
    height: 38px;
    border: 1px solid #ddd;
    border-radius: 5px 0px 0px 5px;
}

.coupon-form form button {
    width: 20%;
    background: #ff0018;
    color: #fff;
    height: 38px;
    border-radius: 0px 5px 5px 0px;
}

/*====  CART CSS END ====  */



.submit-btnn {
    background: #3347af !important;
    display: block;
    width: 100%;
    border: 0;
    border-radius: 50px;
    padding: 11px 0;
    text-transform: uppercase;
    margin: 6px 0;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
}




/*====  LOGIN CSS START ====*/

/* ====== Stable-border Professional Login (no color change on typing) ====== */

/* Customize variables à¦¯à¦¦à¦¿ à¦¦à¦°à¦•à¦¾à¦° à¦ªà¦¡à¦¼à§‡ */
:root{
  --input-border: #dee1ff;
  --input-bg: #ffffff;
  --input-radius: 50px;
  --icon-color: #0a1b78;
}

/* container (à¦ªà§à¦°à¦«à§‡à¦¶à¦¨à¦¾à¦² à¦²à§à¦•) */
.form-content{
  background: #fff;
  border-radius: 15px;
  margin: 30px auto;
  padding: 28px 24px;
  max-width: 350px;
  border: 1px solid #d8dfff;
  box-shadow: 0 8px 22px rgba(0,0,0,0.06);
  box-sizing: border-box;
}

/* à¦Ÿà¦¾à¦‡à¦Ÿà§‡à¦² */
.auth-title{
  font-size: 18px;
  text-align: center;
  font-weight: 700;
  color: #fff;
  background: linear-gradient(135deg,{{$generalsetting->primary_color}},#3b4fb5);
  padding: 12px 16px;
  border-radius: 8px 8px 6px 6px;
  margin: -28px -24px 20px;
  text-transform: uppercase;
  letter-spacing: .4px;
}

/* à¦«à¦°à§à¦® à¦—à§à¦°à§à¦ª à¦“ à¦†à¦‡à¦•à¦¨ à¦ªà¦œà¦¿à¦¶à¦¨ */
.form-group{ position: relative; margin-bottom: 18px; }

/* à¦†à¦‡à¦•à¦¨ */
.form-group i,
.form-group .fa,
.form-group svg {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 16px;
  color: var(--icon-color);
  pointer-events: none;
}

/* à¦‡à¦¨à¦ªà§à¦Ÿ â€” DEFAULT */
.auth-section input,
.auth-section textarea,
.auth-section select {
  width: 100%;
  padding: 12px 12px 12px 22px;   /* à¦¬à¦¾à¦® à¦¥à§‡à¦•à§‡ à¦†à¦‡à¦•à¦¨à§‡à¦° à¦œà¦¨à§à¦¯ à¦¸à§à¦ªà§‡à¦¸ */
  border: 1px solid var(--input-border);
  background: var(--input-bg);
  border-radius: var(--input-radius);
  font-size: 14px;
  box-sizing: border-box;
  transition: none; /* à¦•à§‹à¦¨à§‹ à¦°à¦™/à¦¶à§à¦¯à¦¾à¦¡à§‹ à¦Ÿà§à¦°à¦¾à¦¨à¦œà¦¿à¦¶à¦¨ à¦¨à§‡à¦‡ */
  outline: none;
}

/* HOVER â€” à¦•à¦¿à¦¨à§à¦¤à§ à¦¬à¦°à§à¦¡à¦¾à¦° à¦à¦•à¦‡ à¦¥à¦¾à¦•à¦¬à§‡ */
.auth-section input:hover,
.auth-section textarea:hover,
.auth-section select:hover {
  border: 1px solid var(--input-border);
  box-shadow: none;
}

/* FOCUS â€” **à¦…à¦ªà§‡à¦•à§à¦·à¦¿à¦¤ à¦ªà¦°à¦¿à¦¬à¦°à§à¦¤à¦¨ à¦¨à§‡à¦‡**: à¦à¦•à¦‡ à¦¬à¦°à§à¦¡à¦¾à¦°, à¦•à§‹à¦¨ à¦¶à§à¦¯à¦¾à¦¡à§‹/à¦°à¦™ à¦ªà¦°à¦¿à¦¬à¦°à§à¦¤à¦¨ à¦¨à§‡à¦‡ */
.form-content .auth-section input:focus,
.form-content .auth-section textarea:focus,
.form-content .auth-section select:focus {
  border: 1px solid var(--input-border) !important;
  background: var(--input-bg) !important;
  box-shadow: none !important;
  outline: none !important;
  color: inherit !important;
}




input:-webkit-autofill,
textarea:-webkit-autofill {
  -webkit-box-shadow: 0 0 0px 1000px var(--input-bg) inset !important;
  box-shadow: 0 0 0px 1000px var(--input-bg) inset !important;
  -webkit-text-fill-color: inherit !important;
}


.register-now.no-account a {
  display: block;
  text-align: center;
  margin-top: 12px;
  color: var(--link-color); 
  text-decoration: none;
  font-weight: 600;
  padding: 8px 12px;
  border-radius: 50px; 
  transition: background 0.3s, color 0.3s;
}

.register-now.no-account a:hover {
  background: {{$generalsetting->primary_color}}; 
  color: #ffffff;   
}




/*====  LOGIN CSS END ====*/

/*====  BRAND CSS START ====  */

.brand-section {
    position: relative;
}

.brand-item img {
    opacity: 0.4;
    transition: 0.35s all;
}

.brand-item img:hover {
    opacity: 1;
}

.brand-slider .owl-nav button span {
    font-size: 35px;
}

.brand-slider .owl-nav button {
    height: 50px;
    width: 45px;
    background: #ddd !important;
    transition: 0.35s all;
}

.brand-slider .owl-nav button:hover {
    background: #ff0018 !important;
    color: #fff;
}

.brand-slider .owl-prev {
    position: absolute;
    left: -50px;
    top: 50%;
    transform: translateY(-50%);
    border-radius: 5px;
}

.brand-slider .owl-next {
    position: absolute;
    right: -50px;
    top: 50%;
    transform: translateY(-50%);
    border-radius: 5px;
}

/*====  BRAND CSS END ====  */

/*====  PROFILE CSS START ====  */
.customer-auth {
    display: grid;
    grid-template-columns: 60px auto;
    grid-gap: 10px;
    background: none;
    padding: 10px;
    border-radius: 5px;
}

.customer-img img {
    border-radius: 50px;
}

.customer-section {
    padding: 20px 0;
}

.sidebar-menu {
    background: #f5f7f9;
    margin: 15px 0;
    border-radius: 5px;
}

.sidebar-menu ul li {
    display: block;
}

.sidebar-menu li a {
    padding: 10px 10px;
    display: block;
    font-size: 15px;
}

.customer-content {
    background: #092be142;
    padding: 15px;
    border-radius: 5px;
}

.backend_img {
    height: 80px;
    width: 80px;
    border-radius: 50px;
}

.account-title {
    margin-bottom: 15px;
    font-size: 16px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 8px;
    text-align: left;
    font-weight: 600;
    color:white 
}

.invoice_btn,
.invoice_btn:focus {
    background: #ff0018;
    color: #fff;
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 5px;
}

.sidebar-menu li a.active {
    color: #ff0018;
}

/*====  PROFILE CSS END ====  */

.payment_option {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr !important;
}

/*====  CHECKOUT CSS START ====  */
.cartlist img{
    height:30px;
    width:30px;
}
.cartlist span{
    height:20px;
    width:20px;
    border-radius:50px;
}
.chheckout-section {
    padding: 35px 0;
}

.checkout-shipping .card-header {
    background: #f5f7f9;
    padding: 10px 15px;
}

.checkout-shipping h5, .cart_details h5 {
    font-size: 16px;
    text-transform: uppercase;
    font-weight: 600;
    color: #000;
}
.cartlist .text-left {
    text-align: left;
}
.checkout-shipping label {
    margin-bottom: 5px;
    font-size: 17px;
    font-family: Potro Sans Bangla;
    font-weight: 600;
}

.checkout-shipping h6 {
    font-weight: 600;
    color: {{$generalsetting->primary_color}};
}
.checkout-shipping select {
    font-size: 14px;
}

.checkout-shipping input,
.checkout-shipping input:focus,
.checkout-shipping select,
.checkout-shipping select:focus {
    border: 1px solid #ddd;
    height: 40px;
}

.select2-container--default .select2-selection--single {
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
    height: 40px !important;
}

#select2-district-container {
    line-height: 40px;
}

.select2-results li {
    display: block;
}
.checkout-shipping .form-check-input {
    height: 14px !important;
}
.nagadform p,
.bkashform p,
.rocketform p {
    padding: 5px 0;
}

.bkashform,
.nagadform,
.rocketform {
    display: none;
    background: #f5f7f9;
    padding: 20px;
    margin: 15px 0;
    border-radius: 5px;
}

.order_place {
    background: {{$generalsetting->primary_color}};
    display: block;
    width: 100%;
    border-radius: 5px;
    padding: 10px 0;
    color: #fff;
    text-transform: uppercase;
    font-weight: 600;
    font-size: 16px;
    margin-top: 15px;
    transition: 0.35s all;
}

.order_place:hover {
    background: #ff0018;
}

/*====  CHECKOUT CSS END ====  */

/*====  CONTACT CSS START ====  */
.contact-section {
    padding: 15px 0;
    background: #fff;
    margin-bottom: 10px;
}

.contact-form input,
.contact-form input:focus,
.contact-form textarea,
.contact-form textarea:focus {
    border: 1px solid #ddd;
    border-radius: 0;
    background: {{$generalsetting->primary_color}}00;
    color: #ffffff; /* typed text white */
}

.contact-form input::placeholder,
.contact-form textarea::placeholder {
    color: rgba(255,255,255,0.7); /* placeholder light white */
}


.contact-form label {
    margin-bottom: 5px;
    font-weight: 600;
    font-size: 15px;
    color:white
}

.social-media.footer-about {
    text-align: left;
    margin-top: 20px;
}

.social-media.footer-about li a {
    text-align: center;
}

.social-media.footer-about h6 {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 15px;
}

/*====  CONTACT CSS END ====  */

/*====  ALL CATEGORY CSS START ====  */
.filter_btn, .filter_close {
    display: none;
}
.category-thumb {
    background: #f1f1f1;
    text-align: center;
    padding: 25px 15px;
    border-radius: 5px;
}

.category-thumb img {
    width: 24px;
}

.all-category {
    display: grid;
    grid-template-columns: repeat(5, 20%);
    grid-gap: 15px;
}

.category-thumb p {
    text-align: center;
    text-transform: uppercase;
    margin-top: 10px;
}

.menu-more {
    color: #ff0018 !important;
    font-weight: 600;
}

.menu-more i {
    color: #ff0018 !important;
    font-weight: 600;
}

/*====  ALL CATEGORY CSS END ====  */

.page-description ul li {
    display: list-item;
    list-style: initial;
}

.page-description ul {
    padding-left: 20px;
}

.front-view-flex {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 20px 50px;
}

.front-view-image {
    margin-bottom: 15px;
}

.front-view-image a {
    display: block;
}

.front-view-image a img {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    transform: translateY(0px);
    transition: all 0.3s ease;
}

.front-view-item:hover .front-view-image a img {
    transform: translateY(-20px);
}

.front-view-title a {
    display: block;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
}

.category-banner-products {
    border-top: 1px dashed #ccc;
    padding-top: 20px;
    padding-bottom: 20px;
    padding-right: 7px;
    background: #fff;
}

.home-page-section-title-box {
    margin-bottom: 10px;
}

.home-page-section-title-box h3 {
    font-size: 30px;
    margin-top: 0;
    text-transform: capitalize;
    font-weight: 600;
}

.view-all-button-box.pull-right {
    float: right;
}

a.custom-button {
    border: 1px solid transparent;
    padding: 5px 15px;
    border-radius: 5px;
    color: #000;
    font-weight: 500;
    box-shadow: 0 0.275rem 0.75rem -0.0625rem rgb(11 15 25 / 6%), 0 0.125rem 0.4rem -0.0625rem rgb(11 15 25 / 3%) !important;
    display: inline-block;
    position: relative;
    background: transparent;
    transition: color 0.1s linear 0.05s;
    text-decoration: none;
}

.product-item-box {
    box-shadow: 0 2px 10px -1px rgb(0 0 0 / 12%);
    border-radius: 15px;
    background: #fff;
    margin-bottom: 15px;
}

.product-img-outer-box {
    position: relative;
    overflow: hidden;
}

.product-img-outer-box a {
    border-radius: 10px 10px 0px 0px;
}

.product-img-outer-box a img {
    transform: scale(1);
    transition: all 0.5s ease-out;
    object-fit: contain;
    background: #fff;
    width: 100%;
}

.product-img-outer-box a:hover img {
    transform: scale(1.1);
    transition: all 0.5s ease-out;
}

.product-desc-main-box {
    padding: 5px;
    text-align: center;
}

.product-title-box h4 {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}

.product-title-box h4 a {
    font-size: 14px;
}

.product-price-box h4 {
    font-weight: bold !important;
    font-size: 14px;
    color: #b70053;
    font-family: var(--font);
}

.product-inner button.owl-prev,
.related-product-section button.owl-prev,
.product-inner button.owl-next,
.related-product-section button.owl-next {
    top: 50%;
    position: absolute;
    display: inline-block;
    transform: translateY(-50%);
}

.product-inner button.owl-prev i,
.related-product-section button.owl-prev i,
.product-inner button.owl-next i,
.related-product-section button.owl-next i {
    width: 35px;
    height: 35px;
    background-color: #ddd;
    border-radius: 50%;
    line-height: 35px;
    text-align: center;
    transform: scale(1);
    transition: all 0.8s cubic-bezier(0.075, 0.82, 0.165, 1);
}

.product-inner button.owl-prev:hover i,
.related-product-section button.owl-prev:hover i,
.product-inner button.owl-next:hover i,
.related-product-section button.owl-next:hover i {
    background-color: #ff0018;
    border-radius: 50%;
    line-height: 35px;
    text-align: center;
    transform: scale(1.3);
    color: #fff;
}

.product-inner button.owl-prev,
.related-product-section button.owl-prev {
    left: 0;
}

.related-product-section button.owl-prev {
    left: -40px;
}

.product-inner button.owl-next,
.related-product-section button.owl-next {
    right: 0;
}

.related-product-section button.owl-next {
    right: -40px;
}

section.product-inner {
    border-top: 1px dashed #ccc;
    padding-top: 20px;
    padding-bottom: 20px;
}

.section-title-left h4 {
    font-size: 30px;
}

.product-img-outer-box a {
    position: relative;
    overflow: hidden;
    height: 260px;
    display: block;
}

.product-inner .product-img-outer-box a {
    height: 290px;
}

.category-banner-products .product-img-outer-box a {
    position: relative;
    overflow: hidden;
    height: auto;
    display: block;
}

.slide-img-box {
    border-radius: 10px;
    overflow: hidden;
}

.category-img-banner {
    margin-bottom: 15px;
}

.category-img-banner a {
    display: block;
    overflow: hidden;
}

.category-img-banner a img {
    transform: scale(1);
    transition: all 0.3s cubic-bezier(0.215, 0.61, 0.355, 1);
    border-radius: 15px;
}

.category-img-banner:hover a img {
    transform: scale(0.95);
}

.category-main-section {
    border-top: 1px dashed #ccc;
    padding: 20px 0;
}

.menu-logo a {
    display: block;
}

.campaign-item a {
    display: block;
    width: 100%;
    height: 420px;
    overflow: hidden;
    position: relative;
}

.campaign-item a img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
}

.campaign-main-section {
    border-top: 1px dashed #ccc;
    padding: 25px 5px;
}

.qty-cart .add-to-cart {
    text-transform: uppercase;
    font-weight: 600;
    font-size: 15px;
    background: #087bce;
    border-radius: 0;
    color: #fff;
    height: 48px;
    width: 160px;
}

.section-meta-description {
    background-color: #fff;
    box-shadow: 0px 0px 10px #ddd;
    padding: 40px;
    border-radius: 20px;
    margin-top: 30px;
    margin-bottom: 30px;
}

/*Design By Milon */

.sec_title H2 {
    font-size: 17px;
    margin-bottom: 15px;
    font-weight: 600;
}

.category-breadcrumb a {
    font-size: 16px;
    color: #666666b3;
    font-weight: 400;
}

.category-breadcrumb span {
    color: #666666b3;
}

.category-breadcrumb strong {
    font-size: 16px;
}

.main_product_inner {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
    grid-gap: 10px;
    overflow: hidden;
}

.category-product {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
}

.product_item_inner {
    border-bottom: 0;
    transition: 0.35s all;
}

.product_item_inner .sale-badge {
    position: absolute;
    top: 15px;
    right: 4px;
    z-index: 1;
}

.product_item_inner .sale-badge-inner {
    --sale-badge-width: 45px;
    width: var(--sale-badge-width);
    height: var(--sale-badge-width);
}

.product_item_inner .sale-badge-box {
    background-color: #ff0000;
    border-radius: 50%;
    height: 40px;
    width: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.product_item_inner span.sale-badge-text {
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    font-family: "Lato", sans-serif;
   
}

.wist_item {
    position: relative;
}

.quick_view_hard {
    position: absolute;
    opacity: 0;
    z-index: 0;
    visibility: hidden;
    transition: 0.4s all;
    top: 0px;
    right: 5px;
}

.wist_item:hover .quick_view_hard {
    opacity: 1;
    overflow: visible;
    z-index: 1;
    visibility: visible;
}

.quick_view_hard a {
    border: 2px solid;
    border-color: silver;
    border-radius: 50%;
    font-size: 20px;
    height: 38px;
    width: 38px;
    z-index: 1;
    transition: 0.3s all;
    color: silver;
    display: flex;
    justify-content: center;
    align-items: center;
}

.quick_view_hard a:hover {
    background: #a92c2c;
    color: #fff;
    border: 2px solid #a92c2c;
}

.product_item:hover .product_item_inner {
    border-color: transparent;
}

.product_item {
    margin-bottom: 30px;
    position: relative;
    border: 1px solid #eaeaea;
    border-radius: 8px;
    background: #fff;
    transition: 0.35s all;
    padding: 13px;
    z-index: 999;
    margin-top: 6px;
    overflow: hidden;
}

.product_item:hover {
    margin-top: 0px;
    border-color: {{$generalsetting->primary_color}};
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}
.cart_btn.order_button a {
    background: #222;
    color: #fff;
    font-size: 14px;
    text-transform: capitalize;
    display: block;
    transition: all .5s ease;
    border-radius: 5px;
    font-weight: 600;
    height: 36px;
    line-height: 36px;
    position: relative;
    width: 100%;
    text-align: center;
    overflow: hidden;
    cursor: pointer;
}
.cart_btn.order_button a::after {
    content: '\f07a';
    font-family: fontAwesome;
    display: block;
    height: 36px;
    position: absolute;
    top: 36px;
    width: 100%;
    transition: all 0.3s ease;
    left: 0;
    text-align: center;
}
.cart_btn.order_button a:hover::after {
    top: 0px;
}
.cart_btn.order_button a span {
    height: 36px;
    display: block;
    position: absolute;
    top: 0;
    line-height: 36px;
    width: 100%;
    transition: all 0.3s ease;
}
.cart_btn.order_button a:hover span {
    top: -36px;
}

.quick_view_btn {
    width: 100%;
    background: #1d2224;
    position: absolute;
    opacity: 0;
    z-index: 0;
    visibility: hidden;
    transition: 0.4s all;
    padding: 5px 0px;
    text-align: center;
    bottom: -45px;
}

.product_item:hover .quick_view_btn {
    opacity: 1;
    z-index: 1;
    visibility: visible;
    bottom: 0;
}

.quick_view_btn button {
    font-size: 15px;
    color: white;
    background-color: transparent;
    font-weight: 500;
    text-transform: uppercase;
}

.product_item:hover .quick_view_hard {
    opacity: 1;
    overflow: visible;
    z-index: 1;
    visibility: visible;
}

.pro_name {
    height: 50px;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 4;
    overflow: hidden;
    display: block;
    margin-top: 5px;
    /* padding: 0 5px; */
    text-align: left;
}

.pro_name a {
    color: #000;
    font-size: 15px;
    text-transform: capitalize;
    font-weight: 500;
}

.pro_img {
    height: 200px;
    position: relative;
    overflow: hidden;
}

.pro_img>a {
    display: block;
    height: 100%;
}

.pro_img img {
    height: 100%;
    width: 100%;
    object-fit: contain;
    transition: 0.35s all;
}

.product_item_inner:hover .pro_img img {
    transform: scale(1.1);
    transition: opacity 0.5s ease,transform 2s cubic-bezier(0, 0, 0.44, 1.18);
}

.pro_des {
    text-align: center;
}

.pro_price {
    margin-bottom: 5px;
    margin-top: 18px;
}

.pro_price p {
    color: #000000;
    font-weight: 600;
    margin-top: 5px;
    text-align: left;
    font-size: 18px;
}
.product_item i {
    color: #e1031f;
}
.pro_btn form {
    text-align: center;
}

.pro_btn {
    display: grid;
    grid-template-columns: 1fr;
    grid-gap: 4px;
}

.pro_btn button {
    background: {{$generalsetting->primary_color}};
    color: #fff;
    width: 60%;
    padding: 6px 0px;
    font-size: 16px;
    text-transform: uppercase;
    display: block;
    width: 100%;
    transition: all .5s ease;
}

.cart_btn a {
    background: {{$generalsetting->primary_color}};
    width: 100%;
    display: block;
    height: 100%;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    transition: all .5s ease;
    padding:5px
}

.cart_btn a:hover {
    background: #1d2224;
}

.pro_btn button:hover {
    background: #1d2224;
}

.pro_price del {
    color: #999;
    margin-right: 5px;
    font-size: 14px;
    font-weight: 500;
}

.discount {
    position: absolute;
    top: 5px;
    background: #ff0018;
    padding: 2px 7px;
    border-radius: 20px;
    right: 6px;
}

.discount p {
    color: #fff;
    font-size: 11px;
}

.pro_name a:hover {
    text-decoration: underline;
}

.success-img {
    text-align: center;
}

.success-img img {
    width: 320px;
}

.success-title h2 {
    color: {{$generalsetting->primary_color}};
    text-align: center;
    font-weight: 600;
    font-size: 26px;
    margin-bottom: 45px;
}

.success-table {
    background: #3f51b50f;
    padding: 15px;
}

section.createpage-section {
    padding: 50px 0;
    background: #fff;
    margin-bottom: 10px;
}

.category-slider {
    margin-top: 25px;
}

.category-item {
    padding: 25px 0;
}

.category-item p {
    font-weight: 400;
}

.category-item img {
    border: 1px solid #ddd;

    transition: 0.35s all;
}

.category-item img:hover {
    transform: scale(1.09);
}

.category-item p {
    text-align: center;
    margin-top: 5px;
    color: #222;
    font-weight: 500;
}

button.owl-next {
    font-size: 30px !important;
    width: 29px;
    height: 29px;
    line-height: 10px !important;
    text-align: center !important;
    border-radius: 50%;
    color: white !important;
}

button.owl-prev {
    font-size: 30px !important;
    width: 29px;
    height: 29px;
    line-height: 10px !important;
    text-align: center !important;
    border-radius: 50%;
    color: white !important;
}


.cat_down {
    font-size: 10px !important;
    position: absolute;
    top: 20px;
    right: 0;
}

li.cat_bar>a {
    padding: 13px 5px;
    display: flex;
    width: 100%;
    height: 50px;
    font-size: 15px;
    background-color: #ff0018;
    margin-left: 0 !important;
    justify-content: space-between;
    align-items: center;
    text-transform: uppercase;
    font-weight: 500;
}

li.cat_bar {
    position: relative;
    margin-right: 0px;
}

.menu-area .Cat_menu {
    position: absolute;
    background-color: #fff;
    top: 100%;
    transition: 0.35s all;
    border: 1px solid rgba(0, 0, 0, 0.075);
    z-index: 999;
    visibility: hidden;
    opacity: 0;
    /* color: black; */
    width: 230px;
}

.catagory_menu li:hover .Cat_menu {
    visibility: visible;
    opacity: 1;
}

.main-header .menu-area .cat_bar.active .Cat_menu {
    visibility: visible;
    opacity: 1;
}

.fixed-top .menu-area .cat_bar.active .Cat_menu {
    visibility: hidden;
    opacity: 0;
}

.main-header.sticky .menu-area .cat_bar:hover .Cat_menu,
.main-header .menu-area .cat_bar:hover .Cat_menu {
    visibility: visible;
    opacity: 1;
    top: 100%;
}

ul.Cat_menu li a i {
    position: absolute;
    top: 15px;
    right: 5px;
    color: #000;
}

.Cat_list {
    display: block !important;
}

span.Cat_img img {
    width: 25px !important;
}

ul.Cat_menu li {
    padding: 10px 0px;
    border-bottom: 1px solid #ddd !important;
    width: 100%;
    transition: 0.35s all;
    position: relative;

}

ul.Cat_menu li a span {
    color: #000;
    font-weight: 600;
}

li.cat_bar span {
    padding-right: 10px;
}

li.Cat_list i {
    text-align: end;
    width: 20px;
}

ul.child_menu li {
    background: none;
    width: 100%;
    border-bottom: 0px !important;
    padding: 7px 0;
    border-bottom: 1px solid #ddd !important;
}

li.child_main {
    width: 33.33%;
}

ul.child_menu li a {
    background: none;
    color: #087bce;
}

.child_main>a {
    font-size: 14px;
    color: #444 !important;
    text-transform: capitalize !important;
    position: relative;
    height: 100%;
}

ul.child_sub li a {
    display: block;
    padding: 5px 0;
    margin: 0;
    font-size: 16px;
    border-bottom: 1px solid #ddd;
}

ul.child_sub li {
    padding: 0px;
    margin: 0px;
    margin-left: 3px !important;
}

ul.child_sub li a {
    color: #666 !important;
}

li.child_main>a {
    font-size: 14px;
    font-weight: 500;
    color: #000 !important;
    text-transform: uppercase;
    font-weight: 600;
    padding-bottom: 6px;
}

.cat_list_hover:hover .child_menu {
    overflow: visible;
    visibility: visible;
    opacity: 1;
    z-index: 1;
}

.child_menu {
    position: absolute;
    left: 100%;
    background: white;
    width: 230px;
    overflow: hidden;
    visibility: hidden;
    opacity: 0;
    z-index: 1;
    top: 0px;
    border-left: 1px solid #ddd;
    transition: 0.25s all;
}

.cat_list_hover1:hover .child_menu {
    overflow: visible;
    visibility: visible;
    opacity: 1;
    z-index: 1;
}

.cat_list_hover2:hover .child_menu {
    overflow: visible;
    visibility: visible;
    opacity: 1;
    z-index: 1;
}

.cat_list_hover3:hover .child_menu {
    overflow: visible;
    visibility: visible;
    opacity: 1;
    z-index: 1;
}

.cat_list_hover4:hover .child_menu {
    overflow: visible;
    visibility: visible;
    opacity: 1;
    z-index: 1;
}

.cat_list_hover5:hover .child_menu {
    overflow: visible;
    visibility: visible;
    opacity: 1;
    z-index: 1;
}

ul.child_menu.child_top {
    margin-top: -50px;
}

ul.child_menu.child_top1 {
    margin-top: -83px;
}

ul.child_menu.child_top2 {
    margin-top: -123px;
}

ul.child_menu.child_top3 {
    margin-top: -152px;
}

ul.child_menu.child_top4 {
    margin-top: -184px;
}

.sec_title h2 {
    border-bottom: 2px solid #ddd;
}

span.left_bar {
    color: white;
}

li.Cat_list span {
    color: white;
    margin-left: 0px;
}

.left_cat_menu {
    margin-top: -10px;
}

/*===========================details Cat_gory=================*/
ul.cat_wrapper {
    background: #ff0101;
    width: 67%;
    height: 37px;
    margin-top: -67px;
}

.cat_wrapper {
    position: relative;
}

.cat_wrapper:hover .Cat_menu {
    overflow: visible;
    opacity: 1;
    visibility: visible;
    z-index: 1;
}

section.slider-section .row .col-sm-2 {
    width: 200px;
}

form.sort-form .form-select,
form.sort-form .form-select:focus {
    border: 1px solid #ddd;
    border-radius: 0;
}

.sorting-section {
    margin-bottom: 20px;
}

.showing-data {
    height: 100%;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.showing-data span {
    font-size: 16px;
    display: block;
    text-align: right;
}

.category-breadcrumb {
    column-gap: 8px;
}

.description .nav.nav-tabs {
    border-top: 1px solid;
    border-color: #ddd;
    border-bottom: none;
}

.nav-tabs .nav-item.show .nav-link,
.nav-tabs .nav-link.active {
    border-radius: 0;
    border-right: 0;
    border-left: 0;
    position: relative;
    top: -1px;
    padding: 0 5px;
}

.breadcrumb ul li a {
    color: #666666b3;
    font-size: 16px;
}

.breadcrumb ul li span {
    color: #666666b3;
}

.feature-products-wrapper a {
    font-size: 16px;
}

.details_slider {
    position: relative;
    border: 1px solid {{$generalsetting->primary_color}};
    border-radius: 5px;
}

.details-page-wishlist {
    position: absolute;
    z-index: 9;
    right: 20px;
    top: 20px;
}

.details-page-wishlist a {
    border: 2px solid;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 16px;
    color: #ddd;
    transition: all 0.3s ease;
}

.details-page-wishlist a:hover {
    border-color: #000;
    color: #000;
}

.side_cat_img {
    width: 20px;
    height: auto;
    margin-right: 5px;
}

#content {
    width: 100%;
    padding-top: {{ $contentPaddingTop }};
}

@media only screen and (min-width: 992px) {
    section.slider-section .row {
        align-items: stretch;
    }

    section.slider-section .col-sm-9 {
        padding-left: 15px;
    }

    section.slider-section .home-slider-container {
        border-radius: 5px;
        overflow: hidden;
    }
}

.main-header {
    background: #fff;
}

.main_slider .owl-prev {
    width: 41px;
    height: 40px;
    z-index: 9999;
    position: absolute;
    left: -4px;
    top: 50%;
    cursor: pointer !important;
    z-index: 99999999;
    transform: translateY(-50%);
}

.main_slider .owl-next {
    width: 41px;
    height: 40px;
    z-index: 9999;
    position: absolute;
    right: -4px;
    top: 50%;
    cursor: pointer !important;
    transform: translateY(-50%);
}

.main_slider .owl-next i:hover,
.main_slider .owl-prev i:hover {
    color: #FECD03;
}

a.view_more_btn {
    text-align: right;
    float: right;
    background: {{$generalsetting->primary_color}};
    text-transform: capitalize;
    font-size: 15px;
    padding: 10px 15px;
    color: #fff;
    font-family: "Lato", sans-serif;
}

.register-now.no-account {
    background: #fff;
    padding: 0 10px 15px 10px;
}

section.related-product-section {
    background: #fff;
    margin-top: 15px;
    padding-top: 10px;
    margin-bottom: 10px;
}

.description h2 {
    font-size: 18px;
    color: #000;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

ul.social_link li {
    display: inline-block;
    margin-right: 15px;
    font-size: 20px;
}

ul.social_link li:last-child {
    margin-right: 0;
}

ul.social_link {
    text-align: center;
    margin-top: -10px;
}

.d_app {
    text-align: center;
}

.d_app h2 {
    font-size: 15px;
    text-transform: uppercase;
    color: #ffffff;
    font-weight: 600;
    letter-spacing: 1px;
}

.stay_conn {
    text-align: center;
}

.comn_sec {
    padding: 20px 0;
    display: none
}

.cmn_menu ul {
    display: flex;
    justify-content: space-between;
}

.cmn_menu ul li {
    position: relative;

}

.cmn_menu ul li:after {
    content: "";
    position: absolute;
    right: -15px;
    top: 6px;
    width: 1px;
    height: 10px;
    background: #000;
}

.cmn_menu ul li:last-child:after {
    display: none;
}

.cmn_menu ul li a {
    transition: all .5s ease;
}

.cmn_menu ul li a:hover {
    color: #ff0018;
}

.contact-form {
    background: linear-gradient(135deg, {{$generalsetting->primary_color}}, #3b4fb5); /* gradient background */
    padding: 50px;
    border-radius: 20px;
    margin-top: 12px; /* space from header */
}


.cont_item {
    text-align: center;
}

.cont_item a {
    font-size: 15px;
    font-weight: 600;
}

.cont_item {
    text-align: center;
    margin-bottom: 15px;
    display:none 
}

.copyright p a {
    color: #ff0018;
}

/* ====Color Size Details Css ====*/
.selector {
    position: relative;
    width: 100%;
    background-color: inherit;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    column-gap: 10px;
}

.size-container {
    margin-bottom: 12px;
}

.selector-item {
    position: relative;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.selector-item_radio {
    appearance: none;
    display: none;
}

.color_inner .selector .selector-item label {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    border: 1px solid #ddd;
}
.selector-item label {
    border: 1px solid #7D7D7D;
}

.color_inner .selector-item label span img {
    width: 220px;
    height: 20px;
    opacity: 0;
}

.color_inner .selector-item input[type="radio"]:checked+label span img {
    opacity: 1;
}

.selector-item_label {
    position: relative;
    height: 100%;
    width: 100%;
    text-align: center;
    border-radius: 0;
    line-height: 30px;
    font-weight: 600;
    transition-duration: .5s;
    transition-property: transform, color, box-shadow;
    transform: none;
    margin: 0;
    padding: 0px 8px;
    color: #000;
}

.selector-item_radio:checked+.selector-item_label,
.selector-item_label:hover {
    background-color: #7D7D7D;
    color: #fff;
    border-color: #7D7D7D;
    cursor: pointer;
}

/* ====Color Size Details Css ====*/
.color_size {
    display: flex;
    justify-content: center;
    padding: 5px;
}

.color_size span {
    padding: 5px;
}

.syotimer-cell__unit {
    display: none;
}

.timer_inner {
    display: flex;
    justify-content: space-between;
}

.timer_inner .syotimer__body {
    display: flex;
    grid-gap: 5px;
}

.timer_inner .syotimer-cell {
    width: 38px;
    text-align: center;
    background: {{$generalsetting->primary_color}};
    color: #fff;
    border-radius: 5px;
    font-size: 15px;
    padding: 10px;
    font-weight: 600;
}

.indicator_thumb {
    position: relative;
}

.indicator_thumb {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    grid-gap: 5px;
    margin: 10px 0;
}

.indicator-item {
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
}

.indicator-item img {
    height: 80px !important;
    width: 100%;
    object-fit: contain;
}

.dimage_item img {
    height: 500px !important;
    object-fit: contain;
}

.hightlight_cont p {
    font-weight: 600;
    text-align: center;
}

.color_inner {
    display: flex;
    margin-top: 10px;
}

.color_inner p {
    margin-right: 10px;
    margin-top: 6px;
    font-weight: 600;
}

.size_inner {
    display: flex;
}

.size_inner p {
    margin-right: 10px;
    margin-top: 6px;
    font-weight: 600;
}

.pro_unig label {
    font-weight: 600;
    margin-bottom: 10px;
}

.pro_brand p {
    font-weight: 600;
}

.pro_brand {
    margin-bottom: 7px;
    margin-top: 2px;
}

.description-nav-wrapper {
    background-color: #fff;
    padding: 10px;
    width: 100%;
    box-shadow: 0px 0px 1px {{$generalsetting->primary_color}};
}

.desc-nav-ul {
    display: flex;
    column-gap: 10px;
    flex-wrap: wrap;
    margin-left: 12px;
}

.desc-nav-ul li a {
    padding: 6px 20px;
    display: inline-block;
    box-shadow: 0px 0px 3px #ddd;
    border-radius: 5px;
    background-color: #fff;
    cursor: pointer;
}

.desc-nav-ul li.active a,
.desc-nav-ul li:hover a {
    background-color: #0a3a66;
    color: #fff;
}

.description {
    margin-top: 10px;
    background: #ffffff;
    padding: 15px;
    border: 1px solid {{$generalsetting->primary_color}};  
    border-radius: 5px;    
    box-shadow: 0 2px 6px rgba(0,0,0,0.1); 
}

.page-title h5 {
    font-weight: 600;
    font-size: 18px;
}

.pro_vide h2 {
    font-size: 18px;
    margin-bottom: 15px;
}

section.pro_details_area .product-details-content-row {
    align-items: stretch;
}

section.pro_details_area .description-nav-in-content {
    margin-bottom: 0;
    padding-left: 0;
    padding-right: 0;
    box-shadow: none;
}

section.pro_details_area .product-video-col {
    position: relative;
}

section.pro_details_area .product-video-sticky-wrap {
    position: relative;
    z-index: 5;
}

.pro_vide,
.pro_vide.product-video-sticky {
    border: 1px solid #ddd;
    padding: 15px;
    margin-top: 0;
}

a.forget-link {
    font-size: 14px;
}

.cart_details .card-header {
    padding: 10px 15px;
    background: #f5f7f9;
}

.cart_btn.order_button a {
    background: {{$generalsetting->primary_color}};
    color: #fff;
    width: 60%;
    padding: 6px 0px;
    font-size: 16px;
    text-transform: uppercase;
    display: block;
    width: 100%;
    transition: all .5s ease;
}

.cart_btn.order_button a:hover {
    background: #1d2224;
}

span.sale-badge-text p {
    font-size: 11px;
    color: #fff;
}

.hightlight_cont ul li {
    display: block;
    line-height: 27px;
}

.hightlight_cont ul li i {
    margin-right: 5px;
}

.desktop_hide {
    display: none;
}

.hightlight_cont p i {
    margin-right: 5px;
}

.sub-category>li {
    position: relative;
}

ul.child-category {
    position: absolute;
    right: -100%;
    background: #fff;
    top: 0;
    width: 198px;
    visibility: hidden;
    opacity: 0;
    transition: all .5s;
}

.sub-category>li:hover .child-category {
    visibility: visible;
    opacity: 1;
}

.bottoads_area {
    padding: 30px 0;
    background: #fff;
    margin-bottom: 10px;
    display: none
}

.bottoads_inner {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 10px;
}

.cat_img {
    height: 80px;
    width: 80px;
    margin: 0 auto;
}

.cat_name {
    text-align: center;
    padding: 5px 0;
    text-transform: capitalize;
}

.cat_item {
    border: 1px solid #ddd;
    padding: 4px;
    border-radius: 15px 0 15px 0;
    transition: 0.35s all;
    height: 127px;
}

.cat_item:hover {
    border-color: {{$generalsetting->primary_color}};
}

.footertop_ads_inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 10px;
}

.footer_top_ads_area {
    background: #fff;
    margin-bottom: 10px;
    padding: 30px 0;
}

.scrolltop {
    position: fixed;
    right: 34px !important;
    bottom: 10px !important;
    width: 40px !important;
    background: {{$generalsetting->primary_color}} !important;
    height: 40px !important;
    line-height: 40px !important;
    border-radius: 50% !important;
    text-align: center !important;
    color: #ffff !important;
    font-size: 24px !important;
    cursor: pointer;
    z-index: 99;
}

.homeproduct.main-details-page {
    margin-top: 10px;
}

.description ul li {
    display: block;
}

.description ul li {
    display: block;
    display: list-item;
    list-style-position: inside;
    list-style-type: square;
    font-size: 16px;
    line-height: 30px;
}

.sidebar-menu {
    background: #ffffff;
    margin:0;
    border-radius: 5px;
    border: 1px solid {{$generalsetting->primary_color}};
    padding: 0px 5px;
}

.sidebar-menu ul li {
    display: block;
    position: relative;
}

.sidebar-menu li a {
    padding: 10px 15px;
    display: block;
    font-size: 15px;
    line-height: 18px;
    position: relative;
    text-transform: capitalize;
    font-weight: 500;
}

.sidebar-menu ul li a img {
    width: 20px;
    height: auto;
    margin-right: 5px;
}

.sidebar-menu li a i {
    position: absolute;
    right: 12px;
    font-size: 12px;
    top: 18px;
}

.sidebar-submenu {
    position: absolute;
    right: -110%;
    top: 0;
    width: 100%;
    background: #fff;
    visibility: hidden;
    opacity: 0;
    z-index: 9;
    transition: 0.35s all;
}

.sidebar-menu ul li {
    display: block;
    position: relative;
}

.sidebar-submenu>li {
    position: relative !important;
}

.sidebar-childmenu {
    position: absolute;
    right: -100%;
    background: #fff;
    width: 100%;
    border-left: 1px solid #ddd;
    top: 0px;
    visibility: hidden;
    opacity: 0;
    z-index: 9;
}

.sidebar-menu ul li:hover>a {
    background-color: #a5b2fc2b;
    color: #000000;
}

.sidebar-menu ul li:hover .sidebar-submenu {
    visibility: visible;
    opacity: 1;
    left: 100%;
}

.sidebar-submenu>li:hover .sidebar-childmenu {
    visibility: visible;
    opacity: 1;
    right: -100%;
}

.col-sm-3.hidetosm {
    padding-right: 0;
}

.track_btn a {
    display: inline-block;
    text-align: center;
    position: relative;
    font-size: 15px;
    font-weight: normal;
}

.track_btn a i {
    font-size: 16px !important;
}

section.pro_details_area {
    background: #ffffff;
    padding: 25px 0;
    overflow: visible;
}

section.pro_details_area .container,
section.pro_details_area .product-details-content-row {
    overflow: visible;
}

.flext_area {
    display: flex;
    align-items: center;
}

.flext_area i {
    font-size: 35px;
    margin-right: 10px;
}

.details_right {
    border: 1px solid {{$generalsetting->primary_color}};
    padding: 10px 20px 15px;
    height: auto;
    border-radius: 5px;
}

.main-details-page .product-section > .container > .row {
    align-items: flex-start;
}

.track_info ul li {
    display: block;
    line-height: 30px;
}

.track_info ul li span {
    width: 80px;
    display: inline-block;
    text-align: right;
    margin-right: 12px;
    font-weight: 600;
}

table.table.table-bordered.tracktable {
    margin-bottom: 0;
}

.tracktable thead {
    background: #ff0018;
    color: #fff;
}

td.tfoot_bg {
    background: #f5f5f5;
    text-align: right;
}


.payment-methods .form-check {
    display:inline-block !important;
}
.payment-methods label {
    font-size: 15px;
    color: #555;
    margin-right: 10px;
}
.product-details-discount-badge {
    position: absolute;
    right: 20px;
    top: 10px;
    z-index: 999;
}
.product-details-discount-badge {
    
}
.product-details-discount-badge span.sale-badge-text p {     
    font-size: 11px;
    color: #fff;
    line-height: 10px;
}
.product-details-discount-badge span.sale-badge-text {
    background-color: {{$generalsetting->primary_color}};
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    text-align: center;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: #fff;
    
}
.details-ratting-wrapper {
    margin-bottom: 10px;
}
.details-ratting-wrapper i {
    color: #FFDF00;
}
.details-ratting-wrapper i.far.fa-star {
    color: #959595;
}
.all-reviews-button  {
    text-decoration: underline;
    margin-left: 20px;
}

.customer-sidebar {
    background-color: #ffffff;
    padding: 10px;
    height: 100%;
    border:1px solid #10107614

}


li.all__category__list {
    padding: 1px 0px 0px 9px;
    background: {{$generalsetting->secodery_color}};
    width: 289px;
    font-size: 19px;
    font-weight: 400;
    position: relative;
}
.side__bar {
    position: absolute;
    left: 0;
    top: 10;
    opacity: 0;
    visibility: hidden;
    display: none;
    transition: all 0.3s ease;
    width: 290px;
    border-radius: 0px;
    background: {{$generalsetting->primary_color}};
}
li.all__category__list:hover .side__bar{
    opacity: 1;
    z-index: 999;
    visibility: visible;
    display: block;
}
.side__bar ul li:hover {
    background-color: #1d2224;
    color: #fff;
}
.side__bar ul li:hover>a {
    background-color: #1d2224;
    color: #fff;
}
.side__barsub{
    width: 290px;
    border-radius: 0px;
    background: {{$generalsetting->primary_color}};
}.side__barchild{
    width: 290px;
    border-radius: 0px;
    background: {{$generalsetting->primary_color}};
}

li.all__category__list i {
    padding-left: 84px;
}
.heder__category {
    padding: 0px;
    display: grid;
    grid-template-columns: 25% 7% 12% 42% 14%;
    height: 48px;
    line-height: 48px;
}
.contact__menu {
    text-align: end;
}
.right__menu__top {
    text-align: end;
}