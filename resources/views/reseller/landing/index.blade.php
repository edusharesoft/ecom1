@extends('reseller.layouts.app')

@section('title', 'ল্যান্ডিং পেজ সেটিংস')
@section('page-title', 'ল্যান্ডিং পেজ')

@push('styles')
<style>
    .landing-card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom:24px; overflow:hidden; }
    .landing-card-header { padding:20px 24px; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-weight:700; }
    .landing-card-body { padding:24px; }
    .form-label-custom { font-weight:600; font-size:0.9rem; color:#475569; margin-bottom:8px; }
    .form-control-custom { border-radius:10px; border:1px solid #e2e8f0; padding:12px 16px; }
    .img-preview { max-height:120px; border-radius:12px; object-fit:contain; border:1px solid #e2e8f0; }
    .slider-item { position:relative; display:inline-block; margin:8px; }
    .slider-item img { width:120px; height:80px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0; }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">ল্যান্ডিং পেজ সেটিংস</h4>
            <p class="text-muted small mb-0">আপনার ল্যান্ডিং পেজ কাস্টমাইজ করুন</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reseller.landing.products') }}" class="btn btn-success">
                <i class="fas fa-box me-2"></i> প্রোডাক্ট ম্যানেজ করুন
            </a>
            <a href="{{ $landingUrl }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt me-2"></i> লাইভ প্রিভিউ
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('reseller.landing.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="landing-card">
                    <div class="landing-card-header">বেসিক তথ্য</div>
                    <div class="landing-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">পেজ ইউআরএল (Slug)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">{{ url('/r/') }}/</span>
                                    <input type="text" name="slug" class="form-control form-control-custom" value="{{ old('slug', $landing->slug) }}" placeholder="my-store" pattern="[a-z0-9\-]+" required>
                                </div>
                                <small class="text-muted">শুধু ইংরেজি অক্ষর, সংখ্যা ও হাইফেন। লিঙ্ক: {{ url('/r') }}/<strong>{{ $landing->slug }}</strong></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">কাস্টম ডোমেইন (ঐচ্ছিক)</label>
                                <input type="text" name="custom_domain" class="form-control form-control-custom" value="{{ old('custom_domain', $landing->custom_domain) }}" placeholder="shop.example.com">
                                <button type="button" class="btn btn-link btn-sm p-0 mt-1 text-primary" data-bs-toggle="collapse" data-bs-target="#customDomainInstruction" aria-expanded="false">
                                    <i class="fas fa-question-circle me-1"></i> কিভাবে কানেক্ট করবেন?
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">ল্যান্ডিং পেজ টাইটেল</label>
                                <input type="text" name="title" class="form-control form-control-custom" value="{{ old('title', $landing->title) }}" placeholder="আমার স্টোর">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">ট্যাগলাইন (ঐচ্ছিক)</label>
                                <input type="text" name="tagline" class="form-control form-control-custom" value="{{ old('tagline', $landing->tagline) }}" placeholder="আপনার বিশ্বস্ত শপিং পার্টনার">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">স্ক্রলিং টেক্সট (ন্যাভ বার এ)</label>
                                <input type="text" name="scrolling_text" class="form-control form-control-custom" value="{{ old('scrolling_text', $landing->scrolling_text ?? '') }}" placeholder="উদাহরণ: বিনামূল্যে ডেলিভারি | নতুন অফার আসছে | কল করুন: ০১৭৭৫৪৫৭০০৮">
                                <small class="text-muted">ন্যাভিগেশন বারে স্ক্রল হতে দেখাবে। খালি রাখলে স্ক্রলিং টেক্সট দেখাবে না।</small>
                            </div>
                        </div>

                        {{-- Custom Domain Instruction (collapsible) --}}
                        <div class="collapse mt-3" id="customDomainInstruction">
                            <div class="alert alert-info border-0 rounded-3 py-3">
                                <h6 class="fw-bold mb-3"><i class="fas fa-link me-2"></i>কাস্টম ডোমেইন কানেক্ট করার ধাপ</h6>
                                <ol class="mb-0 ps-3 small">
                                    <li class="mb-2"><strong>ডোমেইন লিখুন:</strong> উপরের ফিল্ডে শুধু ডোমেইন দিন (যেমন <code>shop.example.com</code>) — <code>https://</code> বা <code>/</code> লাগবে না।</li>
                                    <li class="mb-2"><strong>DNS সেটিং:</strong> আপনার ডোমেইন কেবিনে যান → DNS রেকর্ডে <strong>A রেকর্ড</strong> যোগ করুন। হোস্ট: <code>shop</code> (বা subdomain), ভ্যালু: আপনার সার্ভারের IP ঠিকানা।</li>
                                    <li class="mb-2"><strong>হোস্টিং সেটিং:</strong> cPanel/Plesk এ এই ডোমেইনটিকে আপনার লারাভেল প্রজেক্ট ফোল্ডারে অ্যাড করুন (যেখানে <code>public</code> ফোল্ডার আছে)।</li>
                                    <li class="mb-2"><strong>SSL:</strong> Let's Encrypt বা আপনার হোস্টিংয়ের SSL অপশন দিয়ে HTTPS সক্রিয় করুন।</li>
                                    <li>DNS প্রোপাগেট হলে (৫ মিনিট–৪৮ ঘণ্টা) কাস্টম ডোমেইনে আপনার ল্যান্ডিং পেজ লোড হবে।</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="landing-card">
                    <div class="landing-card-header">লোগো ও ইমেজ</div>
                    <div class="landing-card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label-custom">ফেভআইকন (Browser Tab আইকন)</label>
                                @if($landing->favicon ?? null)
                                    <div class="mb-2"><img src="{{ asset($landing->favicon) }}" class="img-preview" style="max-height:48px;max-width:48px;" alt="Favicon"></div>
                                @endif
                                <input type="file" name="favicon" class="form-control" accept=".ico,.png,.jpg,.jpeg,.webp">
                                <small class="text-muted">PNG, ICO বা JPG। 32×32 বা 64×64 রিকমেন্ডেড</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">লোগো</label>
                                @if($landing->logo)
                                    <div class="mb-2"><img src="{{ asset($landing->logo) }}" class="img-preview" alt="Logo"></div>
                                @endif
                                <input type="file" name="logo" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">ব্যানার ইমেজ</label>
                                @if($landing->banner_image)
                                    <div class="mb-2"><img src="{{ asset($landing->banner_image) }}" class="img-preview" alt="Banner"></div>
                                @endif
                                <input type="file" name="banner_image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">স্লাইডার ইমেজ (একাধিক)</label>
                                @if($landing->slider_images && count($landing->slider_images) > 0)
                                    <div class="mb-3">
                                        @foreach($landing->slider_images as $idx => $img)
                                            <div class="slider-item">
                                                <img src="{{ asset($img) }}" alt="Slider">
                                                <input type="hidden" name="remove_slider_index[]" value="" disabled class="slider-remove">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <input type="file" name="slider_images[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="landing-card">
                    <div class="landing-card-header">যোগাযোগ তথ্য</div>
                    <div class="landing-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">ফোন নম্বর</label>
                                <input type="text" name="phone" class="form-control form-control-custom" value="{{ old('phone', $landing->phone) }}" placeholder="017xxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">ইমেইল</label>
                                <input type="email" name="email" class="form-control form-control-custom" value="{{ old('email', $landing->email) }}" placeholder="email@example.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">ঠিকানা</label>
                                <textarea name="address" class="form-control form-control-custom" rows="3" placeholder="সম্পূর্ণ ঠিকানা">{{ old('address', $landing->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="landing-card">
                    <div class="landing-card-header">সামাজিক মিডিয়া লিংক (ফুটার)</div>
                    <div class="landing-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="show_social_footer" value="1" id="showSocial" {{ ($landing->show_social_footer ?? 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showSocial">ফুটারে Follow Us সেকশন দেখান</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Facebook URL</label>
                                <input type="url" name="facebook_url" class="form-control form-control-custom" value="{{ old('facebook_url', $landing->facebook_url ?? '') }}" placeholder="https://facebook.com/yourpage">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Twitter / X URL</label>
                                <input type="url" name="twitter_url" class="form-control form-control-custom" value="{{ old('twitter_url', $landing->twitter_url ?? '') }}" placeholder="https://twitter.com/yourpage">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">WhatsApp (লিংক বা নম্বর)</label>
                                <input type="text" name="whatsapp_url" class="form-control form-control-custom" value="{{ old('whatsapp_url', $landing->whatsapp_url ?? '') }}" placeholder="https://wa.me/88017xxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">YouTube URL</label>
                                <input type="url" name="youtube_url" class="form-control form-control-custom" value="{{ old('youtube_url', $landing->youtube_url ?? '') }}" placeholder="https://youtube.com/@yourchannel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Instagram URL</label>
                                <input type="url" name="instagram_url" class="form-control form-control-custom" value="{{ old('instagram_url', $landing->instagram_url ?? '') }}" placeholder="https://instagram.com/yourpage">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="landing-card">
                    <div class="landing-card-header">ট্র্যাকিং পিক্সেল ও GTM</div>
                    <div class="landing-card-body">
                        <p class="text-muted small mb-3">ল্যান্ডিং পেজে Facebook Pixel, GTM, TikTok Pixel ও Conversion API ইন্টিগ্রেট করুন।</p>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-custom">Facebook Pixel ID</label>
                                <input type="text" name="facebook_pixel_id" class="form-control form-control-custom" value="{{ old('facebook_pixel_id', $landing->facebook_pixel_id ?? '') }}" placeholder="1234567890123456">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Google Tag Manager (GTM) ID</label>
                                <input type="text" name="gtm_id" class="form-control form-control-custom" value="{{ old('gtm_id', $landing->gtm_id ?? '') }}" placeholder="GTM-XXXXXXX">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">TikTok Pixel ID</label>
                                <input type="text" name="tiktok_pixel_id" class="form-control form-control-custom" value="{{ old('tiktok_pixel_id', $landing->tiktok_pixel_id ?? '') }}" placeholder="XXXXXXXXXXXXXXXX">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Facebook Conversion API Access Token (ঐচ্ছিক)</label>
                                <input type="text" name="facebook_capi_access_token" class="form-control form-control-custom" value="{{ old('facebook_capi_access_token', $landing->facebook_capi_access_token ?? '') }}" placeholder="Server-side events এর জন্য">
                                <small class="text-muted">CAPI ইভেন্ট সার্ভার সাইডে পাঠাতে এই টোকেন লাগে</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="landing-card">
                    <div class="landing-card-header">নিউজলেটার</div>
                    <div class="landing-card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_newsletter_footer" value="1" id="showNewsletter" {{ ($landing->show_newsletter_footer ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="showNewsletter">ফুটারে নিউজলেটার সাবস্ক্রাইব ফর্ম দেখান</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="landing-card">
                    <div class="landing-card-header">পাবলিশ</div>
                    <div class="landing-card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ ($landing->is_active ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">ল্যান্ডিং পেজ সক্রিয়</label>
                        </div>
                        <a href="{{ $landingUrl }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-eye me-2"></i> প্রিভিউ দেখুন
                        </a>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i> সেভ করুন
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
