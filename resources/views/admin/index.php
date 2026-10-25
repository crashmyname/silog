<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Panel · LogiBoard</title>
<meta name="csrf-token" content="<?= (new \Bpjs\Framework\Core\Request())->csrfToken() ?>" />
<meta name="user-role" content="<?= auth()->user()->role ?? 'ADMIN' ?>" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<style>
:root{
  --bg:#f7f8fb; --panel:#ffffff; --panel-2:#fbfcfe; --side:#ffffff;
  --line:#e6e9ef; --line-soft:#eef1f6;
  --text:#0f172a; --text-2:#4b5568; --text-3:#8b95a8;
  --accent:#2563eb; --accent-soft:#eff4ff; --accent-line:#c7d6f7;
  --green:#059669; --green-soft:#ecfdf5;
  --amber:#b45309; --amber-soft:#fff8eb;
  --red:#dc2626; --red-soft:#fef2f2;
  --shadow:0 1px 2px rgba(15,23,42,.04), 0 1px 3px rgba(15,23,42,.06);
  --radius:10px; --radius-sm:7px;
}
html[data-theme="dark"]{
  --bg:#0b0f17; --panel:#101623; --panel-2:#0e1420; --side:#0d131e;
  --line:#1e2635; --line-soft:#171e2b;
  --text:#e9eefb; --text-2:#95a1b8; --text-3:#5f6b82;
  --accent:#60a5fa; --accent-soft:rgba(96,165,250,.12); --accent-line:rgba(96,165,250,.3);
  --green:#34d399; --green-soft:rgba(52,211,153,.12);
  --amber:#fbbf24; --amber-soft:rgba(251,191,36,.12);
  --red:#f87171; --red-soft:rgba(248,113,113,.12);
  --shadow:0 1px 2px rgba(0,0,0,.4);
}
*{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%}
body{
  font-family:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;
  font-size:14px; line-height:1.5;
  background:var(--bg); color:var(--text);
  -webkit-font-smoothing:antialiased;
  transition:background .3s,color .3s;
}
::-webkit-scrollbar{width:9px;height:9px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--line);border-radius:99px;border:2px solid var(--bg)}
::-webkit-scrollbar-thumb:hover{background:var(--text-3)}

.shell{display:flex;min-height:100vh}

.side{
  width:252px;flex:none;
  background:var(--side);
  border-right:1px solid var(--line);
  padding:18px 12px;
  display:flex;flex-direction:column;
  position:sticky;top:0;height:100vh;overflow-y:auto;
  transition:transform .25s;z-index:100;
}
.side::-webkit-scrollbar{width:5px}
.side::-webkit-scrollbar-thumb{background:var(--line);border-radius:99px;border:none}

.brand{
  display:flex;align-items:center;gap:11px;
  padding:6px 10px 18px;margin-bottom:6px;
  border-bottom:1px solid var(--line-soft);
}
.brand .mark{
  width:36px;height:36px;border-radius:9px;flex:none;
  display:grid;place-items:center;color:#fff;font-size:15px;
  background:linear-gradient(135deg,#3b82f6,#8b5cf6);
  box-shadow:0 4px 12px rgba(59,130,246,.28);
}
.brand .txt b{display:block;font-size:14px;font-weight:800;letter-spacing:-.2px;color:var(--text)}
.brand .txt small{font-size:10.5px;color:var(--text-3);letter-spacing:.4px;text-transform:uppercase;font-weight:600}

.group-label{
  font-size:10.5px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;
  color:var(--text-3);padding:16px 12px 8px;
}
.nav{display:flex;flex-direction:column;gap:2px}
.nav button, .nav a{
  display:flex;align-items:center;gap:11px;
  padding:9px 12px;border-radius:7px;cursor:pointer;font-family:inherit;
  background:transparent;border:none;
  color:var(--text-2);font-size:13.5px;font-weight:500;
  text-align:left;width:100%; text-decoration:none;
  transition:background .15s,color .15s;
}
.nav button i, .nav a i{font-size:13px;width:16px;text-align:center;opacity:.85;flex:none}
.nav button:hover, .nav a:hover{background:var(--line-soft);color:var(--text)}
.nav button.active{background:var(--accent-soft);color:var(--accent);font-weight:600}
.nav button.active i{opacity:1}
.nav button .cnt, .nav a .cnt{
  margin-left:auto;font-size:11px;font-weight:600;
  padding:1px 7px;border-radius:99px;
  background:var(--line-soft);color:var(--text-3);
  font-variant-numeric:tabular-nums;
}
.nav button.active .cnt{background:var(--panel);color:var(--accent)}
.nav .ext::after{
  content:"\f35d";font-family:"Font Awesome 6 Free";font-weight:900;
  font-size:9px;color:var(--text-3);margin-left:6px;
}

.side-foot{
  margin-top:auto;padding:16px 12px 4px;
  border-top:1px solid var(--line-soft);
  font-size:11.5px;color:var(--text-3);
  display:flex;align-items:center;gap:8px;
}
.side-foot .dot{
  width:6px;height:6px;border-radius:50%;background:var(--green);
  box-shadow:0 0 0 3px var(--green-soft);
}

.main{flex:1;min-width:0;display:flex;flex-direction:column}
.topbar{
  height:60px;padding:0 26px;
  background:var(--panel);
  border-bottom:1px solid var(--line);
  display:flex;align-items:center;gap:16px;
  position:sticky;top:0;z-index:20;
}
.crumb{
  display:flex;align-items:center;gap:9px;
  font-size:13px;color:var(--text-2);flex:1;min-width:0;
}
.crumb i{font-size:10px;color:var(--text-3)}
.crumb b{color:var(--text);font-weight:600;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.actions{display:flex;align-items:center;gap:8px}

.icon-btn{
  width:36px;height:36px;border-radius:8px;cursor:pointer;
  border:1px solid var(--line);background:var(--panel);
  color:var(--text-2);font-size:13px;
  display:grid;place-items:center;
  transition:background .15s,color .15s,border-color .15s;
}
.icon-btn:hover{background:var(--line-soft);color:var(--text)}
.icon-btn.danger:hover{color:var(--red);border-color:var(--red)}

.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:7px;
  padding:8px 14px;font-size:13px;font-weight:600;font-family:inherit;
  border-radius:7px;cursor:pointer;border:1px solid transparent;
  transition:background .15s,border-color .15s,color .15s,opacity .15s;
  white-space:nowrap;text-decoration:none;
}
.btn:disabled{opacity:.5;cursor:not-allowed}
.btn-sm{padding:6px 11px;font-size:12.5px;border-radius:6px}
.btn-primary{background:var(--accent);color:#fff}
.btn-primary:hover:not(:disabled){opacity:.9}
.btn-ghost{background:var(--panel);color:var(--text-2);border-color:var(--line)}
.btn-ghost:hover:not(:disabled){background:var(--line-soft);color:var(--text)}
.btn-danger{background:var(--red-soft);color:var(--red)}
.btn-danger:hover:not(:disabled){opacity:.85}
.btn-icon{padding:6px 8px;background:var(--panel);border:1px solid var(--line);color:var(--text-2)}
.btn-icon:hover{background:var(--line-soft);color:var(--text)}
.btn-icon.danger:hover{color:var(--red);border-color:var(--red);background:var(--red-soft)}

.body{padding:26px;flex:1}

.panel{display:none}
.panel.active{display:block;animation:fade .3s ease both}
@keyframes fade{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}

.page-head{
  margin-bottom:22px;display:flex;align-items:flex-start;justify-content:space-between;
  gap:16px;flex-wrap:wrap;
}
.page-head h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--text);margin-bottom:4px}
.page-head p{font-size:13px;color:var(--text-2);line-height:1.55;max-width:640px}
.page-head .btn{flex:none}

.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:22px}
.stat{
  padding:16px 18px;border-radius:var(--radius);
  background:var(--panel);border:1px solid var(--line);
  box-shadow:var(--shadow);transition:transform .2s,border-color .2s;
}
.stat:hover{transform:translateY(-2px);border-color:var(--accent-line)}
.stat .label{
  font-size:11.5px;font-weight:600;letter-spacing:.4px;text-transform:uppercase;
  color:var(--text-3);display:flex;align-items:center;gap:7px;
}
.stat .label i{color:var(--accent);font-size:11px}
.stat .value{
  font-size:26px;font-weight:800;color:var(--text);
  letter-spacing:-.7px;margin-top:8px;font-variant-numeric:tabular-nums;
}

.card{background:var(--panel);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
.card-head{
  padding:14px 18px;border-bottom:1px solid var(--line-soft);
  display:flex;align-items:center;gap:12px;flex-wrap:wrap;
}
.card-head h3{font-size:14px;font-weight:700;color:var(--text);letter-spacing:-.2px}

.toolbar{
  padding:14px 18px;border-bottom:1px solid var(--line-soft);
  display:flex;align-items:center;gap:12px;flex-wrap:wrap;
}
.search{position:relative;flex:1;min-width:200px;max-width:340px}
.search i{
  position:absolute;left:12px;top:50%;transform:translateY(-50%);
  color:var(--text-3);font-size:12.5px;pointer-events:none;
}
.search input{
  width:100%;padding:8px 12px 8px 34px;
  font-size:13px;font-family:inherit;
  background:var(--panel-2);color:var(--text);
  border:1px solid var(--line);border-radius:7px;
  transition:border-color .15s,background .15s,box-shadow .15s;
}
.search input:focus{
  outline:none;border-color:var(--accent);background:var(--panel);
  box-shadow:0 0 0 3px var(--accent-soft);
}

.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13.5px}
thead th{
  text-align:left;font-weight:600;font-size:11.5px;letter-spacing:.4px;
  text-transform:uppercase;color:var(--text-3);
  padding:11px 18px;background:var(--panel-2);
  border-bottom:1px solid var(--line);white-space:nowrap;
}
tbody td{
  padding:13px 18px;color:var(--text-2);
  border-bottom:1px solid var(--line-soft);vertical-align:middle;
}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--panel-2)}
td .strong{color:var(--text);font-weight:600}
td .muted{color:var(--text-3);font-size:12.5px}
td .actions{display:flex;gap:6px;justify-content:flex-end}
.icon-cell{font-size:15px;color:var(--accent);margin-right:8px;vertical-align:middle}

.tag{
  display:inline-flex;align-items:center;gap:5px;
  font-size:11px;font-weight:600;padding:3px 9px;border-radius:99px;
  background:var(--line-soft);color:var(--text-2);white-space:nowrap;
}
.tag.ok{background:var(--green-soft);color:var(--green)}
.tag.warn{background:var(--amber-soft);color:var(--amber)}
.tag.err{background:var(--red-soft);color:var(--red)}
.tag.info{background:var(--accent-soft);color:var(--accent)}
.tag .dot{width:5px;height:5px;border-radius:50%;background:currentColor}

