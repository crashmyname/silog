<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Actual Delivery · LogiBoard</title>
<meta name="csrf-token" content="<?= (new \Bpjs\Framework\Core\Request())->csrfToken() ?>" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<style>
:root{
  --bg:#f7f8fb; --panel:#fff; --panel-2:#fbfcfe;
  --line:#e6e9ef; --line-soft:#eef1f6;
  --text:#0f172a; --text-2:#4b5568; --text-3:#8b95a8;
  --accent:#2563eb; --accent-soft:#eff4ff;
  --green:#059669; --green-soft:#ecfdf5;
  --amber:#b45309; --amber-soft:#fff8eb;
  --red:#dc2626; --red-soft:#fef2f2;
  --shadow:0 1px 3px rgba(15,23,42,.06);
}
html[data-theme="dark"]{
  --bg:#0b0f17; --panel:#101623; --panel-2:#0e1420;
  --line:#1e2635; --line-soft:#171e2b;
  --text:#e9eefb; --text-2:#95a1b8; --text-3:#5f6b82;
  --accent:#60a5fa; --accent-soft:rgba(96,165,250,.12);
  --green:#34d399; --green-soft:rgba(52,211,153,.12);
  --amber:#fbbf24; --amber-soft:rgba(251,191,36,.12);
  --red:#f87171; --red-soft:rgba(248,113,113,.12);
  --shadow:0 1px 2px rgba(0,0,0,.4);
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',system-ui,sans-serif;background:var(--bg);color:var(--text);font-size:14px;line-height:1.5}
.app{max-width:1400px;margin:0 auto;padding:24px}

.head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:14px;flex-wrap:wrap}
.head h1{font-size:22px;font-weight:800;letter-spacing:-.5px}
.head p{color:var(--text-2);font-size:13px;margin-top:3px}

