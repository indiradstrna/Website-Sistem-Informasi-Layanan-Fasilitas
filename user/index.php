<?php
// ============================================================
// user/index.php — Dashboard User (Staff)
// Setara dengan: app/user/page.tsx
// ============================================================

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$session   = getSession();
$userName  = $session['fullName'];
$userLogin = $session['username'];
$userRole  = $session['role'];
$dept      = $session['department'];
$teleChatId = $session['telegram_chat_id'];
$waNumber   = $session['whatsapp_number'];
$waApikey   = $session['callmebot_apikey'];

renderPageHead('Dashboard User');
?>

<style>
/* Notif Dropdown */
.notif-dropdown {
  position: absolute;
  top: 100%;
  right: -50px;
  width: 340px;
  background: #fff;
  border: 1px solid #e3e6f0;
  border-radius: 0.35rem;
  box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
  margin-top: 1.15rem;
  display: none;
  z-index: 1060;
  overflow: hidden;
}
.notif-dropdown::after {
  content: '';
  position: absolute;
  top: -10px;
  right: 58px;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
  border-bottom: 10px solid #fff;
}
.notif-dropdown.open {
  display: block;
  animation: slideDown .2s ease-out;
}
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}
.notif-header {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #fff;
}
.notif-header h3 {
  font-size: 0.85rem;
  font-weight: 700;
  margin: 0;
  color: #1e293b;
}
.notif-header .count {
  font-size: 0.8rem;
  color: #64748b;
}
.notif-list {
  max-height: 400px;
  overflow-y: auto;
  overflow-x: hidden;
}
.notif-list::-webkit-scrollbar {
  width: 5px;
}
.notif-list::-webkit-scrollbar-thumb {
  background: #e3e6f0;
  border-radius: 10px;
}
.notif-item {
  padding: 0.85rem 1.25rem;
  border-bottom: 1px solid #f1f5f9;
  cursor: pointer;
  transition: all 0.2s;
  display: block;
  width: 100%;
  text-decoration: none !important;
}
.notif-item:hover {
  background: #f8fafc;
}
.notif-item:last-child {
  border-bottom: none;
}
.notif-item-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.4rem;
}
.notif-type-badge {
  padding: 0.15rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.6rem;
  font-weight: 800;
  text-transform: uppercase;
  background: #eff6ff;
  color: #2563eb;
}
.notif-date {
  font-size: 0.75rem;
  color: #94a3b8;
}
.notif-title {
  font-weight: 700;
  font-size: 0.85rem;
  color: #334155;
  margin-bottom: 0.25rem;
  line-height: 1.3;
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.notif-subtitle {
  font-size: 0.72rem;
  color: #858796;
}
#notif-badge {
  position: absolute;
  top: 1px;
  right: 1px;
  font-size: 0.6rem;
  padding: 2px 4px;
  min-width: 16px;
  height: 16px;
  border: 1px solid #fff;
  border-radius: 999px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 800;
  line-height: 1;
}
.notif-empty {
  padding: 2rem 1rem;
  text-align: center;
  color: #94a3b8;
  font-size: 0.85rem;
}
</style>

  <div class="topbar">
    <div class="topbar-content">
      <div class="topbar-left">
        <div class="topbar-logo">
          <img src="../assets/img/logo.png" alt="SEAMEO BIOTROP" />
        </div>
        <button class="btn btn-ghost btn-sm" id="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Toggle Sidebar">☰</button>
        <span class="topbar-title" id="page-title">Dashboard</span>
      </div>
      <div class="topbar-user">
        <div style="display:flex; align-items:center;">
          <!-- Notifications -->
          <div id="notification-area" style="cursor:pointer; position:relative; display:flex; align-items:center; padding: 0.5rem; color: #d1d3e2;" onclick="toggleNotifDropdown(event)">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            <span id="notif-badge" class="nav-badge-count" style="display:none; background-color: #e74a3b;">0</span>
            
            <div id="notif-dropdown" class="notif-dropdown">
              <div class="notif-header">
                <h3>Notifikasi Proses</h3>
                <span class="count" id="notif-header-count">0 Baru</span>
              </div>
              <div id="notif-list" class="notif-list">
                <!-- Items rendered by JS -->
              </div>
            </div>
          </div>

          <div class="topbar-divider"></div>

          <!-- User Link -->
          <div class="topbar-user-link" onclick="switchView('profile')">
            <span class="topbar-user-name"><?= htmlspecialchars($userName) ?></span>
            <div class="user-avatar-sm">
              <?= strtoupper(mb_substr($userName, 0, 1)) ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<div class="app-layout">
  <?php renderSidebar('user', 'dashboard', $userName, '../'); ?>



  <div class="main-content">
    <div class="page-content">
      <div class="page-content-inner" id="view-container">
        <div style="text-align:center;padding:3rem;">
          <div class="spinner" style="border-color:rgba(16,185,129,.2);border-top-color:var(--color-primary-600);width:2.5rem;height:2.5rem;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL: FORM PENGAJUAN -->
<div class="modal-overlay" id="modal-form">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title" id="modal-form-title">Form Pengajuan</h3>
      <button class="modal-close modal-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body" id="modal-form-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline modal-close-btn">Batal</button>
      <button class="btn btn-primary" id="modal-submit-btn" onclick="doSubmitForm()">
        <span id="submit-btn-text">Kirim Pengajuan</span>
      </button>
    </div>
  </div>
</div>

<!-- MODAL: DETAIL REPORT -->
<div class="modal-overlay" id="modal-report-detail">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h3 class="modal-title" id="modal-report-title">Detail Laporan</h3>
      <button class="modal-close modal-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body" id="modal-report-body"></div>
    <div class="modal-footer"><button class="btn btn-outline modal-close-btn">Tutup</button></div>
  </div>
</div>

<!-- MODAL: SELESAIKAN PENGAJUAN -->
<div class="modal-overlay" id="modal-complete">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Konfirmasi Penyelesaian</h3>
      <button class="modal-close modal-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Feedback / Komentar (Opsional)</label>
        <textarea id="complete-feedback" class="form-textarea" placeholder="Bagikan pengalaman atau saran Anda..."></textarea>
      </div>
      <p style="font-size: 0.82rem; color: var(--color-slate-500); line-height: 1.5;"> Status pengajuan ini akan diubah menjadi <span class="badge badge-completed">Selesai</span>. </p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline modal-close-btn">Batal</button>
      <button class="btn btn-primary" id="btn-submit-complete">Selesaikan & Simpan</button>
    </div>
  </div>
</div>

<div id="toast-container"></div>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/tele.js"></script>
<script>
window.BASE_URL = '<?= BASE_URL ?>';
const USER_NAME = <?= json_encode($userName) ?>;
const USER_DEPT = <?= json_encode($dept) ?>;
let USER_TELE_CHAT_ID = <?= json_encode($teleChatId) ?>;
let USER_WA_NUMBER = <?= json_encode($waNumber) ?>;
let USER_WA_APIKEY = <?= json_encode($waApikey) ?>;
const API_BASE  = '<?= BASE_URL ?>/api/';

let myVehicle = [], myRoom = [], myZoom  = [], myRepair = [], myItem  = [];
let allVehicle= [], allRoom  = [], allZoom  = [], allRepair = [];
let currentRequests = [];
let currentPage = 1;
let itemsPerPage = 10;
let currentDetailReq = null;
let previousView = 'dashboard';

// --- NOTIFICATION LOGIC ---
function toggleNotifDropdown(e) {
  e.stopPropagation();
  const dropdown = document.getElementById('notif-dropdown');
  if (dropdown) dropdown.classList.toggle('open');
}

function renderNotifDropdown() {
  const container = document.getElementById('notif-list');
  const badge = document.getElementById('notif-badge');
  const headerCount = document.getElementById('notif-header-count');
  if (!container) return;

  // For user, "notifications" are submissions that are NOT 'pending' 
  // We'll filter for submissions that have been updated recently and are NOT read yet.
  const processed = currentRequests.filter(r => {
    const isProcessed = r.status !== 'pending' || (r.note && r.note.trim() !== '');
    const isRead = localStorage.getItem(`read_${r._type.toLowerCase()}_${r.id}`);
    return isProcessed && !isRead;
  });
  // Take top 10 most recent
  const recentUpdates = processed.slice(0, 10);

  if (recentUpdates.length === 0) {
    container.innerHTML = `<div class="notif-empty">Belum ada pembaruan status untuk pengajuan Anda.</div>`;
    if (badge) badge.style.display = 'none';
    if (headerCount) headerCount.textContent = '0 Baru';
    return;
  }

  // Update badge with count of non-pending but relatively new items
  // For now, let's just show the count of processed items if any
  const notifCount = recentUpdates.length;
  if (badge) {
    badge.textContent = notifCount;
    badge.style.display = 'flex';
  }
  if (headerCount) headerCount.textContent = `${notifCount} Info Baru`;

  container.innerHTML = recentUpdates.map(r => {
    const safeCreated = (r.created_at && r.created_at.includes(' ')) ? r.created_at.replace(' ', 'T') : r.created_at;
    const d = new Date(safeCreated);
    const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    const typeLabel = { Vehicle:'Mobil', Room:'Ruang', Zoom:'Zoom', Repair:'Perbaikan', Item:'Barang' }[r._type] || r._type;
    const detail = r._type === 'Vehicle' ? (VEHICLE_MAP[r.vehicle_id] || 'Mobilitas') :
                   r._type === 'Room'    ? r.room_id :
                   r._type === 'Zoom'    ? r.zoom_account_id :
                   r._type === 'Repair'  ? r.location_detail :
                   r._type === 'Item'    ? r.item_name : '-';
    
    return `
      <div class="notif-item" onclick="openReportDetail(${r.id}, '${r._type.toLowerCase()}')">
        <div class="notif-item-top">
          <span class="notif-type-badge" style="background:${getStatusColor(r.status)}20; color:${getStatusColor(r.status)}">${typeLabel}</span>
          <span class="notif-date">${dateStr}</span>
        </div>
        <div class="notif-title">${detail}</div>
        <div class="notif-subtitle">Status: <span style="font-weight:700; color:${getStatusColor(r.status)}">${getStatusLabel(r.status)}</span></div>
      </div>
    `;
  }).join('');
}

function getStatusColor(status) {
  const colors = {
    pending: '#f59e0b',
    approved: '#10b981',
    completed: '#3b82f6',
    returned: '#3b82f6',
    rejected: '#ef4444',
    verified: '#6366f1',
    'waiting_manager_fmd': '#6366f1',
    'waiting_manager_fad': '#6366f1',
    'waiting_ppk': '#a855f7',
    'waiting_bod': '#db2777',
    'approved_waiting_fund': '#d97706',
    'in-progress': '#f97316',
    'ready_for_user': '#0891b2'
  };
  return colors[status] || '#64748b';
}

// Close dropdown on click outside
document.addEventListener('click', (e) => {
  const dropdown = document.getElementById('notif-dropdown');
  const area = document.getElementById('notification-area');
  if (dropdown && dropdown.classList.contains('open')) {
    if (!dropdown.contains(e.target) && !area.contains(e.target)) {
      dropdown.classList.remove('open');
    }
  }
});

// Available resources (setara constants di Next.js)
const ROOMS = [
  { id: 'RUANG_STUDIO',   name: 'Ruang Studio (10-12 org)' },
  { id: 'RUANG_MATOA',    name: 'Ruang Matoa (45-75 org)' },
  { id: 'RUANG_JATI',     name: 'Ruang Jati (30-45 org)' },
  { id: 'RUANG_KENARI',   name: 'Ruang Kenari (10-12 org)' },
  { id: 'RUANG_EBONY',    name: 'Ruang Ebony (10-12 org)' },
  { id: 'GEDUNG_BUNDAR',  name: 'Gedung Bundar (75-100 org)' },
  { id: 'MAHONI',         name: 'Mahoni (10-12 org)' },
  { id: 'RG_DEWAN',       name: 'Rg. Dewan (5-7 org)' },
  { id: 'RG_PDID',        name: 'Rg. PDID (20-30 org)' },
  { id: 'RUANG_KMD',      name: 'Ruang Rapat KMD (8-10 orang)' },
  { id: 'RUANG_HERBARIUM',name: 'Ruang Rapat Herbarium (30-35 org)' },
];
const ZOOM_ACCOUNTS = [
  { id: 'zoom_01', name: 'Zoom Premium 1 (Kap. 100)' },
  { id: 'zoom_02', name: 'Zoom Webinar (Kap. 500)' },
];

async function loadMyData(silent = false) {
  try {
    const [v,r,z,rep,itm, av, ar, az, arep] = await Promise.all([
      api(API_BASE + 'requests.php?action=get_vehicle_by_user'),
      api(API_BASE + 'requests.php?action=get_room_by_user'),
      api(API_BASE + 'requests.php?action=get_zoom_by_user'),
      api(API_BASE + 'requests.php?action=get_repair_by_user'),
      api(API_BASE + 'requests.php?action=get_item_by_user'),
      api(API_BASE + 'requests.php?action=get_vehicle'),
      api(API_BASE + 'requests.php?action=get_room'),
      api(API_BASE + 'requests.php?action=get_zoom'),
      api(API_BASE + 'requests.php?action=get_repair'),
    ]);
    myVehicle = Array.isArray(v)   ? v   : [];
    myRoom    = Array.isArray(r)   ? r   : [];
    myZoom    = Array.isArray(z)   ? z   : [];
    myRepair  = Array.isArray(rep) ? rep : [];
    myItem    = Array.isArray(itm) ? itm : [];
    allVehicle= Array.isArray(av)  ? av  : [];
    allRoom   = Array.isArray(ar)  ? ar  : [];
    allZoom   = Array.isArray(az)  ? az  : [];
    allRepair = Array.isArray(arep)? arep: [];

    currentRequests = [
      ...myVehicle.map(r=>({...r,_type:'Vehicle'})),
      ...myRoom.map(r=>({...r,_type:'Room'})),
      ...myZoom.map(r=>({...r,_type:'Zoom'})),
      ...myRepair.map(r=>({...r,_type:'Repair'})),
      ...myItem.map(r=>({...r,_type:'Item'})),
    ].sort((a,b)=>new Date(b.created_at)-new Date(a.created_at));

    renderNotifDropdown();

    if (silent) {
        if (window._currentView === 'my_reports' && !document.getElementById('my-reports-search')?.value) {
            const status = document.getElementById('my-reports-status')?.value || 'all';
            if (status === 'all') {
                document.getElementById('my-reports-body').innerHTML = renderMyReportsRows(currentRequests, currentPage);
            }
        } else if (window._currentView === 'dashboard') {
            // Update dashboard counts silently if needed
            const pending = currentRequests.filter(r=>['pending','waiting_manager_fmd'].includes(r.status)).length;
            const approved = currentRequests.filter(r=>['approved', 'ready_for_user'].includes(r.status)).length;
            const done = currentRequests.filter(r=>['completed','returned'].includes(r.status)).length;
            
            const cards = document.querySelectorAll('.stat-value');
            if (cards.length >= 3) {
                cards[0].textContent = currentRequests.length;
                cards[1].textContent = pending;
                cards[2].textContent = approved;
            }
        }
    } else {
        renderCurrentView();
    }
  } catch(e) {
    console.error(e);
    if (!silent) Toast.error('Gagal memuat data.');
  }
}

