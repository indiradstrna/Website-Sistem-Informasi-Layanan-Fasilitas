<?php
// ============================================================
// supervisor/index.php — Dashboard Supervisor FMD
// Setara dengan: app/supervisor/page.tsx
// ============================================================

require_once __DIR__ . '/../includes/auth.php';
requireRole('supervisor', 'admin');
require_once __DIR__ . '/../includes/layout.php';

$session  = getSession();
$userName = $session['fullName'];

renderPageHead('Dashboard Supervisor');
?>

  <div class="topbar">
    <div class="topbar-content">
      <div class="topbar-left">
        <div class="topbar-logo">
          <img src="../assets/img/logo.png" alt="SEAMEO BIOTROP" />
        </div>
        <span class="topbar-title" id="page-title">Dashboard Supervisor</span>
      </div>
      <div class="topbar-user">
        <span style="font-size:.82rem;color:var(--color-slate-600);"><?= htmlspecialchars($userName) ?></span>
      </div>
    </div>
  </div>

<div class="app-layout">
  <?php renderSidebar('supervisor', 'dashboard', $userName, '../'); ?>

  <div class="main-content">
    <div class="page-content">
      <div class="page-content-inner" id="view-container">
        <div style="text-align:center;padding:3rem;">
          <div class="spinner" style="border-color:rgba(16,185,129,.2);border-top-color:var(--color-emerald-600);width:2.5rem;height:2.5rem;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal-overlay" id="modal-detail">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h3 class="modal-title" id="modal-detail-title">Detail Perbaikan</h3>
      <button class="modal-close modal-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body" id="modal-detail-body"></div>
    <div class="modal-footer" id="modal-detail-footer"></div>
  </div>
</div>

<!-- MODAL RAB INPUT -->
<div class="modal-overlay" id="modal-rab">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h3 class="modal-title">Input RAB (Rincian Anggaran Biaya)</h3>
      <button class="modal-close modal-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="grid-3" style="margin-bottom:1rem;">
        <div class="form-group" style="grid-column:1/3;">
          <label class="form-label">Nama Item</label>
          <input type="text" id="rab-item-name" class="form-input" placeholder="Contoh: Cat Tembok, Keramik Lantai..." />
        </div>
        <div class="form-group"><label class="form-label">Qty</label><input type="number" id="rab-item-qty" class="form-input" value="1" min="1" /></div>
        <div class="form-group"><label class="form-label">Harga Satuan (Rp)</label><input type="number" id="rab-item-price" class="form-input" value="0" min="0" /></div>
        <div style="display:flex;align-items:flex-end;"><button class="btn btn-primary btn-full" onclick="addRabItem()">+ Tambah</button></div>
      </div>
      <div class="rab-table-wrap">
        <table>
          <thead><tr><th>Item</th><th style="text-align:right">Qty</th><th style="text-align:right">Harga</th><th style="text-align:right">Total</th><th></th></tr></thead>
          <tbody id="rab-table-body"><tr><td colspan="5" style="text-align:center;color:var(--color-slate-400);padding:1.5rem;">Belum ada item</td></tr></tbody>
        </table>
      </div>
      <div style="margin-top:1rem;text-align:right;font-weight:700;font-size:1rem;">
        Total RAB: <span id="rab-total" style="color:var(--color-blue-600);">Rp 0</span>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline modal-close-btn">Batal</button>
      <button class="btn btn-primary" onclick="submitRAB()">Ajukan RAB ke Manager FMD</button>
    </div>
  </div>
</div>

<div id="toast-container"></div>
<script src="../assets/js/main.js"></script>
<script>
window.BASE_URL = '<?= BASE_URL ?>';
const SVP_NAME = <?= json_encode($userName) ?>;
const API_BASE = '<?= BASE_URL ?>/api/';

let allRepairs = [];
let currentRequestId = null;
let currentRequestNote = '';
let rabItems = [];
let currentPage = 1;
let itemsPerPage = 10;

