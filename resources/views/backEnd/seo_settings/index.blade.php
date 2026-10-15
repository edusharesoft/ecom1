@extends('backEnd.layouts.master')
@section('title','SEO Configuration')

@section('content')

<style>
    /* Professional SEO Studio Styling */
    :root {
        --seo-bg: #f8f9fc;
        --seo-card: #ffffff;
        --google-blue: #1a0dab;
        --google-green: #006621;
        --google-gray: #545454;
    }
    
    .seo-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #334155;
    }

    /* Left Side: Editor */
    .editor-card {
        background: var(--seo-card);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    
    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border: 1px solid #cbd5e1;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Right Side: Google Preview */
    .preview-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 25px;
        position: sticky;
        top: 20px;
    }
    
    .preview-header {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 10px;
    }

    /* Simulating Google Result */
    .google-result {
        font-family: Arial, sans-serif;
        max-width: 600px;
    }
    .g-cite {
        color: #202124;
        font-size: 14px;
        line-height: 1.3;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    .g-cite img {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        border-radius: 50%;
        background: #f1f1f1;
    }
    .g-title {
        color: var(--google-blue);
        font-size: 20px;
        line-height: 1.3;
        cursor: pointer;
        text-decoration: none;
        display: block;
        margin-bottom: 3px;
    }
    .g-title:hover { text-decoration: underline; }
    .g-desc {
        color: #4d5156;
        font-size: 14px;
        line-height: 1.58;
    }

    /* Character Counter */
    .char-count {
        font-size: 12px;
        margin-top: 4px;
        text-align: right;
        display: block;
        font-weight: 500;
    }
    .count-ok { color: #10b981; }
    .count-warn { color: #f59e0b; }
    .count-error { color: #ef4444; }

    .header-img-shadow {
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
    }
</style>

<div class="container-fluid py-4 seo-container">
    
    <div class="row g-4">
        
        <div class="col-lg-7">
            
            <div class="editor-card">
                <div class="p-4 border-bottom bg-light bg-opacity-25 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="header-img-shadow me-3 rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center fw-bold"
                              style="width: 48px; height: 48px; font-size: 1.1rem;"
                              aria-hidden="true">SEO</span>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">SEO Configuration</h5>
                            <small class="text-muted">Optimize your site for search engines</small>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    @if(session('success'))
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success d-flex align-items-center mb-4">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.seo_settings.update') }}" method="POST">
                        @csrf
                        
                        <h6 class="text-primary fw-bold mb-3 text-uppercase small ls-1"><i class="fas fa-layer-group me-2"></i>General Meta</h6>
                        
                        <div class="mb-4">
                            <label class="form-label">Meta Title</label>
                            <input type="text" id="inputTitle" name="meta_title" 
                                   class="form-control" 
                                   value="{{ old('meta_title', optional($seo)->meta_title ?? '') }}" 
                                   placeholder="Enter page title..."
                                   oninput="updatePreview()">
                            <span id="titleCount" class="char-count text-muted">0/60 characters</span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Meta Description</label>
                            <textarea id="inputDesc" name="meta_description" rows="4" 
                                      class="form-control" 
                                      placeholder="Write a compelling description..."
                                      oninput="updatePreview()">{{ old('meta_description', optional($seo)->meta_description ?? '') }}</textarea>
                            <span id="descCount" class="char-count text-muted">0/160 characters</span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Keywords / Tags</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-tags text-muted"></i></span>
                                <input type="text" name="meta_tags" class="form-control border-start-0" 
                                       value="{{ old('meta_tags', optional($seo)->meta_tags ?? '') }}" 
                                       placeholder="seo, laravel, optimization (comma separated)">
                            </div>
                            <small class="text-muted mt-1" style="font-size: 11px;">Separate keywords with commas.</small>
                        </div>

                        <hr class="my-4 border-light">

                        <h6 class="text-primary fw-bold mb-3 text-uppercase small ls-1"><i class="fab fa-google me-2"></i>Webmaster Tools</h6>
                        
                        <div class="mb-4">
                            <label class="form-label">Google Search Console Verification</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-key text-muted"></i></span>
                                <input type="text" name="search_console_verification" class="form-control"
                                       value="{{ old('search_console_verification', optional($seo)->search_console_verification ?? '') }}" 
                                       placeholder="google-site-verification=...">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-2">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5 d-none d-lg-block">
            <div class="preview-card shadow-sm">
                <div class="preview-header">
                    <i class="fab fa-google me-1"></i> Search Result Preview
                </div>
                
                <div class="google-result">
                    <div class="g-cite">
                        <img src="{{ asset(isset($generalsetting->favicon) ? $generalsetting->favicon : 'public/backEnd/assets/images/favicon.ico') }}" alt="" width="16" height="16">
                        <div class="d-flex flex-column">
                            <span style="font-size: 14px; color: #202124;">{{ config('app.name') }}</span>
                            <span style="font-size: 12px; color: #5f6368;">{{ url('/') }}</span>
                        </div>
                    </div>
                    <a href="javascript:void(0)" class="g-title" id="previewTitle" role="presentation">
                        {{ optional($seo)->meta_title ?? 'Your Page Title Goes Here' }}
                    </a>
                    <div class="g-desc" id="previewDesc">
                        {{ \Illuminate\Support\Str::limit(optional($seo)->meta_description ?? 'This is how your page description will look in Google search results. Start typing in the form to see real-time updates.', 160) }}
                    </div>
                </div>

                <div class="mt-5 p-3 bg-light rounded-3 border border-light">
                    <h6 class="fw-bold text-dark mb-2"><i class="far fa-lightbulb text-warning me-2"></i>Pro Tips</h6>
                    <ul class="mb-0 ps-3 text-muted small" style="line-height: 1.6;">
                        <li>Keep <strong>Title</strong> under 60 characters for best visibility.</li>
                        <li>Keep <strong>Description</strong> between 150-160 characters.</li>
                        <li>Use relevant keywords in the beginning of your title.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Live Preview & Character Counter Logic
    function updatePreview() {
        // Elements
        const titleInput = document.getElementById('inputTitle');
        const descInput = document.getElementById('inputDesc');
        const prevTitle = document.getElementById('previewTitle');
        const prevDesc = document.getElementById('previewDesc');
        
        const titleCount = document.getElementById('titleCount');
        const descCount = document.getElementById('descCount');

        // Logic for Title
        let titleVal = titleInput.value;
        prevTitle.innerText = titleVal ? titleVal : 'Your Page Title Goes Here';
        
        // Count Title
        titleCount.innerText = titleVal.length + '/60 characters';
        if(titleVal.length > 60) {
            titleCount.className = 'char-count count-error';
        } else if(titleVal.length > 50) {
            titleCount.className = 'char-count count-warn';
        } else {
            titleCount.className = 'char-count count-ok';
        }

        // Logic for Description
        let descVal = descInput.value;
        // Truncate for preview visually if too long (simulate Google)
        if (descVal.length > 160) {
            prevDesc.innerText = descVal.substring(0, 160) + '...';
        } else {
            prevDesc.innerText = descVal ? descVal : 'This is how your page description will look in search results...';
        }

        // Count Description
        descCount.innerText = descVal.length + '/160 characters';
        if(descVal.length > 160) {
            descCount.className = 'char-count count-error';
        } else if(descVal.length > 140) {
            descCount.className = 'char-count count-warn';
        } else {
            descCount.className = 'char-count count-ok';
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>

@endsection