function switchView(viewId) {
  document.querySelectorAll('.nav-item').forEach(el => el.classList.toggle('active', el.dataset.view === viewId));
  const titles = { dashboard:'Dashboard', vehicle:'Permohonan Kendaraan Dinas', room:'Ruangan', zoom:'Zoom Meeting', repair:'Perbaikan Fasilitas', item:'Peminjaman Barang', my_reports:'Riwayat Pengajuan', profile:'Profil', detail_pengajuan: 'Detail Pengajuan' };
  const titleEl = document.getElementById('page-title');
  if (titleEl) titleEl.textContent = titles[viewId] || viewId;
  previousView = window._currentView || 'dashboard';
  window._currentView = viewId;
  currentPage = 1;
  
  // Scroll to top when switching views
  window.scrollTo({ top: 0, behavior: 'instant' });

  // Handle scroll listener for mark-as-read
  window.removeEventListener('scroll', handleDetailScroll);
  if (viewId === 'detail_pengajuan') {
    setTimeout(() => {
        window.addEventListener('scroll', handleDetailScroll);
        // also check if already at bottom (short pages)
        handleDetailScroll();
    }, 200);
  }

  renderCurrentView();
}

function renderCurrentView() {
  const view = window._currentView || 'dashboard';
  const ct   = document.getElementById('view-container');
  switch(view) {
    case 'dashboard':   ct.innerHTML = renderDashboard();          break;
    case 'vehicle':     ct.innerHTML = renderServicePage('vehicle');break;
    case 'room':        ct.innerHTML = renderServicePage('room');   break;
    case 'zoom':        ct.innerHTML = renderServicePage('zoom');   break;
    case 'repair':      ct.innerHTML = renderServicePage('repair'); break;
    case 'item':        ct.innerHTML = renderServicePage('item');   break;
    case 'my_reports':  ct.innerHTML = renderMyReports();          break;
    case 'profile':     ct.innerHTML = renderProfile();            break;
    case 'detail_pengajuan': ct.innerHTML = renderDetailPengajuan(); break;
  }
}

function renderDashboard() {
  const all = [...myVehicle,...myRoom,...myZoom,...myRepair,...myItem];
  const total    = all.length;
  const pending  = all.filter(r=>['pending','waiting_manager_fmd'].includes(r.status)).length;
  const approved = all.filter(r=>['approved', 'ready_for_user'].includes(r.status)).length;
  const done     = all.filter(r=>['completed','returned'].includes(r.status)).length;
  const recent   = all.sort((a,b)=>new Date(b.created_at)-new Date(a.created_at)).slice(0,4);

  return `
  <div class="page-header">
    <h1>Selamat Datang, ${USER_NAME.split(' ')[0]}!</h1>
    <p>Dashboard layanan fasilitas SEAMEO BIOTROP</p>
  </div>
  <div class="stats-grid">
    <div class="stat-card border-left-blue">
      <div class="stat-label">Total Pengajuan</div>
      <div class="stat-value" style="color:var(--color-blue-600);">${total}</div>
    </div>
    <div class="stat-card border-left-amber">
      <div class="stat-label">Pending</div>
      <div class="stat-value" style="color:var(--color-amber-600);">${pending}</div>
    </div>
    <div class="stat-card border-left-primary">
      <div class="stat-label">Disetujui</div>
      <div class="stat-value" style="color:var(--color-primary-600);">${approved}</div>
    </div>
  </div>

  <div class="grid-2" style="gap:1.5rem;">
    <div class="card">
      <div class="card-header"><div class="card-title">Layanan Tersedia</div></div>
      <div class="card-body grid-2-sm">
        ${[
          {id:'vehicle',icon:'🚗',label:'Kendaraan Dinas',desc:'Ajukan peminjaman kendaraan dinas'},
          {id:'room',icon:'🏢',label:'Ruangan',desc:'Booking ruang rapat/seminar'},
          {id:'zoom',icon:'💻',label:'Zoom Meeting',desc:'Request akun Zoom untuk rapat online'},
          {id:'repair',icon:'🔧',label:'Perbaikan',desc:'Laporkan kerusakan fasilitas'},
          {id:'item',icon:'📦',label:'Peminjaman Barang',desc:'Pinjam peralatan kantor'},
        ].map(s=>`
        <div style="padding:.75rem;border:1px solid var(--color-slate-200);border-radius:var(--radius-md);cursor:pointer;transition:all .15s;" onclick="switchView('${s.id}')" onmouseover="this.style.borderColor='var(--color-primary-500)';this.style.background='var(--color-primary-50)'" onmouseout="this.style.borderColor='var(--color-slate-200)';this.style.background=''">
          <div style="font-size:1.5rem;margin-bottom:.3rem;">${s.icon}</div>
          <div style="font-weight:700;font-size:.875rem;">${s.label}</div>
          <div style="font-size:.75rem;color:var(--color-slate-500);">${s.desc}</div>
        </div>`).join('')}
      </div>
    </div>

    <div class="card" style="border-left: 4px solid var(--color-emerald-500);">
      <div class="card-header">
        <div class="card-title" style="display:flex; align-items:center; gap:0.5rem;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--color-emerald-600);"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3ZM12 9v4M12 17h.01"/></svg>
          Disclaimer & Tata Tertib
        </div>
        <div class="card-desc">Aturan penggunaan fasilitas SEAMEO BIOTROP</div>
      </div>
      <div class="card-body">
        <div style="display:flex; flex-direction:column; gap:0.75rem;">
          <div style="display:flex; gap:0.75rem; padding:0.75rem; background:var(--color-emerald-50); border-radius:0.5rem; border:1px solid var(--color-emerald-100); font-size:0.875rem; color:var(--color-emerald-900);">
            <span style="font-weight:700; color:var(--color-emerald-600);">•</span>
            <p style="margin:0;">Pengajuan peminjaman (Kendaraan/Ruangan) wajib dilakukan minimal <b>H-3</b> sebelum kegiatan.</p>
          </div>
          <div style="display:flex; gap:0.75rem; padding:0.75rem; background:var(--color-emerald-50); border-radius:0.5rem; border:1px solid var(--color-emerald-100); font-size:0.875rem; color:var(--color-emerald-900);">
            <span style="font-weight:700; color:var(--color-emerald-600);">•</span>
            <p style="margin:0;">Pembatalan jadwal harus dikonfirmasi kepada admin minimal <b>1x24 jam</b>.</p>
          </div>
          <div style="display:flex; gap:0.75rem; padding:0.75rem; background:var(--color-emerald-50); border-radius:0.5rem; border:1px solid var(--color-emerald-100); font-size:0.875rem; color:var(--color-emerald-900);">
            <span style="font-weight:700; color:var(--color-emerald-600);">•</span>
            <p style="margin:0;">Status <b>Pending</b> berarti permohonan sedang ditinjau oleh PIC.</p>
          </div>
          <div style="display:flex; gap:0.75rem; padding:0.75rem; background:var(--color-emerald-50); border-radius:0.5rem; border:1px solid var(--color-emerald-100); font-size:0.875rem; color:var(--color-emerald-900);">
            <span style="font-weight:700; color:var(--color-emerald-600);">•</span>
            <p style="margin:0;">Segala kerusakan fasilitas selama masa peminjaman menjadi tanggung jawab peminjam/unit terkait.</p>
          </div>
          <div style="display:flex; gap:0.75rem; padding:0.75rem; background:var(--color-emerald-50); border-radius:0.5rem; border:1px solid var(--color-emerald-100); font-size:0.875rem; color:var(--color-emerald-900);">
            <span style="font-weight:700; color:var(--color-emerald-600);">•</span>
            <p style="margin:0;">Pastikan status pengajuan sudah <b>"Approved"</b> sebelum menggunakan fasilitas.</p>
          </div>
        </div>
      </div>
    </div>
  </div>`;
}

function renderServicePage(type) {
  const typeInfo = {
    vehicle:{ title:'Kendaraan Dinas', desc:'Ajukan permohonan kendaraan dinas', data:'allVehicle', takenKey: 'vehicle_id' },
    room:   { title:'Ruangan',         desc:'Booking ruang rapat atau seminar' },
    zoom:   { title:'Zoom Meeting',    desc:'Request akun Zoom untuk online meeting' },
    repair: { title:'Laporan Perbaikan', desc:'Laporkan kerusakan/masalah fasilitas' },
    item:   { title:'Peminjaman Barang', desc:'Ajukan peminjaman peralatan' },
  };
  const info = typeInfo[type];

  let tableHTML = `
  <div class="card">
    <div class="card-header"><div class="card-title">Riwayat Pengajuan Saya ${type === 'repair' ? '(Laporan Kerusakan)' : ''}</div></div>
    <div class="table-wrap">
      <table>
        <thead><tr>
          <th>${type === 'repair' ? 'Lokasi & Masalah' : 'Detail'}</th>
          <th>${type === 'repair' ? 'Prioritas' : 'Periode'}</th>
          <th>Status</th>
          <th>Dibuat</th>
          <th style="text-align:center">Aksi</th>
        </tr></thead>
        <tbody id="my-${type}-table-body">
          ${renderMyTypeRows(type)}
        </tbody>
      </table>
    </div>
  </div>`;

  let layoutHTML = tableHTML;
  if(type === 'vehicle') {
    layoutHTML = `
    <div style="display:grid; grid-template-columns: 100%; gap:1.5rem; align-items:start;">
      <div class="grid-main">
        ${tableHTML}

        <div class="card" style="position:sticky;top:1rem;">
          <div class="card-header">
            <div class="card-title">Jadwal Penggunaan Kendaraan</div>
            <div class="card-desc">Klik tanggal untuk melihat detail</div>
          </div>
          <div class="card-body" style="padding:0.75rem;">

            <!-- Navigasi Bulan -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
              <button onclick="vehicleCalPrevMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8249;</button>
              <div id="vehicle-cal-title" style="font-weight:700;font-size:0.9rem;color:var(--color-slate-800);"></div>
              <button onclick="vehicleCalNextMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8250;</button>
            </div>

            <!-- Header hari -->
            <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;font-size:0.65rem;font-weight:700;color:var(--color-slate-400);margin-bottom:0.3rem;">
              <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
            </div>

            <!-- Grid kalender -->
            <div id="vehicle-cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;"></div>

            <!-- Legenda -->
            <div style="display:flex;gap:0.8rem;margin-top:0.75rem;font-size:0.7rem;color:var(--color-slate-500);flex-wrap:wrap;">
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#16a34a;"></div>Disetujui</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;"></div>Pending</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#e11d48;"></div>Lainnya</div>
            </div>

            <!-- Detail tanggal yang dipilih -->
            <div id="vehicle-cal-detail" style="margin-top:0.75rem;"></div>
          </div>
        </div>
      </div>
    </div>`;
    setTimeout(() => { if(window._currentView === 'vehicle') renderVehicleCalendar(); }, 50);
  }

  if(type === 'zoom') {
    const nowD = new Date();
    layoutHTML = `
    <div style="display:grid; grid-template-columns: 100%; gap:1.5rem; align-items:start;">
      <div class="grid-main">
        ${tableHTML}
        
        <div class="card" style="position:sticky;top:1rem;">
          <div class="card-header">
            <div class="card-title">Jadwal Kegiatan/Rapat</div>
            <div class="card-desc">Klik tanggal untuk melihat detail</div>
          </div>
          <div class="card-body" style="padding:0.75rem;">

            <!-- Navigasi Bulan -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
              <button onclick="zoomCalPrevMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8249;</button>
              <div id="zoom-cal-title" style="font-weight:700;font-size:0.9rem;color:var(--color-slate-800);"></div>
              <button onclick="zoomCalNextMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8250;</button>
            </div>

            <!-- Header hari -->
            <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;font-size:0.65rem;font-weight:700;color:var(--color-slate-400);margin-bottom:0.3rem;">
              <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
            </div>

            <!-- Grid kalender -->
            <div id="zoom-cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;"></div>

            <!-- Legenda -->
            <div style="display:flex;gap:0.8rem;margin-top:0.75rem;font-size:0.7rem;color:var(--color-slate-500);flex-wrap:wrap;">
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#16a34a;"></div>Disetujui</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;"></div>Pending</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#e11d48;"></div>Lainnya</div>
            </div>

            <!-- Detail tanggal yang dipilih -->
            <div id="zoom-cal-detail" style="margin-top:0.75rem;"></div>
          </div>
        </div>
      </div>
    </div>`;
    setTimeout(() => { if(window._currentView === 'zoom') renderZoomCalendar(); }, 50);
  }

  if(type === 'room') {
    layoutHTML = `
    <div style="display:grid; grid-template-columns: 100%; gap:1.5rem; align-items:start;">
      <div class="grid-main">
        ${tableHTML}

        <div class="card" style="position:sticky;top:1rem;">
          <div class="card-header">
            <div class="card-title">Jadwal Peminjaman Ruangan</div>
            <div class="card-desc">Klik tanggal untuk melihat detail</div>
          </div>
          <div class="card-body" style="padding:0.75rem;">

            <!-- Navigasi Bulan -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
              <button onclick="roomCalPrevMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8249;</button>
              <div id="room-cal-title" style="font-weight:700;font-size:0.9rem;color:var(--color-slate-800);"></div>
              <button onclick="roomCalNextMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8250;</button>
            </div>

            <!-- Header hari -->
            <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;font-size:0.65rem;font-weight:700;color:var(--color-slate-400);margin-bottom:0.3rem;">
              <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
            </div>

            <!-- Grid kalender -->
            <div id="room-cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;"></div>

            <!-- Legenda -->
            <div style="display:flex;gap:0.8rem;margin-top:0.75rem;font-size:0.7rem;color:var(--color-slate-500);flex-wrap:wrap;">
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#16a34a;"></div>Disetujui</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;"></div>Pending</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#e11d48;"></div>Lainnya</div>
            </div>

            <!-- Detail tanggal yang dipilih -->
            <div id="room-cal-detail" style="margin-top:0.75rem;"></div>
          </div>
        </div>
      </div>
    </div>`;
    setTimeout(() => { if(window._currentView === 'room') renderRoomCalendar(); }, 50);
  }

  if(type === 'repair') {
    layoutHTML = `
    <div style="display:grid; grid-template-columns: 100%; gap:1.5rem; align-items:start;">
      <div class="grid-main">
        ${tableHTML}
        
        <div class="card" style="position:sticky;top:1rem;">
          <div class="card-header">
            <div class="card-title">Kalender Laporan Perbaikan</div>
            <div class="card-desc">Laporan insiden di SEAMEO BIOTROP</div>
          </div>
          <div class="card-body" style="padding:0.75rem;">
            <!-- Navigasi Bulan -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
              <button onclick="repairCalPrevMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8249;</button>
              <div id="repair-cal-title" style="font-weight:700;font-size:0.9rem;color:var(--color-slate-800);"></div>
              <button onclick="repairCalNextMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8250;</button>
            </div>

            <!-- Header hari -->
            <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;font-size:0.65rem;font-weight:700;color:var(--color-slate-400);margin-bottom:0.3rem;">
              <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
            </div>

            <!-- Grid kalender -->
            <div id="repair-cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;"></div>

            <!-- Legenda -->
            <div style="display:flex;gap:0.8rem;margin-top:0.75rem;font-size:0.7rem;flex-wrap:wrap;">
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:6px;height:6px;border-radius:50%;background:#ef4444;"></div> Critical</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:6px;height:6px;border-radius:50%;background:#f97316;"></div> High</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:6px;height:6px;border-radius:50%;background:#f59e0b;"></div> Medium</div>
              <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:6px;height:6px;border-radius:50%;background:#10b981;"></div> Low</div>
            </div>


            <!-- Detail tanggal yang dipilih -->
            <div id="repair-cal-detail" style="margin-top:0.75rem;"></div>
          </div>
        </div>
      </div>
    </div>`;
    setTimeout(() => { if(window._currentView === 'repair') renderRepairCalendar(); }, 50);
  }

  return `
  <div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
      <div><h1>${info.title}</h1><p>${info.desc}</p></div>
      <button class="btn btn-primary" onclick="openForm('${type}')">+ Buat Pengajuan</button>
    </div>
  </div>
  ${layoutHTML}`;
}

