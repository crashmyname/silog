<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Master Customer · LogiBoard</title>
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
.app{max-width:1280px;margin:0 auto;padding:24px}
.head{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;gap:14px;flex-wrap:wrap}
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
.btn-danger:hover{opacity:.85}
.btn-sm{padding:6px 11px;font-size:12.5px;border-radius:6px}
.btn-icon{padding:6px 8px;background:var(--panel);border:1px solid var(--line);color:var(--text-2)}
.btn-icon:hover{background:var(--line-soft);color:var(--text)}
.btn-icon.danger:hover{color:var(--red);border-color:var(--red)}

.layout{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1.4fr);gap:20px}
@media(max-width:960px){.layout{grid-template-columns:1fr}}

.card{background:var(--panel);border:1px solid var(--line);border-radius:12px;box-shadow:var(--shadow);overflow:hidden}
.card-head{padding:14px 18px;border-bottom:1px solid var(--line-soft);display:flex;align-items:center;justify-content:space-between;gap:12px}
.card-head h3{font-size:14px;font-weight:700;letter-spacing:-.2px}
.card-body{padding:16px 18px}

.search{position:relative;margin-bottom:14px}
.search i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-3);font-size:12px}
.search input{
  width:100%;padding:9px 12px 9px 34px;font-size:13px;font-family:inherit;
  background:var(--panel-2);color:var(--text);
  border:1px solid var(--line);border-radius:8px;
}
.search input:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}

table{width:100%;border-collapse:collapse;font-size:13px}
thead th{
  text-align:left;font-weight:600;font-size:11px;letter-spacing:.4px;text-transform:uppercase;
  color:var(--text-3);padding:10px 14px;background:var(--panel-2);
  border-bottom:1px solid var(--line);white-space:nowrap;
}
tbody td{padding:12px 14px;border-bottom:1px solid var(--line-soft);color:var(--text-2);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--panel-2)}
tbody tr.selected{background:var(--accent-soft)}
td .strong{color:var(--text);font-weight:600}
td .actions{display:flex;gap:5px;justify-content:flex-end}

.field{margin-bottom:14px}
.field label{display:block;font-size:12.5px;font-weight:600;color:var(--text-2);margin-bottom:6px}
.field label .req{color:var(--red)}
.input,.textarea{
  width:100%;padding:9px 12px;font-size:13.5px;font-family:inherit;
  background:var(--panel);color:var(--text);
  border:1px solid var(--line);border-radius:8px;transition:.15s;
}
.input:focus,.textarea:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}
.textarea{resize:vertical;min-height:70px;line-height:1.55}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}

.cycle-list{display:flex;flex-direction:column;gap:10px;margin-top:6px}
.cycle-row{
  display:grid;grid-template-columns:56px 1fr 1fr 40px;gap:8px;align-items:center;
  padding:10px 12px;background:var(--panel-2);
  border:1px solid var(--line);border-radius:9px;
}
.cycle-row .num{
  display:grid;place-items:center;font-weight:800;font-size:12.5px;
  background:var(--accent-soft);color:var(--accent);
  height:30px;border-radius:7px;
}
.cycle-row .x{
  width:32px;height:32px;border-radius:8px;display:grid;place-items:center;cursor:pointer;
  color:var(--text-3);background:transparent;border:1px solid var(--line);
}
.cycle-row .x:hover{color:var(--red);border-color:var(--red);background:var(--red-soft)}
.cycle-row .input{padding:7px 9px;font-size:12.5px}

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
      <h1>Master Customer</h1>
      <p>Kelola customer + cycle delivery (maks 15 per customer)</p>
    </div>
    <a class="btn btn-ghost" href="/silog/admin">
      <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="layout">

    <!-- KIRI: daftar customer -->
    <div class="card">
      <div class="card-head">
        <h3><i class="fa-solid fa-users" style="color:var(--accent)"></i> Daftar Customer</h3>
        <button class="btn btn-primary btn-sm" id="btnNew">
          <i class="fa-solid fa-plus"></i> Customer Baru
        </button>
      </div>
      <div class="card-body">
        <div class="search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="searchCust" placeholder="Cari kode / nama…" />
        </div>
        <div id="listWrap"></div>
      </div>
    </div>

    <!-- KANAN: form -->
    <div class="card">
      <div class="card-head">
        <h3 id="formTitle"><i class="fa-solid fa-pen"></i> Form Customer</h3>
        <span class="tag" id="formMode" style="display:none"></span>
      </div>
      <div class="card-body">
        <form id="customerForm">
          <input type="hidden" id="cf_id" />

          <div class="grid-2">
            <div class="field">
              <label>Kode <span class="req">*</span></label>
              <input type="text" class="input" id="cf_code" required maxlength="30" placeholder="A19" />
            </div>
            <div class="field">
              <label>Nama Customer <span class="req">*</span></label>
              <input type="text" class="input" id="cf_name" required maxlength="120" />
            </div>
          </div>

          <div class="grid-2">
            <div class="field">
              <label>Contact Person</label>
              <input type="text" class="input" id="cf_contact" maxlength="100" />
            </div>
            <div class="field">
              <label>Telepon</label>
              <input type="text" class="input" id="cf_phone" maxlength="30" />
            </div>
          </div>

          <div class="field">
            <label>Alamat</label>
            <textarea class="textarea" id="cf_address" maxlength="300"></textarea>
          </div>

          <div class="field" style="display:flex;align-items:center;justify-content:space-between">
            <label style="margin:0">
              Cycle Delivery <small style="color:var(--text-3);font-weight:500">(maks 15)</small>
            </label>
            <button type="button" class="btn btn-ghost btn-sm" id="btnAddCycle">
              <i class="fa-solid fa-plus"></i> Tambah Cycle
            </button>
          </div>

          <div class="cycle-list" id="cycleList"></div>

          <div style="display:flex;gap:10px;margin-top:16px">
            <button type="submit" class="btn btn-primary" id="btnSave">
              <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
            <button type="button" class="btn btn-ghost" id="btnReset">
              <i class="fa-solid fa-rotate"></i> Reset
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
const MAX_CYCLE = 15;

