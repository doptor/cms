{{-- resources/views/emails/welcome.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to RyaanCMS</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0f;color:#e8e8f0;padding:20px}
  .container{max-width:580px;margin:0 auto;background:#111118;border:1px solid #222232;border-radius:20px;overflow:hidden}
  .header{background:linear-gradient(135deg,#6c63ff,#00d4aa);padding:40px 40px 50px;text-align:center}
  .logo{display:inline-flex;align-items:center;gap:10px;margin-bottom:24px}
  .logo-icon{width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px}
  .logo-text{font-size:22px;font-weight:800;color:white;letter-spacing:-0.5px}
  .header h1{color:white;font-size:28px;font-weight:800;margin-bottom:8px}
  .header p{color:rgba(255,255,255,0.8);font-size:15px}
  .body{padding:40px}
  .greeting{font-size:18px;font-weight:600;color:#ffffff;margin-bottom:16px}
  .text{font-size:14px;color:#9999bb;line-height:1.7;margin-bottom:20px}
  .btn{display:block;text-align:center;background:linear-gradient(135deg,#6c63ff,#8b5cf6);color:white;text-decoration:none;padding:16px 32px;border-radius:12px;font-size:15px;font-weight:700;margin:28px 0}
  .features{background:#1a1a24;border:1px solid #222232;border-radius:12px;padding:20px;margin:24px 0}
  .feature{display:flex;align-items:flex-start;gap:12px;margin-bottom:16px}
  .feature:last-child{margin-bottom:0}
  .feature-icon{font-size:20px;flex-shrink:0}
  .feature-text{font-size:13px;color:#9999bb;line-height:1.5}
  .feature-title{color:#ffffff;font-weight:600;display:block;margin-bottom:2px}
  .footer{padding:24px 40px;border-top:1px solid #222232;text-align:center}
  .footer p{font-size:12px;color:#555577}
  .footer a{color:#6c63ff;text-decoration:none}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="logo">
      <div class="logo-icon">⚡</div>
      <span class="logo-text">RyaanCMS</span>
    </div>
    <h1>Welcome aboard! 🎉</h1>
    <p>Your AI-powered Laravel CMS is ready</p>
  </div>
  <div class="body">
    <p class="greeting">Hi {{ $user->name }},</p>
    <p class="text">
      You're now part of RyaanCMS — the world's first free, open-source AI-powered Laravel CMS that runs on shared hosting.
      Start building complex applications just by describing what you want.
    </p>

    <a href="{{ route('dashboard') }}" class="btn">🚀 Go to Your Dashboard →</a>

    <div class="features">
      <div class="feature">
        <span class="feature-icon">🤖</span>
        <div class="feature-text">
          <span class="feature-title">Add your AI API keys</span>
          Go to Settings → API Keys to add your Claude or DeepSeek key. Start with DeepSeek — it gives 5M free tokens on signup.
        </div>
      </div>
      <div class="feature">
        <span class="feature-icon">📁</span>
        <div class="feature-text">
          <span class="feature-title">Create your first project</span>
          Click "New Project", choose a type, and let AI build it for you. Takes less than a minute.
        </div>
      </div>
      <div class="feature">
        <span class="feature-icon">🛒</span>
        <div class="feature-text">
          <span class="feature-title">Browse the Marketplace</span>
          Install ready-made apps and templates into any project with one click.
        </div>
      </div>
    </div>

    <p class="text" style="font-size:13px">
      Need help? Check our <a href="#" style="color:#6c63ff">documentation</a> or <a href="https://github.com" style="color:#6c63ff">GitHub issues</a>.
    </p>
  </div>
  <div class="footer">
    <p>RyaanCMS · Free & Open Source · MIT License</p>
    <p style="margin-top:6px"><a href="#">Unsubscribe</a> · <a href="#">Privacy Policy</a></p>
  </div>
</div>
</body>
</html>