function renderMyTypeRows(type) {
  const dataMap = { vehicle: myVehicle, room: myRoom, zoom: myZoom, repair: myRepair, item: myItem };
  const data    = dataMap[type] || [];
  if (!data.length) return `<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Belum ada pengajuan ${type}</td></tr>`;

  return data.map(r => {
    let detail = '-';
    let period = '-';
    if (type === 'vehicle') { detail = VEHICLE_MAP[r.vehicle_id] || r.vehicle_id; period = `${r.date_start||''} ${r.time_start||''} → ${r.date_end||''} ${r.time_end||''}`; }
    else if (type === 'room')   { detail = r.room_id;           period = `${r.date_start||''} ${r.time_start||''}`; }
    else if (type === 'zoom')   { detail = r.zoom_account_id;   period = `${r.date_start||''} ${r.time_start||''}`; }
    else if (type === 'repair') { 
      detail = `<div style="font-weight:700;">${r.location_detail || '-'}</div><div style="font-size:0.75rem;color:var(--color-slate-500);max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${r.issue_description}">${r.issue_description}</div>`; 
      const prio = (r.priority || 'medium').toLowerCase();
      const prioColors = { high: '#ef4444', medium: '#f59e0b', low: '#10b981' };
      const prioLabels = { high: 'TINGGI (Urgent)', medium: 'SEDANG', low: 'RENDAH' };
      period = `<span style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.75rem;font-weight:700;color:${prioColors[prio]};">
        <div style="width:6px;height:6px;border-radius:50%;background:${prioColors[prio]}"></div> ${prioLabels[prio]}
      </span>`; 
    }
    else if (type === 'item')   { detail = `${r.item_name} (${r.item_quantity}x)`; period = `${r.loan_date||''} → ${r.return_date||''}`; }
    return `
    <tr>
      <td data-label="${type === 'repair' ? 'Lokasi' : 'Detail'}" style="font-weight:600;font-size:.875rem;">${detail}</td>
      <td data-label="${type === 'repair' ? 'Prioritas' : 'Periode'}" style="font-size:.78rem;color:var(--color-slate-500);">${period}</td>
      <td data-label="Status">${getStatusBadge(r.status)}</td>
      <td data-label="Dibuat" style="font-size:.78rem;color:var(--color-slate-400);">${formatDate(r.created_at)}</td>
      <td data-label="Aksi" style="text-align:center"><button class="btn btn-ghost btn-sm" onclick="openReportDetail(${r.id},'${type}')">Detail</button></td>
    </tr>`;
  }).join('');
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

function renderMyReports() {
  const html = `
  <div class="page-header">
    <h1>Riwayat Pengajuan</h1>
    <p>Riwayat dan status pengajuan fasilitas Anda</p>
  </div>
  <div class="card">
    <div class="card-header">
      <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
        <div class="search-wrap" style="flex:1;min-width:200px;">
          <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" class="form-input" id="my-reports-search" placeholder="Cari detail atau tipe..." oninput="filterMyReports()" style="padding-left:2.5rem;" />
        </div>
        <select class="form-select" id="my-reports-type" onchange="filterMyReports()" style="width:140px;">
          <option value="all">Semua Tipe</option>
          <option value="Vehicle">Kendaraan</option>
          <option value="Room">Ruangan</option>
          <option value="Zoom">Zoom</option>
          <option value="Repair">Perbaikan</option>
          <option value="Item">Barang</option>
        </select>
        <select class="form-select" id="my-reports-status" onchange="filterMyReports()" style="width:160px;">
          <option value="all">Semua Status</option>
          <option value="pending">Pending PIC</option>
          <option value="waiting_manager_fmd">Waiting Manager FMD</option>
          <option value="approved">Approved</option>
          <option value="completed">Completed</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Tipe</th><th>Detail</th><th>Tanggal Buat</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody id="my-reports-table-body">
          ${renderMyReportsRows(currentRequests, 1)}
        </tbody>
      </table>
    </div>
    <div style="padding:1rem;" id="reports-pagination"></div>
  </div>`;
  setTimeout(() => updatePagination(currentRequests, 'reports-pagination', 'goReportsPage', 'changeReportsRows'), 50);
  return html;
}

function renderMyReportsRows(data, page) {
  const start = (page - 1) * itemsPerPage;
  const paged = data.slice(start, start + itemsPerPage);
  if (paged.length === 0) return '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Data tidak ditemukan</td></tr>';

  return paged.map(r => {
    const typeLabel = { Vehicle:'Mobil', Room:'Ruangan', Zoom:'Zoom', Repair:'Perbaikan', Item:'Barang' }[r._type] || r._type;
    const detail = r._type === 'Vehicle' ? (VEHICLE_MAP[r.vehicle_id] || 'Menunggu Plotting') :
                   r._type === 'Room'    ? r.room_id :
                   r._type === 'Zoom'    ? r.zoom_account_id :
                   r._type === 'Repair'  ? r.location_detail :
                   r._type === 'Item'    ? r.item_name : '-';
    return `
    <tr>
      <td data-label="ID" style="font-size:.78rem;color:var(--color-slate-400);">#${r.id}</td>
      <td data-label="Tipe"><span style="font-size:.78rem;font-weight:600;padding:.15rem .5rem;background:var(--color-slate-100);border-radius:var(--radius-sm);">${typeLabel}</span></td>
      <td data-label="Detail" style="font-size:.875rem;">${detail}</td>
      <td data-label="Tanggal" style="font-size:.82rem;">${formatDate(r.created_at, true)}</td>
      <td data-label="Status">${getStatusBadge(r.status)}</td>
      <td data-label="Aksi"><button class="btn btn-ghost btn-sm" onclick="openReportDetail(${r.id},'${r._type.toLowerCase()}')">Detail</button></td>
    </tr>`;
  }).join('');
}

function renderRecentIncidents() {
  const recent = [...allRepair].sort((a,b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 10);
  if (recent.length === 0) return '<div style="padding:2.5rem;text-align:center;color:var(--color-slate-400);font-size:0.875rem;">Belum ada laporan perbaikan</div>';

  return recent.map(r => {
    const prio = (r.priority || 'medium').toLowerCase();
    const prioColors = {
      high:   { bg: '#fee2e2', text: '#b91c1c', label: 'High' },
      medium: { bg: '#fef3c7', text: '#92400e', label: 'Medium' },
      low:    { bg: '#d1fae5', text: '#065f46', label: 'Low' }
    };
    const c = prioColors[prio] || prioColors.medium;
    const dtStr = r.created_at || r.incident_date;
    const safeDtStr = (dtStr && dtStr.includes(' ')) ? dtStr.replace(' ', 'T') : dtStr;
    const dateFormatted = new Date(safeDtStr).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });

    return `
    <div style="padding:1.25rem; border-bottom:1px solid var(--color-slate-50); position:relative; transition: background 0.2s;" onmouseover="this.style.background='var(--color-slate-50)'" onmouseout="this.style.background='transparent'">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.4rem;">
        <div style="font-weight:700; color:var(--color-slate-800); font-size:0.95rem; line-height:1.4; flex:1; padding-right:1rem;">
          ${r.issue_description.split(' ').slice(0, 5).join(' ')}${r.issue_description.split(' ').length > 5 ? '...' : ''}
        </div>
        <div style="background:${c.bg}; color:${c.text}; font-size:0.65rem; font-weight:800; padding:0.25rem 0.6rem; border-radius:4px; text-transform:uppercase; letter-spacing:0.04em; flex-shrink:0;">
          ${c.label}
        </div>
      </div>
      <div style="font-size:0.82rem; color:var(--color-slate-600); margin-bottom:0.25rem; display:flex; gap:0.4rem;">
        <span style="font-weight:600; color:var(--color-slate-500);">Failur Object:</span> 
        <span style="color:var(--color-slate-800);">${r.location_detail}</span>
      </div>
      <div style="font-size:0.82rem; color:var(--color-slate-500); line-height:1.5; margin-bottom:1rem;">
        ${r.issue_description}
      </div>
      <div style="display:flex; justify-content:flex-end;">
        <div style="font-size:0.75rem; color:var(--color-slate-400); font-weight:500;">
          ${dateFormatted}
        </div>
      </div>
    </div>`;
  }).join('');
}

function filterMyReports() {
  const search = (document.getElementById('my-reports-search')?.value || '').toLowerCase();
  const type   = document.getElementById('my-reports-type')?.value || 'all';
  const status = document.getElementById('my-reports-status')?.value || 'all';
  
  let data = currentRequests;
  if (type !== 'all') data = data.filter(r => r._type === type);
  if (status !== 'all') data = data.filter(r => r.status === status);
  
  if (search) {
    data = data.filter(r => {
      const detail = (r._type === 'Vehicle' ? (VEHICLE_MAP[r.vehicle_id] || '') : 
                     r._type === 'Room' ? r.room_id : 
                     r._type === 'Zoom' ? r.zoom_account_id : 
                     r._type === 'Repair' ? r.location_detail : 
                     r.item_name || '').toLowerCase();
      return detail.includes(search) || r._type.toLowerCase().includes(search);
    });
  }
  currentPage = 1;
  document.getElementById('my-reports-table-body').innerHTML = renderMyReportsRows(data, 1);
  updatePagination(data, 'reports-pagination', 'goReportsPage', 'changeReportsRows');
}

function goReportsPage(page) {
  currentPage = page;
  const search = (document.getElementById('my-reports-search')?.value || '').toLowerCase();
  const type   = document.getElementById('my-reports-type')?.value || 'all';
  const status = document.getElementById('my-reports-status')?.value || 'all';
  
  let data = currentRequests;
  if (type !== 'all') data = data.filter(r => r._type === type);
  if (status !== 'all') data = data.filter(r => r.status === status);
  
  if (search) {
    data = data.filter(r => {
      const detail = (r._type === 'Vehicle' ? (VEHICLE_MAP[r.vehicle_id] || '') : 
                     r._type === 'Room' ? r.room_id : 
                     r._type === 'Zoom' ? r.zoom_account_id : 
                     r._type === 'Repair' ? r.location_detail : 
                     r.item_name || '').toLowerCase();
      return detail.includes(search) || r._type.toLowerCase().includes(search);
    });
  }
  document.getElementById('my-reports-table-body').innerHTML = renderMyReportsRows(data, page);
  updatePagination(data, 'reports-pagination', 'goReportsPage', 'changeReportsRows');
}

function changeReportsRows(val) { itemsPerPage = parseInt(val); currentPage = 1; filterMyReports(); }

function renderProfile() {
  return `
  <div class="page-header">
    <h1>Profil Saya</h1>
    <p>Kelola informasi akun Anda</p>
  </div>
  <div class="grid-2" style="gap: 1.5rem; align-items: start;">
    <!-- Profile Card (Kiri/Atas) -->
    <div class="card">
      <div class="card-body text-center" style="padding: 2.5rem 1.5rem;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; margin: 0 auto 1.5rem; border: 4px solid var(--color-slate-50); box-shadow: var(--shadow-lg);">
          <?= strtoupper(mb_substr($userName, 0, 1)) ?>
        </div>
        <h5 style="font-size: 1.25rem; font-weight: 700; color: var(--foreground); margin-bottom: 0.25rem;"><?= htmlspecialchars($userName) ?></h5>
        <div style="font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;"><?= ($userRole === 'admin' ? 'Administrator' : ($userRole === 'supervisor' ? 'Supervisor FMD' : 'Staff / Member')) ?></div>
        
        <div style="margin-top: 1.5rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
          <div style="font-size: 0.82rem; color: var(--muted-foreground); margin-bottom: 0.5rem;">NIK / Username</div>
          <div style="font-weight: 700; color: var(--foreground);">@<?= htmlspecialchars($userLogin) ?></div>
        </div>
      </div>
    </div>

    <!-- Info Card (Kanan/Bawah) -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">Informasi Akun</div>
      </div>
      <div class="card-body">
        <div style="display:grid; gap: 1.25rem;">
          <div>
            <label style="display:block; font-size: 0.7rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.25rem;">Nama Lengkap</label>
            <div style="font-weight: 700; color: var(--foreground); font-size: 0.95rem;"><?= htmlspecialchars($userName) ?></div>
          </div>
          <div style="height: 1px; background: var(--color-slate-100);"></div>
          <div>
            <label style="display:block; font-size: 0.7rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.25rem;">Unit / Departemen</label>
            <div style="font-weight: 700; color: var(--foreground); font-size: 0.95rem;"><?= htmlspecialchars($dept ?? 'Umum') ?></div>
          </div>
          <div style="height: 1px; background: var(--color-slate-100);"></div>
          <div>
            <label style="display:block; font-size: 0.7rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.25rem;">Telegram Chat ID</label>
            <div style="display:flex; gap:0.5rem;">
                <input type="text" id="p-telegram-chat-id" class="form-input" style="font-size:0.9rem; font-weight:700;" value="${USER_TELE_CHAT_ID || ''}" placeholder="Contoh: 123456789" />
            </div>
            <div style="font-size:0.7rem; color:var(--muted-foreground); margin-top:0.4rem;">
                Masukkan ID Telegram Anda untuk menerima notifikasi pribadi. (Opsional)
            </div>
          </div>
          <div style="height: 1px; background: var(--color-slate-100);"></div>
          <div>
            <label style="display:block; font-size: 0.7rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.25rem;">Nomor WhatsApp</label>
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <input type="text" id="p-whatsapp-number" class="form-input" style="font-size:0.9rem; font-weight:700;" value="${USER_WA_NUMBER || ''}" placeholder="Contoh: 08123456789" />
                <button class="btn btn-primary btn-sm" onclick="saveProfile()">Simpan Kontak (WA & Telegram)</button>
            </div>
            <div style="font-size:0.7rem; color:var(--muted-foreground); margin-top:0.4rem;">
                Masukkan nomor WhatsApp Anda untuk menerima notifikasi otomatis. Anda tidak perlu setup API lagi.
            </div>
          </div>
          <div style="height: 1px; background: var(--color-slate-100);"></div>
          <div>
            <label style="display:block; font-size: 0.7rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase; margin-bottom: 0.25rem;">Status Keanggotaan</label>
            <div style="display:flex; align-items:center; gap:0.5rem;">
              <span class="badge badge-approved" style="padding: 0.25rem 0.75rem;">Aktif</span>
              <span style="font-size:0.82rem; color:var(--muted-foreground);">Akun terverifikasi</span>
            </div>
          </div>
        </div>

        <div style="margin-top: 2rem; background: var(--color-slate-50); padding: 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border);">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--foreground); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Ganti Password
            </div>
            <div style="display:grid; gap: 0.75rem;">
                <div>
                   <label class="form-label" style="font-size: 0.7rem;">Password Lama</label>
                   <input type="password" id="p-old-password" class="form-input" placeholder="Masukkan password lama" />
                </div>
                <div>
                   <label class="form-label" style="font-size: 0.7rem;">Password Baru</label>
                   <input type="password" id="p-new-password" class="form-input" placeholder="Masukkan password baru" />
                </div>
                <div>
                   <label class="form-label" style="font-size: 0.7rem;">Konfirmasi Password Baru</label>
                   <input type="password" id="p-confirm-password" class="form-input" placeholder="Ulangi password baru" />
                </div>
                <button class="btn btn-primary" style="margin-top:0.5rem;" onclick="changePassword()">Perbarui Password</button>
            </div>
        </div>

        <div style="margin-top: 2rem; background: var(--color-slate-50); padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--border); display: flex; gap: 0.75rem; align-items: start;">
          <svg style="color:var(--primary); flex-shrink:0;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <p style="font-size: 0.75rem; color: var(--color-slate-500); margin: 0; line-height: 1.5;">
            Untuk alasan keamanan, perubahan data profil (Nama, NIK, atau Unit) hanya dapat dilakukan melalui Administrator sistem.
          </p>
        </div>
      </div>
    </div>
  </div>`;
}

