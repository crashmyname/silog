<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>LogiBoard · Papan Informasi Logistik</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<style>
/* ============================================================
   THEME TOKENS
   ============================================================ */
:root{
  --bg:#070b14; --bg-2:#0b1220;
  --surface:rgba(255,255,255,.045); --surface-2:rgba(255,255,255,.07);
  --surface-solid:#101828;
  --border:rgba(255,255,255,.09); --border-strong:rgba(255,255,255,.16);
  --text:#eaf0fb; --text-2:#9aa8c0; --text-3:#67748e;

  --brand:#3b82f6; --brand-2:#8b5cf6;
  --green:#22c55e; --amber:#f59e0b; --red:#ef4444;
  --cyan:#06b6d4; --pink:#ec4899; --purple:#a855f7;

  --radius:20px; --radius-sm:14px;
  --shadow:0 18px 48px rgba(0,0,0,.5);
  --shadow-sm:0 8px 22px rgba(0,0,0,.35);
  --glow:0 0 0 1px rgba(255,255,255,.04), 0 20px 50px rgba(0,0,0,.45);
  --base-font:15px; --title-font:17px;
  --hero-font:clamp(28px,3.8vw,44px);
  --card-title:19px; --num-font:26px;
  --ambient:1; --grid-opacity:1;
}
html[data-theme="light"]{
  --bg:#f7f9fc; --bg-2:#eef2f8;
  --surface:#ffffff; --surface-2:#f4f7fb; --surface-solid:#ffffff;
  --border:#e2e8f0; --border-strong:#cbd5e1;
  --text:#0b1424; --text-2:#475569; --text-3:#64748b;

  --brand:#1d4ed8; --brand-2:#6d28d9;
  --green:#15803d; --amber:#b45309; --red:#b91c1c;
  --cyan:#0e7490; --pink:#be185d; --purple:#7e22ce;

  --shadow:0 10px 30px rgba(15,23,42,.08);
  --shadow-sm:0 4px 14px rgba(15,23,42,.06);
  --glow:0 4px 14px rgba(15,23,42,.06);
  --base-font:16px; --title-font:18px;
  --card-title:20px; --num-font:28px;
  --ambient:0; --grid-opacity:0;
}

/* ============================================================
   BASE
   ============================================================ */
*{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%}
body{
  font-family:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;
  font-size:var(--base-font); line-height:1.5;
  background:var(--bg); color:var(--text);
  min-height:100vh; -webkit-font-smoothing:antialiased;
  overflow-x:hidden; transition:background .4s,color .4s;
}
body::before{
  content:"";position:fixed;inset:0;z-index:-2;pointer-events:none;
  background:
    radial-gradient(820px 520px at 8% -8%,  rgba(59,130,246,.28), transparent 60%),
    radial-gradient(760px 520px at 96% 0%,   rgba(139,92,246,.22), transparent 60%),
    radial-gradient(900px 600px at 50% 110%, rgba(6,182,212,.16), transparent 62%);
  opacity:var(--ambient); transition:opacity .4s;
}
body::after{
  content:"";position:fixed;inset:0;z-index:-1;pointer-events:none;
  background-image:
    linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);
  background-size:56px 56px; opacity:var(--grid-opacity);
  -webkit-mask-image:radial-gradient(circle at 50% 30%,#000 0%,transparent 78%);
          mask-image:radial-gradient(circle at 50% 30%,#000 0%,transparent 78%);
}
.app{max-width:1500px;margin:0 auto;padding:22px 22px 40px}

/* ============================================================
   TOPBAR
   ============================================================ */
.topbar{
  display:flex;align-items:center;justify-content:space-between;gap:20px;
  background:var(--surface); border:1px solid var(--border);
  border-radius:var(--radius); padding:16px 22px;
  backdrop-filter:blur(20px) saturate(160%);
  box-shadow:var(--shadow-sm);
  position:sticky; top:14px; z-index:40;
}
.brand{display:flex;align-items:center;gap:14px;min-width:0}
.logo{
  width:52px;height:52px;flex:none;border-radius:14px;display:grid;place-items:center;
  font-size:22px;color:#fff;position:relative;overflow:hidden;
  background:linear-gradient(135deg,#3b82f6,#8b5cf6);
  box-shadow:0 10px 26px rgba(59,130,246,.42);
}
.logo::after{
  content:"";position:absolute;inset:0;
  background:linear-gradient(120deg,transparent 30%,rgba(255,255,255,.35),transparent 70%);
  transform:translateX(-120%);animation:sheen 4.5s ease-in-out infinite;
}
@keyframes sheen{0%,60%{transform:translateX(-120%)}100%{transform:translateX(120%)}}
.brand h1{font-size:var(--title-font);font-weight:800;letter-spacing:-.3px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.brand p{font-size:13px;color:var(--text-2);margin-top:3px}

.top-right{display:flex;align-items:center;gap:14px}
.clock-box{text-align:right;line-height:1.15;padding-right:6px}
.clock-box .t{
  font-size:24px;font-weight:800;font-variant-numeric:tabular-nums;letter-spacing:.4px;
  background:linear-gradient(90deg,#60a5fa,#a78bfa);
  -webkit-background-clip:text;background-clip:text;color:transparent;
}
html[data-theme="light"] .clock-box .t{
  background:linear-gradient(90deg,#1d4ed8,#6d28d9);
  -webkit-background-clip:text;background-clip:text;color:transparent;
}
.clock-box .d{font-size:12.5px;color:var(--text-2);margin-top:3px}

.icon-btn{
  width:48px;height:48px;border-radius:14px;cursor:pointer;
  border:1px solid var(--border);background:var(--surface-2);color:var(--text);
  font-size:17px;display:grid;place-items:center;text-decoration:none;
  transition:transform .22s,border-color .22s,background .22s;
}
.icon-btn:hover{transform:translateY(-2px);border-color:var(--brand)}
.icon-btn.admin-btn{
  background:linear-gradient(135deg,rgba(59,130,246,.15),rgba(139,92,246,.15));
  border-color:color-mix(in srgb,var(--brand) 40%,transparent);
  color:var(--brand);
}
.icon-btn.admin-btn:hover{
  background:linear-gradient(135deg,rgba(59,130,246,.25),rgba(139,92,246,.25));
}

/* ============================================================
   PAGE SYSTEM
   ============================================================ */
.page{display:none;animation:pageIn .45s cubic-bezier(.22,1,.36,1) both}
.page.active{display:block}
@keyframes pageIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}

/* ============================================================
   HOME HERO
   ============================================================ */
.hero{
  margin-top:26px;padding:38px 34px;border-radius:24px;position:relative;overflow:hidden;
  background:linear-gradient(135deg,rgba(59,130,246,.18),rgba(139,92,246,.12) 55%,rgba(6,182,212,.10));
  border:1px solid var(--border); box-shadow:var(--glow);
}
html[data-theme="light"] .hero{
  background:linear-gradient(135deg,#eef4ff,#f3efff 55%,#eaf7fb);
  border:1px solid #e2e8f0;
}
.hero::before{
  content:"";position:absolute;right:-90px;top:-90px;width:340px;height:340px;
  border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,.35),transparent 65%);
  filter:blur(10px);pointer-events:none;opacity:var(--ambient);
}
.hero-inner{position:relative;z-index:1;display:flex;justify-content:space-between;
  align-items:center;gap:32px;flex-wrap:wrap}
.hero-text{max-width:680px}
.hero .badge{
  display:inline-flex;align-items:center;gap:9px;font-size:12.5px;font-weight:700;
  letter-spacing:.4px;text-transform:uppercase;color:var(--brand);
  background:color-mix(in srgb,var(--brand) 12%,transparent);
  border:1px solid color-mix(in srgb,var(--brand) 28%,transparent);
  padding:7px 14px;border-radius:999px;
}
.hero .badge .pulse{
  width:7px;height:7px;border-radius:50%;background:var(--green);
  box-shadow:0 0 0 4px color-mix(in srgb,var(--green) 25%,transparent);
  animation:pulse 1.8s ease-in-out infinite;
}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.8)}}
.hero h2{font-size:var(--hero-font);font-weight:800;letter-spacing:-1.2px;
  line-height:1.14;margin:16px 0 12px;color:var(--text)}
