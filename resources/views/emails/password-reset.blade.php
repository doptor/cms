{{-- resources/views/emails/password-reset.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Reset Your Password</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0f;color:#e8e8f0;padding:20px}
  .container{max-width:560px;margin:0 auto;background:#111118;border:1px solid #222232;border-radius:20px;overflow:hidden}
  .header{background:#1a1a24;border-bottom:1px solid #222232;padding:32px 40px;display:flex;align-items:center;gap:12px}
  .logo-icon{width:36px;height:36px;background:linear-gradient(135deg,#6c63ff,#00d4aa);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px}
  .logo-text{font-size:18px;font-weight:800;color:white}
  .logo-text span{color:#00d4aa}
  .body{padding:40px}
  .icon{width:64px;height:64px;background:rgba(108,99,255,0.15);border:1px solid rgba(108,99,255,0.3);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 24px}
  h1{font-size:22px;font-weight:800;color:#ffffff;text-align:center;margin-bottom:12px}
  .text{font-size:14px;color:#9999bb;line-height:1.7;text-align:center;margin-bottom:28px}
  .btn{display:block;text-align:center;background:linear-gradient(135deg,#6c63ff,#8b5cf6);color:white;text-decoration:none;padding:16px 32px;border-radius:12px;font-size:15px;font-weight:700}
  .url-box{background:#1a1a24;border:1px solid #222232;border-radius:10px;padding:12px 16px;margin:20px 0;font-size:11px;font-family:monospace;color:#6c63ff;word-break:break-all}
  .warning{font-size:12px;color:#666688;text-align:center;margin-top:20px}
  .footer{padding:20px 40px;border-top:1px solid #222232;text-align:center;font-size:12px;color:#555577}
  .footer a{color:#6c63ff;text-decoration:none}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="logo-icon">⚡</div>
    <div class="logo-text">Ryaan<span>CMS</span></div>
  </div>
  <div class="body">
    <div class="icon">🔑</div>
    <h1>Reset Your Password</h1>
    <p class="text">We received a request to reset the password for your RyaanCMS account. Click the button below to choose a new password.</p>
    <a href="{{ $resetUrl }}" class="btn">Reset Password →</a>
    <p class="text" style="font-size:12px;margin-top:20px">Or copy this link:</p>
    <div class="url-box">{{ $resetUrl }}</div>
    <p class="warning">⏰ This link expires in 60 minutes. If you didn't request a password reset, you can safely ignore this email.</p>
  </div>
  <div class="footer">
    <p>RyaanCMS · <a href="#">Privacy Policy</a> · <a href="#">Security</a></p>
  </div>
</div>
</body>
</html>

{{-- ================================================================ --}}
{{-- resources/views/emails/deploy-success.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Deployment Successful</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0f;padding:20px}
  .container{max-width:560px;margin:0 auto;background:#111118;border:1px solid #222232;border-radius:20px;overflow:hidden}
  .header{background:linear-gradient(135deg,#00d4aa,#00b894);padding:36px 40px;text-align:center}
  .header h1{color:#0a0a0f;font-size:24px;font-weight:900;margin-bottom:6px}
  .header p{color:rgba(0,0,0,0.6);font-size:14px}
  .body{padding:40px}
  .stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:20px 0}
  .stat{background:#1a1a24;border:1px solid #222232;border-radius:12px;padding:16px;text-align:center}
  .stat-val{font-size:20px;font-weight:800;color:#ffffff;font-family:monospace}
  .stat-label{font-size:11px;color:#666688;margin-top:4px}
  .url-btn{display:block;text-align:center;background:#1a1a24;border:2px solid #00d4aa;color:#00d4aa;text-decoration:none;padding:14px 24px;border-radius:12px;font-size:14px;font-weight:700;margin:24px 0}
  .text{font-size:13px;color:#9999bb;line-height:1.6;margin-bottom:16px}
  .footer{padding:20px 40px;border-top:1px solid #222232;text-align:center;font-size:12px;color:#555577}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div style="font-size:48px;margin-bottom:12px">🚀</div>
    <h1>Deployed Successfully!</h1>
    <p>{{ $project->name }} is now live</p>
  </div>
  <div class="body">
    <div class="stat-grid">
      <div class="stat"><div class="stat-val">{{ $filesUploaded }}</div><div class="stat-label">Files Uploaded</div></div>
      <div class="stat"><div class="stat-val">{{ $duration }}s</div><div class="stat-label">Deploy Time</div></div>
    </div>
    <p class="text">Your project <strong style="color:#fff">{{ $project->name }}</strong> has been successfully deployed to your shared hosting. The site is now live and accessible.</p>
    <a href="http://{{ $project->domain ?? $project->ftp_host }}" class="url-btn">🌐 Visit Your Site →</a>
    <p class="text" style="font-size:12px">Deployed at: {{ now()->format('D, M j Y — H:i T') }}</p>
  </div>
  <div class="footer">
    <p>RyaanCMS · Your AI-powered Laravel CMS</p>
  </div>
</div>
</body>
</html>