async function changePassword() {
  const oldPass = document.getElementById('p-old-password')?.value || '';
  const newPass = document.getElementById('p-new-password')?.value || '';
  const confPass = document.getElementById('p-confirm-password')?.value || '';

  if (!oldPass || !newPass || !confPass) {
    Toast.error('Semua data password harus diisi!');
    return;
  }

  if (newPass !== confPass) {
    Toast.error('Konfirmasi password baru tidak cocok!');
    return;
  }

  if (newPass.length < 5) {
      Toast.error('Password baru minimal 5 karakter!');
      return;
  }

  const res = await apiPost(API_BASE + 'users.php', {
    action: 'change_password',
    old_password: oldPass,
    new_password: newPass
  });

  if (res.success) {
    Toast.success(res.message);
    document.getElementById('p-old-password').value = '';
    document.getElementById('p-new-password').value = '';
    document.getElementById('p-confirm-password').value = '';
  } else {
    Toast.error(res.message);
  }
}

async function saveProfile() {
  const chatId = document.getElementById('p-telegram-chat-id')?.value || '';
  const waNumber = document.getElementById('p-whatsapp-number')?.value || '';
  
  const res = await apiPost(API_BASE + 'users.php', {
    action: 'update_profile',
    telegram_chat_id: chatId,
    whatsapp_number: waNumber
  });
  if (res.success) {
    USER_TELE_CHAT_ID = chatId;
    USER_WA_NUMBER = waNumber;
    Toast.success(res.message);
  } else {
    Toast.error(res.message);
  }
}

// ===== OPEN FORM =====
function openForm(type) {
  currentFormType = type;
  document.getElementById('modal-form-title').textContent = {
    vehicle:'Form Peminjaman Kendaraan Dinas', room:'Form Booking Ruangan',
    zoom:'Form Request Zoom Meeting', repair:'Form Laporan Perbaikan',
    item:'Form Peminjaman Barang'
  }[type] || 'Form Pengajuan';

  document.getElementById('modal-form-body').innerHTML = buildForm(type);
  // Pre-fill name and dept
  const nameEl = document.getElementById('f-applicant-name');
  const unitEl = document.getElementById('f-applicant-unit');
  if (nameEl) nameEl.value = USER_NAME;
  if (unitEl) unitEl.value = USER_DEPT;

  setTimeout(() => setupFormListeners(type), 50);

  Modal.open('modal-form');
}

function setupFormListeners(type) {
  if (type !== 'room' && type !== 'zoom') return;
  
  const dateStart = document.getElementById('f-date-start');
  const timeStart = document.getElementById('f-time-start');
  const dateEnd   = document.getElementById('f-date-end');
  const timeEnd   = document.getElementById('f-time-end');
  const selectId  = type === 'room' ? 'f-room-id' : 'f-zoom-id';
  const selectEl  = document.getElementById(selectId);

  if (!dateStart || !timeStart || !dateEnd || !timeEnd || !selectEl) return;

  const updateDropdown = () => {
    const ds = dateStart.value;
    const ts = timeStart.value;
    const de = dateEnd.value;
    const te = timeEnd.value;
    
    if (!ds || !ts || !de || !te) return;

    const startDT = new Date(`${ds}T${ts}`);
    const endDT   = new Date(`${de}T${te}`);

    if (startDT >= endDT) return;

    const dataList = type === 'room' ? allRoom : allZoom;
    const occupiedStatuses = ['approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','pending'];

    const overlappingIds = new Set();
    dataList.forEach(r => {
      if (!occupiedStatuses.includes(r.status)) return;
      if (!r.date_start || !r.time_start || !r.date_end || !r.time_end) return;

      const rStart = new Date(`${r.date_start}T${r.time_start}`);
      const rEnd   = new Date(`${r.date_end}T${r.time_end}`);

      if (startDT < rEnd && endDT > rStart) {
        overlappingIds.add(type === 'room' ? r.room_id : r.zoom_account_id);
      }
    });

    Array.from(selectEl.options).forEach(opt => {
      if (!opt.value) return; 
      if (overlappingIds.has(opt.value)) {
        opt.disabled = true;
        if (!opt.text.includes('(Tidak Tersedia)')) {
          opt.text = opt.text + ' (Tidak Tersedia)';
        }
      } else {
        opt.disabled = false;
        opt.text = opt.text.replace(/ \(Tidak Tersedia\)$/, '');
      }
    });

    if (selectEl.options[selectEl.selectedIndex] && selectEl.options[selectEl.selectedIndex].disabled) {
      selectEl.value = '';
    }
  };

  dateStart.addEventListener('change', updateDropdown);
  timeStart.addEventListener('change', updateDropdown);
  dateEnd.addEventListener('change', updateDropdown);
  timeEnd.addEventListener('change', updateDropdown);
}

function buildForm(type) {
  const nameUnit = `
  <div class="form-group"><label class="form-label">Nama Pemohon</label><input type="text" id="f-applicant-name" class="form-input" readonly /></div>
  <div class="form-group"><label class="form-label">Unit/Departemen</label><input type="text" id="f-applicant-unit" class="form-input" readonly /></div>`;

  if (type === 'vehicle') return `
  ${nameUnit}
  <div class="grid-2">
    <div class="form-group"><label class="form-label">Tanggal Mulai</label><input type="date" id="f-date-start" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Jam Mulai</label><input type="time" id="f-time-start" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Tanggal Selesai</label><input type="date" id="f-date-end" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Jam Selesai</label><input type="time" id="f-time-end" class="form-input" required /></div>
  </div>
  <div class="form-group"><label class="form-label">Keperluan</label><textarea id="f-purpose" class="form-textarea" required placeholder="Jelaskan keperluan penggunaan kendaraan..."></textarea></div>`;

  if (type === 'room') return `
  ${nameUnit}
  <div class="form-group"><label class="form-label">Ruangan</label>
  <select id="f-room-id" class="form-select" required><option value="">-- Pilih Ruangan --</option>
  ${ROOMS.map(r=>`<option value="${r.id}">${r.name}</option>`).join('')}
  </select></div>
  <div class="grid-2">
    <div class="form-group"><label class="form-label">Tanggal Mulai</label><input type="date" id="f-date-start" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Jam Mulai</label><input type="time" id="f-time-start" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Tanggal Selesai</label><input type="date" id="f-date-end" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Jam Selesai</label><input type="time" id="f-time-end" class="form-input" required /></div>
  </div>
  <div class="form-group"><label class="form-label">Jumlah Peserta</label><input type="number" id="f-participants" class="form-input" min="1" value="1" required /></div>
  <div class="form-group"><label class="form-label">Keperluan</label><textarea id="f-purpose" class="form-textarea" required placeholder="Jelaskan keperluan..."></textarea></div>
  <div class="form-group"><label class="form-label">Kebutuhan Khusus</label><textarea id="f-special-needs" class="form-textarea" placeholder="Proyektor, microphone, dll..."></textarea></div>`;

  if (type === 'zoom') return `
  ${nameUnit}
  <div class="form-group"><label class="form-label">Akun Zoom</label>
  <select id="f-zoom-id" class="form-select" required><option value="">-- Pilih Akun Zoom --</option>
  ${ZOOM_ACCOUNTS.map(z=>`<option value="${z.id}">${z.name}</option>`).join('')}
  </select></div>
  <div class="form-group"><label class="form-label">Nama Kegiatan</label>
  <input type="text" id="f-request-type" class="form-input" required placeholder="Cth: Rapat Koordinasi Tahunan" /></div>
  <div class="grid-2">
    <div class="form-group"><label class="form-label">Tanggal Mulai</label><input type="date" id="f-date-start" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Jam Mulai</label><input type="time" id="f-time-start" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Tanggal Selesai</label><input type="date" id="f-date-end" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Jam Selesai</label><input type="time" id="f-time-end" class="form-input" required /></div>
  </div>
  <div class="form-group"><label class="form-label">Jumlah Peserta</label><input type="number" id="f-participants" class="form-input" min="1" value="1" required /></div>
  <div class="form-group"><label class="form-label">Permintaan Tambahan</label>
  <select id="f-add-reqs" class="form-select">
    <option value="">Pilih Opsi...</option>
    <option value="Tidak Rekam">Tidak Rekam Kegiatan</option>
    <option value="Rekam Cloud">Rekam Cloud</option>
    <option value="Rekam Lokal">Rekam Lokal (Host)</option>
    <option value="Live Youtube">Live Youtube</option>
  </select></div>
  <div class="form-group"><label class="form-label">Kebutuhan Khusus</label>
  <input type="text" id="f-needs" class="form-input" placeholder="Cth: Proyektor, Sound System, Katering" /></div>`;

  if (type === 'repair') return `
  ${nameUnit}
  <div class="form-group"><label class="form-label">Lokasi Fasilitas (yang bermasalah)</label><input type="text" id="f-location" class="form-input" required placeholder="Contoh: Gedung A Lantai 2, Ruang Rapat 201" /></div>
  <div class="grid-2">
    <div class="form-group"><label class="form-label">Waktu Ditemukan/Terjadi</label><input type="date" id="f-date-start" class="form-input" value="${new Date().toISOString().split('T')[0]}" required /></div>
    <div class="form-group"><label class="form-label">(Jam)</label><input type="time" id="f-time-start" class="form-input" value="${new Date().toTimeString().slice(0,5)}" required /></div>
  </div>
  <div class="form-group"><label class="form-label">Tingkat Prioritas</label>
  <select id="f-priority" class="form-select" required>
    <option value="low">🟢 Low (Masih bisa digunakan, tidak mendesak)</option>
    <option value="medium" selected>🟡 Medium (Mengganggu fungsionalitas normal)</option>
    <option value="high">🟠 High (Mendesak / Mendesak)</option>
    <option value="critical">🔴 Critical (Sangat Mendesak / Risiko Keselamatan)</option>
  </select></div>
  <div class="form-group"><label class="form-label">Deskripsi Masalah / Kerusakan</label><textarea id="f-purpose" class="form-textarea" required placeholder="Jelaskan detail kerusakan agar teknisi mudah memahaminya..."></textarea></div>`;

  if (type === 'item') return `
  ${nameUnit}
  <div class="form-group"><label class="form-label">Nama Barang</label><input type="text" id="f-item-name" class="form-input" required placeholder="Contoh: Proyektor" /></div>
  <div class="grid-2">
    <div class="form-group"><label class="form-label">Tanggal Pinjam</label><input type="date" id="f-loan-date" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Jam Pinjam</label><input type="time" id="f-loan-time" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Tanggal Kembali</label><input type="date" id="f-return-date" class="form-input" required /></div>
    <div class="form-group"><label class="form-label">Jam Kembali</label><input type="time" id="f-return-time" class="form-input" required /></div>
  </div>
  <div class="form-group"><label class="form-label">Keperluan</label><textarea id="f-purpose" class="form-textarea" required placeholder="Jelaskan keperluan peminjaman..."></textarea></div>`;

  return '';
}