.btn{
  display:inline-flex;align-items:center;gap:7px;padding:9px 15px;border-radius:8px;
  font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;border:1px solid transparent;
  transition:.15s;text-decoration:none;
}
.btn-primary{background:var(--accent);color:#fff}
.btn-primary:hover{opacity:.9}
.btn-ghost{background:var(--panel);border-color:var(--line);color:var(--text-2)}
.btn-ghost:hover{background:var(--line-soft);color:var(--text)}
.btn-danger{background:var(--red-soft);color:var(--red)}
.btn-sm{padding:6px 11px;font-size:12.5px;border-radius:6px}
.btn-icon{padding:6px 8px;background:var(--panel);border:1px solid var(--line);color:var(--text-2)}
.btn-icon:hover{background:var(--line-soft);color:var(--text)}
.btn-icon.danger:hover{color:var(--red);border-color:var(--red)}

.card{background:var(--panel);border:1px solid var(--line);border-radius:12px;box-shadow:var(--shadow);margin-bottom:18px;overflow:hidden}
.card-head{padding:14px 18px;border-bottom:1px solid var(--line-soft);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.card-head h3{font-size:14px;font-weight:700;letter-spacing:-.2px}
.card-body{padding:18px}

.field{margin-bottom:14px}
.field label{display:block;font-size:12.5px;font-weight:600;color:var(--text-2);margin-bottom:6px}
.field label .req{color:var(--red)}
.input,.select{
  width:100%;padding:9px 12px;font-size:13.5px;font-family:inherit;
  background:var(--panel);color:var(--text);
  border:1px solid var(--line);border-radius:8px;transition:.15s;
}
.input:focus,.select:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}
.select{cursor:pointer;appearance:none;
  background-image:linear-gradient(45deg,transparent 50%,var(--text-3) 50%),linear-gradient(135deg,var(--text-3) 50%,transparent 50%);
  background-position:calc(100% - 16px) 50%,calc(100% - 11px) 50%;
  background-size:5px 5px,5px 5px;background-repeat:no-repeat;padding-right:32px;
}
.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
@media(max-width:900px){.grid-4{grid-template-columns:1fr 1fr}.grid-3{grid-template-columns:1fr}}

/* Cycle card */
.cycle-card{
  border:1px solid var(--line);border-radius:10px;padding:14px;margin-bottom:14px;
  background:var(--panel-2);
}
.cycle-card-head{
  display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;
}
.cycle-badge{
  display:inline-flex;align-items:center;gap:8px;padding:5px 12px;border-radius:999px;
  font-size:12px;font-weight:800;letter-spacing:.3px;
  background:var(--accent-soft);color:var(--accent);
}
.cycle-badge small{font-weight:500;color:var(--text-3)}

.status-tag{
  display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;
  padding:4px 10px;border-radius:99px;text-transform:uppercase;letter-spacing:.3px;
}
.status-waiting{background:var(--line-soft);color:var(--text-2)}
.status-on_the_way{background:var(--accent-soft);color:var(--accent)}
.status-arrived{background:var(--amber-soft);color:var(--amber)}
.status-delivered{background:var(--green-soft);color:var(--green)}
.status-delayed{background:var(--red-soft);color:var(--red)}
.status-cancelled{background:var(--line-soft);color:var(--text-3)}

/* Item table */
.items-table{width:100%;border-collapse:collapse;font-size:12.5px;margin-top:10px}
.items-table th{
  text-align:left;font-weight:600;font-size:10.5px;letter-spacing:.4px;text-transform:uppercase;
  color:var(--text-3);padding:8px 10px;background:var(--panel);border-bottom:1px solid var(--line);
}
.items-table td{padding:8px 10px;border-bottom:1px solid var(--line-soft);vertical-align:middle}
.items-table td input{padding:6px 8px;font-size:12.5px;width:100%}
.items-table td .sm{width:90px}

.empty{padding:44px 16px;text-align:center}
.empty i{font-size:32px;color:var(--text-3);display:block;margin-bottom:12px;opacity:.55}
.empty b{display:block;color:var(--text);margin-bottom:6px;font-size:14px}
.empty p{color:var(--text-3);font-size:12.5px}

.toast{position:fixed;bottom:24px;left:50%;transform:translate(-50%,80px);
  padding:12px 18px;border-radius:9px;font-size:13px;font-weight:600;
  background:var(--panel);color:var(--text);border:1px solid var(--line);
  box-shadow:0 14px 36px rgba(15,23,42,.18);display:flex;align-items:center;gap:9px;
  opacity:0;transition:.3s;z-index:300;pointer-events:none;max-width:90vw;
}
.toast.show{transform:translate(-50%,0);opacity:1}
.toast.ok{border-color:var(--green);color:var(--green)}
.toast.err{border-color:var(--red);color:var(--red)}
</style>
</head>
<body>
<div class="app">

  <div class="head">
    <div>
      <h1>Actual Delivery</h1>
      <p>Input realisasi pengiriman per cycle per customer</p>
    </div>
    <div style="display:flex;gap:8px">
        <a class="btn btn-primary" href="/silog/admin/actual-delivery/board">
        <i class="fa-solid fa-table-cells"></i> Lihat Papan
        </a>
        <a class="btn btn-ghost" href="/silog/admin">
        <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
  </div>

  <!-- FILTER -->
  <div class="card">
    <div class="card-body">
      <div class="grid-3">
        <div class="field" style="margin:0">
          <label>Tanggal</label>
          <input type="date" class="input" id="fltDate" />
        </div>
        <div class="field" style="margin:0">
          <label>Customer</label>
          <select class="select" id="fltCustomer">
            <option value="">— Pilih customer —</option>
          </select>
        </div>
        <div class="field" style="margin:0;display:flex;align-items:flex-end;gap:8px">
          <button class="btn btn-primary" id="btnLoad" style="flex:1">
            <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- CYCLE AREA -->
  <div class="card">
    <div class="card-head">
      <h3><i class="fa-solid fa-truck-fast" style="color:var(--accent)"></i> Data Cycle</h3>
      <button class="btn btn-primary btn-sm" id="btnSaveAll">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua
      </button>
    </div>
    <div class="card-body" id="cyclesWrap">
      <div class="empty">
        <i class="fa-solid fa-truck"></i>
        <b>Pilih tanggal & customer</b>
        <p>Setelah itu daftar cycle akan muncul untuk diisi.</p>
      </div>
    </div>
  </div>

</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]));

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
  const cls = type==='success'?'ok':type==='error'?'err':'info';
  const icon = type==='success'?'fa-circle-check':type==='error'?'fa-circle-xmark':'fa-circle-info';
  el.className = 'toast ' + cls;
  ic.className = 'fa-solid ' + icon;
  sp.textContent = msg;
  requestAnimationFrame(() => el.classList.add('show'));
  clearTimeout(window.__toastTimer);
  window.__toastTimer = setTimeout(() => el.classList.remove('show'), 2800);
}

