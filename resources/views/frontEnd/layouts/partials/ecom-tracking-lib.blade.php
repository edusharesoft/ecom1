{{-- Unified GTM + Facebook Pixel + TikTok — customer data & ecommerce events --}}
<script>
(function (w) {
    'use strict';

    var CURRENCY = 'BDT';

    function dl() {
        w.dataLayer = w.dataLayer || [];
        return w.dataLayer;
    }

    function getCookie(name) {
        var m = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
        return m ? m.pop() : '';
    }

    function normPhone(phone) {
        var d = String(phone || '').replace(/\D/g, '');
        if (d.length === 11 && d.charAt(0) === '0') return '880' + d.slice(1);
        if (d.length === 10 && d.charAt(0) === '1') return '880' + d;
        return d;
    }

    function splitName(name) {
        name = String(name || '').trim();
        if (!name) return { first: '', last: '' };
        var p = name.split(/\s+/);
        return { first: p[0] || '', last: p.slice(1).join(' ') || '' };
    }

    function buildUserPayload(user) {
        if (!user) return null;
        user = user || {};
        var nm = splitName(user.name || ((user.first_name || '') + ' ' + (user.last_name || '')).trim());
        var phone = normPhone(user.phone || '');
        var payload = {
            customer_id: user.external_id || user.customer_id || '',
            email: (user.email || '').toLowerCase().trim(),
            phone: phone,
            name: user.name || (nm.first + ' ' + nm.last).trim(),
            first_name: user.first_name || nm.first,
            last_name: user.last_name || nm.last,
            city: user.city || '',
            address: user.address || '',
            area: user.area || user.city || '',
            country: 'bd',
            fbp: user.fbp || getCookie('_fbp'),
            fbc: user.fbc || getCookie('_fbc')
        };
        return payload;
    }

    function fbUserData(user) {
        var p = buildUserPayload(user);
        if (!p) return {};
        var o = { country: 'bd' };
        if (p.email) o.em = p.email;
        if (p.phone) o.ph = p.phone;
        if (p.first_name) o.fn = p.first_name.toLowerCase();
        if (p.last_name) o.ln = p.last_name.toLowerCase();
        if (p.city) o.ct = p.city.toLowerCase();
        if (p.customer_id) o.external_id = String(p.customer_id);
        return o;
    }

    function tiktokIdentify(user) {
        var p = buildUserPayload(user);
        if (!p || typeof w.ttq === 'undefined') return;
        var id = {};
        if (p.email) id.email = p.email;
        if (p.phone) id.phone_number = p.phone;
        if (p.customer_id) id.external_id = String(p.customer_id);
        if (Object.keys(id).length) w.ttq.identify(id);
    }

    function setPixelUser(user) {
        var fb = fbUserData(user);
        if (typeof w.fbq === 'function' && Object.keys(fb).length) {
            w.fbq('set', 'userData', fb);
        }
        tiktokIdentify(user);
    }

    function pushEvent(eventName, payload) {
        var row = Object.assign({ event: eventName }, payload || {});
        if (row.ecommerce) {
            dl().push({ ecommerce: null });
        }
        dl().push(row);
    }

    function lineToGa4(items) {
        return (items || []).map(function (it, i) {
            return {
                item_id: String(it.id || it.item_id || ''),
                item_name: it.name || it.item_name || '',
                price: Number(it.price || it.item_price || 0),
                quantity: Number(it.qty || it.quantity || 1),
                index: i
            };
        });
    }

    function lineToFbContents(items) {
        return (items || []).map(function (it) {
            return {
                id: String(it.id || it.item_id || ''),
                quantity: Number(it.qty || it.quantity || 1),
                item_price: Number(it.price || it.item_price || 0)
            };
        });
    }

    function lineToTtContents(items) {
        return (items || []).map(function (it) {
            return {
                content_id: String(it.id || it.item_id || ''),
                content_name: it.name || it.item_name || '',
                quantity: Number(it.qty || it.quantity || 1),
                price: Number(it.price || it.item_price || 0)
            };
        });
    }

    var EcomTracking = {
        identify: function (user) {
            var p = buildUserPayload(user);
            if (!p) return;
            setPixelUser(user);
            pushEvent('user_identify', { user_data: p });
        },

        viewContent: function (opts) {
            opts = opts || {};
            var items = opts.items || [opts.item];
            var value = Number(opts.value || 0);
            var ga4 = lineToGa4(items);
            if (ga4.length) {
                pushEvent('view_item', { ecommerce: { currency: CURRENCY, value: value, items: ga4 } });
            }
            if (typeof w.fbq === 'function' && ga4.length) {
                w.fbq('track', 'ViewContent', {
                    content_ids: ga4.map(function (i) { return i.item_id; }),
                    content_name: ga4[0].item_name,
                    content_type: 'product',
                    value: value,
                    currency: CURRENCY
                });
            }
            if (typeof w.ttq !== 'undefined' && ga4.length) {
                w.ttq.track('ViewContent', {
                    content_type: 'product',
                    content_id: ga4[0].item_id,
                    value: value,
                    currency: CURRENCY,
                    contents: lineToTtContents(items)
                });
            }
        },

        addToCart: function (opts) {
            opts = opts || {};
            var items = opts.items || [];
            var value = Number(opts.value || 0);
            var ga4 = lineToGa4(items);
            pushEvent('add_to_cart', { ecommerce: { currency: CURRENCY, value: value, items: ga4 } });
            if (typeof w.fbq === 'function') {
                w.fbq('track', 'AddToCart', {
                    value: value,
                    currency: CURRENCY,
                    content_ids: ga4.map(function (i) { return i.item_id; }),
                    content_type: 'product',
                    contents: lineToFbContents(items)
                });
            }
            if (typeof w.ttq !== 'undefined') {
                w.ttq.track('AddToCart', {
                    content_type: 'product',
                    value: value,
                    currency: CURRENCY,
                    contents: lineToTtContents(items)
                });
            }
        },

        viewCart: function (opts) {
            opts = opts || {};
            var value = Number(opts.value || 0);
            var items = opts.items || [];
            var ga4 = lineToGa4(items);
            pushEvent('view_cart', { ecommerce: { currency: CURRENCY, value: value, items: ga4 } });
            if (typeof w.fbq === 'function') {
                w.fbq('trackCustom', 'ViewCart', {
                    value: value,
                    currency: CURRENCY,
                    num_items: ga4.length,
                    content_ids: ga4.map(function (i) { return i.item_id; })
                });
            }
            if (typeof w.ttq !== 'undefined') {
                w.ttq.track('ViewContent', {
                    content_type: 'product_group',
                    value: value,
                    currency: CURRENCY,
                    quantity: ga4.length
                });
            }
        },

        initiateCheckout: function (opts) {
            opts = opts || {};
            var items = opts.items || [];
            var value = Number(opts.value || 0);
            var ga4 = lineToGa4(items);
            pushEvent('begin_checkout', {
                ecommerce: { currency: CURRENCY, value: value, coupon: opts.coupon || null, items: ga4 }
            });
            if (typeof w.fbq === 'function') {
                w.fbq('track', 'InitiateCheckout', {
                    value: value,
                    currency: CURRENCY,
                    num_items: ga4.length,
                    content_ids: ga4.map(function (i) { return i.item_id; }),
                    contents: lineToFbContents(items),
                    coupon: opts.coupon || undefined
                });
            }
            if (typeof w.ttq !== 'undefined') {
                w.ttq.track('InitiateCheckout', {
                    value: value,
                    currency: CURRENCY,
                    contents: lineToTtContents(items)
                });
            }
        },

        addPaymentInfo: function (opts) {
            opts = opts || {};
            var items = opts.items || [];
            var value = Number(opts.value || 0);
            var ga4 = lineToGa4(items);
            pushEvent('add_payment_info', {
                payment_type: opts.payment_method || opts.payment_type || '',
                ecommerce: { currency: CURRENCY, value: value, coupon: opts.coupon || null, items: ga4 }
            });
            if (typeof w.fbq === 'function') {
                w.fbq('track', 'AddPaymentInfo', {
                    value: value,
                    currency: CURRENCY,
                    payment_method: opts.payment_method || '',
                    num_items: ga4.length,
                    content_ids: ga4.map(function (i) { return i.item_id; })
                });
            }
            if (typeof w.ttq !== 'undefined') {
                w.ttq.track('AddPaymentInfo', {
                    value: value,
                    currency: CURRENCY,
                    contents: lineToTtContents(items)
                });
            }
        },

        purchase: function (opts) {
            opts = opts || {};
            var items = opts.items || [];
            var ga4 = lineToGa4(items);
            var value = Number(opts.value || 0);
            var orderId = String(opts.transaction_id || opts.order_id || '');
            var eventId = opts.event_id || ('purchase_' + orderId);
            var user = opts.user || null;

            if (user) setPixelUser(user);

            pushEvent('purchase', {
                ecommerce: {
                    transaction_id: orderId,
                    value: value,
                    tax: Number(opts.tax || 0),
                    shipping: Number(opts.shipping || 0),
                    currency: CURRENCY,
                    coupon: opts.coupon || null,
                    payment_method: opts.payment_method || '',
                    items: ga4
                },
                user_data: buildUserPayload(user),
                order_info: opts.order_info || {}
            });

            if (typeof w.fbq === 'function') {
                w.fbq('track', 'Purchase', {
                    value: value,
                    currency: CURRENCY,
                    content_ids: ga4.map(function (i) { return i.item_id; }),
                    content_type: 'product',
                    contents: lineToFbContents(items),
                    num_items: ga4.length,
                    order_id: orderId
                }, { eventID: eventId });
            }

            if (typeof w.ttq !== 'undefined') {
                w.ttq.track('CompletePayment', {
                    content_type: 'product',
                    value: value,
                    currency: CURRENCY,
                    order_id: orderId,
                    quantity: ga4.length,
                    contents: lineToTtContents(items)
                }, { event_id: eventId });
            }
        },

        normPhone: normPhone,
        getCookie: getCookie
    };

    w.EcomTracking = EcomTracking;

    var bootUser = @json($trackingUser ?? null);
    if (bootUser) {
        document.addEventListener('DOMContentLoaded', function () {
            EcomTracking.identify(bootUser);
        });
    }
})(window);
</script>