async function loadData(silent = false) {
  try {
    const res = await api(API_BASE + 'requests.php?action=get_repair');
    allRepairs = Array.isArray(res) ? res : [];
    if (silent) {
        if (window._currentView === 'verification') {
            const list = allRepairs.filter(r => ['pending','verified'].includes(r.status));
            const start = (currentPage - 1) * itemsPerPage;
            const paged = list.slice(start, start + itemsPerPage);
            const tbody = document.getElementById('verification-table-body');
            if (tbody) {
                tbody.innerHTML = paged.length === 0 ? `<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Tidak ada data</td></tr>` :
                    paged.map(r => `<tr><td data-label="Pemohon"><div style="font-weight:600;">${r.applicant_name}</div><div style="font-size:.75rem;color:var(--color-slate-400);">${r.applicant_unit}</div></td><td data-label="Lokasi" style="font-size:.82rem;">${r.location_detail || '-'}</td><td data-label="Prioritas">${getStatusBadge(r.priority || 'medium')}</td><td data-label="Tanggal" style="font-size:.82rem;">${formatDate(r.incident_date)}</td><td data-label="Status">${getStatusBadge(r.status)}</td><td data-label="Aksi"><button class="btn btn-primary btn-sm" onclick="openDetail(${r.id})">Tindak Lanjut</button></td></tr>`).join('');
                updatePagination(list, 'verif-pagination', 'goVerifPage', 'changeVerifRows');
            }
        } else if (window._currentView === 'in-progress') {
            const list = allRepairs.filter(r => r.status === 'in-progress' || r.status === 'approved' || r.status === 'waiting_manager_fmd');
            const start = (currentPage - 1) * itemsPerPage;
            const paged = list.slice(start, start + itemsPerPage);
            const tbody = document.getElementById('progress-table-body');
            if (tbody) {
                tbody.innerHTML = paged.length === 0 ? `<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Tidak ada perbaikan aktif</td></tr>` :
                    paged.map(r => `<tr><td data-label="Pemohon"><div style="font-weight:600;">${r.applicant_name}</div><div style="font-size:.75rem;color:var(--color-slate-400);">${r.applicant_unit}</div></td><td data-label="Masalah" style="font-size:.82rem;max-width:180px;">${r.issue_description || '-'}</td><td data-label="Lokasi" style="font-size:.82rem;">${r.location_detail || '-'}</td><td data-label="Status">${getStatusBadge(r.status)}</td><td data-label="Aksi"><button class="btn btn-outline btn-sm" onclick="openDetail(${r.id})">Update</button></td></tr>`).join('');
                updatePagination(list, 'progress-pagination', 'goProgressPage', 'changeProgressRows');
            }
        }
    } else {
        renderCurrentView();
    }
  } catch(e) {
    if (!silent) Toast.error('Gagal memuat data.');
  }
}

function switchView(viewId) {
  document.querySelectorAll('.nav-item').forEach(el => el.classList.toggle('active', el.dataset.view === viewId));
  const titles = { dashboard: 'Dashboard Supervisor', verification: 'Verifikasi & RAB', 'in-progress': 'Sedang Dikerjakan', history: 'Riwayat Perbaikan', profile: 'Profil' };
  const titleEl = document.getElementById('page-title');
  if (titleEl) titleEl.textContent = titles[viewId] || viewId;
  window._currentView = viewId;
  currentPage = 1;
  renderCurrentView();
}

function renderCurrentView() {
  const view = window._currentView || 'dashboard';
  const container = document.getElementById('view-container');
  switch(view) {
    case 'dashboard':    container.innerHTML = renderDashboard(); break;
    case 'verification': container.innerHTML = renderVerification(); break;
    case 'in-progress':  container.innerHTML = renderInProgress(); break;
    case 'history':      container.innerHTML = renderHistory(); break;
    case 'profile':      container.innerHTML = renderProfile(); break;
  }
}

