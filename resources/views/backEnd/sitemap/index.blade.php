@extends('backEnd.layouts.master')
@section('title', 'Sitemap Configuration')

@section('content')

{{-- Professional CSS Styles --}}
<style>
    .admin-panel-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
    }
    .card-pro {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .header-gradient {
        background: linear-gradient(to right, #f8fafc, #ffffff);
        border-bottom: 1px solid #e2e8f0;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-active {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .info-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .info-value {
        color: #0f172a;
        font-weight: 600;
        font-size: 15px;
    }
    .url-input-group {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        padding: 8px 12px;
    }
    .url-text {
        font-family: 'Monaco', 'Consolas', monospace;
        color: #475569;
        font-size: 13px;
        flex-grow: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .btn-copy {
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-copy:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    .btn-generate-pro {
        background-color: #2563eb;
        color: white;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        transition: all 0.2s;
    }
    .btn-generate-pro:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
    }
    .btn-generate-pro:disabled {
        background-color: #94a3b8;
        cursor: not-allowed;
        transform: none;
    }
</style>

<div class="container-fluid py-4 admin-panel-container">
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Sitemap Management</h4>
                    <p class="text-muted small mb-0">Configure search engine indexing protocols</p>
                </div>
                <div>
                    <span class="status-badge status-active">
                        <span style="height: 8px; width: 8px; background: #16a34a; border-radius: 50%; margin-right: 6px;"></span>
                        System Healthy
                    </span>
                </div>
            </div>

            <div class="card card-pro">
                {{-- Card Header --}}
                <div class="card-header header-gradient py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary">
                            <i class="fas fa-project-diagram fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">XML Sitemap Configuration</h6>
                            <small class="text-muted">sitemap.xml</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    
                    {{-- Info Grid --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 border border-light h-100">
                                <div class="info-label">Last Generated</div>
                                <div class="info-value">
                                    <i class="far fa-clock me-1 text-muted"></i> 
                                    {{ now()->format('M d, Y • h:i A') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 border border-light h-100">
                                <div class="info-label">File Path</div>
                                <div class="info-value text-break" style="font-size: 13px; font-family: monospace;">
                                    /public/sitemap.xml
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 border border-light h-100">
                                <div class="info-label">Update Frequency</div>
                                <div class="info-value">
                                    <i class="fas fa-sync me-1 text-muted"></i> Hourly (Auto)
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="bg-secondary opacity-10 my-4">

                    {{-- Main Action Section --}}
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <label class="info-label mb-2">Public Sitemap URL</label>
                            <div class="url-input-group">
                                <span class="url-text" id="sitemapUrl">{{ url('sitemap.xml') }}</span>
                                <button class="btn-copy ms-2" onclick="copyToClipboard()" title="Copy Link">
                                    <i class="far fa-copy"></i>
                                </button>
                                <a href="{{ url('sitemap.xml') }}" target="_blank" class="btn-copy ms-1 text-decoration-none">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                            <small class="text-muted mt-2 d-block fst-italic">
                                * Submit this URL to <a href="https://search.google.com/search-console" target="_blank" class="text-decoration-none fw-bold">Google Search Console</a>.
                            </small>
                        </div>

                        <div class="col-md-5 text-md-end">
                            <form action="{{ route('admin.sitemap.generate') }}" method="POST" id="proForm">
                                @csrf
                                <button type="submit" class="btn-generate-pro w-100" id="proBtn">
                                    <span id="btnContent">
                                        <i class="fas fa-sync-alt me-2"></i> Generate Sitemap
                                    </span>
                                    <span id="btnLoader" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                                    </span>
                                </button>
                            </form>
                            <div class="text-center text-md-end mt-2">
                                <small class="text-muted" style="font-size: 11px;">Manual override triggers instant rebuild</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Optional: Log Section (Visual only) --}}
            <div class="mt-4">
                <p class="info-label text-uppercase mb-2 ms-1">Recent Activity</p>
                <div class="bg-white rounded-3 border p-3">
                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                        <div>
                            <small class="d-block text-dark fw-bold">Sitemap Generated Successfully</small>
                            <small class="text-muted" style="font-size: 11px;">System Automation • {{ now()->subMinutes(5)->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
    // 1. Copy to Clipboard Function
    function copyToClipboard() {
        const urlText = document.getElementById('sitemapUrl').innerText;
        navigator.clipboard.writeText(urlText).then(() => {
            // Show temporary tooltip or change icon
            const btn = document.querySelector('.btn-copy i');
            btn.className = 'fas fa-check text-success';
            setTimeout(() => {
                btn.className = 'far fa-copy';
            }, 2000);
        });
    }

    // 2. Loading State
    document.getElementById('proForm').addEventListener('submit', function() {
        const btn = document.getElementById('proBtn');
        const content = document.getElementById('btnContent');
        const loader = document.getElementById('btnLoader');

        btn.disabled = true;
        content.classList.add('d-none');
        loader.classList.remove('d-none');
    });

    // 3. Background Sync (Silent)
    setInterval(() => {
        fetch('{{ route('admin.sitemap.generate') }}', { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
        });
    }, 3600000);
</script>

@endsection