<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Delivery Control Board · LogiBoard</title>
<meta name="csrf-token" content="<?= (new \Bpjs\Framework\Core\Request())->csrfToken() ?>" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<style>
:root{
  --bg:#f5f7fb; --panel:#fff; --panel-2:#fafbfe;
  --line:#e2e6ee; --line-soft:#eef1f6;
  --text:#0f172a; --text-2:#475569; --text-3:#94a3b8;
  --accent:#2563eb; --accent-soft:#eff4ff;
  --green:#059669; --green-soft:#ecfdf5;
  --amber:#b45309; --amber-soft:#fff8eb;
  --red:#dc2626; --red-soft:#fef2f2;
  --brand-blue:#1e40af; --brand-red:#b91c1c;
}
html[data-theme="dark"]{
  --bg:#0b0f17; --panel:#101623; --panel-2:#0e1420;
  --line:#1e2635; --line-soft:#171e2b;
  --text:#e9eefb; --text-2:#95a1b8; --text-3:#5f6b82;
  --accent:#60a5fa; --accent-soft:rgba(96,165,250,.12);
  --green:#34d399; --green-soft:rgba(52,211,153,.12);
  --amber:#fbbf24; --amber-soft:rgba(251,191,36,.12);
  --red:#f87171; --red-soft:rgba(248,113,113,.12);
}
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Inter',system-ui,sans-serif;
  background:var(--bg); color:var(--text);
  font-size:13px; line-height:1.4;
}
.wrap{padding:16px}

/* HEADER */
.top{
  display:flex;align-items:center;justify-content:space-between;
  gap:14px;flex-wrap:wrap;margin-bottom:14px;
}
.top h1{
  font-size:22px;font-weight:800;letter-spacing:-.6px;
  display:flex;align-items:center;gap:10px;
}
.top h1 .badge{
  background:linear-gradient(135deg,#1e40af,#7c3aed);
  color:#fff;font-size:10px;font-weight:800;letter-spacing:.6px;
  padding:5px 10px;border-radius:6px;
  text-transform:uppercase;
}
.top-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}

.date-info{
  background:var(--panel);border:1px solid var(--line);
  border-radius:8px;padding:8px 14px;
  display:flex;gap:16px;align-items:center;font-size:12.5px;
}
.date-info .lbl{color:var(--text-3);font-weight:600;letter-spacing:.4px;font-size:10.5px;text-transform:uppercase}
.date-info .val{font-weight:700;color:var(--text)}

.input{
  padding:8px 12px;font-size:13px;font-family:inherit;
  background:var(--panel);color:var(--text);
  border:1px solid var(--line);border-radius:8px;
}
.input:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}