function renderDashboard() {
  const pending    = allRepairs.filter(r => r.status === 'pending').length;
  const verified   = allRepairs.filter(r => ['verified','waiting_manager_fmd'].includes(r.status)).length;
  const inProgress = allRepairs.filter(r => r.status === 'in-progress' || r.status === 'approved').length;
  const done       = allRepairs.filter(r => r.status === 'completed').length;

  const waitingRAB = allRepairs.filter(r => r.status === 'pending');

  return `
  <div class="page-header">
    <h1>Dashboard Supervisor FMD</h1>
    <p>Kelola verifikasi dan progress perbaikan fasilitas</p>
  </div>
  <div class="stats-grid">
    <div class="stat-card border-left-amber" style="cursor:pointer;" onclick="switchView('verification')">
      <div class="stat-label">Perlu Verifikasi</div>
      <div class="stat-value" style="color:var(--color-amber-600);">${pending}</div>
      <div class="stat-sub">Menunggu review</div>
    </div>
    <div class="stat-card border-left-orange" style="cursor:pointer;" onclick="switchView('in-progress')">
      <div class="stat-label">Sedang Dikerjakan</div>
      <div class="stat-value" style="color:var(--color-orange-500);">${inProgress}</div>
      <div class="stat-sub">In progress / approved</div>
    </div>
    <div class="stat-card border-left-blue">
      <div class="stat-label">RAB Diajukan</div>
      <div class="stat-value" style="color:var(--color-blue-600);">${verified}</div>
      <div class="stat-sub">Menunggu approval</div>
    </div>
    <div class="stat-card border-left-emerald">
      <div class="stat-label">Selesai</div>
      <div class="stat-value" style="color:var(--color-emerald-600);">${done}</div>
      <div class="stat-sub">Completed</div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header"><div class="card-title">Navigasi Utama</div><div class="card-desc">Akses cepat menu operasional</div></div>
    <div class="card-body grid-main" style="gap:1rem;">
      <div class="grid-3" style="gap:1rem; grid-column: 1 / -1;">
        ${[
          {id:'verification', icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>', label:'Verifikasi & RAB', desc:'Review & hitung biaya'},
          {id:'in-progress', icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 12-8.5 8.5c-.83.83-2.17.83-3 0 0 0 0 0 0 0a2.12.12 0 0 1 0-3L12 9"/><path d="M17.64 15 22 10.64"/><path d="m20.91 11.7-1.25-1.25c-.6-.6-.93-1.4-.93-2.25v-.86L16.01 4.6a5.56 5.56 0 0 0-3.94-1.64H9l.92.82A6.18 6.18 0 0 1 12 8.4v1.56l2 2h2.47l2.26 1.91"/></svg>', label:'Pekerjaan Aktif', desc:'Update progress harian'},
          {id:'history', icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.98"/></svg>', label:'Riwayat Perbaikan', desc:'Track rekaman data lama'},
        ].map(m => `
          <div class="stat-card" style="display:flex; align-items:center; gap:1rem; cursor:pointer; padding:1rem; border:1px solid var(--color-slate-200); transition:all 0.2s;" 
               onclick="switchView('${m.id}')" 
               onmouseover="this.style.borderColor='var(--color-emerald-500)'; this.style.background='var(--color-emerald-50)';" 
               onmouseout="this.style.borderColor='var(--color-slate-200)'; this.style.background='white';">
            <div style="background:var(--color-emerald-50); color:var(--color-emerald-700); width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              ${m.icon}
            </div>
            <div>
              <div style="font-weight:700; font-size:0.875rem; color:var(--color-slate-800);">${m.label}</div>
              <div style="font-size:0.75rem; color:var(--color-slate-500);">${m.desc}</div>
            </div>
          </div>
        `).join('')}
      </div>
    </div>
  </div>

  ${waitingRAB.length > 0 ? `
  <div class="card">
    <div class="card-header">
      <div class="card-title">Perbaikan Baru — Perlu Verifikasi</div>
    </div>
    <div class="card-body">
      ${waitingRAB.slice(0,5).map(r => `
      <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid var(--color-slate-100);">
        <div>
          <div style="font-weight:600;">${r.applicant_name} <span style="color:var(--color-slate-400);font-weight:400;">(${r.applicant_unit})</span></div>
          <div style="font-size:.78rem;color:var(--color-slate-500);">${r.location_detail || '-'}: ${r.issue_description || '-'}</div>
          <div style="font-size:.72rem;color:var(--color-slate-400);">${formatDate(r.incident_date)} ${r.incident_time || ''}</div>
        </div>
        <button class="btn btn-primary btn-sm" onclick="openDetail(${r.id})">Verifikasi</button>
      </div>`).join('')}
    </div>
  </div>` : ''}`;
}