// ===== SUBMIT FORM =====
async function doSubmitForm() {
  const btn    = document.getElementById('modal-submit-btn');
  const btnTxt = document.getElementById('submit-btn-text');
  btn.disabled = true;
  btnTxt.innerHTML = '<span class="spinner"></span> Mengirim...';

  const name = document.getElementById('f-applicant-name')?.value || USER_NAME;
  const unit = document.getElementById('f-applicant-unit')?.value || USER_DEPT;

  const data = { action: 'submit_' + currentFormType, applicant_name: name, applicant_unit: unit };

  if (['vehicle','room','zoom','item'].includes(currentFormType)) {
    data.date_start = document.getElementById('f-date-start')?.value || '';
    data.time_start = document.getElementById('f-time-start')?.value || '';
    data.date_end   = document.getElementById('f-date-end')?.value   || '';
    data.time_end   = document.getElementById('f-time-end')?.value   || '';
    data.purpose    = document.getElementById('f-purpose')?.value    || '';
  }
  if (currentFormType === 'vehicle') {
    data.vehicle_id = 'PENDING_ASSIGNMENT';
  } else if (currentFormType === 'room') {
    data.room_id      = document.getElementById('f-room-id')?.value || '';
    data.participants = document.getElementById('f-participants')?.value || '1';
    data.special_needs= document.getElementById('f-special-needs')?.value || '';
  } else if (currentFormType === 'zoom') {
    data.zoom_account_id= document.getElementById('f-zoom-id')?.value     || '';
    data.request_type   = document.getElementById('f-request-type')?.value || '';
    data.purpose        = data.request_type; // Use Nama Kegiatan as Purpose for Zoom
    data.participants   = document.getElementById('f-participants')?.value  || '1';
    const addReq = document.getElementById('f-add-reqs')?.value || '';
    const needs  = document.getElementById('f-needs')?.value || '';
    data.special_needs = [addReq, needs].filter(x => x).join('; ');
  } else if (currentFormType === 'repair') {
    data.location_detail   = document.getElementById('f-location')?.value   || '';
    data.incident_date     = document.getElementById('f-date-start')?.value || '';
    data.incident_time     = document.getElementById('f-time-start')?.value || '';
    data.priority          = document.getElementById('f-priority')?.value   || 'medium';
    data.issue_description = document.getElementById('f-purpose')?.value    || '';
  } else if (currentFormType === 'item') {
    data.item_name   = document.getElementById('f-item-name')?.value  || '';
    data.loan_date   = document.getElementById('f-loan-date')?.value   || '';
    data.loan_time   = document.getElementById('f-loan-time')?.value   || '';
    data.return_date = document.getElementById('f-return-date')?.value || '';
    data.return_time = document.getElementById('f-return-time')?.value || '';
    data.purpose     = document.getElementById('f-purpose')?.value     || '';
  }

  const res = await apiPost(API_BASE + 'requests.php', data);
  btn.disabled = false;
  btnTxt.textContent = 'Kirim Pengajuan';

  if (res.success) {
    Toast.success(res.message || 'Pengajuan berhasil dikirim!');
    Modal.close('modal-form');

    // --- TELEGRAM NOTIFICATION ---
    try {
      // Escape dynamic values for HTML parse mode
      const eName = typeof escapeHtml === 'function' ? escapeHtml(name) : name;
      const eUnit = typeof escapeHtml === 'function' ? escapeHtml(unit) : unit;
      const ePurpose = typeof escapeHtml === 'function' ? escapeHtml(data.purpose) : data.purpose;

      let typeLabel = { vehicle:'Mobil', room:'Ruangan', zoom:'Zoom', repair:'Perbaikan', item:'Barang' }[currentFormType] || currentFormType;
      let teleMsg = `<b>🔔 PENGAJUAN ${typeLabel.toUpperCase()} BARU</b>\n\n`;
      teleMsg += `<b>Pemohon:</b> ${eName}\n`;
      teleMsg += `<b>Unit:</b> ${eUnit}\n`;
      
      if (currentFormType === 'repair') {
        const eLoc = typeof escapeHtml === 'function' ? escapeHtml(data.location_detail) : data.location_detail;
        const eIssue = typeof escapeHtml === 'function' ? escapeHtml(data.issue_description) : data.issue_description;
        teleMsg += `<b>Lokasi:</b> ${eLoc}\n`;
        teleMsg += `<b>Masalah:</b> ${eIssue}\n`;
        teleMsg += `<b>Prioritas:</b> ${data.priority.toUpperCase()}\n`;
      } else if (currentFormType === 'item') {
        const eItemName = typeof escapeHtml === 'function' ? escapeHtml(data.item_name) : data.item_name;
        teleMsg += `<b>Barang:</b> ${eItemName}\n`;
        teleMsg += `<b>Keperluan:</b> ${ePurpose}\n`;
        teleMsg += `<b>Waktu:</b> ${data.loan_date} ${data.loan_time} s/d ${data.return_date} ${data.return_time}\n`;
      } else {
        let detail = currentFormType === 'vehicle' ? 'Operasional' : (currentFormType === 'room' ? data.room_id : data.zoom_account_id);
        const eDetail = typeof escapeHtml === 'function' ? escapeHtml(detail) : detail;
        teleMsg += `<b>Item:</b> ${eDetail}\n`;
        teleMsg += `<b>Keperluan:</b> ${ePurpose}\n`;
        teleMsg += `<b>Waktu:</b> ${data.date_start} ${data.time_start} s/d ${data.date_end} ${data.time_end}\n`;
      }
      
      teleMsg += `\n<i>ID Pengajuan: #${res.data?.id || 'N/A'}</i>\n`;
      teleMsg += `<i>Silakan cek dashboard FMD untuk tindak lanjut.</i>`;
      
      if (typeof sendTelegram === 'function') {
        // Notifikasi pengajuan baru selalu ke Grup Admin/FMD
        sendTelegram(teleMsg);
      }
    } catch (e) {
      console.error('TeleNotification Error:', e);
    }
    // ----------------------------

    await loadMyData();
  } else {
    Toast.error(res.message || 'Gagal mengirim pengajuan.');
  }
}

// ===== OPEN REPORT DETAIL =====
function openReportDetail(id, type) {
  const dataMap = { vehicle:allVehicle, room:allRoom, zoom:allZoom, repair:myRepair, item:myItem };
  // Since allVehicle, allRoom, allZoom contain all data, we use those for details to be safe
  // For repair and item, we use the user's list as they contain what they need
  const rows = dataMap[type] || [];
  const r = rows.find(x => String(x.id) === String(id));
  if (!r) {
    Toast.error('Data pengajuan tidak ditemukan.');
    return;
  }

  // Ensure type is set correctly for notification tracking
  const typeMap = { vehicle:'Vehicle', room:'Room', zoom:'Zoom', repair:'Repair', item:'Item' };
  r._type = typeMap[type.toLowerCase()] || type;
  if (!r.type) r.type = r._type;
  
  currentDetailReq = r;
  switchView('detail_pengajuan');
}

// Mark as read logic
function markAsRead(id, type) {
  const key = `read_${type.toLowerCase()}_${id}`;
  if (localStorage.getItem(key)) return; // Already read
  
  localStorage.setItem(key, 'true');
  console.log(`Notification marked as read: ${type} #${id}`);
  renderNotifDropdown();
}

function handleDetailScroll() {
  if (window._currentView !== 'detail_pengajuan' || !currentDetailReq) return;
  
  // Cross-browser scroll calculation
  const scrollHeight = document.documentElement.scrollHeight;
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const clientHeight = window.innerHeight;
  
  const position = scrollTop + clientHeight;
  const threshold = 50; // closer to bottom (50px)
  
  // If we've reached near the bottom
  if (position >= scrollHeight - threshold) {
    markAsRead(currentDetailReq.id, currentDetailReq._type);
  }
}

function renderDetailPengajuan() {
  const req = currentDetailReq;
  if (!req) return `<div class="page-header"><h1>Tidak ada data</h1></div>`;

  const content = buildDetailBody(req);
  const footer = buildDetailFooter(req);

  return `
    <div style="display:flex; align-items:center; gap:1rem; margin-bottom: 1.5rem;">
      <button class="btn btn-outline btn-sm" onclick="switchView(previousView || 'dashboard')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.2rem; vertical-align: middle;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali ke Daftar
      </button>
      <h1 style="margin:0; font-size: 1.25rem; font-weight: 700; color: #111827;">Detail Permohonan (Track View)</h1>
    </div>
    <div style="background:#fff; border-radius:0.75rem; box-shadow:0 1px 3px rgba(0,0,0,0.05); overflow:hidden; border: 1px solid #e5e7eb;">
      ${content}
      <div style="padding:1rem 1.5rem; border-top:1px solid #e5e7eb; display:flex; gap:0.5rem; justify-content:flex-end; background:#f9fafb;">
        ${footer}
      </div>
    </div>
  `;
}

function buildDetailBody(req) {
  // Jadwal baris
  const ds = formatDate(req.date_start || req.loan_date);
  const de = (req.date_end || req.return_date) ? ` s/d ${formatDate(req.date_end || req.return_date)}` : '';
  const ts = req.time_start || '';
  const te = req.time_end   || '';
  const timeStr = (ts || te) ? `${ts}${te ? ' - ' + te : ''}` : '(Seharian)';
  const jadwalVal = `${ds}${de}<div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">${timeStr}</div>`;

  const type = (req._type || req.type || '').toLowerCase();
  const catLabel = { vehicle:'Kendaraan Dinas', room:'Ruangan', zoom:'Zoom Meeting', repair:'Perbaikan Fasilitas', item:'Peminjaman Barang' }[type] || type;

  // Timeline Section
  const timelineEvents = getTimelineEvents(req);
  const timelineHtml = `
    <div class="timeline-container">
      ${timelineEvents.map((ev, idx) => {
        const dateObj = new Date(ev.date);
        const dateStr = dateObj.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }).replace(/ /g,'-');
        const timeStr = dateObj.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
        
        return `
        <div class="timeline-item">
          <div class="timeline-date-badge" style="background:${ev.color}">${dateStr}</div>
          <div class="timeline-icon" style="background:${ev.color}">
            ${getIconSvg(ev.icon)}
          </div>
          <div class="timeline-card">
            <div class="timeline-card-header">
              <span class="timeline-card-title">${ev.title}</span>
              <span class="timeline-card-time">
                ${getIconSvg('clock')} ${timeStr}
              </span>
            </div>
            <div class="timeline-card-body">
              <div class="timeline-card-desc">${ev.desc}</div>
              <div class="timeline-card-footer">
                <div class="timeline-user-avatar">${(ev.user || '?').charAt(0).toUpperCase()}</div>
                <div class="timeline-user-info">
                  <span class="timeline-user-name">${ev.user || 'System'}</span>
                  <span style="color:#9ca3af"> • ${ev.role || 'System'}</span>
                </div>
              </div>
            </div>
          </div>
        </div>`;
      }).reverse().join('')}
    </div>`;

  const detail = type === 'vehicle' ? (VEHICLE_MAP[req.vehicle_id] || 'Menunggu Plotting') :
                 type === 'room'    ? req.room_id :
                 type === 'zoom'    ? req.zoom_account_id :
                 type === 'repair'  ? req.location_detail :
                 type === 'item'    ? req.item_name : '-';

  return `
  <div class="tv-card" style="border:none; box-shadow:none; border-radius:0; margin:0;">
    <div class="tv-header" style="background:var(--primary)">
      <span>Reporting Detail</span>
      <button onclick="switchView(previousView || 'dashboard')" style="color:#fff; border:none; background:none; cursor:pointer;" title="Tutup Detail">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      </button>
    </div>
    <div class="tv-body">
      <div class="tv-row">
        <div class="tv-label">ID REQUEST</div>
        <div class="tv-value tv-id" style="font-weight:800; color:#111827;">#${req.id}</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Pemohon</div>
        <div class="tv-value tv-applicant" style="font-weight:600;">${(req.applicant_name || '-').toUpperCase()} / ${req.applicant_unit || '-'}</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Kategori</div>
        <div class="tv-value">${catLabel}</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Item / Lokasi</div>
        <div class="tv-value tv-item" style="color:#1d4ed8; font-weight:600;">${detail || '-'}</div>
      </div>
      ${type === 'zoom' ? `
      <div class="tv-row">
        <div class="tv-label">Nama Kegiatan</div>
        <div class="tv-value" style="font-weight:600;color:#374151;">${req.request_type || '-'}</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Jumlah Peserta</div>
        <div class="tv-value">${req.participants || '-'} orang</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Permintaan Tambahan</div>
        <div class="tv-value" style="color:#374151;">${req.special_needs || '-'}</div>
      </div>` : `
      <div class="tv-row">
        <div class="tv-label">Keterangan</div>
        <div class="tv-value" style="color:#374151;line-height:1.6;">${req.purpose || '-'}</div>
      </div>`}
      <div class="tv-row">
        <div class="tv-label">Jadwal</div>
        <div class="tv-value">${jadwalVal}</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Status</div>
        <div class="tv-value">${getStatusBadge(req.status)}</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Tanggal Ajuan</div>
        <div class="tv-value" style="color:#6b7280;">${formatDate(req.created_at, true) || '-'}</div>
      </div>
      ${type === 'repair' ? `<div id="rab-view-container" style="border-top: 1px solid #e5e7eb; background: #fff;"></div>` : ''}
    </div>
    
    <div style="padding: 1rem 1.25rem; background: #f8fafc; border-top: 1px solid #e5e7eb; font-weight: 700; color: #4b5563; font-size: 0.9rem;">
      Riwayat Permohonan
    </div>
    
    ${timelineHtml}
  </div>`;
}

// Tambahkan timeout untuk trigger load RAB jika tipe repair
setTimeout(() => {
  if (window._currentView === 'detail_pengajuan' && currentDetailReq && (currentDetailReq._type === 'Repair' || currentDetailReq.type === 'Repair' || (currentDetailReq.issue_description))) {
    loadRABView(currentDetailReq.id);
  }
}, 100);

function buildDetailFooter(req) {
  const type = (req._type || req.type || '').toLowerCase();
  const isReadyForUser = req.status === 'ready_for_user';
  const isNotRepair = type !== 'repair';
  
  let html = `<button class="btn btn-outline" onclick="switchView(previousView || 'dashboard')">Tutup</button>`;
  
  if (isReadyForUser && isNotRepair) {
    html = `
      <button class="btn btn-success" onclick="completeRequest(${req.id}, '${req._type || req.type}')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:0.3rem;"><polyline points="20 6 9 17 4 12"/></svg>
        Selesaikan Pengajuan
      </button>
      ` + html;
  }
  
  return html;
}

async function completeRequest(id, type) {
  const all = [...myVehicle, ...myRoom, ...myZoom, ...myRepair, ...myItem];
  const req = all.find(x => String(x.id) === String(id));
  if(!req) return;

  const feedbackArea = document.getElementById('complete-feedback');
  if(feedbackArea) feedbackArea.value = '';

  const btnSubmit = document.getElementById('btn-submit-complete');
  btnSubmit.onclick = async () => {
    const feedback = feedbackArea ? feedbackArea.value.trim() : '';
    const note = feedback ? `Pengajuan diselesaikan oleh Pemohon. Feedback: ${feedback}` : 'Pengajuan diselesaikan oleh Pemohon.';
    const prevNote = req.note || '';

    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner"></span> Memproses...';

    const res = await apiPost(API_BASE + 'requests.php', {
      action: 'update_status',
      id: id,
      type: type,
      status: 'completed',
      note: note,
      prev_note: prevNote
    });

    btnSubmit.disabled = false;
    btnSubmit.innerHTML = 'Selesaikan & Simpan';

    if (res.success) {
      Toast.success('Pengajuan berhasil diselesaikan!');
      Modal.close('modal-complete');
      await loadMyData();
      
      const latest = [...myVehicle, ...myRoom, ...myZoom, ...myRepair, ...myItem];
      const r = latest.find(x => String(x.id) === String(id));
      if (r) {
        if (!r.type) r.type = type;
        currentDetailReq = r;
      }
      switchView('detail_pengajuan');
    } else {
      Toast.error(res.message || 'Gagal menyelesaikan pengajuan.');
    }
  };

  Modal.open('modal-complete');
}