.btn{
  display:inline-flex;align-items:center;gap:7px;padding:8px 14px;
  border-radius:8px;font-size:12.5px;font-weight:600;font-family:inherit;
  cursor:pointer;border:1px solid transparent;text-decoration:none;
  transition:.15s;white-space:nowrap;
}
.btn-primary{background:var(--accent);color:#fff}
.btn-primary:hover{opacity:.9}
.btn-ghost{background:var(--panel);border-color:var(--line);color:var(--text-2)}
.btn-ghost:hover{background:var(--line-soft);color:var(--text)}

/* BOARD TABLE */
.board-scroll{
  overflow:auto;
  background:var(--panel);
  border:1px solid var(--line);
  border-radius:10px;
  max-height:calc(100vh - 160px);
}

.board-table{
  border-collapse:collapse;
  font-size:11.5px;
  min-width:100%;
}

.board-table th,
.board-table td{
  border:1px solid var(--line);
  padding:6px 8px;
  white-space:nowrap;
  vertical-align:middle;
  text-align:center;
}

/* Sticky left 2 cols */
.board-table .stick-1{
  position:sticky;left:0;z-index:3;
  background:var(--panel-2);
  min-width:82px;max-width:82px;
  text-align:left;
  border-right:2px solid var(--line);
}
.board-table .stick-2{
  position:sticky;left:82px;z-index:3;
  background:var(--panel-2);
  min-width:130px;max-width:130px;
  text-align:left;
  border-right:2px solid var(--line);
  font-size:10.5px;font-weight:600;color:var(--text-2);
  text-transform:uppercase;letter-spacing:.3px;
}

/* Sticky header row */
.board-table thead th{
  position:sticky;top:0;z-index:5;
  background:var(--panel-2);
  font-weight:700;
  font-size:10.5px;letter-spacing:.4px;
  text-transform:uppercase;color:var(--text-2);
}
.board-table thead tr:nth-child(2) th{
  top:28px;
}

/* Sticky header + left col intersection */
.board-table thead th.stick-1,
.board-table thead th.stick-2{
  z-index:6;
}

/* Customer header */
.cust-head{
  background:linear-gradient(180deg,#1e40af,#1e3a8a) !important;
  color:#fff !important;
  font-size:13px !important;
  font-weight:800 !important;
  letter-spacing:1px;
  padding:8px !important;
  border-color:#0f172a !important;
}

/* Cycle header */
.cycle-head{
  background:var(--accent-soft) !important;
  color:var(--accent) !important;
  font-weight:800 !important;
  font-size:11px !important;
  padding:5px 6px !important;
}

/* Row labels */
.row-label{
  background:var(--panel-2) !important;
  font-weight:700 !important;
  color:var(--text-2) !important;
  font-size:10.5px !important;
  letter-spacing:.3px;
  text-transform:uppercase;
  text-align:left !important;
}

/* Cell styles */
.cell-time{
  font-weight:600;font-variant-numeric:tabular-nums;
  color:var(--text);
}
.cell-time.empty{color:var(--text-3);font-weight:400}
.cell-vehicle{
  font-size:11px;color:var(--text);
  max-width:180px;overflow:hidden;text-overflow:ellipsis;
}
.cell-vehicle.empty{color:var(--text-3);font-weight:400;font-style:italic}

.status-chip{
  display:inline-flex;align-items:center;gap:4px;
  padding:3px 8px;border-radius:99px;
  font-size:9.5px;font-weight:800;letter-spacing:.3px;text-transform:uppercase;
  white-space:nowrap;
}
.st-waiting{background:var(--line-soft);color:var(--text-2)}
.st-on_the_way{background:var(--accent-soft);color:var(--accent)}
.st-arrived{background:var(--amber-soft);color:var(--amber)}
.st-delivered{background:var(--green-soft);color:var(--green)}
.st-delayed{background:var(--red-soft);color:var(--red)}
.st-cancelled{background:var(--line-soft);color:var(--text-3)}

/* Item rows */
.item-row td.stick-1{
  font-family:monospace;font-size:11px;font-weight:700;color:var(--text);
  text-align:left;
}
.item-row td.stick-2{
  font-size:11px;color:var(--text-2);font-weight:500;
  text-transform:none;letter-spacing:0;text-align:left;
  white-space:normal;overflow:hidden;text-overflow:ellipsis;
  max-width:130px;
}

/* Plan/actual cell — berbeda warna */
.cell-plan{
  background:var(--panel-2) !important;
  color:var(--text-2);
  font-variant-numeric:tabular-nums;
}
.cell-actual{
  background:color-mix(in srgb,var(--green) 5%,var(--panel)) !important;
  color:var(--green);
  font-weight:700;
  font-variant-numeric:tabular-nums;
}
.cell-actual.mismatch{
  background:color-mix(in srgb,var(--red) 8%,var(--panel)) !important;
  color:var(--red);
}
.cell-zero{
  color:var(--text-3) !important;
  font-weight:400 !important;
}

/* Sub-header untuk Plan/Act */
.sub-head{
  background:var(--panel-2) !important;
  font-size:9px !important;
  font-weight:700 !important;
  color:var(--text-3) !important;
  letter-spacing:.4px;
  padding:3px 4px !important;
}

/* Empty state */
.empty{
  padding:80px 20px;text-align:center;
  background:var(--panel);
  border:1px solid var(--line);border-radius:10px;
}
.empty i{font-size:44px;color:var(--text-3);display:block;margin-bottom:14px;opacity:.5}
.empty b{display:block;color:var(--text);font-size:15px;margin-bottom:6px}
.empty p{color:var(--text-3);font-size:12.5px}

/* Loading */
.loading{
  padding:60px;text-align:center;
  background:var(--panel);border-radius:10px;
}
.loading .spin{
  width:36px;height:36px;border-radius:50%;
  border:3px solid var(--line);border-top-color:var(--accent);
  animation:sp .8s linear infinite;margin:0 auto 12px;
}
@keyframes sp{to{transform:rotate(360deg)}}
.loading p{color:var(--text-3);font-size:13px}

/* Legenda */
.legend{
  margin-top:12px;padding:12px 16px;
  background:var(--panel);border:1px solid var(--line);
  border-radius:8px;
  display:flex;gap:24px;flex-wrap:wrap;
  font-size:11.5px;color:var(--text-2);
}
.legend .item{display:flex;align-items:center;gap:6px}
.legend .dot{
  width:10px;height:10px;border-radius:3px;flex:none;
}
</style>
</head>
<body>
<div class="wrap">

  <!-- HEADER -->
  <div class="top">
    <h1>
      DELIVERY CONTROL BOARD
      <span class="badge"><?= date('F Y') ?></span>
    </h1>

    <div class="top-actions">
      <div class="date-info">
        <div>
          <div class="lbl">Tanggal</div>
          <input type="date" id="pickDate" class="input" style="padding:4px 8px;font-size:12px" />
        </div>
        <div>
          <div class="lbl">Hari</div>
          <div class="val" id="dayLabel">—</div>
        </div>
      </div>

      <button class="btn btn-primary" id="btnLoad">
        <i class="fa-solid fa-rotate"></i> Muat Ulang
      </button>
      <a class="btn btn-ghost" href="/silog/admin/actual-delivery">
        <i class="fa-solid fa-pen-to-square"></i> Input
      </a>
      <a class="btn btn-ghost" href="/silog/admin">
        <i class="fa-solid fa-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <!-- BOARD -->
  <div id="boardArea">
    <div class="loading">
      <div class="spin"></div>
      <p>Memuat data papan…</p>
    </div>
  </div>

  <!-- LEGENDA -->
  <div class="legend">
    <div class="item"><span class="dot" style="background:var(--green)"></span> Actual = Plan (sesuai)</div>
    <div class="item"><span class="dot" style="background:var(--red)"></span> Actual ≠ Plan (beda)</div>
    <div class="item"><span class="dot" style="background:var(--line-soft);border:1px solid var(--line)"></span> Belum diisi</div>
    <div class="item"><i class="fa-solid fa-circle-info" style="color:var(--accent)"></i> Klik Input untuk mengisi / ubah data</div>
  </div>

</div>

<script>
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]));