function renderRowsSelector(onChangeFunc) {
  return `
    <div style="display:flex;align-items:center;gap:.5rem;font-size:.78rem;color:var(--color-slate-500);">
      <span>Show</span>
      <select class="form-select" onchange="${onChangeFunc}(this.value)" style="width:70px;padding:.25rem .5rem;height:auto;font-size:.78rem;">
        ${[5, 10, 25, 50].map(v => `<option value="${v}" ${v == itemsPerPage ? 'selected' : ''}>${v}</option>`).join('')}
      </select>
      <span>entries</span>
    </div>
  `;
}

function updatePagination(data, containerId, goPageFunc, changeRowsFunc) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const total = data.length;
  const totalPages = Math.ceil(total / itemsPerPage);

  let html = `<div style="display:flex;align-items:center;gap:.5rem;justify-content:space-between;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:1.5rem;">
      <span style="font-size:.78rem;color:var(--color-slate-400);">Menampilkan ${Math.min(itemsPerPage, total)} dari ${total} data</span>
      ${renderRowsSelector(changeRowsFunc)}
    </div>
    <div class="pagination">`;
  html += `<button class="pag-btn" onclick="${goPageFunc}(${currentPage-1})" ${currentPage===1?'disabled':''}>‹</button>`;
  for(let i=1;i<=totalPages;i++) {
    if(i===1 || i===totalPages || Math.abs(i-currentPage) <= 1) {
      html += `<button class="pag-btn ${i===currentPage?'active':''}" onclick="${goPageFunc}(${i})">${i}</button>`;
    } else if(Math.abs(i-currentPage) === 2) {
      html += `<span style="padding:0 4px;color:#94a3b8">…</span>`;
    }
  }
  html += `<button class="pag-btn" onclick="${goPageFunc}(${currentPage+1})" ${currentPage===totalPages?'disabled':''}>›</button>`;
  html += `</div></div>`;
  container.innerHTML = html;
}

function renderVerification() {
  const list = allRepairs.filter(r => ['pending','verified'].includes(r.status));
  const start = (currentPage - 1) * itemsPerPage;
  const paged = list.slice(start, start + itemsPerPage);

  const html = `
  <div class="page-header"><h1>Verifikasi & RAB</h1><p>Verifikasi laporan perbaikan dan input RAB</p></div>
  <div class="card">
    <div class="table-wrap table-stack-mobile">
      <table>
        <thead><tr><th>Pemohon</th><th>Lokasi</th><th>Prioritas</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody id="verification-table-body">
          ${paged.length === 0 ? `<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Tidak ada data</td></tr>` :
            paged.map(r => `
            <tr>
              <td data-label="Pemohon"><div style="font-weight:600;">${r.applicant_name}</div><div style="font-size:.75rem;color:var(--color-slate-400);">${r.applicant_unit}</div></td>
              <td data-label="Lokasi" style="font-size:.82rem;">${r.location_detail || '-'}</td>
              <td data-label="Prioritas">${getStatusBadge(r.priority || 'medium')}</td>
              <td data-label="Tanggal" style="font-size:.82rem;">${formatDate(r.incident_date)}</td>
              <td data-label="Status">${getStatusBadge(r.status)}</td>
              <td data-label="Aksi"><button class="btn btn-primary btn-sm" onclick="openDetail(${r.id})">Tindak Lanjut</button></td>
            </tr>`).join('')}
        </tbody>
      </table>
    </div>
    <div style="padding:1rem;" id="verif-pagination"></div>
  </div>`;
  setTimeout(() => updatePagination(list, 'verif-pagination', 'goVerifPage', 'changeVerifRows'), 50);
  return html;
}

function goVerifPage(page) { currentPage = page; renderCurrentView(); }
function changeVerifRows(val) { itemsPerPage = parseInt(val); currentPage = 1; renderCurrentView(); }