/* ============================================================
   STATE
   ============================================================ */
let customers = [];
let items     = [];
let vehicles  = [];
let activeCustomerId = null;
let activeCustomer   = null;
let deliveriesByCycle = {};  // { cycleId: {...delivery...} }

const STATUS_OPTIONS = [
  { v: 'waiting',    l: 'Waiting' },
  { v: 'on_the_way', l: 'On The Way' },
  { v: 'arrived',    l: 'Arrived' },
  { v: 'delivered',  l: 'Delivered' },
  { v: 'delayed',    l: 'Delayed' },
  { v: 'cancelled',  l: 'Cancelled' },
];

/* ============================================================
   LOAD MASTER DROPDOWNS
   ============================================================ */
async function loadMasters() {
  try {
    const [cRes, iRes, vRes] = await Promise.all([
      fetch('/silog/admin/customers/list', { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin' }).then(r=>r.json()),
      fetch('/silog/admin/items/list',     { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin' }).then(r=>r.json()),
      fetch('/silog/admin/vehicles/list',  { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin' }).then(r=>r.json()),
    ]);
    customers = cRes.data || [];
    items     = iRes.data || [];
    vehicles  = vRes.data || [];
  } catch (e) {
    toast('Gagal memuat master data', 'error');
  }

  const sel = $('fltCustomer');
  sel.innerHTML = '<option value="">— Pilih customer —</option>' +
    customers.map(c => `<option value="${c.id}">${esc(c.code)} — ${esc(c.name)}</option>`).join('');
}

/* ============================================================
   LOAD DELIVERY BY DATE + CUSTOMER
   ============================================================ */
async function loadDeliveries() {
  const date = $('fltDate').value;
  const cid  = parseInt($('fltCustomer').value || '0');

  if (!date)   { toast('Pilih tanggal', 'error'); return; }
  if (!cid)    { toast('Pilih customer', 'error'); return; }

  activeCustomerId = cid;
  activeCustomer   = customers.find(c => c.id === cid);

  try {
    const url = `/silog/admin/actual-delivery/list?date=${encodeURIComponent(date)}&customer_id=${cid}`;
    const res = await fetch(url, {
      headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
      credentials:'same-origin',
    });
    const json = await res.json();

    deliveriesByCycle = {};
    (json.data || []).forEach(d => {
      deliveriesByCycle[d.customer_cycle_id] = d;
    });

    renderCycles();
  } catch (e) {
    toast('Gagal memuat: ' + e.message, 'error');
  }
}

/* ============================================================
   RENDER
   ============================================================ */
function renderCycles() {
  const wrap = $('cyclesWrap');

  if (!activeCustomer) {
    wrap.innerHTML = `<div class="empty">
      <i class="fa-solid fa-truck"></i>
      <b>Pilih customer</b>
      <p>Customer tidak ditemukan.</p>
    </div>`;
    return;
  }

  const cycles = activeCustomer.cycles || [];
  if (!cycles.length) {
    wrap.innerHTML = `<div class="empty">
      <i class="fa-solid fa-circle-info"></i>
      <b>Customer ini belum punya cycle</b>
      <p>Buka <a href="/silog/admin/customers" style="color:var(--accent)">Master Customer</a> untuk menambahkan cycle.</p>
    </div>`;
    return;
  }

  wrap.innerHTML = cycles.map(cc => renderCycleCard(cc)).join('');

  // Bind events
  wrap.querySelectorAll('.cycle-card').forEach(card => {
    const cid = card.dataset.cycleId;

    // Add item
    card.querySelector('[data-act="add-item"]')?.addEventListener('click', () => {
        addItemRow(card, {});
    });

    // Remove item
    card.querySelectorAll('[data-act="remove-item"]').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('tr').remove());
    });

    // Status badge update
    const statusSel = card.querySelector('[data-role="status"]');
    if (statusSel) {
        statusSel.addEventListener('change', () => {
        const badge = card.querySelector('.status-badge');
        badge.className = 'status-badge status-tag status-' + statusSel.value;
        badge.textContent = statusSel.options[statusSel.selectedIndex].text;
        });
    }

    // ============ TAMBAHKAN INI ============
    // Vehicle dropdown → update vehicle_note
    const vehSel = card.querySelector('[data-role="vehicle_id"]');
    if (vehSel) {
        vehSel.addEventListener('change', () => {
        const vid = parseInt(vehSel.value || '0');
        const hidden = card.querySelector('[data-role="vehicle_note"]');
        if (!vid) {
            hidden.value = '';
            return;
        }
        const v = vehicles.find(x => x.id === vid);
        if (!v) return;
        const label = [v.plate_number, v.tonase, v.jenis].filter(Boolean).join(' / ');
        hidden.value = label;
        });
    }
    // ======================================
    });
}

function renderCycleCard(cc) {
  const existing = deliveriesByCycle[cc.id] || {};
  const cycleNum = cc.cycle_number;
  const status   = existing.status || 'waiting';
  const statusLbl = STATUS_OPTIONS.find(o => o.v === status)?.l || 'Waiting';

  const existingItems = existing.items || [];

  return `
    <div class="cycle-card" data-cycle-id="${cc.id}">
      <div class="cycle-card-head">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
          <span class="cycle-badge">CYCLE ${cycleNum}</span>
          <small style="color:var(--text-3);font-size:12px">
            ETD plan: <b>${cc.etd_from_ise?.substring(0,5) || '—'}</b>
            · ETA plan: <b>${cc.eta_at_customer?.substring(0,5) || '—'}</b>
          </small>
        </div>
        <span class="status-badge status-tag status-${status}">${statusLbl}</span>
      </div>

      <div class="grid-4">
        <div class="field" style="margin:0">
            <label>Kendaraan</label>
            <select class="select" data-role="vehicle_id">
                <option value="">— Pilih kendaraan —</option>
                ${vehicles.map(v => {
                const label = [
                    v.plate_number,
                    v.tonase,
                    v.jenis
                ].filter(Boolean).join(' / ');
                return `<option value="${v.id}" ${String(v.id) === String(existing.vehicle_id || '') ? 'selected' : ''}>
                    ${esc(label)}
                </option>`;
                }).join('')}
            </select>
            <input type="hidden" data-role="vehicle_note" value="${esc(existing.vehicle_note || '')}" />
            </div>
        <div class="field" style="margin:0">
          <label>Actual Departure</label>
          <input type="time" class="input" data-role="actual_departure"
                 value="${(existing.actual_departure || '').substring(0,5)}" />
        </div>
        <div class="field" style="margin:0">
          <label>Actual Arrival</label>
          <input type="time" class="input" data-role="actual_arrival"
                 value="${(existing.actual_arrival || '').substring(0,5)}" />
        </div>
        <div class="field" style="margin:0">
          <label>Status</label>
          <select class="select" data-role="status">
            ${STATUS_OPTIONS.map(o => `<option value="${o.v}" ${o.v===status?'selected':''}>${o.l}</option>`).join('')}
          </select>
        </div>
      </div>

      <div style="margin-top:14px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
          <label style="font-size:12.5px;font-weight:600;color:var(--text-2)">Detail Item (ICS)</label>
          <button type="button" class="btn btn-ghost btn-sm" data-act="add-item">
            <i class="fa-solid fa-plus"></i> Tambah Item
          </button>
        </div>

        <table class="items-table">
          <thead>
            <tr>
              <th>ICS</th>
              <th style="width:100px">Total</th>
              <th style="width:100px">Plan</th>
              <th style="width:100px">Actual</th>
              <th style="width:40px"></th>
            </tr>
          </thead>
          <tbody data-role="items-body">
            ${existingItems.length
              ? existingItems.map(it => renderItemRow(it)).join('')
              : ''}
          </tbody>
        </table>
      </div>

      <div class="field" style="margin-top:12px;margin-bottom:0">
        <label>Catatan</label>
        <input type="text" class="input" data-role="notes" maxlength="255"
               value="${esc(existing.notes || '')}" placeholder="Catatan opsional" />
      </div>
    </div>
  `;
}

function renderItemRow(it) {
  return `
    <tr>
      <td>
        <select class="select" data-role="item_id">
          <option value="">— Pilih ICS —</option>
          ${items.map(m => `<option value="${m.id}" ${String(m.id)===String(it.item_id||'')?'selected':''}>
            ${esc(m.ics_code)} — ${esc(m.ics_name)}
          </option>`).join('')}
        </select>
      </td>
      <td><input type="number" class="input sm" data-role="total_delivery" min="0" value="${it.total_delivery || 0}" /></td>
      <td><input type="number" class="input sm" data-role="plan_delivery"  min="0" value="${it.plan_delivery  || 0}" /></td>
      <td><input type="number" class="input sm" data-role="actual_delivery" min="0" value="${it.actual_delivery || 0}" /></td>
      <td style="text-align:center">
        <button type="button" class="btn btn-icon btn-sm danger" data-act="remove-item" title="Hapus">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </td>
    </tr>
  `;
}

function addItemRow(card, data) {
  const tbody = card.querySelector('[data-role="items-body"]');
  const tr = document.createElement('tr');
  tr.innerHTML = renderItemRow(data).trim();
  // replace tr outer? simpler: create wrapper
  const wrapper = document.createElement('tbody');
  wrapper.innerHTML = renderItemRow(data).trim();
  const newTr = wrapper.firstElementChild;
  tbody.appendChild(newTr);

  newTr.querySelector('[data-act="remove-item"]').addEventListener('click', () => newTr.remove());
}

/* ============================================================
   COLLECT & SAVE
   ============================================================ */
function collectAll() {
  const date = $('fltDate').value;
  const cid  = activeCustomerId;
  if (!date || !cid) return null;

  const cycles = [];
  document.querySelectorAll('#cyclesWrap .cycle-card').forEach(card => {
    const cycleId = parseInt(card.dataset.cycleId);
    const items = [];
    card.querySelectorAll('[data-role="items-body"] tr').forEach(tr => {
      const itemId = parseInt(tr.querySelector('[data-role="item_id"]').value || '0');
      if (!itemId) return;
      items.push({
        item_id: itemId,
        total_delivery:  parseInt(tr.querySelector('[data-role="total_delivery"]').value || '0'),
        plan_delivery:   parseInt(tr.querySelector('[data-role="plan_delivery"]').value  || '0'),
        actual_delivery: parseInt(tr.querySelector('[data-role="actual_delivery"]').value || '0'),
      });
    });

    cycles.push({
        cycle_id:         cycleId,
        vehicle_id:       parseInt(card.querySelector('[data-role="vehicle_id"]').value || '0') || null,
        vehicle_note:     card.querySelector('[data-role="vehicle_note"]').value.trim() || null,
        actual_departure: card.querySelector('[data-role="actual_departure"]').value || null,
        actual_arrival:   card.querySelector('[data-role="actual_arrival"]').value   || null,
        status:           card.querySelector('[data-role="status"]').value,
        notes:            card.querySelector('[data-role="notes"]').value.trim(),
        items:            items,
    });
  });

  return {
    delivery_date: date,
    customer_id:   cid,
    cycles:        cycles,
  };
}

async function saveAll() {
  const payload = collectAll();
  if (!payload) { toast('Lengkapi filter dulu', 'error'); return; }
  if (!payload.cycles.length) { toast('Tidak ada cycle untuk disimpan', 'error'); return; }

  const btn = $('btnSaveAll');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';

  try {
    const res = await fetch('/silog/admin/actual-delivery', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    });
    const json = await res.json();
    if (!res.ok) {
      toast(json.errors ? Object.values(json.errors)[0] : (json.message || 'Gagal'), 'error');
      return;
    }
    toast('Actual delivery tersimpan', 'success');
    await loadDeliveries();
  } catch (e) {
    toast('Error: ' + e.message, 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Semua';
  }
}

/* ============================================================
   INIT
   ============================================================ */
$('fltDate').value = new Date().toISOString().slice(0,10);
$('btnLoad').addEventListener('click', loadDeliveries);
$('btnSaveAll').addEventListener('click', saveAll);

(async function init() {
  await loadMasters();
})();
</script>
</body>
</html>