.swatch{
  display:inline-block;width:15px;height:15px;border-radius:4px;
  vertical-align:middle;margin-right:8px;
  border:1px solid rgba(0,0,0,.08);
}

.empty{padding:52px 20px;text-align:center}
.empty i{font-size:32px;color:var(--text-3);margin-bottom:14px;display:block;opacity:.55}
.empty b{display:block;font-size:14.5px;font-weight:700;color:var(--text);margin-bottom:6px}
.empty p{font-size:12.5px;color:var(--text-3);max-width:380px;margin:0 auto;line-height:1.6}

.form{padding:22px;max-width:720px}
.field{margin-bottom:16px}
.field label{display:block;font-size:12.5px;font-weight:600;color:var(--text-2);margin-bottom:7px;letter-spacing:.1px}
.field label .req{color:var(--red);margin-left:2px}
.input,.select,.textarea{
  width:100%;padding:9px 12px;
  font-size:13.5px;font-family:inherit;
  background:var(--panel);color:var(--text);
  border:1px solid var(--line);border-radius:7px;
  transition:border-color .15s,box-shadow .15s,background .15s;
}
.input:focus,.select:focus,.textarea:focus{
  outline:none;border-color:var(--accent);
  box-shadow:0 0 0 3px var(--accent-soft);
}
.textarea{resize:vertical;min-height:80px;line-height:1.55}
.select{
  cursor:pointer;appearance:none;
  background-image:linear-gradient(45deg,transparent 50%,var(--text-3) 50%),linear-gradient(135deg,var(--text-3) 50%,transparent 50%);
  background-position:calc(100% - 16px) 50%,calc(100% - 11px) 50%;
  background-size:5px 5px,5px 5px;background-repeat:no-repeat;
  padding-right:34px;
}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:600px){.grid-2{grid-template-columns:1fr}}

.file{
  display:block;border:1.5px dashed var(--line);
  border-radius:9px;padding:22px 16px;text-align:center;
  cursor:pointer;transition:border-color .15s,background .15s;
  background:var(--panel-2);
}
.file:hover,.file.drag{border-color:var(--accent);background:var(--accent-soft)}
.file input{display:none}
.file .fi{font-size:22px;color:var(--accent);margin-bottom:8px}
.file .ft{font-size:13px;font-weight:600;color:var(--text);margin-bottom:3px}
.file .fs{font-size:11.5px;color:var(--text-3)}
.file.has-file{border-style:solid;border-color:var(--green);background:var(--green-soft)}
.file.has-file .fi{color:var(--green)}

.icon-grid{display:grid;grid-template-columns:repeat(8,1fr);gap:6px}
.icon-opt{
  aspect-ratio:1;border-radius:6px;cursor:pointer;
  display:grid;place-items:center;font-size:13px;
  background:var(--panel);border:1px solid var(--line);
  color:var(--text-2);transition:all .15s;
}
.icon-opt:hover{border-color:var(--accent);color:var(--accent)}
.icon-opt.sel{background:var(--accent-soft);border-color:var(--accent);color:var(--accent)}
.color-grid{display:flex;gap:8px;flex-wrap:wrap}
.color-opt{
  width:28px;height:28px;border-radius:50%;cursor:pointer;
  border:2px solid transparent;position:relative;
  transition:transform .15s;
}
.color-opt:hover{transform:scale(1.1)}
.color-opt.sel{border-color:var(--text);box-shadow:0 0 0 2px var(--panel),0 0 0 3.5px var(--text-2)}
@media(max-width:600px){.icon-grid{grid-template-columns:repeat(6,1fr)}}

.modal-bg{
  position:fixed;inset:0;z-index:200;
  background:rgba(15,23,42,.5);
  backdrop-filter:blur(3px);
  display:none;align-items:flex-start;justify-content:center;
  padding:40px 20px;overflow-y:auto;
}
.modal-bg.active{display:flex;animation:fade .2s ease both}
.modal{
  background:var(--panel);border:1px solid var(--line);
  border-radius:14px;box-shadow:0 24px 60px rgba(15,23,42,.25);
  width:100%;max-width:580px;margin:auto;
  animation:scale .25s cubic-bezier(.22,1,.36,1) both;
}
@keyframes scale{from{opacity:0;transform:scale(.96) translateY(10px)}to{opacity:1;transform:none}}
.modal-head{
  padding:18px 22px;border-bottom:1px solid var(--line-soft);
  display:flex;align-items:center;justify-content:space-between;gap:12px;
}
.modal-head h3{font-size:15.5px;font-weight:700;color:var(--text);letter-spacing:-.2px}
.modal-head .close{
  width:30px;height:30px;border-radius:7px;cursor:pointer;
  border:1px solid var(--line);background:var(--panel);
  color:var(--text-2);font-size:12px;
  display:grid;place-items:center;
  transition:background .15s,color .15s;
}
.modal-head .close:hover{background:var(--line-soft);color:var(--text)}
.modal-body{padding:20px 22px;max-height:65vh;overflow-y:auto}
.modal-foot{
  padding:14px 22px;border-top:1px solid var(--line-soft);
  display:flex;gap:10px;justify-content:flex-end;
}
.modal-foot .btn{min-width:88px}

.toast{
  position:fixed;bottom:24px;left:50%;transform:translate(-50%,80px);
  padding:12px 18px;border-radius:9px;font-size:13px;font-weight:600;
  background:var(--panel);color:var(--text);
  border:1px solid var(--line);box-shadow:0 14px 36px rgba(15,23,42,.18);
  display:flex;align-items:center;gap:9px;
  opacity:0;transition:all .3s cubic-bezier(.22,1,.36,1);
  z-index:300;pointer-events:none;max-width:90vw;
}
.toast.show{transform:translate(-50%,0);opacity:1}
.toast.ok{border-color:var(--green);color:var(--green)}
.toast.err{border-color:var(--red);color:var(--red)}
.toast.info{border-color:var(--accent);color:var(--accent)}

.cf-bg{
  position:fixed;inset:0;z-index:400;
  background:rgba(15,23,42,.55);
  backdrop-filter:blur(4px);
  display:none;align-items:center;justify-content:center;
  padding:20px;
}
html[data-theme="dark"] .cf-bg{background:rgba(0,0,0,.65)}
.cf-bg.active{display:flex;animation:cfFade .2s ease both}
@keyframes cfFade{from{opacity:0}to{opacity:1}}

.cf{
  background:var(--panel);border:1px solid var(--line);
  border-radius:16px;box-shadow:0 24px 60px rgba(15,23,42,.25);
  width:100%;max-width:420px;
  padding:26px 24px 22px;text-align:center;
  animation:cfScale .28s cubic-bezier(.22,1,.36,1) both;
}
html[data-theme="dark"] .cf{box-shadow:0 24px 60px rgba(0,0,0,.5)}
@keyframes cfScale{from{opacity:0;transform:scale(.94) translateY(12px)}to{opacity:1;transform:none}}
.cf-icon{
  position:relative;width:62px;height:62px;margin:0 auto 16px;border-radius:50%;
  display:grid;place-items:center;font-size:26px;
  background:var(--red-soft);color:var(--red);
  animation:cfIconIn .4s cubic-bezier(.22,1,.36,1) both;
}
.cf-icon.warn{background:var(--amber-soft);color:var(--amber)}
.cf-icon.info{background:var(--accent-soft);color:var(--accent)}
.cf-icon.ok{background:var(--green-soft);color:var(--green)}
.cf-icon::before{
  content:"";position:absolute;inset:-4px;border-radius:50%;
  background:currentColor;opacity:.12;
  animation:cfPulse 2s ease-in-out infinite;
}
@keyframes cfIconIn{from{transform:scale(.5);opacity:0}to{transform:scale(1);opacity:1}}
@keyframes cfPulse{0%,100%{transform:scale(1);opacity:.12}50%{transform:scale(1.25);opacity:0}}
.cf h3{font-size:17px;font-weight:800;letter-spacing:-.3px;color:var(--text);margin-bottom:8px}
.cf p{font-size:13.5px;line-height:1.6;color:var(--text-2);margin-bottom:22px;white-space:pre-line;word-break:break-word}
.cf p b{color:var(--text);font-weight:700}
.cf-actions{display:flex;gap:10px;justify-content:stretch}
.cf-actions .btn{flex:1;min-width:0;padding:10px 18px;justify-content:center;font-size:13.5px}

.menu-toggle{display:none}
@media(max-width:900px){
  .side{position:fixed;left:-270px;top:0;box-shadow:4px 0 20px rgba(0,0,0,.12)}
  .side.open{left:0}
  .menu-toggle{display:grid}
  .body{padding:16px}
  .topbar{padding:0 16px}
  .page-head h1{font-size:19px}
}
</style>
</head>
<body>