function renderInProgress() {
  const list = allRepairs.filter(r => ['approved','in-progress'].includes(r.status));
  const start = (currentPage - 1) * itemsPerPage;
  const paged = list.slice(start, start + itemsPerPage);

  const html = `
  <div class="page-header"><h1>Sedang Dikerjakan</h1><p>Perbaikan yang sedang dalam progress</p></div>
  <div class="card">
    <div class="table-wrap table-stack-mobile">
      <table>
        <thead><tr><th>Pemohon</th><th>Masalah</th><th>Lokasi</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody id="progress-table-body">
          ${paged.length === 0 ? `<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Tidak ada perbaikan aktif</td></tr>` :
            paged.map(r => `
            <tr>
              <td data-label="Pemohon"><div style="font-weight:600;">${r.applicant_name}</div><div style="font-size:.75rem;color:var(--color-slate-400);">${r.applicant_unit}</div></td>
              <td data-label="Masalah" style="font-size:.82rem;max-width:180px;">${r.issue_description || '-'}</td>
              <td data-label="Lokasi" style="font-size:.82rem;">${r.location_detail || '-'}</td>
              <td data-label="Status">${getStatusBadge(r.status)}</td>
              <td data-label="Aksi"><button class="btn btn-outline btn-sm" onclick="openDetail(${r.id})">Update</button></td>
            </tr>`).join('')}
        </tbody>
      </table>
    </div>
    <div style="padding:1rem;" id="progress-pagination"></div>
  </div>`;
  setTimeout(() => updatePagination(list, 'progress-pagination', 'goProgressPage', 'changeProgressRows'), 50);
  return html;
}

function goProgressPage(page) { currentPage = page; renderCurrentView(); }
function changeProgressRows(val) { itemsPerPage = parseInt(val); currentPage = 1; renderCurrentView(); }

function renderHistory() {
  const list = allRepairs.filter(r => ['completed','rejected'].includes(r.status));
  const start = (currentPage - 1) * itemsPerPage;
  const paged = list.slice(start, start + itemsPerPage);

  const html = `
  <div class="page-header"><h1>Riwayat Perbaikan</h1></div>
  <div class="card">
    <div class="table-wrap table-stack-mobile">
      <table>
        <thead><tr><th>Pemohon</th><th>Masalah</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          ${paged.length === 0 ? `<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Belum ada riwayat</td></tr>` :
            paged.map(r => `
            <tr>
              <td data-label="Pemohon"><div style="font-weight:600;">${r.applicant_name}</div><div style="font-size:.75rem;color:var(--color-slate-400);">${r.applicant_unit}</div></td>
              <td data-label="Masalah" style="font-size:.82rem;">${r.issue_description || '-'}</td>
              <td data-label="Tanggal" style="font-size:.82rem;">${formatDate(r.incident_date)}</td>
              <td data-label="Status">${getStatusBadge(r.status)}</td>
              <td data-label="Aksi"><button class="btn btn-ghost btn-sm" onclick="openDetail(${r.id})">Detail</button></td>
            </tr>`).join('')}
        </tbody>
      </table>
    </div>
    <div style="padding:1rem;" id="history-pagination"></div>
  </div>`;
  setTimeout(() => updatePagination(list, 'history-pagination', 'goHistoryPage', 'changeHistoryRows'), 50);
  return html;
}

function goHistoryPage(page) { currentPage = page; renderCurrentView(); }
function changeHistoryRows(val) { itemsPerPage = parseInt(val); currentPage = 1; renderCurrentView(); }

function renderProfile() {
  return `
  <div class="page-header"><h1>Profil</h1></div>
  <div class="card" style="max-width:450px;">
    <div class="card-body">
      <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div style="width:60px;height:60px;border-radius:50%;background:var(--color-emerald-700);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;font-weight:800;">${SVP_NAME.charAt(0).toUpperCase()}</div>
        <div><div style="font-size:1.1rem;font-weight:700;">${SVP_NAME}</div><div style="color:var(--color-slate-500);font-size:.875rem;">Supervisor FMD</div></div>
      </div>
    </div>
  </div>`;
}

