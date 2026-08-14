<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;background:#f4f7fc;font-family:Arial, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
    <tr>
      <td align="center">
        <table width="480" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.08);">
          <tr>
            <td style="background:#0d6efd;padding:24px;text-align:center;">
              <h2 style="color:#ffffff;margin:0;">SMH Laboratory</h2>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              <p style="font-size:15px;color:#333;">Hi {{ $name }},</p>
              <p style="font-size:15px;color:#333;">
                We received a request to reset your password. Use the code below to continue.
                This code expires in <strong>10 minutes</strong>.
              </p>
              <div style="text-align:center;margin:30px 0;">
                <span style="display:inline-block;font-size:32px;letter-spacing:8px;font-weight:800;color:#0d6efd;background:#f0f6ff;padding:16px 24px;border-radius:10px;">
                  {{ $otp }}
                </span>
              </div>
              <p style="font-size:13px;color:#888;">
                If you didn't request this, you can safely ignore this email — your password will not be changed.
              </p>
            </td>
          </tr>
          <tr>
            <td style="background:#f4f7fc;padding:16px;text-align:center;">
              <p style="font-size:12px;color:#999;margin:0;">&copy; 2026 SMH Laboratory System. All rights reserved.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>