let customers = [];
let editingId = null;

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
   LOAD LIST
   ============================================================ */
async function loadCustomers() {
  const q = ($('searchCust')?.value || '').trim();
  try {
    const res = await fetch('/silog/admin/customers/list?q=' + encodeURIComponent(q), {
      headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
      credentials: 'same-origin',
    });
    const json = await res.json();
    customers = json.data || [];
  } catch (e) {
    customers = [];
  }
  renderList();
}

function renderList() {
  const el = $('listWrap');
  if (!customers.length) {
    el.innerHTML = `<div class="empty">
      <i class="fa-solid fa-users"></i>
      <b>Belum ada customer</b>
      <p>Klik "Customer Baru" untuk mulai.</p>
    </div>`;
    return;
  }

  el.innerHTML = `
    <table>
      <thead><tr>
        <th>Kode</th>
        <th>Nama</th>
        <th style="width:70px;text-align:center">Cycle</th>
        <th style="width:100px;text-align:right">Aksi</th>
      </tr></thead>
      <tbody>
        ${customers.map(c => `
          <tr data-id="${c.id}" class="${editingId === c.id ? 'selected' : ''}">
            <td class="strong">${esc(c.code_customer)}</td>
            <td>${esc(c.name)}</td>
            <td style="text-align:center">
              <span style="font-weight:700;color:var(--accent)">${c.cycle_count || 0}</span>
              <small style="color:var(--text-3)">/${MAX_CYCLE}</small>
            </td>
            <td>
              <div class="actions">
                <button class="btn btn-icon btn-sm" onclick="editCustomer(${c.id})" title="Edit">
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn btn-icon btn-sm danger" onclick="deleteCustomer(${c.id})" title="Hapus">
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

/* ============================================================
   FORM
   ============================================================ */
function resetForm() {
  editingId = null;
  $('cf_id').value = '';
  $('cf_code').value = '';
  $('cf_name').value = '';
  $('cf_contact').value = '';
  $('cf_phone').value = '';
  $('cf_address').value = '';
  $('cycleList').innerHTML = '';
  $('formTitle').innerHTML = '<i class="fa-solid fa-plus"></i> Customer Baru';
  $('formMode').style.display = 'none';
  renderList();
}

function editCustomer(id) {
  const c = customers.find(x => x.id === id);
  if (!c) return;

  editingId = id;
  $('cf_id').value = c.id;
  $('cf_code').value = c.code_customer || '';
  $('cf_name').value = c.name || '';
  $('cf_contact').value = c.contact_person || '';
  $('cf_phone').value = c.phone || '';
  $('cf_address').value = c.address || '';

  $('cycleList').innerHTML = '';
  (c.cycles || []).forEach(cc => addCycleRow({
    id: cc.id,
    cycle_number: cc.cycle_number,
    etd_from_ise: (cc.etd_from_ise || '').substring(0,5),
    eta_at_customer: (cc.eta_at_customer || '').substring(0,5),
  }));

  $('formTitle').innerHTML = '<i class="fa-solid fa-pen"></i> Edit Customer · ' + esc(c.code_customer);
  $('formMode').style.display = 'inline-flex';
  renderList();
  window.scrollTo({top:0, behavior:'smooth'});
}

function addCycleRow(data = null) {
  const list = $('cycleList');
  const rows = list.querySelectorAll('.cycle-row');

  if (rows.length >= MAX_CYCLE) {
    toast('Maksimal ' + MAX_CYCLE + ' cycle per customer', 'error');
    return;
  }

  // Tentukan nomor cycle berikutnya
  const usedNums = Array.from(rows).map(r => parseInt(r.querySelector('input[data-k="cycle_number"]').value || '0'));
  let next = 1;
  for (let i = 1; i <= MAX_CYCLE; i++) {
    if (!usedNums.includes(i)) { next = i; break; }
  }

  const row = document.createElement('div');
  row.className = 'cycle-row';
  row.innerHTML = `
    <input type="hidden" data-k="id" value="${data?.id || ''}" />
    <div class="num" data-role="num">#${next}</div>
    <input type="hidden" data-k="cycle_number" value="${data?.cycle_number || next}" />
    <input type="time" class="input" data-k="etd_from_ise" value="${data?.etd_from_ise || ''}" placeholder="ETD from ISE" />
    <input type="time" class="input" data-k="eta_at_customer" value="${data?.eta_at_customer || ''}" placeholder="ETA at Customer" />
    <button type="button" class="x" title="Hapus cycle"><i class="fa-solid fa-xmark"></i></button>
  `;

  row.querySelector('.x').addEventListener('click', () => {
    row.remove();
    renumberCycles();
  });

  list.appendChild(row);
}

function renumberCycles() {
  // Tidak perlu renumber — biarkan gap. Cukup update label visual.
  $('cycleList').querySelectorAll('.cycle-row').forEach((row, i) => {
    row.querySelector('[data-role="num"]').textContent = '#' + (i + 1);
  });
}

function collectForm() {
  const cycles = [];
  $('cycleList').querySelectorAll('.cycle-row').forEach(row => {
    const id = row.querySelector('[data-k="id"]').value;
    const n = parseInt(row.querySelector('[data-k="cycle_number"]').value || '0');
    const etd = row.querySelector('[data-k="etd_from_ise"]').value;
    const eta = row.querySelector('[data-k="eta_at_customer"]').value;

    cycles.push({
      id: id || undefined,
      cycle_number: n,
      etd_from_ise: etd || null,
      eta_at_customer: eta || null,
      is_active: 1,
    });
  });
  return cycles;
}

/* ============================================================
   SUBMIT
   ============================================================ */
$('customerForm').addEventListener('submit', async e => {
  e.preventDefault();

  const id   = $('cf_id').value;
  const code_customer = $('cf_code').value.trim();
  const name = $('cf_name').value.trim();

  if (!code_customer) { toast('Kode wajib diisi', 'error'); return; }
  if (!name) { toast('Nama wajib diisi', 'error'); return; }

  const cycles = collectForm();

  const payload = {
    code_customer, name,
    contact_person: $('cf_contact').value.trim() || null,
    phone:          $('cf_phone').value.trim() || null,
    address:        $('cf_address').value.trim() || null,
  };

  if (cycles.length) payload.cycles = cycles;

  const btn = $('btnSave');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';

  try {
    let res;
    if (id) {
      res = await fetch(`/silog/admin/${id}/customers`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });
    } else {
      res = await fetch('/silog/admin/customers', {
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
    }

    const json = await res.json();
    if (!res.ok) {
      toast(json.errors ? Object.values(json.errors)[0] : (json.message || 'Gagal'), 'error');
      return;
    }

    toast(id ? 'Customer diperbarui' : 'Customer ditambahkan', 'success');
    resetForm();
    await loadCustomers();
  } catch (err) {
    toast('Error: ' + err.message, 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
  }
});

/* ============================================================
   DELETE
   ============================================================ */
async function deleteCustomer(id) {
  const c = customers.find(x => x.id === id);
  if (!c) return;
  if (!confirm(`Hapus customer "${c.name}" beserta semua cyclenya?`)) return;

  try {
    const res = await fetch(`/silog/admin/${id}/customers`, {
      method: 'DELETE',
      headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
      credentials: 'same-origin',
    });
    const json = await res.json();
    if (!res.ok) { toast(json.message || 'Gagal', 'error'); return; }
    toast('Customer dihapus', 'success');
    if (editingId === id) resetForm();
    await loadCustomers();
  } catch (err) {
    toast('Error: ' + err.message, 'error');
  }
}

/* ============================================================
   BUTTONS
   ============================================================ */
$('btnNew').addEventListener('click', resetForm);
$('btnReset').addEventListener('click', resetForm);
$('btnAddCycle').addEventListener('click', () => addCycleRow());
$('searchCust').addEventListener('input', debounce(loadCustomers, 250));

function debounce(fn, ms) {
  let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
}

/* Init */
loadCustomers();
</script>
</body>
</html>