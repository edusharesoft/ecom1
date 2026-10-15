@extends('backEnd.layouts.master')
@section('title', 'Sales Notification')

@section('css')
<style>
.snx-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.07); margin-bottom:20px; overflow:hidden; }
.snx-card-header { padding:13px 18px; display:flex; align-items:center; gap:8px; font-weight:700; font-size:13px; color:#fff; flex-wrap:wrap; gap:6px; }
.snx-card-header.purple { background:linear-gradient(135deg,#667eea,#764ba2); }
.snx-card-header.green  { background:linear-gradient(135deg,#11998e,#38ef7d); color:#1a3a2a; }
.snx-card-header.orange { background:linear-gradient(135deg,#f7971e,#ffd200); color:#5a3a00; }
.snx-card-header.red    { background:linear-gradient(135deg,#eb3349,#f45c43); }
.snx-card-header.blue   { background:linear-gradient(135deg,#2193b0,#6dd5ed); }
.snx-card-body { padding:18px; }

.snx-toggle { position:relative; display:inline-block; width:46px; height:24px; flex-shrink:0; }
.snx-toggle input { opacity:0; width:0; height:0; }
.snx-slider { position:absolute; cursor:pointer; inset:0; background:#ccc; border-radius:24px; transition:.3s; }
.snx-slider:before { position:absolute; content:""; height:16px; width:16px; left:4px; bottom:4px; background:#fff; border-radius:50%; transition:.3s; }
.snx-toggle input:checked + .snx-slider { background:#22c55e; }
.snx-toggle input:checked + .snx-slider:before { transform:translateX(22px); }

/* Drag list */
.snx-list { list-style:none; padding:0; margin:0; max-height:480px; overflow-y:auto; }
.snx-item { display:flex; align-items:center; gap:7px; padding:8px 12px; border-bottom:1px solid #f1f5f9; background:#fff; }
.snx-item:last-child { border-bottom:none; }
.snx-item:hover { background:#f8fafc; }
.snx-item.sortable-ghost { background:#e0e7ff; opacity:.6; }
.snx-item.sortable-chosen { background:#eff6ff; }
.drag-h { cursor:grab; color:#94a3b8; font-size:16px; flex-shrink:0; }
.drag-h:active { cursor:grabbing; }

.b-real   { background:#d1fae5; color:#065f46; padding:1px 7px; border-radius:20px; font-size:10px; font-weight:700; white-space:nowrap; }
.b-custom { background:#fef3c7; color:#92400e; padding:1px 7px; border-radius:20px; font-size:10px; font-weight:700; white-space:nowrap; }

.sn-name { font-weight:700; font-size:12px; color:#1e293b; white-space:nowrap; min-width:65px; max-width:85px; overflow:hidden; text-overflow:ellipsis; flex-shrink:0; }
.sn-prod { font-size:12px; color:#475569; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; flex:1; min-width:0; }
.sn-time { font-size:11px; color:#94a3b8; white-space:nowrap; flex-shrink:0; }

/* Real table */
.real-wrap { max-height:500px; overflow-y:auto; }
.real-wrap table { font-size:12px; }
.real-wrap thead th { position:sticky; top:0; background:#f8fafc; z-index:2; font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; padding:9px 10px; white-space:nowrap; }
.real-wrap tbody td { padding:8px 10px; vertical-align:middle; border-color:#f1f5f9; }

#save-order-btn { display:none; }
#save-order-btn.show { display:inline-flex; }

.snx-tabs .nav-link { font-weight:600; font-size:13px; color:#64748b; border-radius:8px 8px 0 0; }
.snx-tabs .nav-link.active { color:#667eea; border-bottom:2px solid #667eea; background:#f5f3ff; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    <div class="mb-3">
        <h4 class="fw-bold mb-0">🔔 Sales Notification Popup</h4>
        <small class="text-muted">Website এর bottom-left এ popup manage করুন</small>
    </div>

    <div class="row g-3">

        {{-- ── LEFT: Settings + Add ─────────────────── --}}
        <div class="col-lg-4">

            <div class="snx-card">
                <div class="snx-card-header purple"><i class="mdi mdi-cog me-1"></i> Global Settings</div>
                <div class="snx-card-body">
                    <form action="{{ route('admin.sale-notification.settings') }}" method="POST">
                        @csrf
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-2" style="background:#f0eeff;border:1.5px solid #c4b5fd;">
                            <div><div class="fw-bold small">Popup চালু/বন্ধ</div></div>
                            <label class="snx-toggle"><input type="checkbox" name="is_enabled" value="1" {{ $setting->is_enabled ? 'checked' : '' }}><span class="snx-slider"></span></label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-2" style="background:#f0fdf4;border:1.5px solid #86efac;">
                            <div><div class="fw-bold small">✅ Real Orders দেখাবে</div><small class="text-muted" style="font-size:10px;">Active করা orders</small></div>
                            <label class="snx-toggle"><input type="checkbox" name="show_real_orders" value="1" {{ $setting->show_real_orders ? 'checked' : '' }}><span class="snx-slider"></span></label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-2" style="background:#fffbeb;border:1.5px solid #fcd34d;">
                            <div><div class="fw-bold small">🎭 Custom Notifications</div></div>
                            <label class="snx-toggle"><input type="checkbox" name="show_fake_orders" value="1" {{ $setting->show_fake_orders ? 'checked' : '' }}><span class="snx-slider"></span></label>
                        </div>
                        <hr class="my-2">
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label fw-bold" style="font-size:10px;">দেখানোর সময় (সে)</label>
                                <input type="number" name="display_duration" class="form-control form-control-sm" value="{{ $setting->display_duration }}" min="2" max="30">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold" style="font-size:10px;">Min Interval (সে)</label>
                                <input type="number" name="interval_min" class="form-control form-control-sm" value="{{ $setting->interval_min }}" min="3" max="60">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold" style="font-size:10px;">Max Interval (সে)</label>
                                <input type="number" name="interval_max" class="form-control form-control-sm" value="{{ $setting->interval_max }}" min="5" max="120">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-2 rounded-pill fw-bold btn-sm">💾 Settings Save</button>
                    </form>
                </div>
            </div>

            <div class="snx-card">
                <div class="snx-card-header green"><i class="mdi mdi-plus-circle me-1"></i> Custom Add</div>
                <div class="snx-card-body">
                    <form action="{{ route('admin.sale-notification.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label fw-bold small mb-1">Customer Name *</label>
                            <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="যেমন: Reyad" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small mb-1">Product Name *</label>
                            <input type="text" name="product_name" class="form-control form-control-sm" placeholder="যেমন: Samsung Galaxy A55" required>
                        </div>
                        <div class="mb-2">
                            <select class="form-select form-select-sm" onchange="fillProduct(this)">
                                <option value="">-- Product select (optional) --</option>
                                @foreach($products as $p)
                                <option value="{{ $p->name }}" data-url="{{ route('product', $p->slug) }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="product_url" id="product_url_input">
                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold btn-sm">➕ Add</button>
                    </form>
                </div>
            </div>

            <div class="snx-card">
                <div class="snx-card-header orange"><i class="mdi mdi-format-list-bulleted me-1"></i> Bulk Add</div>
                <div class="snx-card-body">
                    <form action="{{ route('admin.sale-notification.bulk') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label fw-bold small mb-1">প্রতি লাইনে: <code>নাম | পণ্য</code></label>
                            <textarea name="bulk_data" class="form-control" rows="5" style="font-family:monospace;font-size:12px;"
                                placeholder="Reyad | Samsung Galaxy A55&#10;Jamil | iPhone 15 Pro"></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold btn-sm">⬆️ Bulk Add</button>
                    </form>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: Two tabs ───────────────────────── --}}
        <div class="col-lg-8">

            <ul class="nav nav-tabs snx-tabs" id="snxTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tabReal">
                        ✅ Real Orders <span class="badge bg-success ms-1">{{ $realNotifs->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tabCustom">
                        🎭 Custom <span class="badge bg-warning text-dark ms-1">{{ $customNotifs->count() }}</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                {{-- ── Real Orders Tab ── --}}
                <div class="tab-pane fade show active" id="tabReal">
                    <div class="snx-card" style="border-radius:0 12px 12px 12px;">
                        <div class="snx-card-header blue">
                            <span>✅ Real Orders — Active করলে website এ দেখাবে</span>
                            <span class="ms-auto d-flex gap-2 align-items-center">
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="syncRealOrders()" id="sync-btn" style="font-size:11px;">
                                    🔄 Orders Sync করুন
                                </button>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" onclick="deleteAllReal()" style="font-size:11px;">
                                    🗑️ সব Delete
                                </button>
                            </span>
                        </div>
                        <div class="snx-card-body p-0">
                            @if($realNotifs->count() > 0)
                            <div class="real-wrap">
                                <table class="table table-hover mb-0" id="real-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>Product</th>
                                            <th>Time</th>
                                            <th class="text-center">Active</th>
                                            <th class="text-center">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody id="real-tbody">
                                        @foreach($realNotifs as $i => $n)
                                        <tr id="real-row-{{ $n->id }}">
                                            <td class="text-muted">{{ $i+1 }}</td>
                                            <td><span class="fw-bold">{{ $n->customer_name }}</span></td>
                                            <td><span class="d-block text-truncate" style="max-width:200px;" title="{{ $n->product_name }}">{{ $n->product_name }}</span></td>
                                            <td class="text-muted small">{{ $n->created_at ? $n->created_at->diffForHumans() : '-' }}</td>
                                            <td class="text-center">
                                                <label class="snx-toggle">
                                                    <input type="checkbox" {{ $n->is_active ? 'checked' : '' }}
                                                           onchange="toggleItem({{ $n->id }}, this)">
                                                    <span class="snx-slider"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0"
                                                        onclick="deleteItem({{ $n->id }}, 'real-row-{{ $n->id }}')" style="font-size:12px;">✕</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5 text-muted" id="real-empty">
                                <div style="font-size:40px;">📦</div>
                                <div class="mt-2 fw-bold small">এখনো কোনো order sync হয়নি</div>
                                <div class="small">"🔄 Orders Sync করুন" button এ click করুন</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Custom Tab ── --}}
                <div class="tab-pane fade" id="tabCustom">
                    <div class="snx-card" style="border-radius:0 12px 12px 12px;">
                        <div class="snx-card-header red">
                            <span>☰ Drag করে order পরিবর্তন করুন</span>
                            <span class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 show" id="save-order-btn" onclick="saveOrder()" style="font-size:11px;display:none!important;">
                                    💾 Order Save
                                </button>
                                <form action="{{ route('admin.sale-notification.destroy-all') }}" method="POST" class="d-inline mb-0"
                                      onsubmit="return confirm('সব custom notification delete করবেন?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3" style="font-size:11px;">🗑️ সব Delete</button>
                                </form>
                            </span>
                        </div>
                        <div class="snx-card-body p-0">
                            @if($customNotifs->count() > 0)
                            <ul class="snx-list" id="snx-sortable">
                                @foreach($customNotifs as $n)
                                <li class="snx-item" data-id="{{ $n->id }}">
                                    <span class="drag-h">☰</span>
                                    <span class="b-custom">🎭</span>
                                    <span class="sn-name">{{ $n->customer_name }}</span>
                                    <span class="sn-prod" title="{{ $n->product_name }}">{{ $n->product_name }}</span>
                                    <span class="sn-time">{{ $n->created_at->diffForHumans() }}</span>
                                    <label class="snx-toggle">
                                        <input type="checkbox" {{ $n->is_active ? 'checked' : '' }}
                                               onchange="toggleItem({{ $n->id }}, this)">
                                        <span class="snx-slider"></span>
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0 flex-shrink-0"
                                            onclick="deleteItem({{ $n->id }}, null, this)" style="font-size:12px;">✕</button>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <div class="text-center py-5 text-muted">
                                <div style="font-size:40px;">🎭</div>
                                <div class="mt-2 fw-bold small">কোনো custom notification নেই</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <div class="alert alert-info border-0 rounded-3 mt-2 small py-2">
                <strong>💡 Tips:</strong>
                <span>Real Orders sync করুন → Active/Inactive করুন → website এ দেখাবে। Custom notifications drag করে order set করুন।</span>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
var CSRF           = '{{ csrf_token() }}';
var TOGGLE_URL     = '{{ url("admin/sale-notification") }}';
var REORDER_URL    = '{{ route("admin.sale-notification.reorder") }}';
var SYNC_REAL_URL  = '{{ route("admin.sale-notification.sync-real") }}';
var DEL_ALL_REAL   = '{{ route("admin.sale-notification.destroy-all-real") }}';
var orderChanged   = false;

// ── Drag & Drop ──────────────────────────────────────────────────────────────
var sortableList = document.getElementById('snx-sortable');
var saveBtn      = document.getElementById('save-order-btn');
if (sortableList) {
    Sortable.create(sortableList, {
        handle: '.drag-h', animation: 150,
        ghostClass: 'sortable-ghost', chosenClass: 'sortable-chosen',
        onEnd: function() {
            orderChanged = true;
            saveBtn.style.display = 'inline-flex';
        }
    });
}

function saveOrder() {
    var ids = Array.from(document.querySelectorAll('#snx-sortable .snx-item')).map(function(el){ return el.dataset.id; });
    saveBtn.disabled = true; saveBtn.textContent = '⏳ Saving...';
    post(REORDER_URL, {ids: ids}).then(function(){
        saveBtn.disabled = false; saveBtn.textContent = '✅ Saved!';
        setTimeout(function(){ saveBtn.textContent = '💾 Order Save'; saveBtn.style.display='none'; orderChanged=false; }, 2000);
    }).catch(function(){ saveBtn.disabled=false; saveBtn.textContent='❌ Error'; });
}

// ── Toggle ───────────────────────────────────────────────────────────────────
function toggleItem(id, checkbox) {
    fetch(TOGGLE_URL + '/' + id + '/toggle', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'}
    })
    .then(function(r){ return r.json(); })
    .then(function(res){ if (!res.success) checkbox.checked = !checkbox.checked; })
    .catch(function(){ checkbox.checked = !checkbox.checked; });
}

// ── Delete single ────────────────────────────────────────────────────────────
function deleteItem(id, rowId, btn) {
    if (!confirm('Delete করবেন?')) return;
    fetch(TOGGLE_URL + '/' + id, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'}
    })
    .then(function(r){ return r.json(); })
    .then(function(res){
        if (res.success) {
            if (rowId) {
                var row = document.getElementById(rowId);
                if (row) row.remove();
            } else if (btn) {
                var li = btn.closest('li');
                if (li) li.remove();
            }
        }
    });
}

// ── Sync Real Orders ─────────────────────────────────────────────────────────
function syncRealOrders() {
    var btn = document.getElementById('sync-btn');
    btn.disabled = true; btn.textContent = '⏳ Syncing...';
    post(SYNC_REAL_URL, {}).then(function(d){
        btn.disabled = false; btn.textContent = '✅ Synced! Reload করুন';
        setTimeout(function(){ location.reload(); }, 1500);
    }).catch(function(){ btn.disabled=false; btn.textContent='❌ Error'; });
}

// ── Delete all real ──────────────────────────────────────────────────────────
function deleteAllReal() {
    if (!confirm('সব Real Order notifications delete করবেন?')) return;
    fetch(DEL_ALL_REAL, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'}
    })
    .then(function(r){ return r.json(); })
    .then(function(res){ if (res.success) location.reload(); });
}

// ── Helper ───────────────────────────────────────────────────────────────────
function post(url, data) {
    return fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
        body: JSON.stringify(data)
    }).then(function(r){ return r.json(); });
}

function fillProduct(sel) {
    var name = sel.value;
    var url  = sel.options[sel.selectedIndex].dataset.url || '';
    if (name) {
        document.querySelector('[name="product_name"]').value = name;
        document.getElementById('product_url_input').value   = url;
    }
}
</script>
@endsection
