@php
    $generalsetting = $generalsetting ?? \App\Models\GeneralSetting::first();
    $resellerOn = ($generalsetting?->reseller_enabled ?? 1) == 1;
    $vendorOn = ($generalsetting?->vendor_enabled ?? 1) == 1;
    $bad = fn ($name) => $errors->has($name);
@endphp
@extends('layouts.mb-auth')
@section('title', 'Customer Register')
@section('auth_kind', 'register')
@section('heading', 'Create Your Account')
@section('subheading', 'আপনার তথ্য দিয়ে ফর্মটি পূরণ করুন')

@push('styles')
<style>
.acc-type { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px; margin-bottom: 14px; }
.acc-type label { display: flex; align-items: center; gap: 8px; border: 1px solid #dfe3ea; border-radius: 6px; padding: 9px 12px; cursor: pointer; font-size: 13.5px; background: #fff; margin: 0; transition: .15s; }
.acc-type label:has(input:checked) { border-color: #8b87e0; background: #f5f3ff; box-shadow: 0 0 0 3px rgba(139, 135, 224, .15); }
.acc-type input { width: 17px; height: 17px; }
.au-form .au-field input[type=file] { padding: 7px 10px; font-size: 13px; }
.au-sec { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .6px; margin: 4px 0 8px; }
</style>
@endpush

@section('content')
<form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    @if ($resellerOn || $vendorOn)
        <div class="au-sec">একাউন্টের ধরন</div>
        <div class="acc-type">
            <label><input type="radio" name="_acc" value="customer" checked> <i class="fa-regular fa-user text-primary"></i> কাস্টমার</label>
            @if ($resellerOn)
                <label><input type="checkbox" id="is_reseller" name="is_reseller" value="1" @checked(old('is_reseller')) onchange="toggleResellerFields()"> <i class="fa-solid fa-user-tie text-success"></i> রিসেলার একাউন্ট</label>
            @endif
            @if ($vendorOn)
                <label><input type="checkbox" id="is_seller" name="is_seller" value="1" @checked(old('is_seller')) onchange="toggleSellerFields()"> <i class="fa-solid fa-store text-warning"></i> সেলার (ভেন্ডর) একাউন্ট</label>
            @endif
        </div>
    @endif

    <div class="row g-2">
        <div class="col-sm-6">
            <div @class(['au-field', 'is-invalid' => $bad('name')])><span class="ic"><i class="fa-regular fa-user"></i></span><input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="আপনার নাম *" required></div>
            @error('name')<div class="au-err">{{ $message }}</div>@enderror
            <div class="au-help" id="owner_name_label" style="display:none">দোকান মালিকের নাম লিখুন</div>
        </div>
        <div class="col-sm-6">
            <div @class(['au-field', 'is-invalid' => $bad('phone')])><span class="ic"><i class="fa-solid fa-mobile-screen"></i></span><input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="মোবাইল নাম্বার (01XXXXXXXXX) *" inputmode="tel" required></div>
            @error('phone')<div class="au-err">{{ $message }}</div>@enderror
        </div>
    </div>

    <div id="email_field" style="display:none">
        <div @class(['au-field', 'is-invalid' => $bad('email')])><span class="ic"><i class="fa-regular fa-envelope"></i></span><input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="ইমেইল *"></div>
        @error('email')<div class="au-err">{{ $message }}</div>@enderror
    </div>

    {{-- reseller --}}
    <div id="reseller_shop_name_field" style="display:none">
        <div @class(['au-field', 'is-invalid' => $bad('reseller_shop_name')])><span class="ic"><i class="fa-solid fa-store"></i></span><input type="text" id="reseller_shop_name" name="reseller_shop_name" value="{{ old('reseller_shop_name') }}" placeholder="Shop Name *"></div>
        @error('reseller_shop_name')<div class="au-err">{{ $message }}</div>@enderror
    </div>
    <div id="reseller_verification_note" style="display:none" class="au-note mb-3 mt-0">
        <i class="fa-solid fa-circle-info text-primary"></i> রেজিস্ট্রেশনের পর লগইন করে <b>রিসেলার ড্যাশবোর্ড → ভেরিফিকেশন</b> পেজ থেকে ভোটার আইডি (উভয় পাশ) ও আপনার ছবি আপলোড করুন।
    </div>

    {{-- seller / vendor --}}
    <div id="shop_name_field" style="display:none">
        <div @class(['au-field', 'is-invalid' => $bad('shop_name')])><span class="ic"><i class="fa-solid fa-store"></i></span><input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name') }}" placeholder="শপের নাম *"></div>
        @error('shop_name')<div class="au-err">{{ $message }}</div>@enderror
    </div>
    <div id="slug_field" style="display:none">
        <div @class(['au-field', 'is-invalid' => $bad('slug')])><span class="ic"><i class="fa-solid fa-globe"></i></span><span class="pre">{{ preg_replace('#^https?://#', '', url('/')) }}/</span><input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="my-shop-name *" autocomplete="off" autocapitalize="off" spellcheck="false"></div>
        @error('slug')<div class="au-err">{{ $message }}</div>@else<div class="au-help">শুধুমাত্র ইংরেজি অক্ষর, সংখ্যা এবং হাইফেন (-) ব্যবহার করুন</div>@enderror
    </div>
    <div id="address_field" style="display:none">
        <div @class(['au-field', 'is-invalid' => $bad('address')])><span class="ic"><i class="fa-solid fa-location-dot"></i></span><textarea id="address" name="address" rows="2" placeholder="আপনার শপের ঠিকানা">{{ old('address') }}</textarea></div>
        @error('address')<div class="au-err">{{ $message }}</div>@enderror
    </div>
    <div class="row g-2">
        <div class="col-sm-6" id="logo_field" style="display:none">
            <div @class(['au-field', 'is-invalid' => $bad('logo')])><span class="ic"><i class="fa-regular fa-image"></i></span><input type="file" id="logo" name="logo" accept="image/*"></div>
            @error('logo')<div class="au-err">{{ $message }}</div>@else<div class="au-help">শপ লোগো (সর্বোচ্চ 2MB)</div>@enderror
        </div>
        <div class="col-sm-6" id="banner_field" style="display:none">
            <div @class(['au-field', 'is-invalid' => $bad('banner')])><span class="ic"><i class="fa-regular fa-images"></i></span><input type="file" id="banner" name="banner" accept="image/*"></div>
            @error('banner')<div class="au-err">{{ $message }}</div>@else<div class="au-help">শপ ব্যানার (সর্বোচ্চ 3MB)</div>@enderror
        </div>
    </div>
    <div id="seller_verification_note" style="display:none" class="au-note mb-3 mt-0">
        <i class="fa-solid fa-circle-info text-primary"></i> লগইন করার পর <b>ভেন্ডর ড্যাশবোর্ড → ভেরিফিকেশন</b> থেকে এনআইডি ও আপনার ছবি আপলোড করুন। (লোগো ও ব্যানার এখানে ঐচ্ছিক।)
    </div>

    <div class="row g-2">
        <div class="col-sm-6" id="password_col">
            <div @class(['au-field', 'is-invalid' => $bad('password')])><span class="ic"><i class="fa-solid fa-lock"></i></span><input type="password" id="password" name="password" placeholder="পাসওয়ার্ড *" autocomplete="new-password" required><button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button></div>
            @error('password')<div class="au-err">{{ $message }}</div>@enderror
        </div>
        <div class="col-sm-6" id="password_confirmation_field" style="display:none">
            <div @class(['au-field', 'is-invalid' => $bad('password_confirmation')])><span class="ic"><i class="fa-solid fa-lock"></i></span><input type="password" id="password_confirmation" name="password_confirmation" placeholder="পাসওয়ার্ড নিশ্চিত করুন *" autocomplete="new-password"><button type="button" class="eye" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button></div>
            @error('password_confirmation')<div class="au-err">{{ $message }}</div>@enderror
        </div>
    </div>

    <button type="submit" class="au-btn mt-1"><i class="fa-solid fa-user-plus"></i> রেজিস্ট্রেশন করুন</button>
    <a href="{{ route('customer.login') }}" class="au-btn-2">আগেই একাউন্ট আছে? লগিন করুন</a>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const $ = id => document.getElementById(id);
    const show = (id, on) => { const el = $(id); if (el) el.style.display = on ? '' : 'none'; };
    const req = (id, on) => { const el = $(id); if (el) on ? el.setAttribute('required', 'required') : el.removeAttribute('required'); };
    const isReseller = () => !!($('is_reseller') && $('is_reseller').checked);
    const isSeller = () => !!($('is_seller') && $('is_seller').checked);
    const customerRadio = document.querySelector('input[name="_acc"]');

    function refresh() {
        const r = isReseller(), s = isSeller();
        if (customerRadio) customerRadio.checked = !r && !s;
        // reseller only
        show('reseller_shop_name_field', r); req('reseller_shop_name', r);
        show('reseller_verification_note', r);
        // seller only
        ['shop_name_field', 'slug_field', 'address_field', 'logo_field', 'banner_field', 'seller_verification_note'].forEach(id => show(id, s));
        req('shop_name', s); req('slug', s);
        show('owner_name_label', s);
        // both
        show('email_field', r || s); req('email', r || s);
        show('password_confirmation_field', r || s); req('password_confirmation', r || s);
        const pc = $('password_col'); if (pc) pc.className = (r || s) ? 'col-sm-6' : 'col-12';
    }
    window.toggleResellerFields = () => { if (isReseller() && $('is_seller')) $('is_seller').checked = false; refresh(); };
    window.toggleSellerFields = () => { if (isSeller() && $('is_reseller')) $('is_reseller').checked = false; refresh(); };
    if (customerRadio) customerRadio.addEventListener('change', () => {
        if ($('is_reseller')) $('is_reseller').checked = false;
        if ($('is_seller')) $('is_seller').checked = false;
        refresh();
    });

    // auto slug from shop name
    const shop = $('shop_name'), slug = $('slug');
    if (shop && slug) shop.addEventListener('input', function () {
        slug.value = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
    });
    refresh();
})();
</script>
@endpush