function openDetail(id) {
  const r = allRepairs.find(row => String(row.id) === String(id));
  if (!r) return;
  currentRequestId   = id;
  currentRequestNote = r.note || '';

  document.getElementById('modal-detail-title').textContent = `Detail Perbaikan #${id}`;
  document.getElementById('modal-detail-body').innerHTML = `
  <div class="detail-info">
    <div><div class="detail-label">Pemohon</div><div class="detail-value">${r.applicant_name}</div></div>
    <div><div class="detail-label">Unit</div><div class="detail-value">${r.applicant_unit}</div></div>
    <div><div class="detail-label">Lokasi</div><div class="detail-value">${r.location_detail || '-'}</div></div>
    <div><div class="detail-label">Prioritas</div><div class="detail-value">${getStatusBadge(r.priority || 'medium')}</div></div>
    <div><div class="detail-label">Tanggal Kejadian</div><div class="detail-value">${r.incident_date || '-'} ${r.incident_time || ''}</div></div>
    <div><div class="detail-label">Status</div><div class="detail-value">${getStatusBadge(r.status)}</div></div>
    <div class="full-width detail-box"><div class="detail-label">Deskripsi Masalah</div><div class="detail-value">${r.issue_description || '-'}</div></div>
    ${r.note ? `<div class="full-width"><div class="detail-label" style="color:var(--color-amber-800);">Catatan</div><div class="note-history">${r.note}</div></div>` : ''}
    ${!['completed','rejected'].includes(r.status) ? `
    <div class="full-width"><div class="detail-label">Catatan</div><textarea id="svp-note" class="form-textarea" placeholder="Catatan supervisor..."></textarea></div>` : ''}
  </div>`;

  const footer = document.getElementById('modal-detail-footer');
  const isFinal = ['completed','rejected'].includes(r.status);
  if (isFinal) {
    footer.innerHTML = `<span style="color:var(--color-slate-400);font-size:.875rem;">Pengajuan telah selesai.</span><button class="btn btn-outline modal-close-btn">Tutup</button>`;
  } else {
    let btns = `<button class="btn btn-outline modal-close-btn">Batal</button>`;
    if (r.status === 'pending') {
      btns += `<button class="btn btn-outline" onclick="openRABModal()">Input RAB</button>`;
      btns += `<button class="btn btn-primary" onclick="doUpdateStatus(${id},'in-progress')">→ Mulai Kerjakan</button>`;
      btns += `<button class="btn btn-danger" onclick="doUpdateStatus(${id},'rejected')">✕ Tolak</button>`;
    } else if (r.status === 'in-progress') {
      btns += `<button class="btn btn-success" onclick="doUpdateStatus(${id},'completed')">✓ Selesai</button>`;
    } else if (r.status === 'verified') {
      btns += `<button class="btn btn-primary" onclick="approveRAB(${id})">Approve RAB ke Manager FMD</button>`;
    }
    footer.innerHTML = btns;
  }

  // Load RAB
  loadRABView(id);
  Modal.open('modal-detail');
}

async function loadRABView(id) {
  const container = document.getElementById('modal-detail-body');
  const existing  = document.getElementById('rab-section');
  const res = await api(API_BASE + `requests.php?action=get_repair_budget&request_id=${id}`);
  const items = Array.isArray(res) ? res : [];

  let rabHtml = '';
  if (items.length > 0) {
    const total = items.reduce((s, i) => s + parseFloat(i.total_price || 0), 0);
    rabHtml = `<div id="rab-section" style="margin-top:1rem;">
      <div class="detail-label" style="margin-bottom:.5rem;">Rincian RAB</div>
      <div class="rab-table-wrap">
        <table>
          <thead><tr><th>Item</th><th style="text-align:right">Qty</th><th style="text-align:right">Harga</th><th style="text-align:right">Total</th></tr></thead>
          <tbody>
            ${items.map(i => `<tr><td style="font-size:.82rem;">${i.item_name}</td><td style="text-align:right;font-size:.82rem;">${i.quantity}</td><td style="text-align:right;font-size:.82rem;">${formatRupiah(i.unit_price)}</td><td style="text-align:right;font-size:.82rem;font-weight:600;">${formatRupiah(i.total_price)}</td></tr>`).join('')}
            <tr class="rab-total-row"><td colspan="3" style="text-align:right;padding-right:1rem;">Total:</td><td style="text-align:right;color:var(--color-blue-600);">${formatRupiah(total)}</td></tr>
          </tbody>
        </table>
      </div>
    </div>`;
  }

  if (rabHtml && container) {
    container.insertAdjacentHTML('beforeend', rabHtml);
  }
}

