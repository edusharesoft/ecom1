    body.delivery-login-page {
        background: #fafafa !important;
    }
    .login-screen {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .login-panel {
        width: 100%;
        max-width: 400px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 2rem 1.75rem 1.75rem;
        box-shadow:
            0 1px 2px rgba(15, 23, 42, 0.04),
            0 8px 24px rgba(15, 23, 42, 0.06);
    }
    @media (min-width: 480px) {
        .login-panel {
            padding: 2.25rem 2rem 2rem;
            border-radius: 14px;
        }
    }
    .login-brand {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
    }
    .login-brand svg {
        width: 26px;
        height: 26px;
        color: #334155;
    }
    .login-panel h1 {
        margin: 0 0 0.35rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .login-lead {
        margin: 0 0 1.5rem;
        font-size: 0.875rem;
        line-height: 1.55;
        color: #64748b;
    }
    .login-field { margin-bottom: 1rem; }
    .login-field label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.35rem;
    }
    .login-input-wrap { position: relative; }
    .login-input-wrap .icon-left {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #9ca3af;
        pointer-events: none;
    }
    .login-input-wrap input {
        width: 100%;
        padding: 11px 12px 11px 40px;
        min-height: 46px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 16px;
        background: #fff;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .login-input-wrap.has-toggle input { padding-right: 44px; }
    .login-input-wrap input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .login-toggle-pw {
        position: absolute;
        right: 2px;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        border-radius: 6px;
        cursor: pointer;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-toggle-pw:hover { color: #2563eb; background: #f3f4f6; }
    .login-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 1.25rem;
        font-size: 0.875rem;
        color: #4b5563;
        cursor: pointer;
        user-select: none;
    }
    .login-remember input {
        width: 16px;
        height: 16px;
        accent-color: #2563eb;
    }
    .login-submit {
        width: 100%;
        min-height: 46px;
        border: none;
        border-radius: 8px;
        background: #2563eb;
        color: #fff;
        font-weight: 600;
        font-size: 0.9375rem;
        cursor: pointer;
        transition: background 0.15s;
    }
    .login-submit:hover { background: #1d4ed8; }
    .login-submit:active { background: #1e40af; }
    .login-foot {
        margin: 1.25rem 0 0;
        text-align: center;
        font-size: 0.8125rem;
        color: #9ca3af;
        line-height: 1.45;
    }
    .auth-back-link {
        display: inline-block;
        margin-top: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #2563eb;
        text-decoration: none;
    }
    .auth-back-link:hover { text-decoration: underline; }