function getTimelineEvents(req) {
  const events = [];
  // 1. Creation
  events.push({
    id: 'start',
    title: 'Request Created',
    date: req.created_at || req.date_start,
    desc: 'Permohonan berhasil dikirim dan menunggu verifikasi.',
    user: req.applicant_name,
    role: 'Applicant',
    icon: 'file-text',
    color: '#94a3b8'
  });

  // 2. Parsed from Notes
  if (req.note) {
    const lines = req.note.split('\n');
    lines.forEach((line, idx) => {
      const match = line.match(/^\[(.*?)\]\s*\[(.*?)\]\s*-\s*(.*?):\s*(.*)$/);
      if (match) {
        const [_, ts, role, statusRaw, msg] = match;
        const status = statusRaw.toLowerCase();
        let title = 'Status Update';
        let color = '#3b82f6';
        let icon  = 'history';

        if (status === 'approved') { title = 'Approved by FMD Manager'; color = '#16a34a'; icon = 'check-circle'; }
        else if (status === 'ready_for_user') { title = 'Ready for User'; color = '#0891b2'; icon = 'check-circle'; }
        else if (status === 'completed') { title = 'Completed'; color = '#2563eb'; icon = 'check-circle'; }
        else if (status === 'returned') { title = 'Returned'; color = '#2563eb'; icon = 'check-circle'; }
        else if (status === 'rejected') { title = 'Rejected'; color = '#dc2626'; icon = 'x-circle'; }
        else if (status === 'verified') { title = 'Verified by Admin'; color = '#6366f1'; icon = 'check-circle'; }
        else if (status === 'in-progress') { title = 'Processed by PIC'; color = '#f97316'; icon = 'wrench'; }
        else if (status === 'waiting_manager_fmd') { title = 'Waiting for FMD Manager'; color = '#6366f1'; }
        else if (status === 'waiting_manager_fad') { title = 'Waiting for FAD Manager'; color = '#6366f1'; }
        else if (status === 'waiting_ppk') { title = 'Waiting for PPK'; color = '#a855f7'; }
        else if (status === 'waiting_bod') { title = 'Waiting for BOD'; color = '#db2777'; }
        else if (status === 'approved_waiting_fund') { title = 'Approved - Waiting Fund'; color = '#d97706'; }

        events.push({
          id: 'note-' + idx,
          title: title,
          date: parseLogDate(ts),
          desc: msg,
          user: role.replace(/[\[\]]/g, ''),
          role: role,
          icon: icon,
          color: color
        });
      } else {
        const match2 = line.match(/^\[(.*?)\]\s*\[(.*?)\]:\s*(.*)$/);
        if (match2) {
          const [_, ts, user, msg] = match2;
          events.push({
            id: 'note-' + idx,
            title: 'Catatan / Komentar',
            date: parseLogDate(ts),
            desc: msg,
            user: user.replace(/[\[\]]/g, ''),
            role: 'System/Admin',
            icon: 'file-text',
            color: '#64748b'
          });
        }
      }
    });
  }
  return events.sort((a, b) => new Date(a.date) - new Date(b.date));
}

