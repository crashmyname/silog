<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Masuk · LogiBoard</title>
<meta name="csrf-token" content="<?= (new \Bpjs\Framework\Core\Request())->csrfToken() ?>" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<style>
:root{
  --bg:#f4f6fb; --panel:#ffffff; --line:#e6e9ef; --line-soft:#eef1f6;
  --text:#0f172a; --text-2:#4b5568; --text-3:#8b95a8;
  --accent:#2563eb; --accent-2:#7c3aed; --accent-soft:#eff4ff;
  --green:#059669; --green-soft:#ecfdf5;
  --red:#dc2626; --red-soft:#fef2f2;
  --shadow:0 20px 60px rgba(15,23,42,.10);
}
*{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%}
body{
  font-family:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;
  font-size:14px; line-height:1.5;
  background:var(--bg); color:var(--text);
  -webkit-font-smoothing:antialiased;
  min-height:100vh; overflow-x:hidden;
}

/* SPLIT LAYOUT */
.wrap{display:grid;grid-template-columns:1fr 1fr;min-height:100vh}
@media(max-width:900px){
  .wrap{grid-template-columns:1fr}
  .left{display:none}
}

/* LEFT — BRAND */
.left{
  position:relative;overflow:hidden;
  padding:56px 60px;
  display:flex;flex-direction:column;justify-content:space-between;
  color:#fff;
  background:
    radial-gradient(800px 600px at 20% 0%, rgba(59,130,246,.35), transparent 60%),
    radial-gradient(700px 500px at 90% 100%, rgba(124,58,237,.35), transparent 60%),
    linear-gradient(135deg, #1d4ed8, #6d28d9);
}
.left::after{
  content:"";position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px);
  background-size:44px 44px;
  -webkit-mask-image:radial-gradient(circle at 40% 30%, #000, transparent 75%);
          mask-image:radial-gradient(circle at 40% 30%, #000, transparent 75%);
  pointer-events:none;
}
.left-inner{position:relative;z-index:1}
.brand{display:flex;align-items:center;gap:13px}
.brand .mark{
  width:46px;height:46px;border-radius:13px;flex:none;
  display:grid;place-items:center;color:#fff;font-size:19px;
  background:rgba(255,255,255,.15);
  border:1px solid rgba(255,255,255,.25);
  backdrop-filter:blur(10px);
}
.brand b{font-size:17px;font-weight:800;letter-spacing:-.3px;display:block}
.brand small{font-size:10.5px;letter-spacing:1.4px;text-transform:uppercase;opacity:.75;font-weight:600}
.left-content{max-width:480px;margin:auto 0;padding:40px 0}
.left-content h1{
  font-size:clamp(28px,3.4vw,42px);
  font-weight:800;letter-spacing:-1.2px;line-height:1.1;margin-bottom:18px;
}
.left-content h1 span{
  background:linear-gradient(90deg,#93c5fd,#c4b5fd);
  -webkit-background-clip:text;background-clip:text;color:transparent;
}
.left-content p{font-size:15.5px;line-height:1.7;opacity:.85;margin-bottom:34px}
.feature-list{display:flex;flex-direction:column;gap:14px}
.feature{
  display:flex;align-items:center;gap:14px;
  padding:14px 16px;border-radius:12px;
  background:rgba(255,255,255,.08);
  border:1px solid rgba(255,255,255,.12);
  backdrop-filter:blur(10px);
  transition:transform .2s, background .2s;
}
.feature:hover{transform:translateX(4px);background:rgba(255,255,255,.12)}
.feature .fi{
  width:38px;height:38px;border-radius:10px;flex:none;
  display:grid;place-items:center;font-size:15px;
  background:rgba(255,255,255,.15);
}
.feature b{display:block;font-size:13.5px;font-weight:700}
.feature small{font-size:12px;opacity:.75}
.left-footer{position:relative;z-index:1;font-size:12px;opacity:.7;letter-spacing:.3px}

/* RIGHT — FORM */
.right{display:flex;align-items:center;justify-content:center;padding:40px 28px;background:var(--bg)}
.form-card{
  width:100%;max-width:420px;
  background:var(--panel);border:1px solid var(--line);
  border-radius:20px;padding:40px 36px;
  box-shadow:var(--shadow);
  animation:rise .6s cubic-bezier(.22,1,.36,1) both;
}
@keyframes rise{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}

.form-brand{display:none;align-items:center;gap:12px;margin-bottom:26px}
@media(max-width:900px){.form-brand{display:flex}}
.form-brand .mark{
  width:40px;height:40px;border-radius:11px;flex:none;
  display:grid;place-items:center;color:#fff;font-size:17px;
  background:linear-gradient(135deg,#3b82f6,#8b5cf6);
}
.form-brand b{font-size:15px;font-weight:800}
.form-brand small{display:block;font-size:10.5px;color:var(--text-3);letter-spacing:.5px;text-transform:uppercase}

.form-header{margin-bottom:28px}
.form-header h2{font-size:24px;font-weight:800;letter-spacing:-.6px;margin-bottom:6px}
.form-header p{font-size:13.5px;color:var(--text-2);line-height:1.55}

.field{margin-bottom:16px;position:relative}
.field label{display:block;font-size:12.5px;font-weight:600;color:var(--text-2);margin-bottom:7px}
.input-wrap{position:relative}
.input-wrap i.pre{
  position:absolute;left:14px;top:50%;transform:translateY(-50%);
  color:var(--text-3);font-size:14px;pointer-events:none;transition:color .2s;
}
.input{
  width:100%;padding:12px 14px 12px 42px;
  font-size:14px;font-family:inherit;
  background:#fbfcfe;color:var(--text);
  border:1px solid var(--line);border-radius:10px;
  transition:border-color .18s, box-shadow .18s, background .18s;
}
.input:focus{
  outline:none;border-color:var(--accent);background:#fff;
  box-shadow:0 0 0 3px var(--accent-soft);
}
.input-wrap:focus-within i.pre{color:var(--accent)}

.toggle-pass{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:transparent;border:none;cursor:pointer;
  color:var(--text-3);font-size:14px;padding:6px;border-radius:6px;
  transition:color .15s, background .15s;
}
.toggle-pass:hover{color:var(--text);background:var(--line-soft)}

.error-box{
  display:none;margin-bottom:16px;padding:11px 14px;
  background:var(--red-soft);color:var(--red);
  border:1px solid rgba(220,38,38,.2);
  border-radius:9px;font-size:12.5px;line-height:1.55;
}
.error-box.show{display:block;animation:shake .35s}
@keyframes shake{
  0%,100%{transform:translateX(0)}
  25%{transform:translateX(-4px)}
  75%{transform:translateX(4px)}
}

.btn-login{
  width:100%;padding:13px 20px;
  font-family:inherit;font-size:14px;font-weight:700;
  color:#fff;border:none;border-radius:10px;cursor:pointer;
  background:linear-gradient(135deg,#2563eb,#7c3aed);
  box-shadow:0 8px 24px rgba(37,99,235,.28);
  transition:transform .2s, box-shadow .2s, opacity .2s;
  display:flex;align-items:center;justify-content:center;gap:9px;
  margin-top:8px;
}
.btn-login:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 12px 30px rgba(37,99,235,.36)}
.btn-login:disabled{opacity:.65;cursor:not-allowed;transform:none}

.hint{
  margin-top:22px;padding:12px 14px;
  font-size:12px;line-height:1.6;color:var(--text-2);
  background:var(--accent-soft);
  border:1px dashed rgba(37,99,235,.35);
  border-radius:9px;
}
.hint b{color:var(--text);display:block;margin-bottom:4px}
.hint code{
  font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
  font-size:11.5px;padding:2px 6px;border-radius:5px;
  background:#fff;border:1px solid var(--line);color:var(--accent);
}
.form-footer{margin-top:24px;text-align:center;font-size:11.5px;color:var(--text-3)}
</style>
</head>
<body>

<div class="wrap">

  <aside class="left">
    <div class="brand">
      <div class="mark"><i class="fa-solid fa-truck-fast"></i></div>
      <div>
        <b>LogiBoard</b>
        <small>Admin Panel</small>
      </div>
    </div>

    <div class="left-inner left-content">
      <h1>Kelola logistik dalam <span>satu papan</span>.</h1>
      <p>Masuk untuk mengelola dokumen, kategori, dan informasi operasional logistik dari satu tempat.</p>

      <div class="feature-list">
        <div class="feature">
          <div class="fi"><i class="fa-solid fa-file-pdf"></i></div>
          <div><b>Manajemen Dokumen</b><small>Upload & kelola arsip PDF</small></div>
        </div>
        <div class="feature">
          <div class="fi"><i class="fa-solid fa-tags"></i></div>
          <div><b>Kategori Fleksibel</b><small>Kelompokkan dokumen sesuai kebutuhan</small></div>
        </div>
        <div class="feature">
          <div class="fi"><i class="fa-solid fa-shield-halved"></i></div>
          <div><b>Aman & Terkontrol</b><small>Akses khusus admin terverifikasi</small></div>
        </div>
      </div>
    </div>

    <div class="left-footer">
      &copy; <?= date('Y') ?> Divisi Logistik · LogiBoard v1.0
    </div>
  </aside>

  <main class="right">
    <div class="form-card">

      <div class="form-brand">
        <div class="mark"><i class="fa-solid fa-truck-fast"></i></div>
        <div><b>LogiBoard</b><small>Admin Panel</small></div>
      </div>

      <div class="form-header">
        <h2>Selamat datang kembali</h2>
        <p>Masuk dengan akun admin Anda untuk melanjutkan.</p>
      </div>

      <form id="loginForm" autocomplete="on" novalidate>
        <?= (new \Bpjs\Framework\Core\Request())->csrfField() ?>

        <div class="error-box" id="errorBox"></div>

        <div class="field">
          <label for="username">Username</label>
          <div class="input-wrap">
            <i class="fa-solid fa-user pre"></i>
            <input type="text" id="username" name="username"
                   class="input" placeholder="Masukkan username"
                   autocomplete="username" required autofocus />
          </div>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <div class="input-wrap">
            <i class="fa-solid fa-lock pre"></i>
            <input type="password" id="password" name="password"
                   class="input" placeholder="Masukkan password"
                   autocomplete="current-password" required />
            <button type="button" class="toggle-pass" id="togglePass" tabindex="-1">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login" id="loginBtn">
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
          <span>Masuk</span>
        </button>
      </form>

      <div class="hint">
        <b><i class="fa-solid fa-circle-info"></i> Akun demo</b>
        Username: <code>admin</code> · Password: <code>admin123</code>
      </div>

      <div class="form-footer">
        &copy; <?= date('Y') ?> Divisi Logistik
      </div>
    </div>
  </main>

</div>

<script>
/* ============================================================
   BASE PATH DETECTION
   ============================================================ */
const BASE = (function () {
  const path = window.location.pathname;
  const idx  = path.lastIndexOf('/');
  if (idx === -1) return '';
  let base = path.substring(0, idx);
  if (base.endsWith('/index.php')) base = base.replace(/\/index\.php$/, '');
  return base;
})();

const LOGIN_URL = BASE + '/auth/login';
const ADMIN_URL = BASE + '/admin';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

console.log('[Login] BASE:', BASE);
console.log('[Login] LOGIN_URL:', LOGIN_URL);
console.log('[Login] ADMIN_URL:', ADMIN_URL);

const form  = document.getElementById('loginForm');
const btn   = document.getElementById('loginBtn');
const errBx = document.getElementById('errorBox');

/* ============================================================
   TOGGLE PASSWORD
   ============================================================ */
document.getElementById('togglePass').addEventListener('click', function () {
  const inp = document.getElementById('password');
  const ic  = this.querySelector('i');
  if (inp.type === 'password') {
    inp.type = 'text';
    ic.className = 'fa-solid fa-eye-slash';
  } else {
    inp.type = 'password';
    ic.className = 'fa-solid fa-eye';
  }
});

/* ============================================================
   SUBMIT LOGIN
   ============================================================ */
form.addEventListener('submit', async function (e) {
  e.preventDefault();
  errBx.classList.remove('show');
  errBx.textContent = '';

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;

  if (!username) { showError('Username wajib diisi.'); return; }
  if (!password) { showError('Password wajib diisi.'); return; }

  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Memeriksa…</span>';

  let redirected = false;

  try {
    const res = await fetch(LOGIN_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json, text/html',
      },
      body: new URLSearchParams({ username, password }),
      credentials: 'same-origin',
    });

    console.log('[Login] status:', res.status, 'redirected:', res.redirected, 'url:', res.url);

    /* Coba parse JSON kalau content-type JSON */
    let json = null;
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      try { json = await res.json(); } catch (_) {}
    }
    console.log('[Login] response:', json);

    /* ==================================================
       GAGAL
       ================================================== */
    if (!res.ok) {
      const msg = json?.errors
        ? Object.values(json.errors)[0]
        : (json?.message || `Login gagal (HTTP ${res.status}).`);
      showError(msg);
      return;
    }

    /* ==================================================
       SUKSES — semua jalur di bawah ini akan redirect
       ================================================== */

    /* Prioritas 1: server kasih URL redirect eksplisit */
    const target =
      json?.redirect ||
      json?.url ||
      json?.location ||
      (res.redirected ? res.url : null) ||
      ADMIN_URL;

    console.log('[Login] Redirecting to:', target);

    btn.innerHTML = '<i class="fa-solid fa-circle-check"></i><span>Berhasil! Mengalihkan…</span>';
    redirected = true;

    // Delay dikit biar user lihat konfirmasi
    setTimeout(() => {
      window.location.href = target;
    }, 400);

  } catch (err) {
    console.error('[Login] error:', err);
    showError('Terjadi kesalahan jaringan. Coba lagi.');
  } finally {
    // Jangan reset tombol kalau sedang redirect
    if (!redirected) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-arrow-right-to-bracket"></i><span>Masuk</span>';
    }
  }
});

function showError(msg) {
  errBx.textContent = msg;
  errBx.classList.add('show');
  errBx.style.animation = 'none';
  void errBx.offsetWidth;
  errBx.style.animation = 'shake .35s';
}
</script>
</body>
</html>