.hero h2 span{
  background:linear-gradient(90deg,#60a5fa,#a78bfa,#22d3ee);
  -webkit-background-clip:text;background-clip:text;color:transparent;
}
html[data-theme="light"] .hero h2 span{
  background:linear-gradient(90deg,#1d4ed8,#6d28d9,#0e7490);
  -webkit-background-clip:text;background-clip:text;color:transparent;
}
.hero p{font-size:15.5px;color:var(--text-2);line-height:1.7;max-width:580px}
.hero-stats{display:flex;gap:30px;flex-wrap:wrap}
.hero-stats .st{min-width:90px}
.hero-stats .st b{display:block;font-size:28px;font-weight:800;letter-spacing:-.8px;
  font-variant-numeric:tabular-nums;color:var(--text)}
.hero-stats .st small{font-size:12.5px;color:var(--text-2);font-weight:500}

/* ============================================================
   SECTION TITLE
   ============================================================ */
.section-title{
  display:flex;align-items:center;justify-content:space-between;
  gap:14px;margin:38px 4px 20px;flex-wrap:wrap;
}
.section-title h3{font-size:18px;font-weight:800;letter-spacing:-.3px;
  display:flex;align-items:center;gap:11px;color:var(--text)}
.section-title h3 i{color:var(--brand)}
.section-title p{font-size:13.5px;color:var(--text-2)}

/* ============================================================
   CARD GRID
   ============================================================ */
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:22px}
.card{
  position:relative;overflow:hidden;cursor:pointer;
  border-radius:22px;padding:26px;
  background:var(--surface);border:1px solid var(--border);
  backdrop-filter:blur(18px) saturate(160%);
  box-shadow:var(--shadow-sm);
  transition:transform .32s cubic-bezier(.22,1,.36,1),border-color .32s,box-shadow .32s;
  animation:cardIn .55s cubic-bezier(.22,1,.36,1) both;
  isolation:isolate;
}
html[data-theme="light"] .card{background:#fff;box-shadow:0 4px 14px rgba(15,23,42,.06)}
@keyframes cardIn{from{opacity:0;transform:translateY(22px) scale(.98)}to{opacity:1;transform:none}}
.card::before{
  content:"";position:absolute;inset:0;z-index:-1;opacity:0;
  background:radial-gradient(420px 260px at var(--mx,50%) var(--my,50%),
    color-mix(in srgb,var(--tone) 26%,transparent),transparent 65%);
  transition:opacity .35s;
}
.card:hover::before{opacity:var(--ambient)}
.card:hover{
  transform:translateY(-8px);
  border-color:color-mix(in srgb,var(--tone) 55%,transparent);
  box-shadow:0 26px 60px color-mix(in srgb,var(--tone) 28%,transparent);
}
html[data-theme="light"] .card:hover{box-shadow:0 20px 42px rgba(15,23,42,.14)}
.card:active{transform:translateY(-4px) scale(.995)}
.card .top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.card .icon{
  width:60px;height:60px;border-radius:17px;display:grid;place-items:center;
  font-size:24px;color:#fff;flex:none;
  background:linear-gradient(135deg,var(--tone),color-mix(in srgb,var(--tone) 55%,#000));
  box-shadow:0 12px 28px color-mix(in srgb,var(--tone) 42%,transparent);
  transition:transform .35s cubic-bezier(.22,1,.36,1);
}
.card:hover .icon{transform:rotate(-6deg) scale(1.06)}
.card .arrow{
  width:38px;height:38px;border-radius:12px;display:grid;place-items:center;
  font-size:13px;color:var(--text-2);
  background:var(--surface-2);border:1px solid var(--border);
  transition:transform .3s,color .3s,border-color .3s,background .3s;
}
.card:hover .arrow{transform:translate(3px,-3px);color:#fff;background:var(--tone);border-color:var(--tone)}
.card h4{font-size:var(--card-title);font-weight:800;letter-spacing:-.4px;
  margin-top:20px;color:var(--text)}
.card .desc{font-size:14px;color:var(--text-2);line-height:1.65;margin-top:9px;min-height:44px}
.card .meta{
  display:flex;align-items:center;gap:10px;margin-top:20px;
  padding-top:18px;border-top:1px dashed var(--border);
}
.card .meta .num{font-size:var(--num-font);font-weight:800;letter-spacing:-.8px;
  font-variant-numeric:tabular-nums;color:var(--text)}
.card .meta .lbl{font-size:12.5px;color:var(--text-2);line-height:1.35}
.card .meta .tag{
  margin-left:auto;font-size:11px;font-weight:800;letter-spacing:.5px;
  text-transform:uppercase;padding:6px 12px;border-radius:999px;color:var(--tone);
  background:color-mix(in srgb,var(--tone) 12%,transparent);
  border:1px solid color-mix(in srgb,var(--tone) 30%,transparent);
}

/* sub-menu smaller card */
.menu-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:18px}
.menu-card{
  position:relative;cursor:pointer;border-radius:18px;padding:22px;
  background:var(--surface);border:1px solid var(--border);
  backdrop-filter:blur(16px);box-shadow:var(--shadow-sm);
  transition:transform .28s cubic-bezier(.22,1,.36,1),border-color .28s,box-shadow .28s;
  animation:cardIn .5s cubic-bezier(.22,1,.36,1) both;
  display:flex;flex-direction:column;
}
html[data-theme="light"] .menu-card{background:#fff}
.menu-card:hover{
  transform:translateY(-5px);
  border-color:color-mix(in srgb,var(--tone) 55%,transparent);
  box-shadow:0 18px 42px color-mix(in srgb,var(--tone) 22%,transparent);
}
html[data-theme="light"] .menu-card:hover{box-shadow:0 16px 34px rgba(15,23,42,.12)}
.menu-card .mi{
  width:52px;height:52px;border-radius:15px;display:grid;place-items:center;
  font-size:20px;color:#fff;margin-bottom:16px;
  background:linear-gradient(135deg,var(--tone),color-mix(in srgb,var(--tone) 55%,#000));
  box-shadow:0 10px 24px color-mix(in srgb,var(--tone) 38%,transparent);
  transition:transform .3s;
}
.menu-card:hover .mi{transform:scale(1.06) rotate(-4deg)}
.menu-card h4{font-size:16.5px;font-weight:800;letter-spacing:-.3px;color:var(--text)}
.menu-card p{font-size:13px;color:var(--text-2);line-height:1.6;margin-top:7px;flex:1}
.menu-card .go{
  display:inline-flex;align-items:center;gap:8px;margin-top:16px;
  font-size:12.5px;font-weight:700;color:var(--tone);
}
.menu-card .go i{transition:transform .25s}
.menu-card:hover .go i{transform:translateX(4px)}

/* ============================================================
   DETAIL HEAD
   ============================================================ */
.detail-head{
  display:flex;align-items:center;gap:20px;flex-wrap:wrap;
  margin-top:22px;padding:24px 26px;border-radius:22px;
  background:var(--surface);border:1px solid var(--border);
  backdrop-filter:blur(18px) saturate(160%);
  box-shadow:var(--shadow-sm);
}
html[data-theme="light"] .detail-head{background:#fff}
.back-btn{
  display:inline-flex;align-items:center;gap:10px;cursor:pointer;
  font-size:14px;font-weight:700;color:var(--text);
  padding:13px 20px;border-radius:13px;font-family:inherit;
  background:var(--surface-2);border:1px solid var(--border);
  transition:transform .2s,border-color .2s,background .2s;
}
.back-btn:hover{transform:translateX(-3px);border-color:var(--brand)}
.detail-title{display:flex;align-items:center;gap:16px;flex:1;min-width:240px}
.detail-title .icon{
  width:58px;height:58px;border-radius:16px;display:grid;place-items:center;
  font-size:22px;color:#fff;flex:none;
  background:linear-gradient(135deg,var(--tone),color-mix(in srgb,var(--tone) 55%,#000));
  box-shadow:0 12px 26px color-mix(in srgb,var(--tone) 40%,transparent);
}
.detail-title h2{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--text)}
.detail-title p{font-size:13.5px;color:var(--text-2);margin-top:4px}

/* ============================================================
   ADMIN PANEL CARD (dipakai untuk view dokumen publik juga)
   ============================================================ */
.admin-card{
  border-radius:18px;padding:22px;
  background:var(--surface);border:1px solid var(--border);
  backdrop-filter:blur(16px);box-shadow:var(--shadow-sm);
  animation:cardIn .5s cubic-bezier(.22,1,.36,1) both;
}
html[data-theme="light"] .admin-card{background:#fff}
.admin-card > h3{
  font-size:15.5px;font-weight:800;letter-spacing:-.3px;color:var(--text);
  display:flex;align-items:center;gap:10px;margin-bottom:18px;
}
.admin-card > h3 i{color:var(--tone,var(--brand))}

.form-group{margin-bottom:16px}
.form-group label{
  display:block;font-size:13px;font-weight:600;margin-bottom:8px;
  color:var(--text-2);
}
.form-control{
  width:100%;padding:12px 14px;font-size:14px;font-family:inherit;
  background:var(--surface-2);color:var(--text);
  border:1px solid var(--border);border-radius:11px;
  transition:border-color .2s,box-shadow .2s,background .2s;
}
.form-control:focus{
  outline:none;border-color:var(--brand);
  box-shadow:0 0 0 3px color-mix(in srgb,var(--brand) 20%,transparent);
}
textarea.form-control{resize:vertical;min-height:80px;line-height:1.55}
select.form-control{cursor:pointer;appearance:none;
  background-image:linear-gradient(45deg,transparent 50%,var(--text-2) 50%),
                   linear-gradient(135deg,var(--text-2) 50%,transparent 50%);
  background-position:calc(100% - 18px) 50%,calc(100% - 13px) 50%;
  background-size:5px 5px,5px 5px;background-repeat:no-repeat;
  padding-right:38px;
}

/* ============================================================
   BUTTONS
   ============================================================ */
.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:9px;
  padding:12px 20px;font-size:14px;font-weight:700;font-family:inherit;
  border-radius:11px;cursor:pointer;border:none;text-decoration:none;
  transition:transform .2s,opacity .2s,background .2s;
}
.btn-block{width:100%}
.btn-primary{background:var(--brand);color:#fff}
.btn-primary:hover{transform:translateY(-2px);opacity:.92}
.btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none}
.btn-ghost{
  background:var(--surface-2);color:var(--text);
  border:1px solid var(--border);
}
.btn-ghost:hover{border-color:var(--brand)}
.btn-sm{padding:8px 12px;font-size:12.5px;border-radius:9px}

/* ============================================================
   DOCUMENT LIST
   ============================================================ */
.doc-toolbar{
  display:flex;gap:12px;align-items:center;flex-wrap:wrap;
  margin-bottom:16px;
}
.doc-toolbar .form-control{max-width:260px;flex:1;min-width:180px}
.doc-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
.doc-item{
  background:var(--surface-2);border:1px solid var(--border);
  border-radius:14px;padding:16px;
  display:flex;flex-direction:column;gap:12px;
  transition:transform .2s,border-color .2s,box-shadow .2s;
  animation:cardIn .45s cubic-bezier(.22,1,.36,1) both;
}
.doc-item:hover{
  transform:translateY(-3px);
  border-color:color-mix(in srgb,var(--tone) 50%,transparent);
  box-shadow:0 14px 30px color-mix(in srgb,var(--tone) 18%,transparent);
}
html[data-theme="light"] .doc-item:hover{box-shadow:0 12px 28px rgba(15,23,42,.10)}
.doc-item .dh{display:flex;align-items:flex-start;gap:12px}
.doc-item .df{
  width:44px;height:44px;flex:none;border-radius:12px;display:grid;place-items:center;
  font-size:18px;color:#fff;
  background:linear-gradient(135deg,var(--tone),color-mix(in srgb,var(--tone) 55%,#000));
}
.doc-item .dt{flex:1;min-width:0}
.doc-item .dt b{
  display:block;font-size:13.5px;font-weight:700;color:var(--text);
  word-break:break-word;
}
.doc-item .dt small{
  display:block;font-size:11.5px;color:var(--text-2);margin-top:4px;
  word-break:break-all;
}
.doc-item .dd{
  font-size:12.5px;color:var(--text-2);line-height:1.55;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
  overflow:hidden;
}
.doc-item .dm{
  display:flex;align-items:center;justify-content:space-between;gap:10px;
  padding-top:12px;border-top:1px dashed var(--border);
}
.doc-item .dm .cattag{
  font-size:10.5px;font-weight:800;letter-spacing:.3px;text-transform:uppercase;
  padding:4px 9px;border-radius:6px;color:var(--tone);
  background:color-mix(in srgb,var(--tone) 14%,transparent);
  border:1px solid color-mix(in srgb,var(--tone) 30%,transparent);
}
.doc-item .dm .size{font-size:11.5px;color:var(--text-3)}

.empty{
  text-align:center;padding:44px 20px;border-radius:14px;
  border:1px dashed var(--border-strong);background:var(--surface-2);
  grid-column:1/-1;
}
.empty i{font-size:36px;color:var(--text-3);margin-bottom:14px;display:block}
.empty b{display:block;font-size:14.5px;font-weight:700;color:var(--text);margin-bottom:6px}
.empty p{font-size:12.5px;color:var(--text-2);max-width:380px;margin:0 auto;line-height:1.6}

/* ============================================================
   PAGINATION
   ============================================================ */
.pagination{
  display:flex;gap:8px;justify-content:center;flex-wrap:wrap;
  margin-top:22px;width:100%;
}
.pagination .page-btn{
  min-width:38px;height:38px;padding:0 12px;border-radius:9px;
  font-size:13px;font-weight:700;font-family:inherit;
  display:inline-flex;align-items:center;justify-content:center;
  background:var(--surface-2);color:var(--text);
  border:1px solid var(--border);cursor:pointer;
  transition:all .2s;
}
.pagination .page-btn:hover:not(:disabled){border-color:var(--brand);color:var(--brand)}
.pagination .page-btn.active{
  background:var(--brand);color:#fff;border-color:var(--brand);
}
.pagination .page-btn:disabled{opacity:.4;cursor:not-allowed}

/* ============================================================
   PLACEHOLDER (sub pages & job)
   ============================================================ */
.placeholder{
  margin-top:20px;padding:60px 30px;border-radius:22px;text-align:center;
  background:var(--surface);border:1px solid var(--border);
  backdrop-filter:blur(16px);box-shadow:var(--shadow-sm);
  animation:cardIn .5s cubic-bezier(.22,1,.36,1) both;
}
html[data-theme="light"] .placeholder{background:#fff}
.placeholder .ph-icon{
  width:90px;height:90px;margin:0 auto 22px;border-radius:24px;
  display:grid;place-items:center;font-size:36px;color:#fff;
  background:linear-gradient(135deg,var(--tone),color-mix(in srgb,var(--tone) 55%,#000));
  box-shadow:0 16px 40px color-mix(in srgb,var(--tone) 42%,transparent);
}
.placeholder h2{font-size:24px;font-weight:800;letter-spacing:-.6px;color:var(--text)}
.placeholder p{
  font-size:14.5px;color:var(--text-2);margin-top:10px;max-width:520px;
  margin-left:auto;margin-right:auto;line-height:1.7;
}
.placeholder .pill-soon{
  display:inline-flex;align-items:center;gap:8px;margin-top:22px;
  padding:9px 16px;border-radius:999px;
  font-size:12px;font-weight:800;letter-spacing:.4px;text-transform:uppercase;
  color:var(--amber);
  background:color-mix(in srgb,var(--amber) 12%,transparent);
  border:1px solid color-mix(in srgb,var(--amber) 34%,transparent);
}
.placeholder .pill-soon i{animation:blink 1.8s ease-in-out infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.25}}

.placeholder a.pill-soon.link{
  color:var(--brand);
  background:color-mix(in srgb,var(--brand) 12%,transparent);
  border-color:color-mix(in srgb,var(--brand) 34%,transparent);
  text-decoration:none;
  cursor:pointer;
}
.placeholder a.pill-soon.link:hover{
  background:color-mix(in srgb,var(--brand) 22%,transparent);
}

/* Job page big CTA */
.job-hero{
  margin-top:20px;padding:50px 34px;border-radius:24px;
  background:linear-gradient(135deg,rgba(245,158,11,.16),rgba(236,72,153,.12) 60%,rgba(168,85,247,.12));
  border:1px solid var(--border);box-shadow:var(--glow);text-align:center;
  animation:cardIn .55s cubic-bezier(.22,1,.36,1) both;
}
html[data-theme="light"] .job-hero{
  background:linear-gradient(135deg,#fff5e6,#fdeaf3 60%,#f3ebff);
  border:1px solid #e2e8f0;
}
.job-hero .jh-icon{
  width:96px;height:96px;margin:0 auto 22px;border-radius:26px;
  display:grid;place-items:center;font-size:40px;color:#fff;
  background:linear-gradient(135deg,#f59e0b,#ec4899);
  box-shadow:0 18px 40px rgba(245,158,11,.42);
}
.job-hero h2{font-size:clamp(22px,2.8vw,32px);font-weight:800;letter-spacing:-.7px;color:var(--text)}
.job-hero p{font-size:15px;color:var(--text-2);margin:14px auto 26px;max-width:580px;line-height:1.7}
.job-hero a.job-cta{
  display:inline-flex;align-items:center;gap:11px;text-decoration:none;
  padding:16px 32px;border-radius:14px;
  font-size:15px;font-weight:800;color:#fff;
  background:linear-gradient(135deg,#f59e0b,#ec4899);
  box-shadow:0 14px 32px rgba(245,158,11,.40);
  transition:transform .25s,box-shadow .25s;
}
.job-hero a.job-cta:hover{
  transform:translateY(-3px);
  box-shadow:0 20px 42px rgba(245,158,11,.5);
}
.job-hero .note{
  margin-top:22px;font-size:12.5px;color:var(--text-3);
  display:inline-flex;align-items:center;gap:8px;
}

/* ============================================================
   TICKER
   ============================================================ */
.ticker{
  margin-top:34px;overflow:hidden;border-radius:18px;
  background:var(--surface);border:1px solid var(--border);
  backdrop-filter:blur(14px);box-shadow:var(--shadow-sm);
  padding:15px 0;
  -webkit-mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent);
          mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent);
}
html[data-theme="light"] .ticker{background:#fff}
.ticker-track{display:flex;gap:58px;width:max-content;animation:scroll 46s linear infinite}
.ticker:hover .ticker-track{animation-play-state:paused}
.ticker-item{font-size:13.5px;color:var(--text-2);white-space:nowrap;
  display:flex;align-items:center;gap:10px}
.ticker-item i{color:var(--brand)}
.ticker-item b{color:var(--text);font-weight:700}
@keyframes scroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}

/* ============================================================
   TOAST
   ============================================================ */
.toast{
  position:fixed;bottom:24px;left:50%;transform:translate(-50%,80px);
  padding:14px 22px;border-radius:14px;font-size:14px;font-weight:600;
  background:var(--surface-solid);color:var(--text);
  border:1px solid var(--border);box-shadow:var(--shadow);
  display:flex;align-items:center;gap:10px;
  opacity:0;transition:all .3s cubic-bezier(.22,1,.36,1);
  z-index:1000;backdrop-filter:blur(20px);
  pointer-events:none;max-width:90vw;
}
.toast.show{transform:translate(-50%,0);opacity:1}
.toast-success{border-color:color-mix(in srgb,var(--green) 45%,transparent);color:var(--green)}
.toast-error{border-color:color-mix(in srgb,var(--red) 45%,transparent);color:var(--red)}
.toast-info{border-color:color-mix(in srgb,var(--brand) 45%,transparent);color:var(--brand)}

/* ============================================================
   FOOTER
   ============================================================ */
.footer{
  margin-top:30px;text-align:center;font-size:12.5px;color:var(--text-3);
  display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;
}
.footer .sep{opacity:.5}

/* ============================================================
   SKELETON LOADER
   ============================================================ */
.skeleton{
  background:linear-gradient(90deg,
    var(--surface-2) 25%,
    var(--surface) 50%,
    var(--surface-2) 75%);
  background-size:200% 100%;
  animation:skel 1.4s ease-in-out infinite;
  border-radius:8px;
}
@keyframes skel{0%{background-position:200% 0}100%{background-position:-200% 0}}
.skeleton-card{
  height:180px;border-radius:14px;
  background:linear-gradient(90deg,
    var(--surface-2) 25%,
    var(--surface) 50%,
    var(--surface-2) 75%);
  background-size:200% 100%;
  animation:skel 1.4s ease-in-out infinite;
}

/* ============================================================
   PDF PREVIEW MODAL
   ============================================================ */
.pdf-modal-bg{
  position:fixed;inset:0;z-index:200;
  background:rgba(7,11,20,.75);
  backdrop-filter:blur(6px);
  display:none;align-items:center;justify-content:center;
  padding:24px;
  animation:pdfFade .25s ease both;
}
.pdf-modal-bg.active{display:flex}
html[data-theme="light"] .pdf-modal-bg{background:rgba(15,23,42,.55)}
@keyframes pdfFade{from{opacity:0}to{opacity:1}}

.pdf-modal{
  width:100%;max-width:1100px;height:92vh;
  background:var(--surface-solid);
  border:1px solid var(--border);
  border-radius:18px;
  box-shadow:0 30px 80px rgba(0,0,0,.6);
  display:flex;flex-direction:column;overflow:hidden;
  animation:pdfScale .3s cubic-bezier(.22,1,.36,1) both;
}
@keyframes pdfScale{
  from{opacity:0;transform:scale(.96) translateY(20px)}
  to{opacity:1;transform:none}
}
html[data-theme="light"] .pdf-modal{
  box-shadow:0 30px 80px rgba(15,23,42,.3);
}

.pdf-modal-head{
  padding:16px 20px;flex:none;
  display:flex;align-items:center;gap:14px;
  border-bottom:1px solid var(--border);
  background:var(--surface);
}
.pdf-modal-head .pdf-icon{
  width:42px;height:42px;flex:none;border-radius:11px;
  display:grid;place-items:center;
  background:linear-gradient(135deg,#dc2626,#991b1b);
  color:#fff;font-size:17px;
}
.pdf-modal-head .pdf-meta{flex:1;min-width:0}
.pdf-modal-head .pdf-meta h3{
  font-size:15px;font-weight:800;letter-spacing:-.2px;
  color:var(--text);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.pdf-modal-head .pdf-meta p{
  font-size:12px;color:var(--text-2);margin-top:3px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.pdf-modal-actions{
  display:flex;align-items:center;gap:8px;flex:none;
}
.pdf-modal-actions .icon-act{
  width:38px;height:38px;border-radius:10px;
  display:grid;place-items:center;cursor:pointer;
  background:var(--surface-2);color:var(--text-2);
  border:1px solid var(--border);
  font-size:14px;text-decoration:none;
  transition:all .2s;
}
.pdf-modal-actions .icon-act:hover{
  color:var(--brand);border-color:var(--brand);
  transform:translateY(-2px);
}
.pdf-modal-actions .icon-act.close:hover{
  color:var(--red);border-color:var(--red);
}

.pdf-modal-body{
  flex:1;min-height:0;position:relative;
  background:var(--bg);
}
.pdf-modal-body iframe{
  width:100%;height:100%;border:0;
  background:#fff;
}

/* Loading spinner di dalam iframe container */
.pdf-loading{
  position:absolute;inset:0;
  display:flex;flex-direction:column;
  align-items:center;justify-content:center;gap:14px;
  background:var(--bg);
  color:var(--text-2);font-size:13px;
  z-index:2;
  transition:opacity .3s;
}
.pdf-loading.hide{opacity:0;pointer-events:none}
.pdf-loading .spinner{
  width:38px;height:38px;border-radius:50%;
  border:3px solid var(--border);
  border-top-color:var(--brand);
  animation:spin .8s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}

/* Mobile */
@media(max-width:760px){
  .pdf-modal-bg{padding:0}
  .pdf-modal{
    max-width:100%;height:100vh;
    border-radius:0;border:none;
  }
} 

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media(max-width:760px){
  .app{padding:14px 14px 32px}
  .topbar{flex-direction:column;align-items:flex-start;padding:16px 18px;position:static}
  .top-right{width:100%;justify-content:space-between}
  .clock-box{text-align:left}
  .hero{padding:28px 22px}
  .hero-stats{gap:20px}
  .card{padding:22px}
  .detail-head{padding:18px 20px}
  :root,html[data-theme="light"]{--base-font:15px}
}
</style>
</head>
<body>

<div class="app">

  <!-- ================= TOPBAR ================= -->
  <header class="topbar">
    <div class="brand">
      <div class="logo"><i class="fa-solid fa-truck-fast"></i></div>
      <div>
        <h1>LogiBoard</h1>
        <p>Papan Informasi Logistik · PT Indonesia Stanley Electric</p>
      </div>
    </div>
    <div class="top-right">
      <div class="clock-box">
        <div class="t" id="clockTime">--:--:--</div>
        <div class="d" id="clockDate">Memuat…</div>
      </div>
      <a class="icon-btn admin-btn" id="adminBtn" href="/admin" title="Admin Panel">
        <i class="fa-solid fa-shield-halved"></i>
      </a>
      <button class="icon-btn" id="themeBtn" title="Ganti tema">
        <i class="fa-solid fa-moon"></i>
      </button>
    </div>
  </header>

  <!-- ============================================================
       HOME
       ============================================================ -->
  <section class="page active" id="page-home">
    <div class="hero">
      <div class="hero-inner">
        <div class="hero-text">
          <span class="badge"><span class="pulse"></span> Semua sistem berjalan normal</span>
          <h2>Selamat datang di <span>Pusat Kendali Logistik</span></h2>
          <p>Pantau dokumen, pengiriman, performa sistem, dan lowongan kerja dalam satu papan informasi yang ringan dan real-time.</p>
        </div>
        <div class="hero-stats">
          <div class="st"><b id="stDocs">0</b><small>Dokumen terupload</small></div>
          <div class="st"><b>4</b><small>Menu Delivery</small></div>
          <div class="st"><b>6</b><small>Sistem internal</small></div>
          <div class="st"><b>1</b><small>Papan produksi</small></div>
        </div>
      </div>
    </div>

    <div class="section-title">
      <h3><i class="fa-solid fa-grip"></i> Modul Informasi</h3>
      <p>Klik salah satu kartu untuk membuka detail</p>
    </div>

    <div class="cards" id="cards"></div>

    <div class="ticker">
      <div class="ticker-track" id="ticker"></div>
    </div>
  </section>

  <!-- ============================================================
       DOKUMEN — VIEW ONLY
       ============================================================ -->
  <section class="page" id="page-dokumen">
    <div class="detail-head">
      <button class="back-btn" data-back><i class="fa-solid fa-arrow-left"></i> Kembali</button>
      <div class="detail-title" style="--tone:var(--brand)">
        <div class="icon"><i class="fa-solid fa-folder-open"></i></div>
        <div>
          <h2>Dokumen</h2>
          <p>Daftar arsip dokumen logistik yang tersimpan</p>
        </div>
      </div>
    </div>

    <div class="admin-card" style="--tone:var(--brand);margin-top:20px">
      <h3><i class="fa-solid fa-file-lines"></i> Daftar Dokumen <span id="docCount" style="color:var(--text-3);font-weight:500">(0)</span></h3>
      <div class="doc-toolbar">
        <input type="text" id="docSearch" class="form-control" placeholder="🔍 Cari dokumen…" />
        <select id="docFilter" class="form-control" style="max-width:200px">
          <option value="">Semua kategori</option>
        </select>
      </div>
      <div class="doc-list" id="docList"></div>
    </div>
  </section>

  <!-- ============================================================
       DELIVERY — MENU
       ============================================================ -->
  <section class="page" id="page-delivery">
    <div class="detail-head">
      <button class="back-btn" data-back><i class="fa-solid fa-arrow-left"></i> Kembali</button>
      <div class="detail-title" style="--tone:var(--green)">
        <div class="icon"><i class="fa-solid fa-truck"></i></div>
        <div>
          <h2>Delivery</h2>
          <p>Pilih menu untuk masuk ke modul terkait</p>
        </div>
      </div>
    </div>
    <div class="menu-grid" id="menuDelivery" style="margin-top:20px"></div>
  </section>

  <!-- ============================================================
       SYSTEM — MENU
       ============================================================ -->
  <section class="page" id="page-system">
    <div class="detail-head">
      <button class="back-btn" data-back><i class="fa-solid fa-arrow-left"></i> Kembali</button>
      <div class="detail-title" style="--tone:var(--purple)">
        <div class="icon"><i class="fa-solid fa-server"></i></div>
        <div>
          <h2>System</h2>
          <p>Sistem internal logistik — tautan akan segera aktif</p>
        </div>
      </div>
    </div>
    <div class="menu-grid" id="menuSystem" style="margin-top:20px"></div>
  </section>

  <!-- ============================================================
       JOB BOARD — REDIRECT
       ============================================================ -->
  <section class="page" id="page-job">
    <div class="detail-head">
      <button class="back-btn" data-back><i class="fa-solid fa-arrow-left"></i> Kembali</button>
      <div class="detail-title" style="--tone:var(--amber)">
        <div class="icon"><i class="fa-solid fa-clipboard-list"></i></div>
        <div>
          <h2>Job Board</h2>
          <p>Papan Manajemen Produksi Logistik</p>
        </div>
      </div>
    </div>

    <div class="job-hero">
      <div class="jh-icon"><i class="fa-solid fa-clipboard-list"></i></div>
      <h2>Papan Manajemen Produksi Logistik</h2>
      <p>Sistem untuk mengelola rencana produksi, monitor progres, dan koordinasi antar divisi logistik dalam satu papan terpusat.</p>
      <a class="job-cta" href="#" id="jobCta">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
        Buka Papan Manajemen Produksi
      </a>
      <div class="note">
        <i class="fa-solid fa-circle-info"></i>
        URL akan diarahkan ke sistem produksi (masih <b>#</b> sementara)
      </div>
    </div>
  </section>

  <!-- ============================================================
       SUB PAGE (untuk menu Delivery & System)
       ============================================================ -->
  <section class="page" id="page-sub">
    <div class="detail-head">
      <button class="back-btn" id="subBack"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
      <div class="detail-title" id="subHead" style="--tone:var(--brand)">
        <div class="icon" id="subIcon"><i class="fa-solid fa-circle"></i></div>
        <div>
          <h2 id="subTitle">—</h2>
          <p id="subSubtitle">—</p>
        </div>
      </div>
    </div>
    <div class="placeholder" id="subPlaceholder" style="--tone:var(--brand)"></div>
  </section>

  <!-- FOOTER -->
  <div class="footer">
    <span>LogiBoard v4.0</span>
    <span class="sep">·</span>
    <span>Papan Informasi Logistik</span>
    <span class="sep">·</span>
    <span>&copy; 2026 Divisi Logistik</span>
  </div>

</div>
<!-- ============================================================
     PDF PREVIEW MODAL
     ============================================================ -->
<div class="pdf-modal-bg" id="pdfModal">
  <div class="pdf-modal">
    <div class="pdf-modal-head">
      <div class="pdf-icon"><i class="fa-solid fa-file-pdf"></i></div>
      <div class="pdf-meta">
        <h3 id="pdfModalTitle">—</h3>
        <p id="pdfModalMeta">—</p>
      </div>
      <div class="pdf-modal-actions">
        <a class="icon-act" id="pdfModalDownload" href="#" target="_blank" title="Unduh">
          <i class="fa-solid fa-download"></i>
        </a>
        <button class="icon-act close" id="pdfModalClose" title="Tutup">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>
    <div class="pdf-modal-body">
      <div class="pdf-loading" id="pdfLoading">
        <div class="spinner"></div>
        <span>Memuat dokumen…</span>
      </div>
      <iframe id="pdfFrame" src="about:blank" allowfullscreen></iframe>
    </div>
  </div>
</div>

<script>
/* ============================================================
   BASE PATH DETECTION
   ============================================================ */
const BASE = (function () {
  // contoh: /silog/          → BASE = /silog
  // contoh: /silog/index.php → BASE = /silog
  // contoh: /                → BASE = ''
  const path = window.location.pathname;
  const idx  = path.lastIndexOf('/');
  if (idx === -1) return '';
  let base = path.substring(0, idx);
  if (base.endsWith('/index.php')) base = base.replace(/\/index\.php$/, '');
  return base;
})();

const URL_CATEGORIES = BASE + '/categories';
const URL_DOCUMENTS  = BASE + '/documents';
const URL_STATS      = BASE + '/stats';
const URL_ADMIN      = BASE + '/admin';

console.log('[LogiBoard] BASE:', BASE);
console.log('[LogiBoard] URLs:', {
  categories: URL_CATEGORIES,
  documents:  URL_DOCUMENTS,
  stats:      URL_STATS,
  admin:      URL_ADMIN,
});

/* Update tombol admin sesuai base path */
document.getElementById('adminBtn').href = URL_ADMIN;

/* ============================================================
   KONFIGURASI MENU STATIS
   ============================================================ */
const DELIVERY_MENU = [
  { id:'actual',   title:'Actual Delivery',
    desc:'Realisasi pengiriman harian, bukti terima, dan status akhir tiap trip.',
    icon:'fa-clipboard-check', tone:'var(--brand)' },
  { id:'logbook',  title:'Log Book Problem Kendaraan',
    desc:'Catatan masalah kendaraan, kerusakan, dan tindak lanjut perbaikan.',
    icon:'fa-book-open', tone:'var(--amber)' },
  { id:'patrol',   title:'Periodik Patrol Kendaraan',
    desc:'Jadwal patroli rutin, checklist kondisi kendaraan, dan hasil inspeksi.',
    icon:'fa-shield-halved', tone:'var(--green)' },
  { id:'costdown', title:'Cost Down Freight',
    desc:'Analisa dan program penghematan biaya angkutan barang per rute.',
    icon:'fa-coins', tone:'var(--purple)' }
];

const SYSTEM_MENU = [
  { id:'ahm',     title:'AHM Delivery',
    desc:'Sistem pengiriman unit AHM (Astra Honda Motor).',
    icon:'fa-industry', tone:'var(--red)', url:'http://10.203.68.47:90/ahmdelivery' },
  { id:'hpm',     title:'HPM Delivery',
    desc:'Sistem pengiriman unit HPM (Honda Prospect Motor).',
    icon:'fa-industry', tone:'var(--brand)', url:'http://10.203.68.47:90/HondaDelivery' },
  { id:'yimm',    title:'YIMM Delivery',
    desc:'Sistem pengiriman unit YIMM (Yamaha Indonesia Motor Mfg).',
    icon:'fa-industry', tone:'var(--amber)', url:'http://10.203.68.47:90/yamahadelivery' },
  { id:'sim',     title:'SIM Delivery',
    desc:'Sistem informasi manajemen delivery internal.',
    icon:'fa-database', tone:'var(--purple)', url:'http://10.203.68.47:90/SzkDelivery' },
  { id:'loading', title:'Kontrol Loading / Unloading',
    desc:'Monitoring aktivitas bongkar-muat barang di gudang.',
    icon:'fa-truck-ramp-box', tone:'var(--cyan)', url:'http://10.203.68.47:90/TruckStat' },
  { id:'gps',     title:'GPS System Kontrol Delivery',
    desc:'Pemantauan posisi kendaraan dan rute pengiriman real-time.',
    icon:'fa-satellite-dish', tone:'var(--green)', url:'#' }
];

const CARDS_HOME = [
  { id:'dokumen',  tone:'var(--brand)',  icon:'fa-folder-open',
    title:'Dokumen', tag:'View',
    desc:'Lihat arsip dokumen logistik yang tersimpan di server.',
    stat:'docs',  lbl:'Dokumen terupload' },
  { id:'delivery', tone:'var(--green)',  icon:'fa-truck',
    title:'Delivery', tag:'4 menu',
    desc:'Actual Delivery, Log Book Problem, Patrol Kendaraan, dan Cost Down Freight.',
    stat:'4', lbl:'Sub-menu aktif' },
  { id:'system',   tone:'var(--purple)', icon:'fa-server',
    title:'System', tag:'6 sistem',
    desc:'AHM, HPM, YIMM, SIM Delivery, Kontrol Loading, dan GPS Kontrol Delivery.',
    stat:'6', lbl:'Sistem internal' },
  { id:'job',      tone:'var(--amber)',  icon:'fa-clipboard-list',
    title:'Job Board', tag:'Produksi',
    desc:'Buka papan manajemen produksi logistik untuk koordinasi antar divisi.',
    stat:'1', lbl:'Papan produksi' }
];

const TICKER = [
  { i:'fa-cloud-arrow-up',        t:'Upload dokumen via <b>Admin Panel</b> — login dulu di /admin' },
  { i:'fa-truck-fast',            t:'<b>Actual Delivery</b> hari ini dipantau real-time' },
  { i:'fa-shield-halved',         t:'Patrol kendaraan <b>Zona 1</b> dijadwalkan sore ini' },
  { i:'fa-coins',                 t:'Program <b>Cost Down Freight</b> rute Jawa capai 12% penghematan' },
  { i:'fa-satellite-dish',        t:'<b>GPS System</b> memantau 38 kendaraan aktif' },
  { i:'fa-clipboard-list',        t:'<b>Papan Manajemen Produksi</b> siap dibuka dari Job Board' }
];

/* ============================================================
   STATE
   ============================================================ */
const state = {
  categories: [],       // dari backend
  documents: [],        // dari backend
  stats: {
    documents: 0,
    categories: 0,
  },
  currentPage: 1,
  totalPages: 1,
  filterQuery: '',
  filterCategory: '',
  isLoading: false,
};

/* ============================================================
   HELPERS
   ============================================================ */
const $  = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"]/g, m =>
  ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m])
);

function fmtSize(b){
  b = Number(b) || 0;
  if (b < 1024) return b + ' B';
  if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
  return (b/1048576).toFixed(2) + ' MB';
}
function fmtDate(iso){
  if (!iso) return '—';
  const d = new Date(String(iso).replace(' ', 'T'));
  if (isNaN(d)) return String(iso);
  return d.toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
}
function debounce(fn, ms = 350){
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

function toast(msg, type='info'){
  const el = document.createElement('div');
  el.className = 'toast toast-' + type;
  const ic = type==='success' ? 'fa-circle-check'
           : type==='error'   ? 'fa-circle-xmark'
           : 'fa-circle-info';
  el.innerHTML = `<i class="fa-solid ${ic}"></i> <span>${esc(msg)}</span>`;
  document.body.appendChild(el);
  requestAnimationFrame(()=> el.classList.add('show'));
  setTimeout(()=>{
    el.classList.remove('show');
    setTimeout(()=> el.remove(), 300);
  }, 3000);
}

/* ============================================================
   FETCH HELPERS
   ============================================================ */
async function fetchJSON(url){
  const res = await fetch(url, {
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    credentials: 'same-origin',
  });

  if (!res.ok) {
    throw new Error(`HTTP ${res.status} untuk ${url}`);
  }
  return res.json();
}

/* ============================================================
   LOAD: STATS
   ============================================================ */
async function loadStats(){
  try {
    const json = await fetchJSON(URL_STATS);
    state.stats.documents  = Number(json.documents)  || 0;
    state.stats.categories = Number(json.categories) || 0;
  } catch(e){
    console.warn('[LogiBoard] loadStats failed:', e.message);
    state.stats = { documents: 0, categories: 0 };
  }
  $('stDocs').textContent = state.stats.documents;
}

/* ============================================================
   LOAD: CATEGORIES
   ============================================================ */
async function loadCategories(){
  try {
    const json = await fetchJSON(URL_CATEGORIES);
    state.categories = Array.isArray(json.data) ? json.data : [];
  } catch(e){
    console.warn('[LogiBoard] loadCategories failed:', e.message);
    state.categories = [];
  }
  syncCategoryDropdown();
}

/* ============================================================
   LOAD: DOCUMENTS
   ============================================================ */
async function loadDocuments(){
  const params = new URLSearchParams();
  if (state.filterQuery)    params.set('q', state.filterQuery);
  if (state.filterCategory) params.set('category_id', state.filterCategory);
  params.set('page', state.currentPage);

  state.isLoading = true;
  renderDocumentSkeleton();

  try {
    const json = await fetchJSON(URL_DOCUMENTS + '?' + params.toString());

    state.documents  = Array.isArray(json.data) ? json.data : [];
    state.totalPages = Number(json.pagination?.last_page) || 1;

    $('docCount').textContent = '(' + (json.pagination?.total ?? state.documents.length) + ')';
  } catch(e){
    console.warn('[LogiBoard] loadDocuments failed:', e.message);
    state.documents  = [];
    state.totalPages = 1;
    $('docCount').textContent = '(0)';
    toast('Gagal memuat dokumen dari server', 'error');
  } finally {
    state.isLoading = false;
  }

  renderDocuments();
}

/* ============================================================
   RENDER SKELETON (loading placeholder)
   ============================================================ */
function renderDocumentSkeleton(){
  const el = $('docList');
  if (!el) return;
  el.innerHTML = Array(6).fill(0).map(() =>
    '<div class="skeleton-card"></div>'
  ).join('');
}

/* ============================================================
   SYNC CATEGORY DROPDOWN
   ============================================================ */
function syncCategoryDropdown(){
  const f = $('docFilter');
  if (!f) return;
  const cur = f.value;
  f.innerHTML = '<option value="">Semua kategori</option>' +
    state.categories.map(c =>
      `<option value="${c.id}">${esc(c.name)}</option>`
    ).join('');
  f.value = state.categories.some(c => String(c.id) === cur) ? cur : '';
}

/* ============================================================
   RENDER HOME CARDS
   ============================================================ */
const cardsEl = $('cards');

function renderHome(){
  cardsEl.innerHTML = CARDS_HOME.map((c, i) => {
    const statVal = c.stat === 'docs' ? state.stats.documents : c.stat;
    return `
      <article class="card" style="--tone:${c.tone};animation-delay:${i*.08}s" data-page="${c.id}">
        <div class="top">
          <div class="icon"><i class="fa-solid ${c.icon}"></i></div>
          <div class="arrow"><i class="fa-solid fa-arrow-up-right-from-square"></i></div>
        </div>
        <h4>${c.title}</h4>
        <p class="desc">${c.desc}</p>
        <div class="meta">
          <div>
            <div class="num">${statVal}</div>
            <div class="lbl">${c.lbl}</div>
          </div>
          <span class="tag">${c.tag}</span>
        </div>
      </article>
    `;
  }).join('');
  $('stDocs').textContent = state.stats.documents;
}

/* pointer glow */
cardsEl.addEventListener('pointermove', e => {
  const card = e.target.closest('.card');
  if (!card) return;
  const r = card.getBoundingClientRect();
  card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
  card.style.setProperty('--my', (e.clientY - r.top)  + 'px');
});

/* ============================================================
   RENDER MENU (Delivery / System)
   ============================================================ */
function renderMenu(){
  $('menuDelivery').innerHTML = DELIVERY_MENU.map((m, i) => `
    <div class="menu-card" style="--tone:${m.tone};animation-delay:${i*.06}s"
         data-sub="delivery" data-id="${m.id}">
      <div class="mi"><i class="fa-solid ${m.icon}"></i></div>
      <h4>${m.title}</h4>
      <p>${m.desc}</p>
      <span class="go">Buka <i class="fa-solid fa-arrow-right"></i></span>
    </div>
  `).join('');

  $('menuSystem').innerHTML = SYSTEM_MENU.map((m, i) => `
    <div class="menu-card" style="--tone:${m.tone};animation-delay:${i*.06}s"
         data-sub="system" data-id="${m.id}">
      <div class="mi"><i class="fa-solid ${m.icon}"></i></div>
      <h4>${m.title}</h4>
      <p>${m.desc}</p>
      <span class="go">Buka <i class="fa-solid fa-arrow-right"></i></span>
    </div>
  `).join('');
}

/* klik menu card → sub page */
document.addEventListener('click', e => {
  const mc = e.target.closest('[data-sub]');
  if (!mc) return;
  goSub(mc.dataset.sub, mc.dataset.id);
});

/* ============================================================
   RENDER DOKUMEN (VIEW ONLY)
   ============================================================ */
function renderDocuments(){
  const el = $('docList');

  if (state.isLoading) return;

  if (state.documents.length === 0){
    el.innerHTML = `
      <div class="empty">
        <i class="fa-solid fa-file-circle-plus"></i>
        <b>Belum ada dokumen</b>
        <p>Dokumen yang diupload melalui <b>Admin Panel</b> akan muncul di sini.</p>
      </div>`;
    return;
  }

  el.innerHTML = state.documents.map((d, i) => {
    const tone = d.category_color || '#64748b';
    const cat  = d.category_name  || 'Tanpa kategori';
    const size = d.file_size_human || fmtSize(d.file_size);
    const url  = d.download_url   || (BASE + '/documents/' + d.id + '/download');

    return `
      <div class="doc-item" style="--tone:${tone};animation-delay:${i*.03}s">
        <div class="dh">
          <div class="df"><i class="fa-solid fa-file-pdf"></i></div>
          <div class="dt">
            <b>${esc(d.title)}</b>
            <small><i class="fa-solid fa-paperclip"></i> ${esc(d.original_name)}</small>
          </div>
        </div>
        ${d.description ? `<div class="dd">${esc(d.description)}</div>` : ''}
        <div class="dm">
          <span class="cattag">${esc(cat)}</span>
          <span class="size">${size} · ${fmtDate(d.created_at)}</span>
        </div>
        <div style="display:flex;gap:8px">
          <button class="btn btn-primary btn-sm preview-btn"
                  data-preview-url="${esc(url)}"
                  data-preview-title="${esc(d.title)}"
                  data-preview-meta="${esc(cat)} · ${esc(size)} · ${esc(d.original_name)}"
                  style="flex:1;justify-content:center">
            <i class="fa-solid fa-eye"></i> Lihat
          </button>
          <a class="btn btn-ghost btn-sm" href="${esc(url)}" target="_blank"
             style="flex:0 0 auto" title="Unduh">
            <i class="fa-solid fa-download"></i>
          </a>
        </div>
      </div>
    `;
  }).join('');

  renderPagination(el);
}

/* ============================================================
   RENDER PAGINATION
   ============================================================ */
function renderPagination(container){
  if (state.totalPages <= 1) return;

  const nav = document.createElement('div');
  nav.className = 'pagination';

  const mkBtn = (label, page, active = false, disabled = false) => {
    const btn = document.createElement('button');
    btn.className = 'page-btn' + (active ? ' active' : '');
    btn.textContent = label;
    btn.disabled = disabled;
    if (!active && !disabled){
      btn.onclick = () => {
        state.currentPage = page;
        loadDocuments();
        $('page-dokumen').scrollIntoView({ behavior: 'smooth', block: 'start' });
      };
    }
    return btn;
  };

  // Prev
  nav.appendChild(mkBtn('‹', state.currentPage - 1, false, state.currentPage <= 1));

  // Nomor halaman (max 7 tombol + elipsis)
  const total = state.totalPages;
  const cur   = state.currentPage;
  const pages = [];

  if (total <= 7){
    for (let p = 1; p <= total; p++) pages.push(p);
  } else {
    pages.push(1);
    if (cur > 4) pages.push('...');
    const start = Math.max(2, cur - 1);
    const end   = Math.min(total - 1, cur + 1);
    for (let p = start; p <= end; p++) pages.push(p);
    if (cur < total - 3) pages.push('...');
    pages.push(total);
  }

  pages.forEach(p => {
    if (p === '...'){
      const span = document.createElement('span');
      span.className = 'page-btn';
      span.style.cursor = 'default';
      span.textContent = '…';
      nav.appendChild(span);
    } else {
      nav.appendChild(mkBtn(String(p), p, p === cur));
    }
  });

  // Next
  nav.appendChild(mkBtn('›', state.currentPage + 1, false, state.currentPage >= total));

  container.appendChild(nav);
}

/* ============================================================
   SEARCH & FILTER
   ============================================================ */
$('docSearch').addEventListener('input', debounce(() => {
  state.filterQuery    = $('docSearch').value.trim();
  state.currentPage    = 1;
  loadDocuments();
}, 400));

$('docFilter').addEventListener('change', () => {
  state.filterCategory = $('docFilter').value;
  state.currentPage    = 1;
  loadDocuments();
});

/* ============================================================
   ROUTING
   ============================================================ */
const PAGES = ['home','dokumen','delivery','system','job','sub'];
let currentSub = null;

function go(page){
  if (!PAGES.includes(page)) page = 'home';

  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  $('page-' + page).classList.add('active');
  window.scrollTo({top:0,behavior:'smooth'});

  if (page !== 'sub') history.replaceState(null,'','#' + page);

  // Lazy load dokumen saat pertama kali masuk
  if (page === 'dokumen' && state.documents.length === 0 && !state.isLoading){
    loadDocuments();
  }
}

function goSub(kind, id){
  const list = kind === 'delivery' ? DELIVERY_MENU : SYSTEM_MENU;
  const item = list.find(x => x.id === id);
  if (!item) return;
  currentSub = { kind, id };

  const tone = item.tone || (kind === 'delivery' ? 'var(--green)' : 'var(--purple)');
  $('subHead').style.setProperty('--tone', tone);
  $('subPlaceholder').style.setProperty('--tone', tone);
  $('subIcon').innerHTML = `<i class="fa-solid ${item.icon}"></i>`;
  $('subTitle').textContent = item.title;
  $('subSubtitle').textContent = kind === 'delivery'
    ? 'Modul Delivery · LogiBoard'
    : 'Sistem internal logistik';

  const parent = kind === 'delivery' ? 'delivery' : 'system';
  const hasUrl = item.url && item.url !== '#';
  const urlLine = hasUrl
    ? `<a class="pill-soon link" href="${esc(item.url)}" target="_blank" rel="noopener">
         <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Sistem
       </a>`
    : `<div class="pill-soon"><i class="fa-solid fa-hourglass-half"></i> Segera terhubung</div>`;

  $('subPlaceholder').innerHTML = `
    <div class="ph-icon"><i class="fa-solid ${item.icon}"></i></div>
    <h2>${esc(item.title)}</h2>
    <p>${esc(item.desc)}</p>
    ${hasUrl
      ? `<p style="font-size:13px;margin-top:14px;color:var(--text-3)">
           Sistem sudah siap. Klik tombol di bawah untuk membuka.
         </p>`
      : `<p style="font-size:13px;margin-top:14px;color:var(--text-3)">
           Halaman ini akan diarahkan ke sistem <b>${esc(item.title)}</b>.
           Untuk saat ini tautan masih berupa <b>#</b> (placeholder).
         </p>`}
    ${urlLine}
  `;

  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  $('page-sub').classList.add('active');
  window.scrollTo({top:0,behavior:'smooth'});
  history.replaceState(null,'','#' + parent + '/' + id);
}

$('subBack').addEventListener('click', () => {
  if (!currentSub){ go('home'); return; }
  go(currentSub.kind);
});

document.querySelectorAll('[data-back]').forEach(b => {
  b.addEventListener('click', () => go('home'));
});

cardsEl.addEventListener('click', e => {
  const card = e.target.closest('.card');
  if (card) go(card.dataset.page);
});

/* ============================================================
   TICKER
   ============================================================ */
function renderTicker(){
  const html = TICKER.map(t =>
    `<div class="ticker-item"><i class="fa-solid ${t.i}"></i> ${t.t}</div>`
  ).join('');
  $('ticker').innerHTML = html + html;
}

/* ============================================================
   CLOCK
   ============================================================ */
function tick(){
  const now = new Date();
  $('clockTime').textContent = now.toLocaleTimeString('id-ID',{hour12:false});
  $('clockDate').textContent = now.toLocaleDateString('id-ID',{
    weekday:'long',day:'numeric',month:'long',year:'numeric'
  });
}
tick();
setInterval(tick, 1000);

/* ============================================================
   THEME
   ============================================================ */
const themeBtn = $('themeBtn');
const root = document.documentElement;
function applyTheme(t){
  root.dataset.theme = t;
  themeBtn.innerHTML = t === 'dark'
    ? '<i class="fa-solid fa-moon"></i>'
    : '<i class="fa-solid fa-sun"></i>';
}
const savedTheme = localStorage.getItem('logiboard-theme') || 'dark';
applyTheme(savedTheme);

themeBtn.addEventListener('click', () => {
  const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
  applyTheme(next);
  localStorage.setItem('logiboard-theme', next);
});

/* ============================================================
   PDF PREVIEW MODAL
   ============================================================ */
const pdfModal       = $('pdfModal');
const pdfFrame       = $('pdfFrame');
const pdfLoading     = $('pdfLoading');
const pdfModalTitle  = $('pdfModalTitle');
const pdfModalMeta   = $('pdfModalMeta');
const pdfDownload    = $('pdfModalDownload');

function openPdfModal(url, title, meta){
  // Set konten
  pdfModalTitle.textContent = title || 'Dokumen';
  pdfModalMeta.textContent  = meta  || '';
  pdfDownload.href          = url;

  // Tampilkan modal
  pdfModal.classList.add('active');
  document.body.style.overflow = 'hidden';

  // Tampilkan loading
  pdfLoading.classList.remove('hide');

  // Set iframe ke PDF
  pdfFrame.src = url;

  // Sembunyikan loading saat iframe selesai load
  pdfFrame.onload = () => {
    setTimeout(() => pdfLoading.classList.add('hide'), 300);
  };

  // Timeout fallback kalau load terlalu lama (5 detik)
  clearTimeout(window.__pdfTimeout);
  window.__pdfTimeout = setTimeout(() => {
    pdfLoading.classList.add('hide');
  }, 5000);
}

function closePdfModal(){
  pdfModal.classList.remove('active');
  document.body.style.overflow = '';

  // Reset iframe supaya PDF berhenti loading
  setTimeout(() => {
    pdfFrame.src = 'about:blank';
  }, 300);

  clearTimeout(window.__pdfTimeout);
}

// Tombol close
$('pdfModalClose').addEventListener('click', closePdfModal);

// Klik backdrop untuk close
pdfModal.addEventListener('click', e => {
  if (e.target === pdfModal) closePdfModal();
});

// ESC untuk close
document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && pdfModal.classList.contains('active')){
    closePdfModal();
  }
});

// Delegasi klik tombol preview (pakai event delegation supaya
// tetap bekerja meskipun doc-item di-render ulang)
document.addEventListener('click', e => {
  const btn = e.target.closest('.preview-btn');
  if (!btn) return;

  openPdfModal(
    btn.dataset.previewUrl,
    btn.dataset.previewTitle,
    btn.dataset.previewMeta
  );
});

/* ============================================================
   INIT
   ============================================================ */
(async function init(){
  // Render static dulu agar halaman tampil cepat
  renderMenu();
  renderTicker();
  renderHome();

  // Fetch dari backend
  await Promise.all([ loadStats(), loadCategories() ]);
  renderHome(); // re-render dengan stat dari DB

  // Routing berdasarkan hash
  const hash = location.hash.replace('#','');
  if (hash === 'dokumen'){
    go('dokumen');
  } else if (hash.startsWith('delivery/')){
    go('delivery'); goSub('delivery', hash.split('/')[1]);
  } else if (hash.startsWith('system/')){
    go('system'); goSub('system', hash.split('/')[1]);
  } else {
    go(hash || 'home');
  }
})();
</script>
</body>
</html>