<div class="shell">

  <aside class="side" id="side">
    <div class="brand">
      <div class="mark"><i class="fa-solid fa-truck-fast"></i></div>
      <div class="txt">
        <b>LogiBoard</b>
        <small>Admin Panel</small>
      </div>
    </div>

    <div class="group-label" id="lblDashboard">Dashboard</div>
    <nav class="nav" id="navDashboard">
      <button data-adm="dashboard" class="active">
        <i class="fa-solid fa-chart-pie"></i> Ringkasan
      </button>
    </nav>

    <div class="group-label" id="lblMaster">Master Data</div>
    <nav class="nav" id="navMaster"></nav>

    <div class="group-label">Operasional Delivery</div>
    <nav class="nav">
      <a href="/silog/admin/actual-delivery" class="ext">
        <i class="fa-solid fa-truck-fast"></i> Actual Delivery
      </a>
      <a href="/silog/admin/customers" class="ext">
        <i class="fa-solid fa-users"></i> Customer + Cycle
      </a>
      <a href="#" class="ext" style="opacity:.5;cursor:not-allowed" title="Coming soon">
        <i class="fa-solid fa-book-open"></i> Log Book Problem
      </a>
      <a href="#" class="ext" style="opacity:.5;cursor:not-allowed" title="Coming soon">
        <i class="fa-solid fa-shield-halved"></i> Periodik Patrol
      </a>
      <a href="#" class="ext" style="opacity:.5;cursor:not-allowed" title="Coming soon">
        <i class="fa-solid fa-coins"></i> Cost Down Freight
      </a>
    </nav>

    <div class="group-label" id="lblKonten">Konten</div>
    <nav class="nav" id="navKonten">
      <button data-adm="docs">
        <i class="fa-solid fa-file-lines"></i> Dokumen
        <span class="cnt" id="cntDocs">0</span>
      </button>
      <button data-adm="delivery">
        <i class="fa-solid fa-truck"></i> Menu Delivery
        <span class="cnt" id="cntDel">0</span>
      </button>
      <button data-adm="system">
        <i class="fa-solid fa-server"></i> Menu System
        <span class="cnt" id="cntSys">0</span>
      </button>
      <button data-adm="job">
        <i class="fa-solid fa-clipboard-list"></i> Job Board
      </button>
    </nav>

    <div class="group-label" id="lblPengaturan">Pengaturan</div>
    <nav class="nav" id="navPengaturan">
      <button data-adm="settings">
        <i class="fa-solid fa-gear"></i> Umum
      </button>
    </nav>

    <div class="side-foot">
      <span class="dot"></span>
      <span>Tersinkronisasi server</span>
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <button class="icon-btn menu-toggle" id="menuToggle">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="crumb">
        <span>Admin</span>
        <i class="fa-solid fa-chevron-right"></i>
        <b id="crumb">Ringkasan</b>
      </div>
      <div class="actions">
        <button class="icon-btn" id="refreshBtn" title="Muat ulang">
          <i class="fa-solid fa-rotate"></i>
        </button>
        <button class="icon-btn" id="themeBtn" title="Ganti tema">
          <i class="fa-solid fa-moon"></i>
        </button>
        <button class="icon-btn danger" id="logoutBtn" title="Keluar">
          <i class="fa-solid fa-right-from-bracket"></i>
        </button>
      </div>
    </header>

    <div class="body">

      <div class="panel active" data-panel="dashboard">
        <div class="page-head">
          <div>
            <h1>Ringkasan</h1>
            <p>Statistik singkat seluruh data yang tersimpan di LogiBoard.</p>
          </div>
        </div>
        <div class="stats" id="statsGrid"></div>

        <div class="card">
          <div class="card-head">
            <h3>Dokumen Terbaru</h3>
          </div>
          <div class="table-wrap" id="recentTable"></div>
        </div>
      </div>

      <div class="panel" data-panel="docs">
        <div class="page-head">
          <div>
            <h1>Dokumen</h1>
            <p>Upload dan kelola dokumen. File PDF disimpan di server, metadata di database.</p>
          </div>
          <button class="btn btn-primary" id="uploadBtn">
            <i class="fa-solid fa-plus"></i> Upload Dokumen
          </button>
        </div>
        <div class="card">
          <div class="toolbar">
            <div class="search">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="docsSearch" placeholder="Cari dokumen…" />
            </div>
            <select class="select" id="docsFilter" style="max-width:200px;flex:none;padding:8px 34px 8px 12px">
              <option value="">Semua kategori</option>
            </select>
          </div>
          <div class="table-wrap" id="docsTable"></div>
        </div>
      </div>

      <div class="panel" data-panel="delivery">
        <div class="page-head">
          <div>
            <h1>Menu Delivery</h1>
            <p>Atur sub-menu yang muncul di halaman Delivery publik.</p>
          </div>
          <button class="btn btn-primary" data-new="deliveryMenu">
            <i class="fa-solid fa-plus"></i> Tambah Menu
          </button>
        </div>
        <div class="card">
          <div class="table-wrap" id="table_deliveryMenu"></div>
        </div>
      </div>

      <div class="panel" data-panel="system">
        <div class="page-head">
          <div>
            <h1>Menu System</h1>
            <p>Atur sistem internal. Isi URL saat siap, biarkan <b>#</b> jika belum.</p>
          </div>
          <button class="btn btn-primary" data-new="systemMenu">
            <i class="fa-solid fa-plus"></i> Tambah Sistem
          </button>
        </div>
        <div class="card">
          <div class="table-wrap" id="table_systemMenu"></div>
        </div>
      </div>

      <div class="panel" data-panel="job">
        <div class="page-head">
          <div>
            <h1>Job Board</h1>
            <p>Atur judul, deskripsi, dan URL papan manajemen produksi logistik.</p>
          </div>
        </div>
        <div class="card">
          <div class="form" style="padding:22px">
            <form id="jobForm">
              <div class="field">
                <label>Judul</label>
                <input type="text" class="input" id="jobTitle" required maxlength="80" />
              </div>
              <div class="field">
                <label>Deskripsi</label>
                <textarea class="textarea" id="jobDesc" required maxlength="300"></textarea>
              </div>
              <div class="field">
                <label>URL Tujuan</label>
                <input type="text" class="input" id="jobUrl" placeholder="#" />
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="panel" data-panel="settings">
        <div class="page-head">
          <div>
            <h1>Pengaturan Umum</h1>
            <p>Nama papan informasi dan preferensi dasar.</p>
          </div>
        </div>
        <div class="card">
          <div class="form" style="padding:22px">
            <form id="settingsForm">
              <div class="grid-2">
                <div class="field">
                  <label>Nama Papan</label>
                  <input type="text" class="input" id="setName" required maxlength="40" />
                </div>
                <div class="field">
                  <label>Sub-judul</label>
                  <input type="text" class="input" id="setSub" required maxlength="80" />
                </div>
              </div>
              <div style="height:1px;background:var(--line-soft);margin:12px 0 20px"></div>
              <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button type="submit" class="btn btn-primary">
                  <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
                </button>
                <button type="button" class="btn btn-danger" id="resetBtn">
                  <i class="fa-solid fa-rotate-left"></i> Reset Data Lokal
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<div class="modal-bg" id="modalBg">
  <div class="modal">
    <div class="modal-head">
      <h3 id="modalTitle">Form</h3>
      <button class="close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="modalBody"></div>
    <div class="modal-foot">
      <button class="btn btn-ghost" id="modalCancel">Batal</button>
      <button class="btn btn-primary" id="modalSave">
        <i class="fa-solid fa-floppy-disk"></i> Simpan
      </button>
    </div>
  </div>
</div>

<div class="cf-bg" id="cfBg">
  <div class="cf" role="dialog" aria-modal="true" aria-labelledby="cfTitle">
    <div class="cf-icon" id="cfIcon">
      <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <h3 id="cfTitle">Konfirmasi</h3>
    <p id="cfMessage">Apakah Anda yakin?</p>
    <div class="cf-actions">
      <button class="btn btn-ghost" id="cfCancel" type="button">Batal</button>
      <button class="btn btn-danger"  id="cfConfirm" type="button">Ya, Hapus</button>
    </div>
  </div>
</div>

<script>
/* ============================================================
   HELPERS
   ============================================================ */
const $   = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]));
const uid = () => 'i_' + Math.random().toString(36).slice(2,8) + Date.now().toString(36).slice(-4);

function fmtSize(b) {
  b = Number(b) || 0;
  if (b < 1024) return b + ' B';
  if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
  return (b/1048576).toFixed(2) + ' MB';
}
function fmtDate(iso) {
  if (!iso) return '—';
  const d = new Date(String(iso).replace(' ', 'T'));
  if (isNaN(d)) return String(iso);
  return d.toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
}
function statusClass(s) {
  const t = (s||'').toLowerCase();
  if (['aktif','tersedia','online','verified'].includes(t)) return 'ok';
  if (['cuti','maintenance','warning','hold','nonaktif'].includes(t)) return 'warn';
  if (['offline','reject','delay'].includes(t)) return 'err';
  return 'info';
}
function debounce(fn, ms = 300) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}
function toast(msg, type='info') {
  let el = document.getElementById('toast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'toast';
    el.className = 'toast';
    el.innerHTML = '<i></i><span></span>';
    document.body.appendChild(el);
  }
  const ic = el.querySelector('i');
  const sp = el.querySelector('span');
  const cls  = type==='success'?'ok':type==='error'?'err':'info';
  const icon = type==='success'?'fa-circle-check':type==='error'?'fa-circle-xmark':'fa-circle-info';
  el.className = 'toast ' + cls;
  ic.className = 'fa-solid ' + icon;
  sp.textContent = msg;
  requestAnimationFrame(() => el.classList.add('show'));
  clearTimeout(window.__toastTimer);
  window.__toastTimer = setTimeout(() => el.classList.remove('show'), 2800);
}

/* ============================================================
   CONFIRM DIALOG
   ============================================================ */
let cfResolve = null;
const cfBg        = $('cfBg');
const cfIconEl    = $('cfIcon');
const cfTitleEl   = $('cfTitle');
const cfMsgEl     = $('cfMessage');
const cfBtnOk     = $('cfConfirm');
const cfBtnCancel = $('cfCancel');

const CF_VARIANTS = {
  danger: { icon: 'fa-solid fa-trash',                cls: '',     btn: 'btn-danger',  text: 'Ya, Hapus'     },
  warn:   { icon: 'fa-solid fa-triangle-exclamation', cls: 'warn', btn: 'btn-primary', text: 'Ya, Lanjutkan' },
  info:   { icon: 'fa-solid fa-circle-info',          cls: 'info', btn: 'btn-primary', text: 'OK'            },
  ok:     { icon: 'fa-solid fa-circle-check',         cls: 'ok',   btn: 'btn-primary', text: 'Ya'            },
};

function showConfirm(opts = {}) {
  if (typeof opts === 'string') opts = { message: opts };

  const {
    title='Konfirmasi', message='Apakah Anda yakin?',
    confirmText=null, cancelText='Batal',
    variant='danger', icon=null, confirmClass=null,
  } = opts;

  const v = CF_VARIANTS[variant] || CF_VARIANTS.danger;

  cfIconEl.className    = 'cf-icon ' + v.cls;
  cfIconEl.innerHTML    = `<i class="${icon || v.icon}"></i>`;
  cfTitleEl.textContent = title;
  cfMsgEl.innerHTML     = String(message).replace(/\n/g, '<br>');
  cfBtnOk.textContent   = confirmText || v.text;
  cfBtnOk.className     = 'btn ' + (confirmClass || v.btn);
  cfBtnCancel.textContent = cancelText;

  cfBg.classList.add('active');
  return new Promise(resolve => {
    cfResolve = resolve;
    setTimeout(() => cfBtnOk.focus(), 100);
  });
}

function closeConfirm(result) {
  cfBg.classList.remove('active');
  if (cfResolve) {
    const r = cfResolve;
    cfResolve = null;
    setTimeout(() => r(result), 50);
  }
}

cfBtnOk.addEventListener('click',     () => closeConfirm(true));
cfBtnCancel.addEventListener('click', () => closeConfirm(false));
cfBg.addEventListener('click', e => { if (e.target === cfBg) closeConfirm(false); });

document.addEventListener('keydown', e => {
  if (!cfBg.classList.contains('active')) return;
  if (e.key === 'Escape') { e.preventDefault(); closeConfirm(false); }
  if (e.key === 'Enter')  { e.preventDefault(); closeConfirm(true);  }
});

/* ============================================================
   CONSTANTS
   ============================================================ */
const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content || '';
const USER_ROLE = document.querySelector('meta[name="user-role"]')?.content || 'ADMIN';
const THEME_KEY = 'logiboard-theme';
const LOCAL_KEY = 'logiboard-local';
const BASE      = '/silog';

