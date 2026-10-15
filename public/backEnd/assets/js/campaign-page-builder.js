/**
 * Elementor Pro Style Campaign Page Builder
 * Section > Row > Column > Widget হায়ারার্কি
 */
(function() {
    'use strict';

    const WIDGETS = {
        section: {
            label: 'সেকশন',
            icon: 'fe-layers',
            category: 'layout',
            defaultStyle: { padding: '60px 20px', background: '#f9fafb', 'border-radius': '0' }
        },
        heading: {
            label: 'হেডিং',
            icon: 'fe-type',
            category: 'basic',
            html: '<h2 class="cpb-editable" data-field="text" data-default="শিরোনাম" style="font-size:28px;color:#111827;margin:0 0 12px;font-weight:600">শিরোনাম</h2>',
            defaultStyle: { 'font-size': '28px', color: '#111827' }
        },
        text: {
            label: 'টেক্সট',
            icon: 'fe-align-left',
            category: 'basic',
            html: '<p class="cpb-editable" data-field="text" data-default="আপনার টেক্সট এখানে লিখুন।" style="font-size:16px;color:#4b5563;line-height:1.7;margin:0">আপনার টেক্সট এখানে লিখুন।</p>',
            defaultStyle: { 'font-size': '16px', color: '#4b5563' }
        },
        image: {
            label: 'ইমেজ',
            icon: 'fe-image',
            category: 'basic',
            hasImage: true,
            imageField: 'src',
            html: '<div class="cpb-widget-inner cpb-image-block"><div class="cpb-image-wrap" style="min-height:180px;border-radius:12px;overflow:hidden;position:relative;background:#f3f4f6"><img class="cpb-editable cpb-img" data-field="src" src="" alt="Image" style="width:100%;height:auto;display:none"><div class="cpb-image-placeholder" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f3f4f6,#e5e7eb);border:2px dashed #d1d5db;color:#6b7280;font-size:14px">ইমেজ আপলোড করুন</div></div></div>',
            defaultStyle: { 'border-radius': '12px' }
        },
        button: {
            label: 'বাটন',
            icon: 'fe-zap',
            category: 'basic',
            html: '<a href="#order" class="cpb-editable cpb-btn" data-field="text" data-default="অর্ডার করুন" style="display:inline-block;padding:14px 28px;background:#16a34a;color:#fff!important;border-radius:999px;text-decoration:none;font-weight:600;margin:8px 0">অর্ডার করুন</a>',
            defaultStyle: { background: '#16a34a', color: '#fff' }
        },
        spacer: {
            label: 'স্পেসার',
            icon: 'fe-minimize-2',
            category: 'layout',
            html: '<div class="cpb-spacer" data-field="height" style="height:40px;min-height:40px;background:repeating-linear-gradient(45deg,#f3f4f6,#f3f4f6 2px,#e5e7eb 2px,#e5e7eb 4px)"></div>',
            defaultStyle: { height: '40px' }
        },
        divider: {
            label: 'ডিভাইডার',
            icon: 'fe-minus',
            category: 'layout',
            html: '<hr class="cpb-divider" data-field="style" style="border:none;height:2px;background:linear-gradient(90deg,transparent,#d1d5db,transparent);margin:24px 0">',
            defaultStyle: {}
        },
        icon_box: {
            label: 'আইকন বক্স',
            icon: 'fe-star',
            category: 'layout',
            html: '<div class="cpb-icon-box" style="text-align:center;padding:24px;background:#fff;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,.06)"><div class="cpb-editable" data-field="icon" style="font-size:40px;margin-bottom:12px;color:#16a34a">★</div><h3 class="cpb-editable" data-field="title" data-default="শিরোনাম" style="font-size:18px;color:#111827;margin:0 0 8px">শিরোনাম</h3><p class="cpb-editable" data-field="desc" data-default="বর্ণনা" style="font-size:14px;color:#6b7280;margin:0;line-height:1.5">বর্ণনা</p></div>',
            defaultStyle: {}
        },
        hero: {
            label: 'হিরো সেকশন',
            icon: 'fe-star',
            category: 'layout',
            hasImage: true,
            imageField: 'hero_img',
            html: '<section class="cpb-hero" style="padding:80px 20px;background:linear-gradient(135deg,#ecfdf3,#d1fae5);text-align:center"><div style="max-width:800px;margin:0 auto"><div class="cpb-hero-img-wrap mb-3" style="min-height:120px"><img class="cpb-editable cpb-img" data-field="hero_img" src="" alt="" style="max-width:300px;margin:0 auto;border-radius:12px;display:none"><div class="cpb-image-placeholder" style="min-height:120px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.5);border:2px dashed #86efac;border-radius:12px;color:#15803d;font-size:14px">হিরো ইমেজ আপলোড</div></div><span class="cpb-editable" data-field="badge" data-default="প্রিমিয়াম" style="display:inline-block;background:#16a34a;color:#fff;padding:6px 14px;border-radius:999px;font-size:14px;margin-bottom:12px">প্রিমিয়াম</span><h1 class="cpb-editable" data-field="title" data-default="আপনার প্রোডাক্ট" style="font-size:42px;color:#14532d;margin:16px 0;font-weight:700">আপনার প্রোডাক্ট</h1><p class="cpb-editable" data-field="desc" data-default="সংক্ষিপ্ত বর্ণনা" style="font-size:18px;color:#4b5563;margin-bottom:24px">সংক্ষিপ্ত বর্ণনা</p><a href="#order" class="cpb-editable cpb-btn" data-field="btn" data-default="অর্ডার করুন" style="display:inline-block;padding:14px 28px;background:#16a34a;color:#fff!important;border-radius:999px;text-decoration:none;font-weight:600">অর্ডার করুন</a></div></section>',
            defaultStyle: {}
        },
        features: {
            label: 'ফিচার কার্ড',
            icon: 'fe-grid',
            category: 'layout',
            hasImage: true,
            imageFields: ['img1', 'img2'],
            html: '<div class="cpb-features" style="display:grid;grid-template-columns:repeat(2,1fr);gap:24px"><div style="background:#fff;padding:24px;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,.06)"><div class="cpb-feature-img-wrap" style="height:80px;margin-bottom:12px;border-radius:8px;overflow:hidden;background:#f3f4f6"><img class="cpb-editable cpb-img" data-field="img1" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none"><div class="cpb-image-placeholder" style="height:100%;display:flex;align-items:center;justify-content:center;font-size:12px;color:#9ca3af">ইমেজ ১</div></div><h3 class="cpb-editable" data-field="t1" data-default="ফিচার ১" style="color:#14532d;margin-bottom:8px;font-size:18px">ফিচার ১</h3><p class="cpb-editable" data-field="p1" data-default="বর্ণনা" style="color:#4b5563;font-size:14px;margin:0">বর্ণনা</p></div><div style="background:#fff;padding:24px;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,.06)"><div class="cpb-feature-img-wrap" style="height:80px;margin-bottom:12px;border-radius:8px;overflow:hidden;background:#f3f4f6"><img class="cpb-editable cpb-img" data-field="img2" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none"><div class="cpb-image-placeholder" style="height:100%;display:flex;align-items:center;justify-content:center;font-size:12px;color:#9ca3af">ইমেজ ২</div></div><h3 class="cpb-editable" data-field="t2" data-default="ফিচার ২" style="color:#14532d;margin-bottom:8px;font-size:18px">ফিচার ২</h3><p class="cpb-editable" data-field="p2" data-default="বর্ণনা" style="color:#4b5563;font-size:14px;margin:0">বর্ণনা</p></div></div></div>',
            defaultStyle: {}
        },
        video: {
            label: 'ভিডিও',
            icon: 'fe-video',
            category: 'media',
            html: '<div class="cpb-video-wrap" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px"></div>',
            defaultStyle: {},
            data: { url: '' }
        },
        accordion: {
            label: 'অ্যাকর্ডিয়ন',
            icon: 'fe-chevron-down',
            category: 'layout',
            html: '<div class="cpb-accordion"><div class="cpb-accordion-item" style="border:1px solid #e5e7eb;border-radius:8px;margin-bottom:8px;overflow:hidden"><div class="cpb-accordion-header" style="padding:14px 16px;background:#f9fafb;cursor:pointer;font-weight:600;display:flex;justify-content:space-between;align-items:center"><span class="cpb-editable" data-field="q1" data-default="প্রশ্ন ১">প্রশ্ন ১</span><span>▼</span></div><div class="cpb-accordion-body" style="padding:14px 16px;border-top:1px solid #e5e7eb"><p class="cpb-editable" data-field="a1" data-default="উত্তর">উত্তর</p></div></div></div>',
            defaultStyle: {}
        },
        order_placeholder: {
            label: 'অর্ডার সেকশন',
            icon: 'fe-shopping-cart',
            category: 'layout',
            html: '<section id="order" class="cpb-order" style="padding:60px 20px;background:#fff"><div style="max-width:1140px;margin:0 auto;text-align:center"><h2 style="color:#15803d;margin-bottom:16px;font-size:28px">অর্ডার করুন এখনই</h2><p style="color:#4b5563;font-size:16px">অর্ডার ফর্ম নিচে দেখাবে</p></div></section>',
            defaultStyle: {}
        }
    };

    const CATEGORIES = { layout: 'লেআউট', basic: 'বেসিক', media: 'মিডিয়া' };
    let blocksData = [];
    let blockIdCounter = 0;
    let selectedEl = null;
    let initialized = false;

    function init() {
        const palette = document.getElementById('cpb-palette');
        const canvas = document.getElementById('cpb-canvas');
        const settingsPanel = document.getElementById('cpb-settings');

        if (!palette || !canvas) return;
        if (initialized) return;
        initialized = true;

        // Widget palette by category
        Object.keys(CATEGORIES).forEach(cat => {
            const items = Object.entries(WIDGETS).filter(([k, v]) => v.category === cat && v.html);
            if (items.length === 0) return;
            const group = document.createElement('div');
            group.className = 'cpb-palette-group';
            group.innerHTML = '<div class="cpb-palette-cat">' + CATEGORIES[cat] + '</div>';
            items.forEach(([key, w]) => {
                const div = document.createElement('div');
                div.className = 'cpb-palette-item';
                div.draggable = true;
                div.dataset.blockType = key;
                div.innerHTML = '<i class="' + (w.icon || 'fe-square') + ' me-2"></i>' + w.label;
                div.addEventListener('dragstart', onPaletteDragStart);
                group.appendChild(div);
            });
            palette.appendChild(group);
        });

        // Section/Row add
        const sectionBtn = document.createElement('div');
        sectionBtn.className = 'cpb-palette-item cpb-palette-highlight';
        sectionBtn.draggable = true;
        sectionBtn.dataset.blockType = 'section';
        sectionBtn.innerHTML = '<i class="fe-layers me-2"></i> নতুন সেকশন যোগ করুন';
        sectionBtn.addEventListener('dragstart', onPaletteDragStart);
        palette.insertBefore(sectionBtn, palette.firstChild);

        canvas.addEventListener('dragover', e => { e.preventDefault(); e.dataTransfer.dropEffect = 'copy'; canvas.classList.add('cpb-drag-over'); });
        canvas.addEventListener('dragleave', () => canvas.classList.remove('cpb-drag-over'));
        canvas.addEventListener('drop', onCanvasDrop);
        canvas.addEventListener('click', e => { if (e.target === canvas || e.target.classList.contains('cpb-canvas-placeholder')) deselect(); });

        if (settingsPanel) {
            bindSettingsPanel(settingsPanel);
        }
    }

    function onPaletteDragStart(e) {
        e.dataTransfer.setData('text/plain', e.target.dataset.blockType);
        e.dataTransfer.effectAllowed = 'copy';
    }

    function onCanvasDrop(e) {
        e.preventDefault();
        document.getElementById('cpb-canvas').classList.remove('cpb-drag-over');
        const type = e.dataTransfer.getData('text/plain');
        if (!type || !WIDGETS[type]) return;
        if (type === 'section') addSection();
        else addBlock(type);
    }

    function addSection(data) {
        const canvas = document.getElementById('cpb-canvas');
        const ph = canvas.querySelector('.cpb-canvas-placeholder');
        if (ph) ph.style.display = 'none';

        const id = 'cpb-' + (++blockIdCounter);
        const section = document.createElement('section');
        section.className = 'cpb-section cpb-block-wrap';
        section.dataset.blockId = id;
        section.dataset.blockType = 'section';
        const style = Object.assign({}, WIDGETS.section.defaultStyle || {}, data?.style || {});
        Object.assign(section.style, { padding: style.padding || '60px 20px', background: style.background || '#f9fafb', 'border-radius': style['border-radius'] || '0' });

        section.innerHTML = `
            <div class="cpb-block-actions">
                <button type="button" class="cpb-btn-up" title="উপরে">↑</button>
                <button type="button" class="cpb-btn-down" title="নিচে">↓</button>
                <button type="button" class="cpb-btn-add" title="উইজেট যোগ">+</button>
                <button type="button" class="cpb-btn-style" title="স্টাইল">⚙</button>
                <button type="button" class="cpb-btn-del" title="মুছুন">×</button>
            </div>
            <div class="cpb-section-inner" style="max-width:1140px;margin:0 auto">
                <div class="cpb-canvas-placeholder cpb-section-placeholder">ব্লক ড্রাগ করে এখানে ফেলুন</div>
            </div>
        `;

        const inner = section.querySelector('.cpb-section-inner');
        inner.addEventListener('dragover', e => { e.preventDefault(); e.stopPropagation(); inner.classList.add('cpb-drag-over'); });
        inner.addEventListener('dragleave', e => { e.stopPropagation(); inner.classList.remove('cpb-drag-over'); });
        inner.addEventListener('drop', e => {
            e.preventDefault();
            e.stopPropagation();
            inner.classList.remove('cpb-drag-over');
            const type = e.dataTransfer.getData('text/plain');
            if (type && type !== 'section' && WIDGETS[type]) addBlockInto(type, inner);
        });

        canvas.appendChild(section);
        blocksData.push({ id, type: 'section', data: data || {}, style: style });
        bindSectionEvents(section, id);
        bindAddButton(section, id);
    }

    function addBlock(type, data, container) {
        const canvas = container || document.getElementById('cpb-canvas');
        const ph = canvas.querySelector && canvas.querySelector('.cpb-canvas-placeholder');
        if (ph) ph.style.display = 'none';

        const block = WIDGETS[type];
        if (!block || !block.html) return;

        const id = 'cpb-' + (++blockIdCounter);
        const wrap = document.createElement('div');
        wrap.className = 'cpb-block-wrap';
        wrap.dataset.blockId = id;
        wrap.dataset.blockType = type;

        wrap.innerHTML = `
            <div class="cpb-block-actions">
                <button type="button" class="cpb-btn-up" title="উপরে">↑</button>
                <button type="button" class="cpb-btn-down" title="নিচে">↓</button>
                <button type="button" class="cpb-btn-style" title="স্টাইল">⚙</button>
                <button type="button" class="cpb-btn-del" title="মুছুন">×</button>
            </div>
            <div class="cpb-block-content">${block.html}</div>
        `;

        (container || canvas).appendChild(wrap);
        blocksData.push({ id, type, data: data || {}, style: block.defaultStyle || {} });
        bindBlockEvents(wrap, id, type);
        bindEditableFields(wrap);
        applyBlockData(wrap, data);
    }

    function addBlockInto(type, container) {
        const ph = container.querySelector('.cpb-section-placeholder');
        if (ph) ph.style.display = 'none';
        addBlock(type, null, container);
    }

    function bindSectionEvents(wrap, id) {
        const canvas = document.getElementById('cpb-canvas');
        wrap.querySelector('.cpb-btn-del').onclick = () => { wrap.remove(); blocksData = blocksData.filter(b => b.id !== id); checkPlaceholder(); };
        wrap.querySelector('.cpb-btn-up').onclick = () => { const prev = wrap.previousElementSibling; if (prev) canvas.insertBefore(wrap, prev); };
        wrap.querySelector('.cpb-btn-down').onclick = () => { const next = wrap.nextElementSibling; if (next) canvas.insertBefore(next, wrap); };
        wrap.querySelector('.cpb-btn-style').onclick = () => selectElement(wrap, id);
    }

    function bindAddButton(section, id) {
        const btn = section.querySelector('.cpb-btn-add');
        if (!btn) return;
        btn.onclick = (e) => {
            e.stopPropagation();
            const inner = section.querySelector('.cpb-section-inner');
            const types = Object.keys(WIDGETS).filter(k => WIDGETS[k].html && k !== 'section');
            const menu = document.createElement('div');
            menu.className = 'cpb-add-menu';
            menu.innerHTML = types.map(k => '<div class="cpb-add-item" data-type="' + k + '"><i class="' + (WIDGETS[k].icon || 'fe-square') + ' me-2"></i>' + WIDGETS[k].label + '</div>').join('');
            menu.style.cssText = 'position:absolute;top:100%;right:0;margin-top:4px;background:#fff;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.15);padding:8px;min-width:180px;z-index:100';
            btn.style.position = 'relative';
            btn.appendChild(menu);
            menu.querySelectorAll('.cpb-add-item').forEach(el => {
                el.onclick = (ev) => {
                    ev.stopPropagation();
                    addBlockInto(el.dataset.type, inner);
                    menu.remove();
                    document.removeEventListener('click', close);
                };
            });
            const close = () => { menu.remove(); document.removeEventListener('click', close); };
            setTimeout(() => document.addEventListener('click', close), 10);
        };
    }

    function bindBlockEvents(wrap, id, type) {
        const parent = wrap.closest('.cpb-section-inner') || wrap.closest('#cpb-canvas');
        wrap.querySelector('.cpb-btn-del').onclick = () => { wrap.remove(); blocksData = blocksData.filter(b => b.id !== id); checkPlaceholder(); };
        wrap.querySelector('.cpb-btn-up').onclick = () => { const prev = wrap.previousElementSibling; if (prev && prev.classList.contains('cpb-block-wrap')) parent.insertBefore(wrap, prev); };
        wrap.querySelector('.cpb-btn-down').onclick = () => { const next = wrap.nextElementSibling; if (next && next.classList.contains('cpb-block-wrap')) parent.insertBefore(next, wrap); };
        wrap.querySelector('.cpb-btn-style').onclick = () => selectElement(wrap, id);
        wrap.addEventListener('click', e => { if (e.target.closest('.cpb-editable')) e.stopPropagation(); });
    }

    function bindEditableFields(wrap) {
        wrap.querySelectorAll('.cpb-editable').forEach(el => {
            if (el.tagName === 'IMG') return;
            el.contentEditable = 'true';
            el.addEventListener('blur', () => saveBlockData(el));
        });
    }

    function saveBlockData(el) {
        const blockWrap = el.closest('.cpb-block-wrap');
        if (!blockWrap) return;
        const id = blockWrap.dataset.blockId;
        const field = el.dataset.field;
        const block = blocksData.find(b => b.id === id);
        if (block) {
            if (!block.data) block.data = {};
            if (el.tagName === 'IMG') block.data[field] = el.src;
            else if (el.classList.contains('cpb-btn')) block.data[field] = el.innerHTML;
            else block.data[field] = el.innerHTML;
        }
    }

    function applyBlockData(wrap, data) {
        if (!data) return;
        wrap.querySelectorAll('.cpb-editable, .cpb-img, img[data-field]').forEach(el => {
            const field = el.dataset.field;
            const val = data[field];
            if (val) {
                if (el.tagName === 'IMG') {
                    el.src = val;
                    el.style.display = 'block';
                    const ph = wrap.querySelector('.cpb-image-placeholder');
                    if (ph) ph.style.display = 'none';
                } else el.innerHTML = val;
            }
        });
    }

    function selectElement(el, id) {
        deselect();
        selectedEl = el;
        selectedEl.classList.add('cpb-selected');
        const block = blocksData.find(b => b.id === id);
        if (block) showSettingsPanel(block);
    }

    function deselect() {
        if (selectedEl) selectedEl.classList.remove('cpb-selected');
        selectedEl = null;
        const panel = document.getElementById('cpb-settings');
        if (panel) panel.classList.remove('cpb-settings-visible');
    }

    function showSettingsPanel(block) {
        const panel = document.getElementById('cpb-settings');
        if (!panel) return;
        panel.classList.add('cpb-settings-visible');
        panel.dataset.selectedId = block.id;

        const content = panel.querySelector('.cpb-settings-content');
        if (!content) return;

        const isSection = block.type === 'section';
        const style = block.style || {};

        if (isSection) {
            content.innerHTML = `
                <div class="cpb-field">
                    <label>ব্যাকগ্রাউন্ড কালার</label>
                    <input type="color" id="cpb-s-bg" value="${style.background || '#f9fafb'}" />
                </div>
                <div class="cpb-field">
                    <label>প্যাডিং</label>
                    <input type="text" id="cpb-s-padding" value="${style.padding || '60px 20px'}" placeholder="60px 20px" />
                </div>
                <div class="cpb-field">
                    <label>বর্ডার রেডিয়াস (px)</label>
                    <input type="number" id="cpb-s-radius" value="${parseInt(String(style['border-radius'] || '0')) || 0}" />
                </div>
            `;
        } else {
            const widget = WIDGETS[block.type] || {};
            const imgFields = widget.imageFields || (widget.imageField ? [widget.imageField] : []);
            const hasImageUpload = imgFields.length > 0;

            function imgUploadHtml(fields) {
                return fields.map((f, i) => {
                    const cur = block.data && block.data[f] ? block.data[f] : '';
                    return `<div class="cpb-field cpb-image-upload-field" data-img-field="${f}">
                        <label>${fields.length > 1 ? 'ইমেজ ' + (i+1) : 'ইমেজ আপলোড'}</label>
                        <input type="file" class="cpb-s-image-upload" data-field="${f}" accept="image/*" style="display:none" />
                        <button type="button" class="cpb-s-upload-btn btn btn-sm btn-success w-100" style="padding:8px 12px;border-radius:6px;border:none;cursor:pointer;background:#16a34a;color:#fff">
                            <i class="fe-upload me-1"></i> সিলেক্ট করুন
                        </button>
                        ${cur ? '<div class="mt-2"><img src="' + cur + '" style="max-width:100%;max-height:80px;border-radius:6px;border:1px solid #e2e8f0" alt=""></div>' : ''}
                    </div>`;
                }).join('');
            }

            content.innerHTML = `
                <div class="cpb-settings-tabs">
                    <button type="button" class="cpb-tab active" data-tab="content">কন্টেন্ট</button>
                    <button type="button" class="cpb-tab" data-tab="style">স্টাইল</button>
                </div>
                <div class="cpb-tab-content active" data-tab="content">
                    ${hasImageUpload ? imgUploadHtml(imgFields) : ''}
                    <div class="cpb-field">
                        <label>টেক্সট কালার</label>
                        <input type="color" id="cpb-s-color" value="${style.color || '#111827'}" />
                    </div>
                    <div class="cpb-field">
                        <label>টেক্সট সাইজ (px)</label>
                        <input type="number" id="cpb-s-fontsize" value="${parseInt(String(style['font-size'] || '16')) || 16}" />
                    </div>
                    <div class="cpb-field">
                        <label>ব্যাকগ্রাউন্ড কালার</label>
                        <input type="color" id="cpb-s-bg" value="${style.background || '#ffffff'}" />
                    </div>
                </div>
                <div class="cpb-tab-content" data-tab="style">
                    <div class="cpb-field">
                        <label>প্যাডিং</label>
                        <input type="text" id="cpb-s-padding" value="${style.padding || '20px'}" placeholder="20px" />
                    </div>
                    <div class="cpb-field">
                        <label>মার্জিন</label>
                        <input type="text" id="cpb-s-margin" value="${style.margin || '0'}" placeholder="0" />
                    </div>
                    <div class="cpb-field">
                        <label>বর্ডার রেডিয়াস (px)</label>
                        <input type="number" id="cpb-s-radius" value="${parseInt(String(style['border-radius'] || '0')) || 0}" />
                    </div>
                    <div class="cpb-field">
                        <label>বক্স শ্যাডো</label>
                        <select id="cpb-s-shadow">
                            <option value="none" ${(style['box-shadow'] || '') === 'none' ? 'selected' : ''}>없음</option>
                            <option value="0 4px 12px rgba(0,0,0,.08)" ${(style['box-shadow'] || '') === '0 4px 12px rgba(0,0,0,.08)' ? 'selected' : ''}>হালকা</option>
                            <option value="0 4px 20px rgba(0,0,0,.12)" ${(style['box-shadow'] || '') === '0 4px 20px rgba(0,0,0,.12)' ? 'selected' : ''}>মাঝারি</option>
                            <option value="0 10px 40px rgba(0,0,0,.15)" ${(style['box-shadow'] || '') === '0 10px 40px rgba(0,0,0,.15)' ? 'selected' : ''}>গাঢ়</option>
                        </select>
                    </div>
                </div>
            `;
            content.querySelectorAll('.cpb-tab').forEach(tab => {
                tab.onclick = () => {
                    content.querySelectorAll('.cpb-tab').forEach(t => t.classList.remove('active'));
                    content.querySelectorAll('.cpb-tab-content').forEach(c => c.classList.remove('active'));
                    tab.classList.add('active');
                    const tc = content.querySelector('.cpb-tab-content[data-tab="' + tab.dataset.tab + '"]');
                    if (tc) tc.classList.add('active');
                };
            });
        }

        const apply = () => {
            const b = blocksData.find(x => x.id === block.id);
            if (!b) return;
            if (!b.style) b.style = {};
            if (isSection) {
                b.style.background = document.getElementById('cpb-s-bg')?.value || b.style.background;
                b.style.padding = document.getElementById('cpb-s-padding')?.value || b.style.padding;
                b.style['border-radius'] = (document.getElementById('cpb-s-radius')?.value || 0) + 'px';
            } else {
                b.style.color = document.getElementById('cpb-s-color')?.value || b.style.color;
                b.style['font-size'] = (document.getElementById('cpb-s-fontsize')?.value || 16) + 'px';
                b.style.background = document.getElementById('cpb-s-bg')?.value || b.style.background;
                b.style.padding = document.getElementById('cpb-s-padding')?.value || b.style.padding;
                b.style.margin = document.getElementById('cpb-s-margin')?.value || b.style.margin;
                b.style['border-radius'] = (document.getElementById('cpb-s-radius')?.value || 0) + 'px';
                b.style['box-shadow'] = document.getElementById('cpb-s-shadow')?.value || 'none';
            }
            applyStylesToElement(selectedEl, b.style);
        };

        const ids = isSection ? ['cpb-s-bg', 'cpb-s-padding', 'cpb-s-radius'] : ['cpb-s-color', 'cpb-s-fontsize', 'cpb-s-bg', 'cpb-s-padding', 'cpb-s-margin', 'cpb-s-radius', 'cpb-s-shadow'];
        ids.forEach(id => {
            const inp = document.getElementById(id);
            if (inp) {
                inp.addEventListener('change', apply);
                if (inp.type === 'color') inp.addEventListener('input', apply);
            }
        });

        // Image upload - multiple fields support
        if ((WIDGETS[block.type] || {}).hasImage) {
            content.querySelectorAll('.cpb-s-upload-btn').forEach(btn => {
                const field = btn.closest('.cpb-image-upload-field')?.dataset?.imgField;
                const inp = btn.closest('.cpb-image-upload-field')?.querySelector('.cpb-s-image-upload');
                if (!inp || !field) return;
                btn.onclick = () => inp.click();
                inp.onchange = function() {
                    const file = this.files[0];
                    if (!file) return;
                    const uploadUrl = document.getElementById('campaign-page-builder')?.dataset?.uploadUrl;
                    if (!uploadUrl) { alert('Upload URL not found'); return; }
                    const fd = new FormData();
                    fd.append('image', file);
                    const token = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
                    if (token) { fd.append('_token', token); }
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fe-loader fe-spin me-1"></i> আপলোড...';
                    const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };
                    if (document.querySelector('meta[name="csrf-token"]')?.content) {
                        headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
                    }
                    fetch(uploadUrl, { method: 'POST', body: fd, headers: headers })
                        .then(r => {
                            if (!r.ok) return r.text().then(t => { throw new Error(r.status + ': ' + (r.status === 419 ? 'CSRF - পেজ রিফ্রেশ করুন' : t.substring(0, 100))); });
                            return r.json();
                        })
                        .then(res => {
                            if (res.success && res.url) {
                                const b = blocksData.find(x => x.id === block.id);
                                if (b) { if (!b.data) b.data = {}; b.data[field] = res.url; }
                                const wrap = selectedEl?.querySelector('.cpb-block-content') || selectedEl;
                                const img = wrap?.querySelector('img[data-field="' + field + '"]');
                                const imgWrap = img?.closest('.cpb-image-wrap, .cpb-hero-img-wrap, .cpb-feature-img-wrap');
                                const ph = imgWrap?.querySelector('.cpb-image-placeholder');
                                if (img) {
                                    img.style.display = 'block';
                                    img.alt = 'Image';
                                    img.onload = function() {
                                        if (ph) ph.style.display = 'none';
                                    };
                                    img.onerror = function() {
                                        img.style.display = 'none';
                                        if (ph) ph.style.display = 'flex';
                                        alert('ইমেজ লোড হয়নি। URL চেক করুন: ' + res.url);
                                    };
                                    img.src = res.url;
                                    if (img.complete && img.naturalWidth) { if (ph) ph.style.display = 'none'; }
                                }
                                apply();
                        } else alert(res.message || 'আপলোড ব্যর্থ');
                    })
                    .catch((err) => alert(err.message || 'আপলোড ব্যর্থ'))
                        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fe-upload me-1"></i> সিলেক্ট করুন'; inp.value = ''; });
                };
            });
        }
    }

    function applyStylesToElement(el, style) {
        if (!el || !style) return;
        if (el.classList.contains('cpb-section')) {
            Object.assign(el.style, style);
        } else {
            const content = el.querySelector('.cpb-block-content');
            if (content) Object.assign(content.style, style);
        }
    }

    function bindSettingsPanel(panel) {
        panel.querySelector('.cpb-settings-close')?.addEventListener('click', deselect);
    }

    function checkPlaceholder() {
        const canvas = document.getElementById('cpb-canvas');
        const sections = canvas.querySelectorAll('.cpb-section');
        const blocks = canvas.querySelectorAll('.cpb-block-wrap:not(.cpb-section)');
        const ph = canvas.querySelector('.cpb-canvas-placeholder:not(.cpb-section-placeholder)');
        if (ph) ph.style.display = (sections.length === 0 && blocks.length === 0) ? 'block' : 'none';
    }

    function exportDesign() {
        const canvas = document.getElementById('cpb-canvas');
        const design = [];
        let html = '';

        function collectBlock(wrap) {
            if (!wrap) return null;
            const type = wrap.dataset.blockType;
            const id = wrap.dataset.blockId;
            const block = blocksData.find(b => b.id === id);

            if (type === 'section') {
                const inner = wrap.querySelector('.cpb-section-inner');
                let sectionHtml = '';
                const children = [];
                inner.querySelectorAll('.cpb-block-wrap').forEach(child => {
                    const childResult = collectBlock(child);
                    if (childResult) {
                        children.push(childResult.design);
                        sectionHtml += childResult.html;
                    }
                });
                const style = block?.style || {};
                const sectionOuter = '<section class="cpb-section" style="padding:' + (style.padding || '60px 20px') + ';background:' + (style.background || '#f9fafb') + ';border-radius:' + (style['border-radius'] || '0') + 'px"><div style="max-width:1140px;margin:0 auto">' + sectionHtml + '</div></section>';
                design.push({ type: 'section', data: block?.data || {}, style, children });
                return { design: { type: 'section', data: block?.data, style, children }, html: sectionOuter };
            }

            const content = wrap.querySelector('.cpb-block-content');
            if (!content) return null;
            let blockData = block ? { ...block.data } : {};
            content.querySelectorAll('.cpb-editable').forEach(el => {
                const f = el.dataset.field;
                if (el.tagName === 'IMG') blockData[f] = el.src;
                else blockData[f] = el.innerHTML;
            });
            const item = { type, data: blockData, style: block?.style };
            design.push(item);
            return { design: item, html: content.innerHTML };
        }

        canvas.querySelectorAll('.cpb-section').forEach(sec => {
            const r = collectBlock(sec);
            if (r && r.html) html += r.html;
        });
        canvas.querySelectorAll('.cpb-block-wrap:not(.cpb-section)').forEach(wrap => {
            const r = collectBlock(wrap);
            if (r && r.html) html += r.html;
        });

        const css = '.cpb-block-wrap{margin:0}.cpb-editable{outline:none}.cpb-section{margin-bottom:0}';
        return { design: JSON.stringify(design), html, css };
    }

    function loadDesign(designJson) {
        try {
            const design = typeof designJson === 'string' ? JSON.parse(designJson) : designJson;
            if (!Array.isArray(design)) return;

            const canvas = document.getElementById('cpb-canvas');
            canvas.innerHTML = '<div class="cpb-canvas-placeholder">ব্লক ড্রাগ করে এখানে ফেলুন</div>';
            blocksData = [];

            design.forEach(item => {
                if (!item || !item.type) return;
                if (item.type === 'section') {
                    addSection(item);
                    const section = canvas.querySelector('.cpb-section:last-child');
                    const inner = section?.querySelector('.cpb-section-inner');
                    if (inner && item.children && item.children.length) {
                        item.children.forEach(child => {
                            if (child && WIDGETS[child.type] && WIDGETS[child.type].html) {
                                addBlock(child.type, child.data, inner);
                                const wrap = inner.querySelector('.cpb-block-wrap:last-child');
                                if (wrap && child.style) {
                                    const block = blocksData[blocksData.length - 1];
                                    if (block) block.style = child.style;
                                    applyStylesToElement(wrap, child.style);
                                }
                            }
                        });
                        inner.querySelectorAll('.cpb-block-wrap').forEach((wrap, idx) => {
                            const child = item.children[idx];
                            if (child && child.data) {
                                wrap.querySelectorAll('.cpb-editable').forEach(el => {
                                    const f = el.dataset.field;
                                    const v = child.data[f];
                                    if (v) { if (el.tagName === 'IMG') el.src = v; else el.innerHTML = v; }
                                });
                            }
                        });
                    }
                } else if (WIDGETS[item.type] && WIDGETS[item.type].html) {
                    addBlock(item.type, item.data);
                    const wrap = canvas.querySelector('.cpb-block-wrap:last-child');
                    if (wrap && item.style) {
                        const block = blocksData[blocksData.length - 1];
                        if (block) block.style = item.style;
                        applyStylesToElement(wrap, item.style);
                    }
                    if (item.data) {
                        wrap.querySelectorAll('.cpb-editable').forEach(el => {
                            const f = el.dataset.field;
                            const v = item.data[f];
                            if (v) { if (el.tagName === 'IMG') el.src = v; else el.innerHTML = v; }
                        });
                    }
                }
            });
        } catch (e) { console.warn('Load design failed', e); }
    }

    window.CampaignPageBuilder = { init, exportDesign, loadDesign };
})();