function getIconSvg(name) {
  const icons = {
    'file-text': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
    'history': '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.98"/>',
    'check-circle': '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
    'x-circle': '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
    'wrench': '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
    'clock': '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'
  };
  return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${icons[name] || icons['file-text']}</svg>`;
}

function parseLogDate(ts) {
  // Typical log format: 2026-02-23 10:30:00
  return ts.replace(/-/g, '/');
}

// INIT
document.addEventListener('DOMContentLoaded', () => {
  Modal.init();
  loadMyData().then(() => switchView('dashboard'));

  // Auto Update: Refresh data every 30 seconds
  setInterval(async () => {
    // Check if modal is open to avoid disrupting user
    const modalForm = document.getElementById('modal-form');
    const modalDetail = document.getElementById('modal-report-detail');
    const isModalOpen = (modalForm && modalForm.classList.contains('open')) || 
                        (modalDetail && modalDetail.classList.contains('open'));

    if (!isModalOpen) {
      await loadMyData(true);
    }
  }, 30000);
});

// Legacy stub
window.renderVehicleAvail = function() {};


// ===== VEHICLE MINI CALENDAR =====
window._vehicleCalYear     = new Date().getFullYear();
window._vehicleCalMonth    = new Date().getMonth();
window._vehicleCalSelected = null;

window.renderVehicleCalendar = function() {
  renderVehicleCalGrid();
};

window.vehicleCalPrevMonth = function() {
  window._vehicleCalMonth--;
  if (window._vehicleCalMonth < 0) { window._vehicleCalMonth = 11; window._vehicleCalYear--; }
  renderVehicleCalGrid();
  window._vehicleCalSelected = null;
  const det = document.getElementById('vehicle-cal-detail');
  if (det) det.innerHTML = '';
};

window.vehicleCalNextMonth = function() {
  window._vehicleCalMonth++;
  if (window._vehicleCalMonth > 11) { window._vehicleCalMonth = 0; window._vehicleCalYear++; }
  renderVehicleCalGrid();
  window._vehicleCalSelected = null;
  const det = document.getElementById('vehicle-cal-detail');
  if (det) det.innerHTML = '';
};

window.renderVehicleCalGrid = function() {
  const yr   = window._vehicleCalYear;
  const mo   = window._vehicleCalMonth;
  const grid  = document.getElementById('vehicle-cal-grid');
  const title = document.getElementById('vehicle-cal-title');
  if (!grid || !title) return;

  const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  title.textContent = `${BULAN[mo]} ${yr}`;

  const occupiedStatuses = ['approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
  const pendingStatuses  = ['pending'];
  const activeStatuses   = [...occupiedStatuses, ...pendingStatuses];

  const dayMap = {};
  allVehicle.filter(r => activeStatuses.includes(r.status)).forEach(r => {
    if (!r.date_start) return;
    const start = new Date(r.date_start);
    const end   = r.date_end ? new Date(r.date_end) : new Date(r.date_start);
    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
      if (d.getMonth() !== mo || d.getFullYear() !== yr) continue;
      const key = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
      if (!dayMap[key]) dayMap[key] = { approved:[], pending:[], other:[] };
      if (occupiedStatuses.includes(r.status)) dayMap[key].approved.push(r);
      else if (pendingStatuses.includes(r.status)) dayMap[key].pending.push(r);
      else dayMap[key].other.push(r);
    }
  });

  const firstDay    = new Date(yr, mo, 1).getDay();
  const daysInMonth = new Date(yr, mo + 1, 0).getDate();
  const today       = new Date();
  const todayStr    = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;

  let html = '';
  for (let i = 0; i < firstDay; i++) html += `<div></div>`;

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr    = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const ev         = dayMap[dateStr];
    const isToday    = dateStr === todayStr;
    const isSelected = dateStr === window._vehicleCalSelected;

    let dots = '';
    if (ev) {
      if (ev.approved.length) dots += `<div style="width:5px;height:5px;border-radius:50%;background:#16a34a;display:inline-block;margin:0 1px;"></div>`;
      if (ev.pending.length)  dots += `<div style="width:5px;height:5px;border-radius:50%;background:#f59e0b;display:inline-block;margin:0 1px;"></div>`;
      if (ev.other.length)    dots += `<div style="width:5px;height:5px;border-radius:50%;background:#e11d48;display:inline-block;margin:0 1px;"></div>`;
    }

    const bgColor   = isSelected ? '#7c3aed' : isToday ? '#f5f3ff' : ev ? 'var(--color-slate-50)' : 'transparent';
    const textColor = isSelected ? '#fff'    : isToday ? '#6d28d9' : 'var(--color-slate-700)';
    const border    = isToday && !isSelected ? '1.5px solid #a78bfa' : isSelected ? 'none' : '1px solid transparent';

    html += `
      <div onclick="showVehicleDayDetail('${dateStr}')" style="
        padding:0.2rem 0; text-align:center; cursor:pointer;
        border-radius:0.4rem; background:${bgColor}; border:${border}; transition:all .12s;
      " onmouseover="if('${dateStr}'!==window._vehicleCalSelected)this.style.background='var(--color-slate-100)'"
         onmouseout="if('${dateStr}'!==window._vehicleCalSelected)this.style.background='${ev ? 'var(--color-slate-50)' : 'transparent'}'">
        <div style="font-size:0.72rem;font-weight:${isToday||isSelected?'700':'500'};color:${textColor};line-height:1.6;">${d}</div>
        <div style="display:flex;justify-content:center;align-items:center;min-height:7px;">${dots}</div>
      </div>`;
  }

  grid.innerHTML = html;
  if (window._vehicleCalSelected) showVehicleDayDetail(window._vehicleCalSelected, false);
};

window.showVehicleDayDetail = function(dateStr, updateGrid = true) {
  window._vehicleCalSelected = dateStr;
  if (updateGrid) renderVehicleCalGrid();

  const det = document.getElementById('vehicle-cal-detail');
  if (!det) return;

  const occupiedStatuses = ['approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
  const activeStatuses   = [...occupiedStatuses, 'pending'];

  // Requests on this date
  const reqs = allVehicle.filter(r => {
    if (!activeStatuses.includes(r.status) || !r.date_start) return false;
    return r.date_start <= dateStr && (r.date_end >= dateStr || r.date_start === dateStr);
  });

  const [yr, mo, dy] = dateStr.split('-');
  const BULAN    = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const dayObj   = new Date(dateStr);
  const dayLabel = `${dayNames[dayObj.getDay()]}, ${parseInt(dy)} ${BULAN[parseInt(mo)-1]} ${yr}`;

  // Kendaraan yang sudah terpakai pada tanggal ini (berdasarkan vehicle_id yg terdaftar)
  const bookedVehicleIds = new Set(reqs.filter(r => r.vehicle_id && r.vehicle_id !== 'PENDING_ASSIGNMENT').map(r => r.vehicle_id));
  const availableVehicles = ALL_VEHICLES.filter(v => !bookedVehicleIds.has(v.id));
  const pendingPlot = reqs.filter(r => r.vehicle_id === 'PENDING_ASSIGNMENT');

  // -- Bagian TERSEDIA --
  let availHtml = availableVehicles.map(v =>
    `<span style="display:inline-block;font-size:0.72rem;background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:0.25rem;padding:0.1rem 0.45rem;margin:0.1rem 0.15rem;">${v.name}</span>`
  ).join('');
  if (!availHtml) availHtml = `<span style="font-size:0.75rem;color:var(--color-slate-400);">Semua kendaraan terpakai</span>`;

  // -- Bagian TERPAKAI --
  let bookedHtml = '';
  reqs.sort((a,b) => (a.time_start||'').localeCompare(b.time_start||'')).forEach(r => {
    const sColor = occupiedStatuses.includes(r.status) ? '#dc2626' : '#f59e0b';
    const sLabel = r.status === 'pending' ? 'PENDING' : 'TERPAKAI';
    const tStr   = (r.time_start||'00:00').substring(0,5);
    const tEnd   = (r.time_end  ||'00:00').substring(0,5);
    const nama   = r.applicant_name || '-';
    const kend   = r.vehicle_id && r.vehicle_id !== 'PENDING_ASSIGNMENT'
                   ? (ALL_VEHICLES.find(v => v.id === r.vehicle_id)?.name || r.vehicle_id)
                   : '(Menunggu Plotting)';
    bookedHtml += `<div style="display:flex;align-items:baseline;gap:0.4rem;padding:0.25rem 0;border-bottom:1px solid var(--color-slate-100);font-size:0.75rem;color:var(--color-slate-600);line-height:1.5;">
      <span style="font-size:0.6rem;font-weight:700;color:${sColor};border:1px solid ${sColor};border-radius:0.2rem;padding:0.05rem 0.3rem;white-space:nowrap;flex-shrink:0;">${sLabel}</span>
      <span>${kend} &ndash; ${tStr}–${tEnd} &ndash; ${nama}</span>
    </div>`;
  });
  if (!bookedHtml) bookedHtml = `<div style="font-size:0.75rem;color:var(--color-slate-400);">Tidak ada.</div>`;

  det.innerHTML = `
    <div style="background:var(--color-slate-50);border:1px solid var(--color-slate-100);border-radius:0.4rem;overflow:hidden;">
      <div style="padding:0.5rem 0.75rem;border-bottom:1px solid var(--color-slate-200);">
        <div style="font-size:0.8rem;font-weight:600;color:var(--color-slate-700);">${dayLabel}</div>
      </div>
      <div style="padding:0.5rem 0.75rem;border-bottom:1px solid var(--color-slate-200);">
        <div style="font-size:0.72rem;font-weight:700;color:#15803d;margin-bottom:0.3rem;">✅ Tersedia (${availableVehicles.length})</div>
        <div>${availHtml}</div>
      </div>
      <div style="padding:0.5rem 0.75rem;">
        <div style="font-size:0.72rem;font-weight:700;color:#dc2626;margin-bottom:0.3rem;">🔴 Terpakai/Pending (${reqs.length})</div>
        ${bookedHtml}
      </div>
    </div>`;
};

// ===== ZOOM MINI CALENDAR =====
window._zoomCalYear  = new Date().getFullYear();
window._zoomCalMonth = new Date().getMonth(); // 0-indexed
window._zoomCalSelected = null;

window.renderZoomCalendar = function() {
  renderZoomCalGrid();
};

window.zoomCalPrevMonth = function() {
  window._zoomCalMonth--;
  if (window._zoomCalMonth < 0) { window._zoomCalMonth = 11; window._zoomCalYear--; }
  renderZoomCalGrid();
  window._zoomCalSelected = null;
  const det = document.getElementById('zoom-cal-detail');
  if (det) det.innerHTML = '';
};

window.zoomCalNextMonth = function() {
  window._zoomCalMonth++;
  if (window._zoomCalMonth > 11) { window._zoomCalMonth = 0; window._zoomCalYear++; }
  renderZoomCalGrid();
  window._zoomCalSelected = null;
  const det = document.getElementById('zoom-cal-detail');
  if (det) det.innerHTML = '';
};

window.renderZoomCalGrid = function() {
  const yr  = window._zoomCalYear;
  const mo  = window._zoomCalMonth;
  const grid = document.getElementById('zoom-cal-grid');
  const title = document.getElementById('zoom-cal-title');
  if (!grid || !title) return;

  const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  title.textContent = `${BULAN[mo]} ${yr}`;

  const occupiedStatuses = ['approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
  const pendingStatuses  = ['pending'];
  const activeStatuses   = [...occupiedStatuses, ...pendingStatuses];

  // Precompute: map 'YYYY-MM-DD' => { approved: [], pending: [], other: [] }
  const dayMap = {};
  allZoom.filter(r => activeStatuses.includes(r.status)).forEach(r => {
    if (!r.date_start) return;
    const start = new Date(r.date_start);
    const end   = r.date_end ? new Date(r.date_end) : new Date(r.date_start);
    // iterate each day in range
    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
      if (d.getMonth() !== mo || d.getFullYear() !== yr) continue;
      const key = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
      if (!dayMap[key]) dayMap[key] = { approved:[], pending:[], other:[] };
      if (occupiedStatuses.includes(r.status)) dayMap[key].approved.push(r);
      else if (pendingStatuses.includes(r.status)) dayMap[key].pending.push(r);
      else dayMap[key].other.push(r);
    }
  });

  const firstDay = new Date(yr, mo, 1).getDay(); // 0=Sun
  const daysInMonth = new Date(yr, mo + 1, 0).getDate();
  const today = new Date();
  const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;

  let html = '';
  // Empty cells before first day
  for (let i = 0; i < firstDay; i++) {
    html += `<div></div>`;
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const ev = dayMap[dateStr];
    const isToday    = dateStr === todayStr;
    const isSelected = dateStr === window._zoomCalSelected;

    let dots = '';
    if (ev) {
      if (ev.approved.length) dots += `<div style="width:5px;height:5px;border-radius:50%;background:#16a34a;display:inline-block;margin:0 1px;"></div>`;
      if (ev.pending.length)  dots += `<div style="width:5px;height:5px;border-radius:50%;background:#f59e0b;display:inline-block;margin:0 1px;"></div>`;
      if (ev.other.length)    dots += `<div style="width:5px;height:5px;border-radius:50%;background:#e11d48;display:inline-block;margin:0 1px;"></div>`;
    }

    const bgColor   = isSelected ? '#2563eb' : isToday ? '#eff6ff' : ev ? 'var(--color-slate-50)' : 'transparent';
    const textColor = isSelected ? '#fff'    : isToday ? '#2563eb' : 'var(--color-slate-700)';
    const border    = isToday && !isSelected ? '1.5px solid #2563eb' : isSelected ? 'none' : '1px solid transparent';

    html += `
      <div onclick="showZoomDayDetail('${dateStr}')" style="
        padding:0.2rem 0; text-align:center; cursor:pointer;
        border-radius:0.4rem; background:${bgColor}; border:${border}; transition:all .12s;
      " onmouseover="if('${dateStr}'!==window._zoomCalSelected)this.style.background='var(--color-slate-100)'"
         onmouseout="if('${dateStr}'!==window._zoomCalSelected)this.style.background='${ev ? 'var(--color-slate-50)' : 'transparent'}'">
        <div style="font-size:0.72rem;font-weight:${isToday||isSelected?'700':'500'};color:${textColor};line-height:1.6;">${d}</div>
        <div style="display:flex;justify-content:center;align-items:center;min-height:7px;">${dots}</div>
      </div>`;
  }

  grid.innerHTML = html;
  // Re-render detail if date still selected
  if (window._zoomCalSelected) showZoomDayDetail(window._zoomCalSelected, false);
};

window.showZoomDayDetail = function(dateStr, updateGrid = true) {
  window._zoomCalSelected = dateStr;
  if (updateGrid) renderZoomCalGrid();

  const det = document.getElementById('zoom-cal-detail');
  if (!det) return;

  const occupiedStatuses = ['approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
  const activeStatuses = [...occupiedStatuses, 'pending'];

  const reqs = allZoom.filter(r => {
    if (!activeStatuses.includes(r.status) || !r.date_start) return false;
    return r.date_start <= dateStr && (r.date_end >= dateStr || r.date_start === dateStr);
  });

  const [yr, mo, dy] = dateStr.split('-');
  const BULAN    = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const dayObj   = new Date(dateStr);
  const dayLabel = `${dayNames[dayObj.getDay()]}, ${parseInt(dy)} ${BULAN[parseInt(mo)-1]} ${yr}`;

  // Akun zoom yang sudah terpakai pada tanggal ini
  const bookedZoomIds = new Set(reqs.filter(r => occupiedStatuses.includes(r.status)).map(r => r.zoom_account_id));
  const availableZoom = ZOOM_ACCOUNTS.filter(z => !bookedZoomIds.has(z.id));

  // -- Bagian TERSEDIA --
  let availHtml = availableZoom.map(z =>
    `<span style="display:inline-block;font-size:0.72rem;background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:0.25rem;padding:0.1rem 0.45rem;margin:0.1rem 0.15rem;">${z.name}</span>`
  ).join('');
  if (!availHtml) availHtml = `<span style="font-size:0.75rem;color:var(--color-slate-400);">Semua akun Zoom terpakai</span>`;

  // -- Bagian TERPAKAI --
  let bookedHtml = '';
  reqs.sort((a,b) => (a.time_start||'').localeCompare(b.time_start||'')).forEach(r => {
    const sColor   = occupiedStatuses.includes(r.status) ? '#dc2626' : '#f59e0b';
    const sLabel   = r.status === 'pending' ? 'PENDING' : 'TERPAKAI';
    const tStr     = (r.time_start||'00:00').substring(0,5);
    const tEnd     = (r.time_end  ||'00:00').substring(0,5);
    const nama     = r.applicant_name || '-';
    const acct     = ZOOM_ACCOUNTS.find(z => z.id === r.zoom_account_id)?.name || r.zoom_account_id;
    const kegiatan = r.request_type || r.purpose || '-';
    bookedHtml += `<div style="display:flex;align-items:baseline;gap:0.4rem;padding:0.25rem 0;border-bottom:1px solid var(--color-slate-100);font-size:0.75rem;color:var(--color-slate-600);line-height:1.5;">
      <span style="font-size:0.6rem;font-weight:700;color:${sColor};border:1px solid ${sColor};border-radius:0.2rem;padding:0.05rem 0.3rem;white-space:nowrap;flex-shrink:0;">${sLabel}</span>
      <span>${acct} &ndash; ${tStr}–${tEnd} &ndash; ${nama} = ${kegiatan}</span>
    </div>`;
  });
  if (!bookedHtml) bookedHtml = `<div style="font-size:0.75rem;color:var(--color-slate-400);">Tidak ada.</div>`;

  det.innerHTML = `
    <div style="background:var(--color-slate-50);border:1px solid var(--color-slate-100);border-radius:0.4rem;overflow:hidden;">
      <div style="padding:0.5rem 0.75rem;border-bottom:1px solid var(--color-slate-200);">
        <div style="font-size:0.8rem;font-weight:600;color:var(--color-slate-700);">${dayLabel}</div>
      </div>
      <div style="padding:0.5rem 0.75rem;border-bottom:1px solid var(--color-slate-200);">
        <div style="font-size:0.72rem;font-weight:700;color:#15803d;margin-bottom:0.3rem;">✅ Tersedia (${availableZoom.length})</div>
        <div>${availHtml}</div>
      </div>
      <div style="padding:0.5rem 0.75rem;">
        <div style="font-size:0.72rem;font-weight:700;color:#dc2626;margin-bottom:0.3rem;">🔴 Terpakai/Pending (${reqs.length})</div>
        ${bookedHtml}
      </div>
    </div>`;
};

// Legacy function stub (tidak dipakai tapi dipertahankan agar tidak error)
window.renderZoomAvail = function() {

  const dateEl = document.getElementById('zoom-avail-date-picker');
  const container = document.getElementById('zoom-avail-container');
  const statusDate = document.getElementById('zoom-status-date');
  if(!dateEl || !container) return;
  const tDate = dateEl.value;
  if(!tDate) return;

  const inputD = new Date(tDate);
  const hariId = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'][inputD.getDay()];
  const bulanId = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][inputD.getMonth()];
  const formattedDate = `${hariId}, ${inputD.getDate()} ${bulanId} ${inputD.getFullYear()}`;
  const monthYearStr = `${bulanId} ${inputD.getFullYear()}`;

  if(statusDate) statusDate.textContent = formattedDate;

  const occupiedStatuses = ['approved','ready_for_user','in-progress', 'verified', 'waiting_manager_fad', 'waiting_ppk', 'waiting_bod', 'approved_waiting_fund'];
  const pendingStatuses  = ['pending'];
  const activeStatuses   = [...occupiedStatuses, ...pendingStatuses];
  
  let html = '';
  ZOOM_ACCOUNTS.forEach(z => {
    // Gunakan allZoom (semua user) bukan myZoom
    const zReqs = allZoom.filter(r => r.zoom_account_id === z.id && activeStatuses.includes(r.status));
    
    const isOccupied = zReqs.some(r => occupiedStatuses.includes(r.status) && (r.date_start <= tDate && r.date_end >= tDate));
    const isPending  = zReqs.some(r => pendingStatuses.includes(r.status) && (r.date_start <= tDate && r.date_end >= tDate));

    let displayBadge = '<span class="badge" style="background:#00a35c;color:#fff;font-size:0.75rem;padding:0.25rem 0.6rem;letter-spacing:0.5px;">TERSEDIA</span>';
    if (isOccupied) {
      displayBadge = '<span class="badge" style="background:var(--color-red-600);color:#fff;font-size:0.75rem;padding:0.25rem 0.6rem;letter-spacing:0.5px;">TERPAKAI</span>';
    } else if (isPending) {
      displayBadge = '<span class="badge" style="background:var(--color-amber-500);color:#fff;font-size:0.75rem;padding:0.25rem 0.6rem;letter-spacing:0.5px;">PENDING</span>';
    }

    // Jadwal dalam bulan yang dipilih (dari SEMUA user)
    const upcoming = zReqs.filter(r => {
       const dStart = new Date(r.date_start);
       return dStart.getMonth() === inputD.getMonth() && dStart.getFullYear() === inputD.getFullYear();
    }).sort((a,b) => new Date(a.date_start) - new Date(b.date_start));

    let scheduleHtml = '';
    if(upcoming.length === 0) {
      scheduleHtml = `<div style="font-size:0.85rem;color:var(--color-slate-400);margin-top:0.25rem;">(Tidak ada jadwal di bulan ${monthYearStr})</div>`;
    } else {
      scheduleHtml += '<div style="display:flex;flex-direction:column;gap:0.5rem;margin-top:0.5rem;">';
      upcoming.forEach(r => {
        const sColor  = r.status === 'pending' ? 'var(--color-amber-500)' : (occupiedStatuses.includes(r.status) ? 'var(--color-red-600)' : 'var(--color-slate-400)');
        const sLabel  = r.status === 'pending' ? 'PENDING' : r.status.toUpperCase().replace(/_/g,' ');
        const dStr    = formatDate(r.date_start);
        const dEnd    = r.date_end && r.date_end !== r.date_start ? ` s/d ${formatDate(r.date_end)}` : '';
        const tStr    = (r.time_start || '').substring(0, 5);
        const tEnd    = (r.time_end   || '').substring(0, 5);
        const waktu   = tStr ? `${tStr}${tEnd ? ' – ' + tEnd : ''}` : '-';
        const kegiatan = r.request_type || r.purpose || '-';
        const peserta  = r.participants ? ` · ${r.participants} peserta` : '';

        scheduleHtml += `
        <div style="background:var(--color-slate-50);border:1px solid var(--color-slate-100);border-left:3px solid ${sColor};border-radius:var(--radius-md);padding:0.5rem 0.75rem;font-size:0.82rem;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.2rem;">
            <span style="font-weight:700;color:var(--color-slate-700);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:65%;" title="${kegiatan}">${kegiatan}</span>
            <span style="font-size:0.7rem;font-weight:700;color:${sColor};padding:0.1rem 0.4rem;border:1px solid ${sColor};border-radius:0.2rem;">${sLabel}</span>
          </div>
          <div style="color:var(--color-slate-500);">
            📅 ${dStr}${dEnd} &nbsp;⏰ ${waktu}${peserta}
          </div>
          <div style="color:var(--color-slate-500);margin-top:0.15rem;">
            👤 ${r.applicant_name || '-'} <span style="color:var(--color-slate-400);">(${r.applicant_unit || '-'})</span>
          </div>
        </div>`;
      });
      scheduleHtml += '</div>';
    }

    html += `
      <div style="border-bottom: 1px solid var(--color-slate-100); padding-bottom: 1.25rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
          <div style="font-weight:700; color:var(--color-slate-800); font-size: 1rem;">${z.name}</div>
          ${displayBadge}
        </div>
        <div style="font-size:0.8rem; color:var(--color-slate-400);margin-bottom:0.1rem;">Jadwal bulan ${monthYearStr} (semua pemohon):</div>
        ${scheduleHtml}
      </div>
    `;
  });

  container.innerHTML = html;
};
// ===== ROOM MINI CALENDAR =====
window._roomCalYear     = new Date().getFullYear();
window._roomCalMonth    = new Date().getMonth();
window._roomCalSelected = null;

window.renderRoomCalendar = function() {
  renderRoomCalGrid();
};

window.roomCalPrevMonth = function() {
  window._roomCalMonth--;
  if (window._roomCalMonth < 0) { window._roomCalMonth = 11; window._roomCalYear--; }
  renderRoomCalGrid();
  window._roomCalSelected = null;
  const det = document.getElementById('room-cal-detail');
  if (det) det.innerHTML = '';
};

window.roomCalNextMonth = function() {
  window._roomCalMonth++;
  if (window._roomCalMonth > 11) { window._roomCalMonth = 0; window._roomCalYear++; }
  renderRoomCalGrid();
  window._roomCalSelected = null;
  const det = document.getElementById('room-cal-detail');
  if (det) det.innerHTML = '';
};

window.renderRoomCalGrid = function() {
  const yr   = window._roomCalYear;
  const mo   = window._roomCalMonth;
  const grid  = document.getElementById('room-cal-grid');
  const title = document.getElementById('room-cal-title');
  if (!grid || !title) return;

  const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  title.textContent = `${BULAN[mo]} ${yr}`;

  const occupiedStatuses = ['approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
  const pendingStatuses  = ['pending'];
  const activeStatuses   = [...occupiedStatuses, ...pendingStatuses];

  // Precompute dayMap dari allRoom
  const dayMap = {};
  allRoom.filter(r => activeStatuses.includes(r.status)).forEach(r => {
    if (!r.date_start) return;
    const start = new Date(r.date_start);
    const end   = r.date_end ? new Date(r.date_end) : new Date(r.date_start);
    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
      if (d.getMonth() !== mo || d.getFullYear() !== yr) continue;
      const key = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
      if (!dayMap[key]) dayMap[key] = { approved:[], pending:[], other:[] };
      if (occupiedStatuses.includes(r.status)) dayMap[key].approved.push(r);
      else if (pendingStatuses.includes(r.status)) dayMap[key].pending.push(r);
      else dayMap[key].other.push(r);
    }
  });

  const firstDay    = new Date(yr, mo, 1).getDay();
  const daysInMonth = new Date(yr, mo + 1, 0).getDate();
  const today       = new Date();
  const todayStr    = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;

  let html = '';
  for (let i = 0; i < firstDay; i++) html += `<div></div>`;

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr    = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const ev         = dayMap[dateStr];
    const isToday    = dateStr === todayStr;
    const isSelected = dateStr === window._roomCalSelected;

    let dots = '';
    if (ev) {
      if (ev.approved.length) dots += `<div style="width:5px;height:5px;border-radius:50%;background:#16a34a;display:inline-block;margin:0 1px;"></div>`;
      if (ev.pending.length)  dots += `<div style="width:5px;height:5px;border-radius:50%;background:#f59e0b;display:inline-block;margin:0 1px;"></div>`;
      if (ev.other.length)    dots += `<div style="width:5px;height:5px;border-radius:50%;background:#e11d48;display:inline-block;margin:0 1px;"></div>`;
    }

    const bgColor   = isSelected ? '#2563eb' : isToday ? '#eff6ff' : ev ? 'var(--color-slate-50)' : 'transparent';
    const textColor = isSelected ? '#fff'    : isToday ? '#2563eb' : 'var(--color-slate-700)';
    const border    = isToday && !isSelected ? '1.5px solid #2563eb' : isSelected ? 'none' : '1px solid transparent';

    html += `
      <div onclick="showRoomDayDetail('${dateStr}')" style="
        padding:0.2rem 0; text-align:center; cursor:pointer;
        border-radius:0.4rem; background:${bgColor}; border:${border}; transition:all .12s;
      " onmouseover="if('${dateStr}'!==window._roomCalSelected)this.style.background='var(--color-slate-100)'"
         onmouseout="if('${dateStr}'!==window._roomCalSelected)this.style.background='${ev ? 'var(--color-slate-50)' : 'transparent'}'">
        <div style="font-size:0.72rem;font-weight:${isToday||isSelected?'700':'500'};color:${textColor};line-height:1.6;">${d}</div>
        <div style="display:flex;justify-content:center;align-items:center;min-height:7px;">${dots}</div>
      </div>`;
  }

  grid.innerHTML = html;
  if (window._roomCalSelected) showRoomDayDetail(window._roomCalSelected, false);
};

window.showRoomDayDetail = function(dateStr, updateGrid = true) {
  window._roomCalSelected = dateStr;
  if (updateGrid) renderRoomCalGrid();

  const det = document.getElementById('room-cal-detail');
  if (!det) return;

  const occupiedStatuses = ['approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
  const activeStatuses   = [...occupiedStatuses, 'pending'];

  const reqs = allRoom.filter(r => {
    if (!activeStatuses.includes(r.status) || !r.date_start) return false;
    return r.date_start <= dateStr && (r.date_end >= dateStr || r.date_start === dateStr);
  });

  const [yr, mo, dy] = dateStr.split('-');
  const BULAN    = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const dayObj   = new Date(dateStr);
  const dayLabel = `${dayNames[dayObj.getDay()]}, ${parseInt(dy)} ${BULAN[parseInt(mo)-1]} ${yr}`;

  // Ruangan yang sudah terpakai
  const bookedRoomIds = new Set(reqs.filter(r => occupiedStatuses.includes(r.status)).map(r => r.room_id));
  const availableRooms = ROOMS.filter(rm => !bookedRoomIds.has(rm.id));

  // -- Bagian TERSEDIA --
  let availHtml = availableRooms.map(rm =>
    `<span style="display:inline-block;font-size:0.72rem;background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:0.25rem;padding:0.1rem 0.45rem;margin:0.1rem 0.15rem;">${rm.name}</span>`
  ).join('');
  if (!availHtml) availHtml = `<span style="font-size:0.75rem;color:var(--color-slate-400);">Semua ruangan terpakai</span>`;

  // -- Bagian TERPAKAI --
  let bookedHtml = '';
  reqs.sort((a,b) => (a.time_start||'').localeCompare(b.time_start||'')).forEach(r => {
    const sColor  = occupiedStatuses.includes(r.status) ? '#dc2626' : '#f59e0b';
    const sLabel  = r.status === 'pending' ? 'PENDING' : 'TERPAKAI';
    const tStr    = (r.time_start||'00:00').substring(0,5);
    const tEnd    = (r.time_end  ||'00:00').substring(0,5);
    const nama    = r.applicant_name || '-';
    const ruangan = ROOMS.find(rm => rm.id === r.room_id)?.name || r.room_id || '-';
    bookedHtml += `<div style="display:flex;align-items:baseline;gap:0.4rem;padding:0.25rem 0;border-bottom:1px solid var(--color-slate-100);font-size:0.75rem;color:var(--color-slate-600);line-height:1.5;">
      <span style="font-size:0.6rem;font-weight:700;color:${sColor};border:1px solid ${sColor};border-radius:0.2rem;padding:0.05rem 0.3rem;white-space:nowrap;flex-shrink:0;">${sLabel}</span>
      <span>${ruangan} &ndash; ${tStr}–${tEnd} &ndash; ${nama}</span>
    </div>`;
  });
  if (!bookedHtml) bookedHtml = `<div style="font-size:0.75rem;color:var(--color-slate-400);">Tidak ada.</div>`;

  det.innerHTML = `
    <div style="background:var(--color-slate-50);border:1px solid var(--color-slate-100);border-radius:0.4rem;overflow:hidden;">
      <div style="padding:0.5rem 0.75rem;border-bottom:1px solid var(--color-slate-200);">
        <div style="font-size:0.8rem;font-weight:600;color:var(--color-slate-700);">${dayLabel}</div>
      </div>
      <div style="padding:0.5rem 0.75rem;border-bottom:1px solid var(--color-slate-200);">
        <div style="font-size:0.72rem;font-weight:700;color:#15803d;margin-bottom:0.3rem;">✅ Tersedia (${availableRooms.length})</div>
        <div>${availHtml}</div>
      </div>
      <div style="padding:0.5rem 0.75rem;">
        <div style="font-size:0.72rem;font-weight:700;color:#dc2626;margin-bottom:0.3rem;">🔴 Terpakai/Pending (${reqs.length})</div>
        ${bookedHtml}
      </div>
    </div>`;
};

// ===== REPAIR RAB VIEW (READ ONLY) =====
async function loadRABView(requestId) {
  const container = document.getElementById('rab-view-container');
  if (!container) return;
  
  try {
    const data  = await api(API_BASE + `requests.php?action=get_repair_budget&request_id=${requestId}`);
    const items = Array.isArray(data) ? data : (data.data || []);

    if (!items.length) {
      container.innerHTML = `
        <div style="padding: 1rem 1.25rem; font-size: 0.875rem; color: #64748b;">
          <strong>Rincian RAB:</strong> Belum ada detail anggaran untuk laporan ini.
        </div>`;
      return;
    }

    const total = items.reduce((s, i) => s + parseFloat(i.total_price || 0), 0);
    container.innerHTML = `
      <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9;">
        <div style="font-size: 0.875rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">📋 Rincian Anggaran (RAB)</div>
        <div class="table-wrap" style="border: 1px solid #f1f5f9; border-radius: 0.5rem; overflow: hidden;">
          <table style="font-size: 0.82rem; width: 100%;">
            <thead style="background: #f8fafc;">
              <tr>
                <th style="padding: 0.5rem; text-align: left;">Item</th>
                <th style="padding: 0.5rem; text-align: right;">Qty</th>
                <th style="padding: 0.5rem; text-align: right;">Harga Satuan</th>
                <th style="padding: 0.5rem; text-align: right;">Total</th>
              </tr>
            </thead>
            <tbody>
              ${items.map(i => `
                <tr style="border-top: 1px solid #f1f5f9;">
                  <td style="padding: 0.5rem;">${i.item_name}</td>
                  <td style="padding: 0.5rem; text-align: right;">${i.quantity}</td>
                  <td style="padding: 0.5rem; text-align: right;">${formatRupiah(i.unit_price)}</td>
                  <td style="padding: 0.5rem; text-align: right; font-weight: 600;">${formatRupiah(i.total_price)}</td>
                </tr>
              `).join('')}
              <tr style="background: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0;">
                <td colspan="3" style="padding: 0.5rem; text-align: right;">TOTAL ESTIMASI BIAYA:</td>
                <td style="padding: 0.5rem; text-align: right; color: var(--color-blue-600);">${formatRupiah(total)}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #64748b; font-style: italic;">
          * Anggaran ini merupakan estimasi perbaikan yang diajukan oleh teknisi/admin.
        </div>
      </div>`;
  } catch (err) {
    console.error("Failed to load RAB:", err);
    container.innerHTML = '<div style="padding:1rem;color:red;">Gagal memuat detail RAB.</div>';
  }
}

function formatRupiah(num) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
}

// ===== REPAIR MINI CALENDAR =====
window._repairCalYear     = new Date().getFullYear();
window._repairCalMonth    = new Date().getMonth();
window._repairCalSelected = null;

window.renderRepairCalendar = function() {
  renderRepairCalGrid();
};

window.repairCalPrevMonth = function() {
  window._repairCalMonth--;
  if (window._repairCalMonth < 0) { window._repairCalMonth = 11; window._repairCalYear--; }
  renderRepairCalGrid();
  window._repairCalSelected = null;
  const det = document.getElementById('repair-cal-detail');
  if (det) det.innerHTML = '';
};

window.repairCalNextMonth = function() {
  window._repairCalMonth++;
  if (window._repairCalMonth > 11) { window._repairCalMonth = 0; window._repairCalYear++; }
  renderRepairCalGrid();
  window._repairCalSelected = null;
  const det = document.getElementById('repair-cal-detail');
  if (det) det.innerHTML = '';
};

window.renderRepairCalGrid = function() {
  const yr   = window._repairCalYear;
  const mo   = window._repairCalMonth;
  const grid  = document.getElementById('repair-cal-grid');
  const title = document.getElementById('repair-cal-title');
  if (!grid || !title) return;

  const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  title.textContent = `${BULAN[mo]} ${yr}`;

  const occupiedStatuses = ['approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
  const pendingStatuses  = ['pending'];
  const activeStatuses   = [...occupiedStatuses, ...pendingStatuses];

  const dayMap = {};
  allRepair.filter(r => activeStatuses.includes(r.status)).forEach(r => {
    if (!r.incident_date) return;
    const key = r.incident_date;
    const d = new Date(key);
    if (d.getMonth() !== mo || d.getFullYear() !== yr) return;
    if (!dayMap[key]) dayMap[key] = { critical:0, high:0, medium:0, low:0 };
    const p = (r.priority || 'medium').toLowerCase();
    if (p === 'critical') dayMap[key].critical++;
    else if (p === 'high') dayMap[key].high++;
    else if (p === 'medium') dayMap[key].medium++;
    else dayMap[key].low++;
  });

  const firstDay = new Date(yr, mo, 1).getDay();
  const daysInMo = new Date(yr, mo + 1, 0).getDate();
  const todayStr = new Date().toISOString().split('T')[0];

  let html = '';
  for (let i = 0; i < firstDay; i++) html += `<div></div>`;

  for (let dy = 1; dy <= daysInMo; dy++) {
    const key = `${yr}-${String(mo+1).padStart(2,'0')}-${String(dy).padStart(2,'0')}`;
    const ev = dayMap[key];
    const isSelected = window._repairCalSelected === key;
    const isToday    = key === todayStr;

    let dots = '';
    if (ev) {
      if (ev.critical) dots += `<div style="width:4px;height:4px;border-radius:50%;background:#ef4444;margin:0 1px;"></div>`;
      if (ev.high)     dots += `<div style="width:4px;height:4px;border-radius:50%;background:#f97316;margin:0 1px;"></div>`;
      if (ev.medium)   dots += `<div style="width:4px;height:4px;border-radius:50%;background:#f59e0b;margin:0 1px;"></div>`;
      if (ev.low)      dots += `<div style="width:4px;height:4px;border-radius:50%;background:#10b981;margin:0 1px;"></div>`;
    }

    const bgColor   = isSelected ? '#16a34a' : isToday ? '#f0fdf4' : ev ? 'var(--color-slate-50)' : 'transparent';
    const textColor = isSelected ? '#fff'    : isToday ? '#16a34a' : 'var(--color-slate-700)';
    const border    = isToday && !isSelected ? '1.5px solid #16a34a' : isSelected ? 'none' : '1px solid transparent';

    html += `
      <div onclick="showRepairDayDetail('${key}')" style="
        padding:0.2rem 0; text-align:center; cursor:pointer;
        border-radius:0.4rem; background:${bgColor}; border:${border}; transition:all .12s;
      " onmouseover="if('${key}'!==window._repairCalSelected)this.style.background='var(--color-slate-100)'"
         onmouseout="if('${key}'!==window._repairCalSelected)this.style.background='${ev ? 'var(--color-slate-50)' : 'transparent'}'">
        <div style="font-size:0.72rem;font-weight:${isToday||isSelected?'700':'500'};color:${textColor};line-height:1.6;">${dy}</div>
        <div style="display:flex;justify-content:center;align-items:center;min-height:7px;">${dots}</div>
      </div>`;
  }
  grid.innerHTML = html;
  if (window._repairCalSelected) showRepairDayDetail(window._repairCalSelected, false);
};

window.showRepairDayDetail = function(key, updateGrid = true) {
  window._repairCalSelected = key;
  if (updateGrid) renderRepairCalGrid();
  const det = document.getElementById('repair-cal-detail');
  if (!det) return;

  const list = allRepair.filter(r => r.incident_date === key);
  if (list.length === 0) {
    det.innerHTML = `<div style="font-size:0.75rem;color:var(--color-slate-400);text-align:center;padding:1rem;">Tidak ada laporan pada tanggal ini.</div>`;
    return;
  }

  const dObj = new Date(key);
  const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const BULAN = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const dayName = dayNames[dObj.getDay()];
  const dateLabel = `${dayName}, ${dObj.getDate()} ${BULAN[dObj.getMonth()]} ${dObj.getFullYear()}`;

  det.innerHTML = `
    <div style="background:var(--color-slate-50);border:1px solid var(--color-slate-100);border-radius:0.4rem;overflow:hidden;">
      <div style="padding:0.5rem 0.75rem;border-bottom:1px solid var(--color-slate-200);">
        <div style="font-size:0.8rem;font-weight:600;color:var(--color-slate-700);">${dateLabel}</div>
      </div>
      <div style="padding:0.5rem 0.75rem;">
        <div style="font-size:0.72rem;font-weight:700;color:#dc2626;margin-bottom:0.3rem;">🔴 Laporan Insiden (${list.length})</div>
        ${list.map(r => {
          const p = (r.priority || 'medium').toLowerCase();
          const pCols = { critical:'#ef4444', high:'#f97316', medium:'#f59e0b', low:'#10b981' };
          return `
          <div style="display:flex;align-items:baseline;gap:0.4rem;padding:0.25rem 0;border-bottom:1px solid var(--color-slate-100);font-size:0.72rem;color:var(--color-slate-600);line-height:1.5;">
            <span style="font-size:0.55rem;font-weight:800;color:${pCols[p]};border:1px solid ${pCols[p]};border-radius:0.2rem;padding:0.05rem 0.3rem;white-space:nowrap;flex-shrink:0;text-transform:uppercase;">${p}</span>
            <span><strong>${r.location_detail}</strong> &ndash; ${r.issue_description} &ndash; ${r.applicant_name}</span>
          </div>`;
        }).join('')}
      </div>
    </div>`;
};
</script>
</body>
</html>