const STATUS_LABELS = {
  waiting:    'Waiting',
  on_the_way: 'On The Way',
  arrived:    'Arrived',
  delivered:  'Delivered',
  delayed:    'Delayed',
  cancelled:  'Cancelled'
};

const DAY_NAMES = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

let boardData = null;

/* ============================================================
   LOAD DATA
   ============================================================ */
async function loadBoard() {
  const date = $('pickDate').value;
  if (!date) return;

  // Tampilkan hari
  const d = new Date(date + 'T00:00:00');
  $('dayLabel').textContent = DAY_NAMES[d.getDay()];

  // Loading
  $('boardArea').innerHTML = `
    <div class="loading">
      <div class="spin"></div>
      <p>Memuat data papan…</p>
    </div>`;

  try {
    const res = await fetch(`/silog/admin/actual-delivery/board-data?date=${encodeURIComponent(date)}`, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    });
    const json = await res.json();
    boardData = json;
    renderBoard();
  } catch (e) {
    $('boardArea').innerHTML = `
      <div class="empty">
        <i class="fa-solid fa-triangle-exclamation" style="color:var(--amber)"></i>
        <b>Gagal memuat data</b>
        <p>${esc(e.message)}</p>
      </div>`;
  }
}

/* ============================================================
   RENDER BOARD
   ============================================================ */
