{{-- Turns the menu's feather icons (<i data-feather="x">) into Font Awesome icons, before feather.replace() runs --}}
<script>
(() => {
    const map = {airplay:'gauge-high','message-circle':'robot',cpu:'cash-register','shopping-cart':'cart-shopping','file-plus':'file-lines',users:'users',
        'rotate-ccw':'rotate-left',list:'list-ul',clock:'clock','check-circle':'circle-check',check:'check',database:'boxes-stacked',package:'box','shopping-bag':'bag-shopping',
        layers:'layer-group','plus-circle':'circle-plus',edit:'pen-to-square','file-text':'file-invoice',truck:'truck',user:'user',calendar:'calendar-days','dollar-sign':'sack-dollar',
        gift:'gift','credit-card':'credit-card',star:'star',search:'magnifying-glass',send:'paper-plane','alert-circle':'triangle-exclamation',mail:'envelope',briefcase:'briefcase',
        shield:'shield-halved','user-check':'user-check','map-pin':'location-dot',settings:'gear',bell:'bell',key:'key',save:'plug',facebook:'facebook',film:'film',
        'trending-up':'chart-line',layout:'table-columns',globe:'globe',video:'video','share-2':'share-nodes',image:'images','message-square':'comment-dots','pie-chart':'chart-pie',
        'trending-down':'arrow-trend-down',archive:'box-archive',activity:'chart-simple',map:'map','refresh-cw':'rotate','log-out':'right-from-bracket',home:'house',
        'dollar':'dollar-sign',grid:'table-cells-large',box:'box','bar-chart':'chart-column','bar-chart-2':'chart-column',wallet:'wallet',tag:'tag',tags:'tags',
        'arrow-down-circle':'circle-arrow-down','arrow-up-circle':'circle-arrow-up',phone:'phone',link:'link',heart:'heart',info:'circle-info','help-circle':'circle-question',
        lock:'lock',unlock:'lock-open',eye:'eye',download:'download',upload:'upload',printer:'print',award:'award',percent:'percent',inbox:'inbox',monitor:'desktop',
        smartphone:'mobile-screen','message':'comment','user-plus':'user-plus',sliders:'sliders',tool:'screwdriver-wrench',zap:'bolt',target:'bullseye',x:'xmark'};
    const brands = ['facebook'];
    document.querySelectorAll('[data-fa-menu] i[data-feather]').forEach(i => {
        const n = i.getAttribute('data-feather');
        const fa = map[n] || 'circle-dot';
        i.removeAttribute('data-feather');
        i.className = (brands.includes(fa) ? 'fa-brands fa-' : 'fa-solid fa-') + fa;
    });
})();
</script>