async function doUpdateStatus(id, newStatus) {
  const note = document.getElementById('svp-note')?.value || '';
  const res  = await apiPost(API_BASE + 'requests.php', { action: 'update_status', id, type: 'Repair', status: newStatus, note, prev_note: currentRequestNote });
  if (res.success) {
    Toast.success('Status diperbarui.');
    Modal.close('modal-detail');
    await loadData();
  } else {
    Toast.error(res.message);
  }
}

async function approveRAB(id) {
  const res = await apiPost(API_BASE + 'requests.php', { action: 'approve_repair_budget', request_id: id });
  if (res.success) {
    Toast.success(res.message);
    Modal.close('modal-detail');
    await loadData();
  } else {
    Toast.error(res.message);
  }
}

// RAB Modal
function openRABModal() {
  rabItems = [];
  renderRABTable();
  Modal.open('modal-rab');
}
function addRabItem() {
  const name  = document.getElementById('rab-item-name')?.value?.trim();
  const qty   = parseInt(document.getElementById('rab-item-qty')?.value || '1');
  const price = parseFloat(document.getElementById('rab-item-price')?.value || '0');
  if (!name || price <= 0 || qty <= 0) { Toast.error('Mohon lengkapi data item RAB'); return; }
  rabItems.push({ id: Date.now(), itemName: name, quantity: qty, unitPrice: price });
  document.getElementById('rab-item-name').value  = '';
  document.getElementById('rab-item-qty').value   = '1';
  document.getElementById('rab-item-price').value = '0';
  renderRABTable();
}
function removeRabItem(id) { rabItems = rabItems.filter(i => i.id !== id); renderRABTable(); }
function renderRABTable() {
  const tbody = document.getElementById('rab-table-body');
  if (!tbody) return;
  if (!rabItems.length) { tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--color-slate-400);padding:1.5rem;">Belum ada item</td></tr>`; document.getElementById('rab-total').textContent = 'Rp 0'; return; }
  let total = 0;
  tbody.innerHTML = rabItems.map(i => { const t = i.quantity * i.unitPrice; total += t; return `<tr><td>${i.itemName}</td><td style="text-align:right;">${i.quantity}</td><td style="text-align:right;">${formatRupiah(i.unitPrice)}</td><td style="text-align:right;font-weight:600;">${formatRupiah(t)}</td><td><button class="btn btn-danger btn-sm" onclick="removeRabItem(${i.id})">✕</button></td></tr>`; }).join('');
  document.getElementById('rab-total').textContent = formatRupiah(total);
}
async function submitRAB() {
  if (!rabItems.length) { Toast.error('Minimal 1 item RAB.'); return; }
  const res = await apiPost(API_BASE + 'requests.php', { action: 'submit_repair_budget', request_id: currentRequestId, items: JSON.stringify(rabItems) });
  if (res.success) { Toast.success('RAB berhasil diajukan!'); Modal.close('modal-rab'); Modal.close('modal-detail'); await loadData(); }
  else { Toast.error(res.message); }
}

// INIT
document.addEventListener('DOMContentLoaded', () => {
  Modal.init();
  loadData().then(() => switchView('dashboard'));

  // Auto Update: Refresh data every 20 seconds
  setInterval(async () => {
    // Check if modal is open to avoid disrupting user
    const modalDetail = document.getElementById('modal-detail');
    const modalRab = document.getElementById('modal-rab');
    const isModalOpen = (modalDetail && modalDetail.classList.contains('open')) || 
                        (modalRab && modalRab.classList.contains('open'));

    if (!isModalOpen) {
      await loadData(true);
    }
  }, 20000);
});
</script>
</body>
</html>