const COLORS = ['#3b82f6','#22c55e','#f59e0b','#ef4444','#a855f7','#06b6d4','#ec4899','#64748b'];
const ICONS = [
  'fa-folder','fa-file','fa-file-lines','fa-file-invoice','fa-file-invoice-dollar',
  'fa-file-contract','fa-file-signature','fa-clipboard-check','fa-clipboard-list',
  'fa-box','fa-boxes-packing','fa-boxes-stacked','fa-truck','fa-truck-fast','fa-truck-ramp-box',
  'fa-book-open','fa-shield-halved','fa-coins','fa-chart-line','fa-database',
  'fa-industry','fa-satellite-dish','fa-route','fa-map-location-dot',
  'fa-user-tie','fa-users','fa-warehouse','fa-gas-pump','fa-gears','fa-passport',
  'fa-arrow-down-to-line','fa-arrow-up-from-bracket','fa-scroll','fa-magnifying-glass-chart'
];

/* ============================================================
   API CLIENT
   ============================================================ */
const API = {
  async call(url, method = 'GET', data = null, isForm = false) {
    const opts = {
      method,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
    };

    if (data) {
      if (isForm) {
        opts.body = data instanceof FormData ? data : (() => {
          const fd = new FormData();
          for (const k in data) fd.append(k, data[k]);
          return fd;
        })();
      } else {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(data);
      }
    }

    const res = await fetch(url, opts);
    let json = null;
    try { json = await res.json(); }
    catch (e) { json = { message: 'Respons server tidak valid.' }; }

    if (!res.ok) {
      const msg = json.errors ? Object.values(json.errors)[0] : (json.message || `HTTP ${res.status}`);
      const err = new Error(msg);
      err.status = res.status;
      err.body   = json;
      throw err;
    }
    return json;
  },
  get:  (url)               => API.call(url, 'GET'),
  post: (url, data, isForm) => API.call(url, 'POST', data, isForm),
  put:  (url, data)         => API.call(url, 'PUT', data),
  del:  (url)               => API.call(url, 'DELETE'),
};

/* ============================================================
   MASTER SCHEMAS
   ============================================================ */
const MASTERS = {
  user: {
    label:'User', singular:'User', icon:'fa-user-gear',
    desc:'Akun pengguna yang dapat login ke Admin Panel.',
    backend: true,
    endpoints: {
      list:'/admin/users/list',
      base:'/admin/users',
      idPattern:'/admin/users/{id}',
    },
    fields: [
      { k:'name',     l:'Nama Lengkap', t:'text',     req:1 },
      { k:'username', l:'Username',     t:'text',     req:1 },
      { k:'role',     l:'Role',         t:'select',   opts:['SUPERADMIN','ADMIN','VIEWER'], def:'VIEWER' },
      { k:'password', l:'Password',     t:'password', hint:'Kosongkan saat edit jika tidak ingin mengubah' },
    ],
    table: [
      { k:'name',     l:'Nama',     cls:'strong' },
      { k:'username', l:'Username' },
      { k:'role',     l:'Role' },
    ]
  },
  
  kategori: {
    label:'Kategori Dokumen', singular:'Kategori', icon:'fa-tags',
    desc:'Kategori untuk mengelompokkan dokumen.',
    backend: true,
    endpoints: { list:'/admin/categories/list', base:'/admin/categories', idPattern:'/admin/{id}/categories' },
    fields: [
      { k:'slug',        l:'Kode',          t:'text', req:1 },
      { k:'name',        l:'Nama Kategori', t:'text', req:1 },
      { k:'color',       l:'Warna',         t:'color', def:'#3b82f6' },
      { k:'icon',        l:'Ikon',          t:'icon',  def:'fa-folder' },
      { k:'description', l:'Keterangan',    t:'textarea' }
    ],
    table: [
      { k:'slug',  l:'Kode', cls:'strong' },
      { k:'name',  l:'Nama' },
      { k:'color', l:'Warna', t:'color' },
      { k:'icon',  l:'Ikon',  t:'icon' }
    ]
  },

  item: {
    label:'Item ICS', singular:'Item', icon:'fa-box',
    desc:'Master item ICS untuk detail delivery.',
    backend: true,
    importable: true,
    endpoints: {
      list:'/admin/items/list',
      base:'/admin/items',
      idPattern:'/admin/{id}/items',
      import:'/admin/items/import',
      template:'/admin/items/import-template',
    },
    fields: [
      { k:'ics_code',    l:'Kode ICS',    t:'text', req:1 },
      { k:'part_number', l:'Part Number', t:'text', req:1 },
      { k:'ics_name',    l:'Nama Item',   t:'text', req:1 },
      { k:'type',        l:'Tipe',        t:'text' },
      { k:'unit',        l:'Satuan',      t:'text', def:'PCS' },
    ],
    table: [
      { k:'ics_code',    l:'Kode ICS',    cls:'strong' },
      { k:'part_number', l:'Part Number' },
      { k:'ics_name',    l:'Nama' },
      { k:'type',        l:'Tipe' },
      { k:'unit',        l:'Satuan' },
    ]
  },

  vehicle: {
    label:'Kendaraan', singular:'Kendaraan', icon:'fa-truck',
    desc:'Master kendaraan / armada logistik.',
    backend: true,
    importable: true,
    endpoints: {
      list:'/admin/vehicles/list',
      base:'/admin/vehicles',
      idPattern:'/admin/{id}/vehicles',
      import:'/admin/vehicles/import',
      template:'/admin/vehicles/import-template',
    },
    fields: [
      { k:'plate_number', l:'No. Polisi', t:'text', req:1 },
      { k:'tonase',       l:'Tonase',     t:'text' },
      { k:'jenis',        l:'Jenis',      t:'select', opts:['Rental','Truk','Wingbox','Trailer','Pickup','Van'], def:'Rental' },
      { k:'vendor',       l:'Vendor',     t:'text' },
    ],
    table: [
      { k:'plate_number', l:'No. Polisi', cls:'strong' },
      { k:'tonase',       l:'Tonase' },
      { k:'jenis',        l:'Jenis' },
      { k:'vendor',       l:'Vendor' },
    ]
  },

  vendor: {
    label:'Vendor', singular:'Vendor', icon:'fa-building',
    desc:'Data vendor / rekanan logistik.',
    backend: true,
    endpoints: { list:'/admin/vendors/list', base:'/admin/vendors', idPattern:'/admin/vendors/{id}' },
    fields: [
      { k:'kode',   l:'Kode Vendor', t:'text', req:1 },
      { k:'nama',   l:'Nama Vendor', t:'text', req:1 },
      { k:'kontak', l:'Kontak Person', t:'text' },
      { k:'telp',   l:'Telepon',     t:'text' },
      { k:'email',  l:'Email',       t:'text' },
      { k:'alamat', l:'Alamat',      t:'textarea' },
      { k:'status', l:'Status',      t:'select', opts:['Aktif','Nonaktif'], def:'Aktif' }
    ],
    table: [
      { k:'kode',   l:'Kode', cls:'strong' },
      { k:'nama',   l:'Nama Vendor' },
      { k:'kontak', l:'Kontak' },
      { k:'telp',   l:'Telepon' },
      { k:'status', l:'Status', t:'status' }
    ]
  },

  driver: {
    label:'Driver', singular:'Driver', icon:'fa-user-tie',
    desc:'Data pengemudi kendaraan logistik.',
    backend: true,
    endpoints: { list:'/admin/drivers/list', base:'/admin/drivers', idPattern:'/admin/drivers/{id}' },
    fields: [
      { k:'nip',    l:'NIP',    t:'text', req:1 },
      { k:'nama',   l:'Nama',   t:'text', req:1 },
      { k:'telp',   l:'Telepon', t:'text' },
      { k:'sim',    l:'Jenis SIM', t:'select', opts:['B1','B2','B2 Umum','C'], def:'B2 Umum' },
      { k:'vendor', l:'Vendor', t:'text' },
      { k:'status', l:'Status', t:'select', opts:['Aktif','Cuti','Nonaktif'], def:'Aktif' }
    ],
    table: [
      { k:'nip',    l:'NIP', cls:'strong' },
      { k:'nama',   l:'Nama' },
      { k:'sim',    l:'SIM' },
      { k:'vendor', l:'Vendor' },
      { k:'status', l:'Status', t:'status' }
    ]
  },

  gudang: {
    label:'Gudang', singular:'Gudang', icon:'fa-warehouse',
    desc:'Master data gudang dan lokasi penyimpanan.',
    fields: [
      { k:'kode',    l:'Kode Gudang', t:'text', req:1 },
      { k:'nama',    l:'Nama Gudang', t:'text', req:1 },
      { k:'lokasi',  l:'Lokasi',      t:'text' },
      { k:'kapasitas', l:'Kapasitas (m²)', t:'text' },
      { k:'pj',      l:'Penanggung Jawab', t:'text' },
      { k:'status',  l:'Status',      t:'select', opts:['Aktif','Nonaktif'], def:'Aktif' }
    ],
    table: [
      { k:'kode',   l:'Kode', cls:'strong' },
      { k:'nama',   l:'Nama Gudang' },
      { k:'lokasi', l:'Lokasi' },
      { k:'pj',     l:'PJ' },
      { k:'status', l:'Status', t:'status' }
    ]
  },

  rute: {
    label:'Rute', singular:'Rute', icon:'fa-route',
    desc:'Master rute pengiriman antar kota.',
    backend: true,
    endpoints: { list:'/admin/routes/list', base:'/admin/routes', idPattern:'/admin/routes/{id}' },
    fields: [
      { k:'kode',      l:'Kode Rute', t:'text', req:1 },
      { k:'asal',      l:'Kota Asal', t:'text', req:1 },
      { k:'tujuan',    l:'Kota Tujuan', t:'text', req:1 },
      { k:'jarak',     l:'Jarak (km)', t:'text' },
      { k:'estimasi',  l:'Estimasi (jam)', t:'text' },
      { k:'tarif',     l:'Tarif (Rp)', t:'text' }
    ],
    table: [
      { k:'kode',   l:'Kode', cls:'strong' },
      { k:'asal',   l:'Asal' },
      { k:'tujuan', l:'Tujuan' },
      { k:'jarak',  l:'Jarak' },
      { k:'estimasi', l:'Estimasi' }
    ]
  },
};

/* ============================================================
   STATE
   ============================================================ */
