@extends('backEnd.layouts.master')
@section('title', 'Email Settings')

@section('css')
<style>
    /* 1. PROFESSIONAL CARD CONTAINER */
    .studio-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-top: 20px;
    }

    /* 2. HEADER SECTION */
    .card-header-custom {
        background: #fff;
        padding: 25px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .header-icon-box {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0284c7;
        font-size: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }

    .header-title h5 {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 4px 0;
    }
    .header-title small {
        color: #64748b;
        font-size: 13px;
    }

    /* 3. FORM ELEMENTS */
    .form-section {
        padding: 30px;
    }

    .form-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 8px;
        letter-spacing: 0.6px;
    }

    .form-control, .form-select {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 14px;
        color: #334155;
        transition: all 0.2s;
    }

    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* 4. BUTTONS */
    .btn-save {
        background: #0f172a;
        color: #fff;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 1px;
        border: none;
        transition: all 0.3s;
    }
    .btn-save:hover {
        background: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        color: #fff;
    }

    /* 5. ALERTS */
    .alert-custom {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark m-0">System Settings</h4>
            <span class="text-muted small">Configure application parameters</span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            @if(session('success'))
                <div class="alert alert-success alert-custom d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-check-circle me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <div class="studio-card">
                
                <div class="card-header-custom">
                    <div class="header-icon-box">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div class="header-title">
                        <h5>Email Configuration (SMTP)</h5>
                        <small>Set up mail server details to enable system emails</small>
                    </div>
                </div>

                <div class="form-section">
                    <form action="{{ route('email_setting.update') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Mailer Driver <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-paper-plane"></i></span>
                                    <input type="text" name="MAIL_MAILER" class="form-control border-start-0" 
                                           value="{{ $mail['MAIL_MAILER'] ?? '' }}" placeholder="e.g. smtp" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mail Host <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-server"></i></span>
                                    <input type="text" name="MAIL_HOST" class="form-control border-start-0" 
                                           value="{{ $mail['MAIL_HOST'] ?? '' }}" placeholder="e.g. smtp.mailtrap.io" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mail Port <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-plug"></i></span>
                                    <select name="MAIL_PORT" class="form-select border-start-0" required>
                                        <option value="465" {{ ($mail['MAIL_PORT'] ?? '') == '465' ? 'selected' : '' }}>465 (SSL)</option>
                                        <option value="587" {{ ($mail['MAIL_PORT'] ?? '') == '587' ? 'selected' : '' }}>587 (TLS)</option>
                                        <option value="2525" {{ ($mail['MAIL_PORT'] ?? '') == '2525' ? 'selected' : '' }}>2525 (Alternative)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Encryption Protocol <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                                    <select name="MAIL_ENCRYPTION" class="form-select border-start-0" required>
                                        <option value="ssl" {{ ($mail['MAIL_ENCRYPTION'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL (Secure Sockets Layer)</option>
                                        <option value="tls" {{ ($mail['MAIL_ENCRYPTION'] ?? '') == 'tls' ? 'selected' : '' }}>TLS (Transport Layer Security)</option>
                                        <option value="null" {{ ($mail['MAIL_ENCRYPTION'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SMTP Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text" name="MAIL_USERNAME" class="form-control border-start-0" 
                                           value="{{ $mail['MAIL_USERNAME'] ?? '' }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SMTP Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-key"></i></span>
                                    <input type="password" name="MAIL_PASSWORD" class="form-control border-start-0" 
                                           value="{{ $mail['MAIL_PASSWORD'] ?? '' }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Sender Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-at"></i></span>
                                    <input type="email" name="MAIL_FROM_ADDRESS" class="form-control border-start-0" 
                                           value="{{ $mail['MAIL_FROM_ADDRESS'] ?? '' }}" placeholder="no-reply@domain.com" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Sender Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="MAIL_FROM_NAME" class="form-control border-start-0" 
                                           value="{{ $mail['MAIL_FROM_NAME'] ?? '' }}" placeholder="e.g. System Admin" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 text-end border-top pt-4">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save me-2"></i> Save Configuration
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection