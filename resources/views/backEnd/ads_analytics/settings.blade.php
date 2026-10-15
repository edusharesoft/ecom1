@extends('backEnd.layouts.master')
@section('title', 'Ads Analytics Settings | API Configuration')

@section('content')
<div class="container-fluid py-3">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Ads Analytics API Settings</h4>
        <a href="{{ route('admin.ads_analytics.dashboard') }}" class="btn btn-outline-primary"><i class="fe-arrow-left"></i> Dashboard</a>
      </div>
    </div>
  </div>

  <form action="{{ route('admin.ads_analytics.save_settings') }}" method="POST">
    @csrf

    {{-- Facebook Ads --}}
    <div class="card mb-3">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fe-facebook me-2"></i>Facebook Ads Manager</h5>
        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" name="facebook_is_active" value="1" {{ optional($settings['facebook'] ?? null)->is_active ? 'checked' : '' }}>
          <label class="form-check-label">Active</label>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-6"><label>Ad Account ID (act_123456)</label><input type="text" name="facebook_ad_account_id" class="form-control" value="{{ optional($settings['facebook'] ?? null)->ad_account_id ?? '' }}" placeholder="act_1234567890"></div>
          <div class="col-md-6"><label>Access Token</label><input type="password" name="facebook_access_token" class="form-control" value="{{ optional($settings['facebook'] ?? null)->access_token ?? '' }}" placeholder="Long-lived token"></div>
          <div class="col-md-6"><label>App ID</label><input type="text" name="facebook_app_id" class="form-control" value="{{ optional($settings['facebook'] ?? null)->app_id ?? '' }}"></div>
          <div class="col-md-6"><label>App Secret</label><input type="password" name="facebook_app_secret" class="form-control" value="{{ optional($settings['facebook'] ?? null)->app_secret ?? '' }}"></div>
        </div>
        <small class="text-muted">Facebook Developers → App → Marketing API → System User Token or User Access Token</small>
      </div>
    </div>

    {{-- Google Ads --}}
    <div class="card mb-3">
      <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fe-globe me-2"></i>Google Ads</h5>
        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" name="google_is_active" value="1" {{ optional($settings['google'] ?? null)->is_active ? 'checked' : '' }}>
          <label class="form-check-label">Active</label>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-6"><label>Customer ID (123-456-7890)</label><input type="text" name="google_ad_account_id" class="form-control" value="{{ optional($settings['google'] ?? null)->ad_account_id ?? '' }}" placeholder="1234567890"></div>
          <div class="col-md-6"><label>Client ID</label><input type="text" name="google_client_id" class="form-control" value="{{ optional($settings['google'] ?? null)->client_id ?? '' }}"></div>
          <div class="col-md-6"><label>Client Secret</label><input type="password" name="google_client_secret" class="form-control" value="{{ optional($settings['google'] ?? null)->client_secret ?? '' }}"></div>
          <div class="col-md-6"><label>Refresh Token</label><input type="text" name="google_refresh_token" class="form-control" value="{{ optional($settings['google'] ?? null)->refresh_token ?? '' }}" placeholder="1//..."></div>
        </div>
        <small class="text-muted">Google Cloud Console → OAuth 2.0 credentials. Application verification required for Google Ads API.</small>
      </div>
    </div>

    {{-- TikTok Ads --}}
    <div class="card mb-3">
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fe-video me-2"></i>TikTok Ads</h5>
        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" name="tiktok_is_active" value="1" {{ optional($settings['tiktok'] ?? null)->is_active ? 'checked' : '' }}>
          <label class="form-check-label">Active</label>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-6"><label>Advertiser ID</label><input type="text" name="tiktok_advertiser_id" class="form-control" value="{{ optional($settings['tiktok'] ?? null)->ad_account_id ?? '' }}" placeholder="1234567890123456789"></div>
          <div class="col-md-6"><label>Access Token</label><input type="password" name="tiktok_access_token" class="form-control" value="{{ optional($settings['tiktok'] ?? null)->access_token ?? '' }}"></div>
        </div>
        <small class="text-muted">TikTok for Business → Tools → API → Create Access Token</small>
      </div>
    </div>

    <button type="submit" class="btn btn-primary"><i class="fe-save"></i> Save</button>
  </form>
</div>
@endsection
