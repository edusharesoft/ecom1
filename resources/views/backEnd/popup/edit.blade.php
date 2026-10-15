@extends('backEnd.layouts.master')
@section('title', 'Edit Popup Campaign')

@section('content')

{{-- Custom CSS for Edit Page --}}
<style>
    .edit-card {
        background: #fff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #64748b;
        margin-bottom: 6px;
    }
    .form-control {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    /* Image Upload Area */
    .image-upload-container {
        position: relative;
        width: 100%;
        overflow: hidden;
        border-radius: 10px;
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        text-align: center;
        padding: 10px;
        transition: all 0.3s;
    }
    .image-upload-container:hover {
        border-color: #6366f1;
        background: #eef2ff;
    }
    .current-img-preview {
        max-width: 100%;
        border-radius: 8px;
        display: block;
        margin: 0 auto;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .upload-btn-overlay {
        margin-top: 15px;
        display: inline-block;
    }
</style>

<div class="container-fluid py-4">
    
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">পপআপ এডিট করুন</h4>
            <small class="text-muted">ইমেজ বা লিংক আপডেট করুন</small>
        </div>
        <a href="{{ route('admin.popup.index') }}" class="btn btn-light border text-muted fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.popup.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="hidden_id" value="{{ $edit->id }}">

        <div class="row g-4">
            
            {{-- প্রধান: ইমেজ ও লিংক --}}
            <div class="col-lg-6">
                <div class="card edit-card h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="far fa-image me-2 text-info"></i>পপআপ ইমেজ</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="image-upload-container" onclick="document.getElementById('editImageInput').click()">
                            <img id="editImgPreview" src="{{ url('public/'.$edit->image) }}" class="current-img-preview" alt="Popup Image">
                            <div class="upload-btn-overlay">
                                <span class="badge bg-light text-dark border px-3 py-2" style="cursor: pointer;">
                                    <i class="fas fa-camera me-1"></i> ইমেজ চেঞ্জ করুন
                                </span>
                            </div>
                            <input type="file" name="image" id="editImageInput" class="d-none" accept="image/*" onchange="previewEditImage(this)">
                        </div>
                        <div class="mt-4 text-start">
                            <label class="form-label fw-bold small">লিংক (ইমেজ ক্লিকে)</label>
                            <input type="text" class="form-control" name="link" value="{{ $edit->link }}" placeholder="https://...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- সেটিংস ও অ্যাডভান্সড --}}
            <div class="col-lg-6">
                
                {{-- স্ট্যাটাস ও সেভ --}}
                <div class="card edit-card mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-rocket me-2 text-success"></i>স্ট্যাটাস</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <label class="form-label mb-0 text-dark">পপআপ সক্রিয়</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ $edit->status == 1 ? 'checked' : '' }} style="width: 3em; height: 1.5em; cursor: pointer;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> আপডেট করুন
                        </button>
                    </div>
                </div>

                {{-- অ্যাডভান্সড টেক্সট (ঐচ্ছিক) --}}
                <div class="card edit-card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-cog me-2 text-secondary"></i>টেক্সট/বাটন (ঐচ্ছিক)</h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="small text-muted mb-3">খালি থাকলে শুধু ইমেজ দেখা যাবে। ভরলে পুরনো লেআউট দেখা যাবে।</p>
                        <div class="mb-3">
                            <label class="form-label small">টাইটেল</label>
                            <input type="text" class="form-control" name="title" value="{{ $edit->title }}" placeholder="Admin list এর জন্য">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">বর্ণনা</label>
                            <textarea class="form-control" name="description" rows="2">{{ $edit->description }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small">বাটন টেক্সট</label>
                                <input type="text" class="form-control" name="btn_text" value="{{ $edit->btn_text }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small">ফুটার টেক্সট</label>
                                <input type="text" class="form-control" name="offer_end_text" value="{{ $edit->offer_end_text }}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

{{-- JavaScript for Image Preview --}}
<script>
    function previewEditImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editImgPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection