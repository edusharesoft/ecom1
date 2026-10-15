<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reset Your Password — {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#eef2f7;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#eef2f7;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.09);">

  {{-- ══ TOP ACCENT BAR ══ --}}
  <tr><td style="background:#4f46e5;height:5px;font-size:0;">&nbsp;</td></tr>

  {{-- ══ HEADER ══ --}}
  <tr>
    <td style="padding:36px 48px 28px;border-bottom:1px solid #f0f0f5;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:1.5px;color:#6366f1;text-transform:uppercase;">Account Security</p>
            <h1 style="margin:0;font-size:26px;font-weight:800;color:#1e1b4b;">Password Reset Request</h1>
          </td>
          <td align="right" valign="top">
            <div style="background:#f0f0ff;border:1px solid #c7d2fe;border-radius:50%;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;text-align:center;line-height:52px;">
              <span style="font-size:24px;">🔐</span>
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ ALERT BANNER ══ --}}
  <tr>
    <td style="padding:0 48px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf5ff;border-left:4px solid #7c3aed;border-radius:0 8px 8px 0;margin:24px 0;">
        <tr>
          <td style="padding:14px 20px;">
            <p style="margin:0;font-size:14px;font-weight:700;color:#1e1b4b;">Hello, {{ $userName }}!</p>
            <p style="margin:4px 0 0;font-size:13px;color:#6b7280;line-height:1.6;">
              We received a request to reset the password for your account
              (<strong style="color:#4f46e5;">{{ $userEmail }}</strong>).
              If this was you, click the button below to set a new password.
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ CTA BUTTON ══ --}}
  <tr>
    <td style="padding:8px 48px 32px;text-align:center;">
      <a href="{{ $resetUrl }}"
         style="display:inline-block;background:#4f46e5;color:#ffffff;padding:15px 44px;border-radius:8px;text-decoration:none;font-size:15px;font-weight:700;letter-spacing:0.5px;">
        Reset My Password →
      </a>
      <p style="margin:14px 0 0;font-size:12px;color:#9ca3af;">This link will expire in <strong>60 minutes</strong>.</p>
    </td>
  </tr>

  {{-- ══ SECURITY NOTICE ══ --}}
  <tr>
    <td style="padding:0 48px 28px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;">
        <tr>
          <td style="padding:14px 18px;">
            <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#92400e;letter-spacing:1px;text-transform:uppercase;">⚠️ Didn't request this?</p>
            <p style="margin:0;font-size:13px;color:#78350f;line-height:1.6;">
              If you did not request a password reset, no action is needed — your password will remain unchanged.
              However, if you believe your account may be at risk, please contact our support team immediately.
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ FALLBACK LINK ══ --}}
  <tr>
    <td style="padding:0 48px 32px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
        <tr>
          <td style="padding:14px 18px;">
            <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:1px;text-transform:uppercase;">Having trouble with the button?</p>
            <p style="margin:0;font-size:12px;color:#6b7280;line-height:1.6;">
              Copy and paste the link below into your browser:
            </p>
            <p style="margin:6px 0 0;font-size:12px;color:#4f46e5;word-break:break-all;">{{ $resetUrl }}</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ FOOTER ══ --}}
  <tr>
    <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:20px 48px;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              <strong style="color:#6b7280;">{{ config('app.name') }}</strong> — Automated Security Notification
            </p>
          </td>
          <td align="right">
            <p style="margin:0;font-size:11px;color:#d1d5db;">{{ now()->format('d M Y') }}</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ BOTTOM ACCENT BAR ══ --}}
  <tr><td style="background:#4f46e5;height:3px;font-size:0;">&nbsp;</td></tr>

</table>
</td></tr>
</table>

</body>
</html>