function renderBoard() {
  const wrap = $('boardArea');

  const customers = boardData.customers || [];
  const items = boardData.items || [];

  if (!customers.length) {
    wrap.innerHTML = `
      <div class="empty">
        <i class="fa-solid fa-inbox"></i>
        <b>Belum ada customer / cycle</b>
        <p>Tambahkan customer dan cycle di <a href="/silog/admin/customers" style="color:var(--accent)">Master Customer</a>.</p>
      </div>`;
    return;
  }

  if (!items.length) {
    wrap.innerHTML = `
      <div class="empty">
        <i class="fa-solid fa-box-open"></i>
        <b>Belum ada item ICS</b>
        <p>Tambahkan item di <a href="/silog/admin" style="color:var(--accent)">Master Data → Item ICS</a>.</p>
      </div>`;
    return;
  }

  // Hitung total kolom cycle (untuk colspan header customer)
  let totalCycles = 0;
  customers.forEach(c => totalCycles += c.cycles.length);

  // Total sub-kolom: setiap cycle punya 2 sub-col (Plan & Act) untuk item section
  // Tapi untuk header timing, 1 cycle = 2 kolom (biar sejajar)
  const colsPerCycle = 2;

  // Bangun HTML
  let html = '<div class="board-scroll"><table class="board-table">';

  /* ============ HEADER ============ */
  html += '<thead>';

  /* Row 1: Customer header */
  html += '<tr>';
  html += '<th class="stick-1 cust-head" colspan="2" style="text-align:left">DESTINASI CUSTOMER</th>';
  customers.forEach(c => {
    html += `<th class="cust-head" colspan="${c.cycles.length * colsPerCycle}">${esc(c.code)} — ${esc(c.name)}</th>`;
  });
  html += '</tr>';

  /* Row 2: Cycle number */
  html += '<tr>';
  html += '<th class="stick-1 cycle-head"></th>';
  html += '<th class="stick-2 cycle-head">CYCLE</th>';
  customers.forEach(c => {
    c.cycles.forEach(cc => {
      html += `<th class="cycle-head" colspan="${colsPerCycle}">CYCLE ${cc.cycle_number}</th>`;
    });
  });
  html += '</tr>';

  /* Row 3: Sub-header Plan/Act for item section — kosong untuk timing */
  html += '<tr>';
  html += '<th class="stick-1 sub-head"></th>';
  html += '<th class="stick-2 sub-head"></th>';
  customers.forEach(c => {
    c.cycles.forEach(cc => {
      html += `<th class="sub-head">Plan</th><th class="sub-head">Act</th>`;
    });
  });
  html += '</tr>';

  html += '</thead>';

  /* ============ BODY ============ */
  html += '<tbody>';

  /* Row: NO POL — Tonase — Rental */
  html += '<tr>';
  html += '<td class="stick-1"></td>';
  html += '<td class="stick-2 row-label">No Pol · Tonase · Rental</td>';
  customers.forEach(c => {
    c.cycles.forEach(cc => {
      const v = cc.vehicle_note || '';
      html += `<td colspan="${colsPerCycle}" class="cell-vehicle ${v ? '' : 'empty'}">${v ? esc(v) : '—'}</td>`;
    });
  });
  html += '</tr>';

  /* Row: ETD FROM ISE */
  html += '<tr>';
  html += '<td class="stick-1"></td>';
  html += '<td class="stick-2 row-label">ETD From ISE</td>';
  customers.forEach(c => {
    c.cycles.forEach(cc => {
      const t = cc.etd_plan ? cc.etd_plan.substring(0,5) : '';
      html += `<td colspan="${colsPerCycle}" class="cell-time ${t ? '' : 'empty'}">${t || '—'}</td>`;
    });
  });
  html += '</tr>';

  /* Row: ACTUAL DEPARTURE */
  html += '<tr>';
  html += '<td class="stick-1"></td>';
  html += '<td class="stick-2 row-label">Actual Departure</td>';
  customers.forEach(c => {
    c.cycles.forEach(cc => {
      const t = cc.actual_departure ? cc.actual_departure.substring(0,5) : '';
      html += `<td colspan="${colsPerCycle}" class="cell-time ${t ? '' : 'empty'}">${t || '—'}</td>`;
    });
  });
  html += '</tr>';

  /* Row: ETA AT CUSTOMER */
  html += '<tr>';
  html += '<td class="stick-1"></td>';
  html += '<td class="stick-2 row-label">ETA At Customer</td>';
  customers.forEach(c => {
    c.cycles.forEach(cc => {
      const t = cc.eta_plan ? cc.eta_plan.substring(0,5) : '';
      html += `<td colspan="${colsPerCycle}" class="cell-time ${t ? '' : 'empty'}">${t || '—'}</td>`;
    });
  });
  html += '</tr>';

  /* Row: ACTUAL ARRIVAL */
  html += '<tr>';
  html += '<td class="stick-1"></td>';
  html += '<td class="stick-2 row-label">Actual Arrival</td>';
  customers.forEach(c => {
    c.cycles.forEach(cc => {
      const t = cc.actual_arrival ? cc.actual_arrival.substring(0,5) : '';
      html += `<td colspan="${colsPerCycle}" class="cell-time ${t ? '' : 'empty'}">${t || '—'}</td>`;
    });
  });
  html += '</tr>';

  /* Row: STATUS */
  html += '<tr>';
  html += '<td class="stick-1"></td>';
  html += '<td class="stick-2 row-label">Status</td>';
  customers.forEach(c => {
    c.cycles.forEach(cc => {
      const s = cc.status || 'waiting';
      const lbl = STATUS_LABELS[s] || s;
      html += `<td colspan="${colsPerCycle}"><span class="status-chip st-${s}">${esc(lbl)}</span></td>`;
    });
  });
  html += '</tr>';

  /* ============ ITEM ROWS ============ */
  items.forEach(item => {
    html += '<tr class="item-row">';
    html += `<td class="stick-1">${esc(item.ics_code)}</td>`;
    html += `<td class="stick-2" title="${esc(item.ics_name)}">${esc(item.ics_name)}</td>`;

    customers.forEach(c => {
      c.cycles.forEach(cc => {
        // Cari item di cycle ini
        const found = (cc.items || []).find(x => x.item_id === item.id);

        const plan   = found ? found.plan_delivery   : 0;
        const actual = found ? found.actual_delivery : 0;

        const isMismatch = found && plan !== actual && actual > 0;

        // Plan
        html += `<td class="cell-plan ${plan ? '' : 'cell-zero'}">${plan || ''}</td>`;

        // Actual
        if (actual > 0) {
          html += `<td class="cell-actual ${isMismatch ? 'mismatch' : ''}">${actual}</td>`;
        } else {
          html += `<td class="cell-actual cell-zero"></td>`;
        }
      });
    });

    html += '</tr>';
  });

  html += '</tbody>';
  html += '</table></div>';

  wrap.innerHTML = html;
}

/* ============================================================
   INIT
   ============================================================ */
(function init() {
  // Set tanggal hari ini
  const today = new Date().toISOString().slice(0, 10);
  $('pickDate').value = today;

  // Event
  $('pickDate').addEventListener('change', loadBoard);
  $('btnLoad').addEventListener('click', loadBoard);

  // Load pertama
  loadBoard();
})();
</script>
</body>
</html>