let state = {
  kategori: [],
  documents: [],
  item: [], vehicle: [],
  vendor: [], driver: [], gudang: [], rute: [],
  deliveryMenu: [
    { id:'d1', title:'Actual Delivery',
      desc:'Realisasi pengiriman harian, bukti terima, dan status akhir tiap trip.',
      icon:'fa-clipboard-check', color:'#3b82f6', url:'#' },
    { id:'d2', title:'Log Book Problem Kendaraan',
      desc:'Catatan masalah kendaraan, kerusakan, dan tindak lanjut perbaikan.',
      icon:'fa-book-open', color:'#f59e0b', url:'#' },
    { id:'d3', title:'Periodik Patrol Kendaraan',
      desc:'Jadwal patroli rutin, checklist kondisi kendaraan, dan hasil inspeksi.',
      icon:'fa-shield-halved', color:'#22c55e', url:'#' },
    { id:'d4', title:'Cost Down Freight',
      desc:'Analisa dan program penghematan biaya angkutan barang per rute.',
      icon:'fa-coins', color:'#a855f7', url:'#' }
  ],
  systemMenu: [], // Dimuat dari API
  jobConfig: {
    title:'Papan Manajemen Produksi Logistik',
    desc:'Sistem untuk mengelola rencana produksi, monitor progres, dan koordinasi antar divisi logistik dalam satu papan terpusat.',
    url:'#'
  },
  settings: {
    siteName:'LogiBoard',
    siteSubtitle:'Papan Informasi Logistik · PT Indonesia Stanley Electric'
  }
};

/* ============================================================
   LOCAL STORAGE (Hanya untuk konten yang belum backend)
   ============================================================ */
function loadLocalState() {
  try {
    const raw = localStorage.getItem(LOCAL_KEY);
    if (!raw) return;
    const parsed = JSON.parse(raw);
    ['deliveryMenu','jobConfig','settings'].forEach(k => {
      if (parsed[k] !== undefined) state[k] = parsed[k];
    });
  } catch (e) { console.warn('loadLocalState', e); }
}
function saveLocalState() {
  try {
    const toSave = {};
    ['deliveryMenu','jobConfig','settings'].forEach(k => {
      toSave[k] = state[k];
    });
    localStorage.setItem(LOCAL_KEY, JSON.stringify(toSave));
  } catch (e) { console.warn('saveLocalState', e); }
}

/* ============================================================
   BACKEND LOADERS
   ============================================================ */
async function loadBackendMaster(key) {
  const m = MASTERS[key];
  if (!m.backend) return;

  try {
    const res = await API.get(BASE + m.endpoints.list);
    state[key] = (res.data || []);
    renderSidebar();
  } catch (e) {
    toast('Gagal memuat ' + m.label + ': ' + e.message, 'error');
    state[key] = [];
  }
}

async function loadSystemMenu() {
  try {
    const res = await API.get(BASE + '/admin/system-menus/list');
    state.systemMenu = (res.data || []).map(m => ({
      id: m.id, title: m.title, desc: m.desc, icon: m.icon, color: m.color, url: m.url
    }));
    renderSidebar();
  } catch (e) {
    toast('Gagal memuat menu system: ' + e.message, 'error');
    state.systemMenu = [];
  }
}

async function loadCategories() { await loadBackendMaster('kategori'); refreshFilter(); }
async function loadItems()      { await loadBackendMaster('item'); }
async function loadUsers() { await loadBackendMaster('user'); }
async function loadVehicles()   { await loadBackendMaster('vehicle'); }
async function loadVendors()    { await loadBackendMaster('vendor'); }
async function loadDrivers()    { await loadBackendMaster('driver'); }
async function loadRoutes()     { await loadBackendMaster('rute'); }

let docPage = 1;

async function loadDocuments() {
  const q     = ($('docsSearch')?.value || '').trim();
  const catId = $('docsFilter')?.value || '';

  const params = new URLSearchParams();
  if (q)     params.set('q', q);
  if (catId) params.set('category_id', catId);
  params.set('page', docPage);

  try {
    const res = await API.get(BASE + '/admin/documents/list?' + params.toString());

    state.documents = (res.data || []).map(d => ({
      id:          d.id,
      title:       d.title,
      desc:        d.description || '',
      kategoriId:  d.category_id,
      fileName:    d.original_name,
      fileSize:    Number(d.file_size),
      fileType:    d.mime_type,
      uploadedAt:  d.created_at,
      categoryName:  d.category_name,
      categoryColor: d.category_color,
      _sizeHuman:    d.file_size_human,
    }));

    $('cntDocs').textContent = res.pagination?.total ?? state.documents.length;
    renderDocs();
    renderDashboard();
  } catch (e) {
    toast('Gagal memuat dokumen: ' + e.message, 'error');
    state.documents = [];
    renderDocs();
  }
}

/* ============================================================
   SIDEBAR
   ============================================================ */
const CRUMBS = {
  dashboard:'Ringkasan',
  docs:'Dokumen',
  delivery:'Menu Delivery',
  system:'Menu System',
  job:'Job Board',
  settings:'Pengaturan Umum'
};

function renderSidebar() {
  $('navMaster').innerHTML = Object.keys(MASTERS).map(key => {
    const m = MASTERS[key];
    const n = (state[key] || []).length;
    return `<button data-adm="m_${key}">
      <i class="fa-solid ${m.icon}"></i> ${esc(m.label)}
      <span class="cnt">${n}</span>
    </button>`;
  }).join('');

  $('cntDocs').textContent = state.documents.length;
  $('cntDel').textContent  = state.deliveryMenu.length;
  $('cntSys').textContent  = state.systemMenu.length;
}

function goAdmin(target) {
  // Proteksi Frontend: Jika ADMIN mencoba mengakses menu terlarang via Console/URL
  if (USER_ROLE.toUpperCase() !== 'SUPERADMIN') {
    if (target !== 'dashboard' && !target.startsWith('m_') && target !== 'docs' && target !== 'delivery' && target !== 'system' && target !== 'job' && target !== 'settings') {
        // Biarkan
    } else if (target !== 'dashboard') {
        toast('Akses Ditolak: Role Anda tidak diizinkan membuka menu ini.', 'error');
        return;
    }
  }

  document.querySelectorAll('.nav button').forEach(b =>
    b.classList.toggle('active', b.dataset.adm === target)
  );

  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));

  let panel = document.querySelector(`.panel[data-panel="${target}"]`);

  if (target.startsWith('m_')) {
    const key = target.slice(2);
    const m = MASTERS[key];
    if (!panel) {
      panel = document.createElement('div');
      panel.className = 'panel';
      panel.dataset.panel = target;
      panel.innerHTML = `
        <div class="page-head">
          <div>
            <h1>${esc(m.label)}</h1>
            <p>${esc(m.desc)}</p>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            ${m.importable ? `
              <button class="btn btn-ghost" data-import="${key}">
                <i class="fa-solid fa-file-import"></i> Import EXCEL
              </button>
            ` : ''}
            <button class="btn btn-primary" data-new="${key}">
              <i class="fa-solid fa-plus"></i> Tambah ${esc(m.singular)}
            </button>
          </div>
        </div>
        <div class="card">
          <div class="toolbar">
            <div class="search">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="search_${key}" placeholder="Cari ${esc(m.singular.toLowerCase())}…" />
            </div>
          </div>
          <div class="table-wrap" id="table_${key}"></div>
        </div>
      `;
      document.querySelector('.body').appendChild(panel);
      panel.querySelector(`#search_${key}`).addEventListener('input',
        debounce(() => renderMasterTable(key), 200));
    }
    $('crumb').textContent = m.label;

    if (m.backend && (state[key] || []).length === 0) {
      loadBackendMaster(key).then(() => renderMasterTable(key));
    }
  } else {
    $('crumb').textContent = CRUMBS[target] || 'Admin';
  }

  panel.classList.add('active');

  if (target === 'dashboard') renderDashboard();
  if (target === 'docs')      { docPage = 1; loadCategories().then(loadDocuments); }
  if (target === 'delivery')  renderMenuTable('deliveryMenu');
  if (target === 'system')    { loadSystemMenu().then(() => renderMenuTable('systemMenu')); }
  if (target === 'job')       renderJobForm();
  if (target === 'settings')  renderSettingsForm();
  if (target.startsWith('m_')) renderMasterTable(target.slice(2));

  $('side').classList.remove('open');
}

document.addEventListener('click', async e => {
  const nav = e.target.closest('[data-adm]');
  if (nav) { goAdmin(nav.dataset.adm); return; }

  const ib = e.target.closest('[data-import]');
  if (ib) { openImportModal(ib.dataset.import); return; }

  const nb = e.target.closest('[data-new]');
  if (nb) {
    const kind = nb.dataset.new;
    if (kind === 'deliveryMenu' || kind === 'systemMenu') openMenuModal(kind, null);
    else openMasterModal(kind, null);
  }
});

$('menuToggle').addEventListener('click', () => $('side').classList.toggle('open'));

/* ============================================================
   DASHBOARD
   ============================================================ */
function renderDashboard() {
  const stats = [
    { l:'Total Dokumen', v:state.documents.length, i:'fa-file-lines' },
    { l:'Kategori',      v:state.kategori.length,  i:'fa-tags' },
    { l:'Item ICS',      v:state.item.length,      i:'fa-box' },
    { l:'Kendaraan',     v:state.vehicle.length,   i:'fa-truck' },
    { l:'Vendor',        v:state.vendor.length,    i:'fa-building' },
    { l:'Driver',        v:state.driver.length,    i:'fa-user-tie' },
    { l:'Gudang',        v:state.gudang.length,    i:'fa-warehouse' },
    { l:'Rute',          v:state.rute.length,      i:'fa-route' }
  ];
  $('statsGrid').innerHTML = stats.map(s => `
    <div class="stat">
      <div class="label"><i class="fa-solid ${s.i}"></i> ${s.l}</div>
      <div class="value">${s.v}</div>
    </div>
  `).join('');

  const recent = state.documents.slice(0, 6);
  if (!recent.length) {
    $('recentTable').innerHTML = `
      <div class="empty">
        <i class="fa-solid fa-file-circle-plus"></i>
        <b>Belum ada dokumen</b>
        <p>Upload dokumen pertama dari menu <b>Dokumen</b>.</p>
      </div>`;
    return;
  }
  $('recentTable').innerHTML = `
    <table>
      <thead><tr>
        <th>Judul</th><th>Kategori</th><th>Ukuran</th><th>Tanggal</th>
      </tr></thead>
      <tbody>
        ${recent.map(d => `
          <tr>
            <td class="strong">${esc(d.title)}</td>
            <td><span class="tag info">${esc(d.categoryName || '—')}</span></td>
            <td class="muted">${d._sizeHuman || fmtSize(d.fileSize)}</td>
            <td class="muted">${fmtDate(d.uploadedAt)}</td>
          </tr>
        `).join('')}
      </tbody>
    </table>
  `;
}

/* ============================================================
   MASTER TABLE
   ============================================================ */
function renderMasterTable(key) {
  const m = MASTERS[key];
  const el = $('table_' + key);
  if (!el) return;

  const q = ($(`search_${key}`)?.value || '').trim().toLowerCase();
  let list = state[key] || [];
  if (q) {
    list = list.filter(row =>
      m.fields.some(f => String(row[f.k] || '').toLowerCase().includes(q))
    );
  }

  const navBtn = document.querySelector(`.nav button[data-adm="m_${key}"]`);
  if (navBtn) navBtn.querySelector('.cnt').textContent = (state[key] || []).length;

  if (!(state[key] || []).length) {
    el.innerHTML = `<div class="empty">
      <i class="fa-solid ${m.icon}"></i>
      <b>Belum ada data</b>
      <p>Klik <b>Tambah ${esc(m.singular)}</b> untuk membuat data pertama.</p>
    </div>`;
    return;
  }
  if (!list.length) {
    el.innerHTML = `<div class="empty">
      <i class="fa-solid fa-magnifying-glass"></i>
      <b>Tidak ditemukan</b>
      <p>Coba kata kunci lain.</p>
    </div>`;
    return;
  }

  const cols = m.table;
  el.innerHTML = `
    <table>
      <thead><tr>
        ${cols.map(c => `<th>${esc(c.l)}</th>`).join('')}
        <th style="width:100px;text-align:right">Aksi</th>
      </tr></thead>
      <tbody>
        ${list.map(row => `
          <tr>
            ${cols.map(c => {
              const v = row[c.k];
              if (c.t === 'color') return `<td><span class="swatch" style="background:${esc(v || '#ccc')}"></span>${esc(v || '')}</td>`;
              if (c.t === 'icon')  return `<td><i class="fa-solid ${esc(v || 'fa-folder')} icon-cell"></i>${esc(v || '')}</td>`;
              if (c.t === 'status')return `<td><span class="tag ${statusClass(v)}"><span class="dot"></span>${esc(v || '—')}</span></td>`;
              return `<td class="${c.cls || ''}">${esc(v || '—')}</td>`;
            }).join('')}
            <td>
              <div class="actions">
                <button class="btn btn-icon btn-sm" data-edit-master="${key}" data-id="${row.id}" title="Edit">
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn btn-icon btn-sm danger" data-del-master="${key}" data-id="${row.id}" title="Hapus">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `).join('')}
      </tbody>
    </table>
  `;
}

document.addEventListener('click', async e => {
  const ed = e.target.closest('[data-edit-master]');
  if (ed) {
    openMasterModal(ed.dataset.editMaster, ed.dataset.id);
    return;
  }
  const dl = e.target.closest('[data-del-master]');
  if (dl) {
    deleteMasterItem(dl.dataset.delMaster, dl.dataset.id);
  }
});

async function deleteMasterItem(key, id) {
  const m = MASTERS[key];
  const item = (state[key] || []).find(x => String(x.id) === String(id));
  if (!item) return;

  const nama = item.name || item.ics_name || item.plate_number || item.nama || item.kode || 'Data ini';

  const ok = await showConfirm({
    title: `Hapus ${m.singular}?`,
    message: `<b>${esc(nama)}</b> akan dihapus permanen.`,
    confirmText: 'Ya, Hapus',
    variant: 'danger',
  });
  if (!ok) return;

  if (m.backend) {
    try {
      const url = BASE + m.endpoints.idPattern.replace('{id}', id);
      await API.del(url);
      toast('Data dihapus', 'success');
      await loadBackendMaster(key);
      renderMasterTable(key);
    } catch (e) {
      toast(e.message, 'error');
    }
    return;
  }

  state[key] = state[key].filter(x => String(x.id) !== String(id));
  saveLocalState();
  renderMasterTable(key);
  renderSidebar();
  toast('Data dihapus', 'success');
}

/* ============================================================
   MASTER MODAL
   ============================================================ */
let modalContext = null;

function openModal(title, bodyHTML, onSave) {
  modalContext = { onSave };
  $('modalTitle').textContent = title;
  $('modalBody').innerHTML = bodyHTML;
  $('modalBg').classList.add('active');
  attachModalPickers();
  setTimeout(() => {
    const f = $('modalBody').querySelector('input:not([type=hidden]),select,textarea');
    f && f.focus();
  }, 80);
}
function closeModal() {
  $('modalBg').classList.remove('active');
  modalContext = null;
}
$('modalClose').addEventListener('click', closeModal);
$('modalCancel').addEventListener('click', closeModal);
$('modalBg').addEventListener('click', e => { if (e.target === $('modalBg')) closeModal(); });
$('modalSave').addEventListener('click', () => {
  if (modalContext && modalContext.onSave) modalContext.onSave();
});

function attachModalPickers() {
  $('modalBody').querySelectorAll('.color-grid').forEach(grid => {
    grid.addEventListener('click', e => {
      const opt = e.target.closest('.color-opt'); if (!opt) return;
      grid.querySelectorAll('.color-opt').forEach(o => o.classList.remove('sel'));
      opt.classList.add('sel');
      const hidden = grid.parentElement.querySelector('input[type=hidden]');
      if (hidden) hidden.value = opt.dataset.color;
    });
  });
  $('modalBody').querySelectorAll('.icon-grid').forEach(grid => {
    grid.addEventListener('click', e => {
      const opt = e.target.closest('.icon-opt'); if (!opt) return;
      grid.querySelectorAll('.icon-opt').forEach(o => o.classList.remove('sel'));
      opt.classList.add('sel');
      const hidden = grid.parentElement.querySelector('input[type=hidden]');
      if (hidden) hidden.value = opt.dataset.icon;
    });
  });
}

function fieldHTML(f, value) {
  const v = value ?? f.def ?? '';
  const req = f.req ? '<span class="req">*</span>' : '';
  const hint = f.hint
    ? `<div style="font-size:11.5px;color:var(--text-3);margin-top:5px">${esc(f.hint)}</div>`
    : '';
  let inner = '';
  if (f.t === 'textarea') {
    inner = `<textarea class="textarea" id="f_${f.k}" ${f.req?'required':''} maxlength="300">${esc(v)}</textarea>`;
  } else if (f.t === 'select') {
    inner = `<select class="select" id="f_${f.k}" ${f.req?'required':''}>
      ${f.opts.map(o => `<option ${o === v ? 'selected' : ''}>${esc(o)}</option>`).join('')}
    </select>`;
  } else if (f.t === 'color') {
    inner = `
      <input type="hidden" id="f_${f.k}" value="${esc(v || '#3b82f6')}" />
      <div class="color-grid">
        ${COLORS.map(c => `<div class="color-opt ${c === v ? 'sel' : ''}" style="background:${c}" data-color="${c}"></div>`).join('')}
      </div>`;
  } else if (f.t === 'icon') {
    inner = `
      <input type="hidden" id="f_${f.k}" value="${esc(v || 'fa-folder')}" />
      <div class="icon-grid">
        ${ICONS.slice(0,16).map(ic => `<div class="icon-opt ${ic === v ? 'sel' : ''}" data-icon="${ic}"><i class="fa-solid ${ic}"></i></div>`).join('')}
      </div>`;
  } else if (f.t === 'password') {
    inner = `<input type="password" class="input" id="f_${f.k}" ${f.req?'required':''} autocomplete="new-password" value="" placeholder="••••••••" />`;
  } else {
    inner = `<input type="text" class="input" id="f_${f.k}" ${f.req?'required':''} value="${esc(v)}" />`;
  }
  return `<div class="field"><label>${esc(f.l)}${req}</label>${inner}${hint}</div>`;
}

function openMasterModal(key, editId) {
  const m = MASTERS[key];
  const item = editId ? (state[key] || []).find(x => String(x.id) === String(editId)) : null;
  const title = (item ? 'Edit ' : 'Tambah ') + m.singular;
  const body = m.fields.map(f => fieldHTML(f, item ? item[f.k] : undefined)).join('');

  openModal(title, body, async () => {
    const data = {};
    for (const f of m.fields) {
      const el = $('f_' + f.k);
      data[f.k] = el ? el.value.trim() : '';
      if (f.req && !data[f.k]) {
        toast(`${f.l} wajib diisi`, 'error');
        el && el.focus();
        return;
      }
    }

    if ('password' in data && !data.password) {
      delete data.password;
    }

    if (key === 'user' && !item && !data.password) {
      toast('Password wajib diisi untuk user baru', 'error');
      $('f_password').focus();
      return;
    }

    const btn = $('modalSave');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';

    try {
      if (m.backend) {
        if (item) {
          const url = BASE + m.endpoints.idPattern.replace('{id}', item.id);
          await API.put(url, data);
        } else {
          await API.post(BASE + m.endpoints.base, data);
        }
        toast(m.singular + (item ? ' diperbarui' : ' ditambahkan'), 'success');
        closeModal();
        await loadBackendMaster(key);
        renderMasterTable(key);
      } else {
        if (item) {
          Object.assign(item, data);
          toast(m.singular + ' diperbarui', 'success');
        } else {
          data.id = uid();
          (state[key] = state[key] || []).push(data);
          toast(m.singular + ' ditambahkan', 'success');
        }
        saveLocalState();
        closeModal();
        renderMasterTable(key);
        renderSidebar();
      }
      if ($('panel-dashboard')?.classList.contains('active')) renderDashboard();
    } catch (e) {
      toast(e.message, 'error');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
    }
  });
}

/* ============================================================
   IMPORT EXCEL
   ============================================================ */
function openImportModal(key) {
  const m = MASTERS[key];

  const body = `
    <div class="field">
      <label>File Excel <span class="req">*</span></label>
      <label class="file" id="imp_drop">
        <input type="file" id="imp_file" accept=".xlsx,.xls,.csv" required />
        <div class="fi"><i class="fa-solid fa-file-excel"></i></div>
        <div class="ft" id="imp_name">Klik untuk pilih file Excel</div>
        <div class="fs" id="imp_info">Format .xlsx / .xls · maks 10 MB</div>
      </label>
    </div>

    <div style="background:var(--panel-2);border:1px solid var(--line);border-radius:8px;padding:12px;font-size:12px;color:var(--text-2);line-height:1.7">
      <b style="display:block;margin-bottom:6px;color:var(--text)">
        <i class="fa-solid fa-circle-info" style="color:var(--accent)"></i> Petunjuk
      </b>
      1. Klik <b>Download Template</b> di bawah<br>
      2. Isi data di Excel sesuai kolom<br>
      3. Save (format <b>.xlsx</b>)<br>
      4. Upload file di sini<br>
      <span style="color:var(--text-3);font-size:11.5px;display:block;margin-top:6px">
        Data dengan kode sama akan <b>diperbarui</b>, bukan duplikat.
      </span>
    </div>

    <button type="button" class="btn btn-primary btn-block" id="imp_template"
            style="margin-top:12px;background:var(--green);color:#fff">
      <i class="fa-solid fa-download"></i> Download Template Excel
    </button>
  `;

  openModal('Import ' + m.singular, body, () => submitImport(key));

  const drop = $('imp_drop');
  const fi   = $('imp_file');

  fi.addEventListener('change', () => {
    const f = fi.files[0];
    if (!f) return;
    $('imp_name').textContent = f.name;
    $('imp_info').textContent = (f.size / 1024).toFixed(1) + ' KB · siap diimport';
    drop.classList.add('has-file');
  });

  $('imp_template').addEventListener('click', () => {
    window.location.href = BASE + m.endpoints.template;
  });
}

async function submitImport(key) {
  const m = MASTERS[key];
  const file = $('imp_file')?.files[0];

  if (!file) { toast('Pilih file Excel dulu', 'error'); return; }

  const btn = $('modalSave');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses…';

  const fd = new FormData();
  fd.append('file', file);

  try {
    const res = await API.post(BASE + m.endpoints.import, fd, true);

    closeModal();
    await loadBackendMaster(key);
    renderMasterTable(key);
    if ($('panel-dashboard')?.classList.contains('active')) renderDashboard();

    const summary = res.summary || {};
    const hasErrors = summary.errors && summary.errors.length > 0;

    if (hasErrors) {
      await showConfirm({
        title: 'Import Selesai dengan Peringatan',
        message:
          `${res.message}\n\n` +
          summary.errors.slice(0, 5).join('\n') +
          (summary.errors.length > 5
            ? `\n…dan ${summary.errors.length - 5} error lainnya.`
            : ''),
        variant: 'warn',
        confirmText: 'OK',
        confirmClass: 'btn-primary',
      });
    } else {
      toast(res.message || 'Import selesai', 'success');
    }
  } catch (e) {
    toast(e.message, 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
  }
}

/* ============================================================
   MENU TABLE (Delivery / System)
   ============================================================ */
function renderMenuTable(listKey) {
  const el = $('table_' + listKey);
  if (!el) return;
  const list = state[listKey] || [];
  if (listKey === 'deliveryMenu') $('cntDel').textContent = list.length;
  if (listKey === 'systemMenu')   $('cntSys').textContent = list.length;

  if (!list.length) {
    el.innerHTML = `<div class="empty">
      <i class="fa-solid fa-inbox"></i>
      <b>Belum ada menu</b>
      <p>Klik <b>Tambah Menu</b> di atas untuk membuat menu pertama.</p>
    </div>`;
    return;
  }
  el.innerHTML = `
    <table>
      <thead><tr>
        <th style="width:60px">Ikon</th>
        <th>Judul</th>
        <th>Deskripsi</th>
        <th>URL</th>
        <th style="width:100px;text-align:right">Aksi</th>
      </tr></thead>
      <tbody>
        ${list.map(x => `
          <tr>
            <td>
              <div style="width:34px;height:34px;border-radius:9px;display:grid;place-items:center;
                          color:#fff;font-size:14px;background:${x.color || '#3b82f6'}">
                <i class="fa-solid ${x.icon || 'fa-circle'}"></i>
              </div>
            </td>
            <td class="strong">${esc(x.title)}</td>
            <td class="muted" style="max-width:340px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              ${esc(x.desc || '')}
            </td>
            <td><span class="tag ${x.url && x.url !== '#' ? 'ok' : 'info'}">${esc(x.url || '#')}</span></td>
            <td>
              <div class="actions">
                <button class="btn btn-icon btn-sm" data-edit-menu="${listKey}" data-id="${x.id}" title="Edit">
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn btn-icon btn-sm danger" data-del-menu="${listKey}" data-id="${x.id}" title="Hapus">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `).join('')}
      </tbody>
    </table>
  `;
}

document.addEventListener('click', async e => {
  const ed = e.target.closest('[data-edit-menu]');
  if (ed) { openMenuModal(ed.dataset.editMenu, ed.dataset.id); return; }
  const dl = e.target.closest('[data-del-menu]');
  if (dl) {
    const key = dl.dataset.delMenu;
    const id  = dl.dataset.id;
    const isSystem = key === 'systemMenu';
    const item = state[key].find(x => String(x.id) === String(id));
    if (!item) return;

    const ok = await showConfirm({
      title: 'Hapus Menu?',
      message: `<b>${esc(item.title)}</b> akan dihapus dari daftar.`,
      confirmText: 'Ya, Hapus',
      variant: 'danger',
    });
    if (!ok) return;

    if (isSystem) {
      try {
        await API.del(BASE + `/admin/system-menus/${id}`);
        toast('Sistem dihapus', 'success');
        await loadSystemMenu();
        renderMenuTable('systemMenu');
      } catch (err) {
        toast(err.message, 'error');
      }
    } else {
      state[key] = state[key].filter(x => String(x.id) !== String(id));
      saveLocalState();
      renderMenuTable(key);
      renderSidebar();
      toast('Menu dihapus', 'success');
    }
  }
});

function openMenuModal(listKey, editId) {
  const isSystem = listKey === 'systemMenu';
  const list = state[listKey];
  const item = editId ? list.find(x => String(x.id) === String(editId)) : null;
  const singular = isSystem ? 'Sistem' : 'Menu Delivery';

  const body = `
    <div class="field">
      <label>Judul<span class="req">*</span></label>
      <input type="text" class="input" id="mf_title" required maxlength="60" value="${esc(item ? item.title : '')}" />
    </div>
    <div class="field">
      <label>Deskripsi<span class="req">*</span></label>
      <textarea class="textarea" id="mf_desc" required maxlength="200">${esc(item ? item.desc : '')}</textarea>
    </div>
    <div class="field">
      <label>URL</label>
      <input type="text" class="input" id="mf_url" placeholder="Kosongkan atau isi # bila belum tersedia" value="${esc(item ? (item.url || '#') : '#')}" />
    </div>
    <div class="field">
      <label>Ikon</label>
      <input type="hidden" id="mf_icon" value="${esc(item ? item.icon : 'fa-clipboard-check')}" />
      <div class="icon-grid">
        ${ICONS.slice(0,16).map(ic => `<div class="icon-opt ${ic === (item ? item.icon : 'fa-clipboard-check') ? 'sel' : ''}" data-icon="${ic}"><i class="fa-solid ${ic}"></i></div>`).join('')}
      </div>
    </div>
    <div class="field">
      <label>Warna</label>
      <input type="hidden" id="mf_color" value="${esc(item ? item.color : '#3b82f6')}" />
      <div class="color-grid">
        ${COLORS.map(c => `<div class="color-opt ${c === (item ? item.color : '#3b82f6') ? 'sel' : ''}" style="background:${c}" data-color="${c}"></div>`).join('')}
      </div>
    </div>
  `;

  openModal((item ? 'Edit ' : 'Tambah ') + singular, body, async () => {
    const title = $('mf_title').value.trim();
    const desc  = $('mf_desc').value.trim();
    const url   = $('mf_url').value.trim() || '#';
    const icon  = $('mf_icon').value;
    const color = $('mf_color').value;
    if (!title) { toast('Judul wajib diisi', 'error'); $('mf_title').focus(); return; }
    if (!desc)  { toast('Deskripsi wajib diisi', 'error'); $('mf_desc').focus(); return; }

    if (isSystem) {
      const btn = $('modalSave');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';
      try {
        const payload = { title, description: desc, url, icon, color };
        if (item) {
          await API.put(BASE + `/admin/system-menus/${item.id}`, payload);
        } else {
          await API.post(BASE + '/admin/system-menus', payload);
        }
        toast('Sistem diperbarui', 'success');
        closeModal();
        await loadSystemMenu();
        renderMenuTable('systemMenu');
      } catch (e) {
        toast(e.message, 'error');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
      }
    } else {
      if (item) {
        Object.assign(item, { title, desc, url, icon, color });
        toast(singular + ' diperbarui', 'success');
      } else {
        list.push({ id:uid(), title, desc, url, icon, color });
        toast(singular + ' ditambahkan', 'success');
      }
      saveLocalState();
      closeModal();
      renderMenuTable(listKey);
      renderSidebar();
    }
  });
}

/* ============================================================
   DOKUMEN
   ============================================================ */
function refreshFilter() {
  const f = $('docsFilter');
  if (!f) return;
  const cur = f.value;
  f.innerHTML = '<option value="">Semua kategori</option>' +
    state.kategori.map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
  f.value = state.kategori.some(c => String(c.id) === cur) ? cur : '';
}

function renderDocs() {
  const el = $('docsTable');

  if (!state.documents.length) {
    el.innerHTML = `<div class="empty">
      <i class="fa-solid fa-file-circle-plus"></i>
      <b>Belum ada dokumen</b>
      <p>Klik tombol Upload Dokumen untuk menambahkan.</p>
    </div>`;
    return;
  }
  el.innerHTML = `
    <table>
      <thead><tr>
        <th>Judul</th><th>Kategori</th><th>File</th><th>Ukuran</th><th>Tanggal</th>
        <th style="width:150px;text-align:right">Aksi</th>
      </tr></thead>
      <tbody>
        ${state.documents.map(d => {
          const color = d.categoryColor || '#64748b';
          const name  = d.categoryName  || '—';
          return `<tr>
            <td>
              <div class="strong">${esc(d.title)}</div>
              ${d.desc ? `<div class="muted" style="margin-top:3px;font-size:12px">${esc(d.desc.substring(0,60))}${d.desc.length>60?'…':''}</div>` : ''}
            </td>
            <td><span class="tag" style="background:${color}22;color:${color};border:1px solid ${color}55">${esc(name)}</span></td>
            <td class="muted"><i class="fa-solid fa-file-pdf" style="color:#dc2626;margin-right:6px"></i>${esc(d.fileName)}</td>
            <td class="muted">${d._sizeHuman || fmtSize(d.fileSize)}</td>
            <td class="muted">${fmtDate(d.uploadedAt)}</td>
            <td>
              <div class="actions">
                <a class="btn btn-icon btn-sm" href="${BASE}/admin/${d.id}/documents/download" target="_blank" title="Unduh">
                  <i class="fa-solid fa-download"></i>
                </a>
                <button class="btn btn-icon btn-sm" onclick="editDoc(${d.id})" title="Edit">
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn btn-icon btn-sm danger" onclick="deleteDoc(${d.id})" title="Hapus">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>`;
        }).join('')}
      </tbody>
    </table>
  `;
}

$('docsSearch').addEventListener('input', debounce(() => { docPage = 1; loadDocuments(); }, 350));
$('docsFilter').addEventListener('change', () => { docPage = 1; loadDocuments(); });

$('uploadBtn').addEventListener('click', async () => {
  if (!state.kategori.length) {
    await loadCategories();
    if (!state.kategori.length) {
      toast('Buat kategori terlebih dahulu di Master Data', 'error');
      return;
    }
  }

  const catOpts = state.kategori.map(c =>
    `<option value="${c.id}">${esc(c.name)}</option>`
  ).join('');

  const body = `
    <div class="field">
      <label>Kategori<span class="req">*</span></label>
      <select class="select" id="uf_cat" required>
        <option value="">— Pilih kategori —</option>
        ${catOpts}
      </select>
    </div>
    <div class="field">
      <label>Judul Dokumen<span class="req">*</span></label>
      <input type="text" class="input" id="uf_title" required maxlength="120" />
    </div>
    <div class="field">
      <label>Deskripsi</label>
      <textarea class="textarea" id="uf_desc" maxlength="1000"></textarea>
    </div>
    <div class="field">
      <label>File PDF<span class="req">*</span></label>
      <label class="file" id="uf_drop">
        <input type="file" id="uf_file" accept="application/pdf,.pdf" required />
        <div class="fi"><i class="fa-solid fa-file-pdf"></i></div>
        <div class="ft" id="uf_fileName">Klik untuk pilih file PDF</div>
        <div class="fs" id="uf_fileInfo">PDF saja — maks 5 MB</div>
      </label>
    </div>
  `;

  openModal('Upload Dokumen', body, submitUpload);

  const drop = $('uf_drop');
  const fi   = $('uf_file');
  drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('drag'); });
  drop.addEventListener('dragleave', () => drop.classList.remove('drag'));
  drop.addEventListener('drop', e => {
    e.preventDefault(); drop.classList.remove('drag');
    if (e.dataTransfer.files[0]) {
      fi.files = e.dataTransfer.files;
      fi.dispatchEvent(new Event('change'));
    }
  });
  fi.addEventListener('change', () => {
    const f = fi.files[0];
    if (!f) return;
    $('uf_fileName').textContent = f.name;
    $('uf_fileInfo').textContent = (f.size / 1024).toFixed(1) + ' KB · siap diupload';
    drop.classList.add('has-file');
  });
});

async function submitUpload() {
  const catId = $('uf_cat').value;
  const title = $('uf_title').value.trim();
  const desc  = $('uf_desc').value.trim();
  const file  = $('uf_file').files[0];

  if (!catId) { toast('Pilih kategori', 'error'); return; }
  if (!title) { toast('Judul wajib diisi', 'error'); return; }
  if (!file)  { toast('Pilih file PDF', 'error'); return; }
  if (file.size > 5 * 1024 * 1024) { toast('File maksimal 5 MB', 'error'); return; }

  const btn = $('modalSave');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengupload…';

  const fd = new FormData();
  fd.append('category_id', catId);
  fd.append('title', title);
  fd.append('description', desc);
  fd.append('file', file);

  try {
    await API.post(BASE + '/admin/documents', fd, true);
    toast('Dokumen berhasil diupload', 'success');
    closeModal();
    docPage = 1;
    await loadCategories();
    await loadDocuments();
  } catch (e) {
    toast(e.message, 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
  }
}

window.editDoc = function (id) {
  const doc = state.documents.find(d => String(d.id) === String(id));
  if (!doc) return;

  const catOpts = state.kategori.map(c =>
    `<option value="${c.id}" ${String(c.id) === String(doc.kategoriId) ? 'selected' : ''}>${esc(c.name)}</option>`
  ).join('');

  const body = `
    <div class="field">
      <label>Kategori<span class="req">*</span></label>
      <select class="select" id="ef_cat" required>${catOpts}</select>
    </div>
    <div class="field">
      <label>Judul Dokumen<span class="req">*</span></label>
      <input type="text" class="input" id="ef_title" required maxlength="120" value="${esc(doc.title)}" />
    </div>
    <div class="field">
      <label>Deskripsi</label>
      <textarea class="textarea" id="ef_desc" maxlength="1000">${esc(doc.desc || '')}</textarea>
    </div>
    <div class="field">
      <label>File (tidak dapat diubah)</label>
      <input type="text" class="input" disabled value="${esc(doc.fileName)}" />
    </div>
  `;

  openModal('Edit Dokumen', body, () => submitEdit(id));
};

async function submitEdit(id) {
  const catId = $('ef_cat').value;
  const title = $('ef_title').value.trim();
  const desc  = $('ef_desc').value.trim();

  if (!catId) { toast('Pilih kategori', 'error'); return; }
  if (!title) { toast('Judul wajib diisi', 'error'); return; }

  const btn = $('modalSave');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';

  try {
    await API.put(BASE + `/admin/${id}/documents`, {
      category_id: catId,
      title: title,
      description: desc,
    });
    toast('Dokumen diperbarui', 'success');
    closeModal();
    await loadDocuments();
  } catch (e) {
    toast(e.message, 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
  }
}

window.deleteDoc = async function (id) {
  const doc = state.documents.find(d => String(d.id) === String(id));

  const ok = await showConfirm({
    title: 'Hapus Dokumen?',
    message: doc
      ? `<b>${esc(doc.title)}</b> beserta file PDF-nya akan dihapus permanen.\nTindakan ini tidak bisa dibatalkan.`
      : 'Dokumen beserta file PDF-nya akan dihapus permanen.',
    confirmText: 'Ya, Hapus',
    variant: 'danger',
  });
  if (!ok) return;

  try {
    await API.del(BASE + `/admin/${id}/documents`);
    toast('Dokumen dihapus', 'success');
    await loadDocuments();
  } catch (e) {
    toast(e.message, 'error');
  }
};

/* ============================================================
   JOB BOARD
   ============================================================ */
function renderJobForm() {
  $('jobTitle').value = state.jobConfig.title;
  $('jobDesc').value  = state.jobConfig.desc;
  $('jobUrl').value   = state.jobConfig.url || '#';
}
$('jobForm').addEventListener('submit', e => {
  e.preventDefault();
  state.jobConfig.title = $('jobTitle').value.trim();
  state.jobConfig.desc  = $('jobDesc').value.trim();
  state.jobConfig.url   = $('jobUrl').value.trim() || '#';
  saveLocalState();
  toast('Job Board diperbarui', 'success');
});

/* ============================================================
   SETTINGS
   ============================================================ */
function renderSettingsForm() {
  $('setName').value = state.settings.siteName;
  $('setSub').value  = state.settings.siteSubtitle;
}
$('settingsForm').addEventListener('submit', e => {
  e.preventDefault();
  state.settings.siteName     = $('setName').value.trim();
  state.settings.siteSubtitle = $('setSub').value.trim();
  saveLocalState();
  toast('Pengaturan disimpan', 'success');
});
$('resetBtn').addEventListener('click', async () => {
  const ok = await showConfirm({
    title: 'Reset Data Lokal?',
    message: 'Semua data lokal (vendor, driver, kendaraan, menu, pengaturan) akan dikembalikan ke default.\n\nData kategori, item, kendaraan, dokumen di server tidak akan terhapus.',
    confirmText: 'Ya, Reset',
    variant: 'warn',
    icon: 'fa-solid fa-rotate-left',
  });
  if (!ok) return;

  localStorage.removeItem(LOCAL_KEY);
  location.reload();
});

/* ============================================================
   REFRESH
   ============================================================ */
$('refreshBtn').addEventListener('click', async () => {
  toast('Memuat ulang…', 'info');
  await Promise.all([
    loadCategories(), loadItems(), loadVehicles(),
    loadVendors(), loadDrivers(), loadRoutes(), loadSystemMenu(),
    loadUsers()
  ]);
  await loadDocuments();
  renderSidebar();
  renderDashboard();
  toast('Data dimuat ulang', 'success');
});

/* ============================================================
   THEME
   ============================================================ */
const root = document.documentElement;
const themeBtn = $('themeBtn');
function applyTheme(t) {
  root.dataset.theme = t;
  themeBtn.innerHTML = t === 'dark'
    ? '<i class="fa-solid fa-sun"></i>'
    : '<i class="fa-solid fa-moon"></i>';
}
const savedTheme = localStorage.getItem(THEME_KEY) || 'light';
applyTheme(savedTheme);
themeBtn.addEventListener('click', () => {
  const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
  applyTheme(next);
  localStorage.setItem(THEME_KEY, next);
});

/* ============================================================
   LOGOUT
   ============================================================ */
const LOGOUT_URL = BASE + '/auth/logout';

$('logoutBtn').addEventListener('click', async () => {
  const ok = await showConfirm({
    title: 'Keluar dari Admin Panel?',
    message: 'Anda akan keluar dari sesi ini. Pastikan semua perubahan sudah tersimpan.',
    confirmText: 'Ya, Keluar',
    variant: 'warn',
    icon: 'fa-solid fa-right-from-bracket',
  });
  if (!ok) return;

  const btn = $('logoutBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

  try {
    const res = await fetch(LOGOUT_URL, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
    });

    let json = null;
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      try { json = await res.json(); } catch (_) {}
    }

    const target = json?.redirect || BASE + '/login';
    window.location.href = target;
  } catch (e) {
    window.location.href = BASE + '/login';
  }
});

/* ============================================================
   ROLE PERMISSIONS (STRICT FRONTEND RESTRICTION)
   ============================================================ */
function applyRolePermissions() {
    // Jika SUPERADMIN, tampilkan semua menu (biarkan default)
    if (USER_ROLE.toUpperCase() === 'SUPERADMIN') {
        return; 
    }

    // Untuk ADMIN/VIEWER:
    // Sembunyikan Dashboard, Master Data, Konten, Pengaturan
    // Biarkan menu Operasional Delivery (termasuk link "Coming soon") tetap tampil.
    const hideElements = [
        'lblDashboard', 'navDashboard',
        'lblMaster', 'navMaster',
        'lblKonten', 'navKonten',
        'lblPengaturan', 'navPengaturan'
    ];

    hideElements.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    // JANGAN melakukan redirect ke /silog/admin/actual-delivery
    // Biarkan user tetap di halaman Admin Panel (SPA)
}

/* ============================================================
   INIT
   ============================================================ */
(async function init() {
  // Terapkan permission role terlebih dahulu (mencegah render menu yang salah)
  applyRolePermissions();

  loadLocalState();
  renderSidebar();
  renderDashboard();
  
  // Panggil goAdmin('dashboard') agar state panel tetap rapi.
  // Karena menu Dashboard disembunyikan untuk ADMIN, ini hanya menampilkan panel Ringkasan di kanan.
  goAdmin('dashboard');

  await Promise.all([
    loadCategories(),
    loadItems(),
    loadVehicles(),
    loadVendors(),
    loadDrivers(),
    loadRoutes(),
    loadSystemMenu(),
    loadUsers()
  ]);
  await loadDocuments();

  if (document.querySelector('.panel[data-panel="m_kategori"]')?.classList.contains('active')) {
    renderMasterTable('kategori');
  }
})();
</script>
</body>
</html>