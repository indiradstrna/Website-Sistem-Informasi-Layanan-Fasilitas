<?php
// ============================================================
// admin/index.php — Dashboard Admin
// Setara dengan: app/admin/page.tsx
// ============================================================

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/layout.php';

$session   = getSession();
$userName  = $session['fullName'];
$userRole  = $session['role'];
$userLogin = $session['username'];

renderPageHead('Dashboard Admin');
?>

<style>
/* Notif Dropdown */

/* Notif Dropdown */
/* Notif Dropdown */
.card-shadow {
  box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
.card-header-stats {
  background-color: #f8f9fc;
  border-bottom: 1px solid #e3e6f0;
  padding: 0.75rem 1.25rem;
}
.text-primary-stats {
  color: #4e73df !important;
}
.text-success-stats {
  color: #1cc88a !important;
}
.text-info-stats {
  color: #36b9cc !important;
}
.text-warning-stats {
  color: #f6c23e !important;
}
.font-weight-bold-stats {
  font-weight: 700 !important;
}
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

/* Global Minimalist UI Polishing */
.sidebar-menu-item {
  border-radius: 10px;
  margin: 0.2rem 0.75rem;
  padding: 0.65rem 1rem;
  color: #64748b;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid transparent;
}

.sidebar-menu-item:hover {
  background: #f1f5f9;
  color: #0f172a;
  transform: translateX(4px);
}

.sidebar-menu-item.active {
  background: #fff;
  color: var(--color-emerald-600);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  border-color: #f1f5f9;
}

/* Topbar & Profile Refinements */
/* Removed redundant topbar classes - already in style.css */
.topbar-user-name {
  font-size: 0.8rem;
  font-weight: 700;
  color: #5a5c69;
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
.chart-tooltip {
  position: fixed;
  display: none;
  background: rgba(0, 0, 0, 0.85);
  color: #fff;
  padding: 0.5rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  pointer-events: none;
  z-index: 10000;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  line-height: 1.4;
  white-space: nowrap;
}
</style>

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-content">
      <div class="topbar-left">
        <div class="topbar-logo">
          <img src="../assets/img/logo.png" alt="SEAMEO BIOTROP" />
        </div>
        <button class="btn btn-ghost btn-sm" id="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Toggle Sidebar">☰</button>
        <span class="topbar-title" id="page-title">Admin Dashboard</span>
      </div>
      <div class="topbar-user">
        <div style="display:flex; align-items:center;">
          <!-- Notifications -->
          <div id="notification-area" style="cursor:pointer; position:relative; display:flex; align-items:center; padding: 0.5rem; color: #d1d3e2;" onclick="toggleNotifDropdown(event)">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            <span id="notif-badge" class="nav-badge-count" style="display:none; background-color: #e74a3b;">0</span>
            
            <div id="notif-dropdown" class="notif-dropdown">
              <div class="notif-header">
                <h3>Notifikasi Pengajuan</h3>
                <span class="count" id="notif-header-count">0 Baru</span>
              </div>
              <div id="notif-list" class="notif-list">
                <!-- Items rendered by JS -->
              </div>
            </div>
          </div>

          <div class="topbar-divider"></div>

          <!-- User Info -->
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

  <?php renderSidebar($userRole, 'dashboard', $userName, '../'); ?>

  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" onclick="document.getElementById('sidebar').classList.remove('open')"></div>

  <!-- MAIN CONTENT -->
  <div class="main-content">

    <!-- PAGE CONTENT -->
    <div class="page-content">
      <div class="page-content-inner" id="view-container">
        <!-- JS will render the active view here -->
        <div style="text-align:center;padding:3rem;color:var(--color-slate-400);">
          <div class="spinner" style="border-color:rgba(16,185,129,.2);border-top-color:var(--color-emerald-600);width:2.5rem;height:2.5rem;"></div>
          <p style="margin-top:1rem;">Memuat data...</p>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ===== MODAL: ADD/EDIT USER ===== -->
<div class="modal-overlay" id="modal-user">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title" id="modal-user-title">Tambah User</h3>
      <button class="modal-close modal-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <form id="user-form">
        <input type="hidden" id="edit-user-id" name="id" value="" />
        <div class="grid-2">
          <div class="form-group" style="grid-column: 1/3;">
            <label class="form-label" for="user-employee-id">Pilih Karyawan *</label>
            <select id="user-employee-id" name="employee_id" class="form-select" required onchange="onEmployeeSelect(this)">
              <option value="">-- Pilih Karyawan --</option>
            </select>
            <p style="font-size:0.75rem; color:var(--color-slate-500); margin-top:0.25rem;">NIP/NIK karyawan ini akan otomatis menjadi Username login.</p>
          </div>
          <div class="form-group">
            <label class="form-label" for="user-fullname">Nama Lengkap Akun *</label>
            <input type="text" id="user-fullname" name="full_name" class="form-input" required placeholder="Contoh: Admin Utama" />
          </div>
          <div class="form-group">
            <label class="form-label" for="user-role">Role Akses *</label>
            <select id="user-role" name="role" class="form-select" required>
              <option value="user">User Standar</option>
              <option value="admin">Administrator</option>
              <option value="supervisor">Supervisor FMD</option>
              <option value="pic_repair">PIC Repair</option>
              <option value="managerFMD">Manager FMD</option>
              <option value="bod">BOD / Director</option>
              <option value="ppk">PPK</option>
              <option value="managerFAD">Manager FAD</option>
              <option value="bendahara">Bendahara</option>
            </select>
          </div>
          <div class="form-group" style="grid-column: 1/3;">
            <label class="form-label" for="user-password">Password Login <span id="pw-hint" style="font-weight:400;color:var(--color-slate-400);">(wajib isi)</span></label>
            <input type="password" id="user-password" name="password" class="form-input" placeholder="••••••••" />
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline modal-close-btn">Batal</button>
      <button class="btn btn-primary" onclick="submitUserForm()">Simpan</button>
    </div>
  </div>
</div>

<!-- ===== MODAL: RAB (Budget) ===== -->
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
          <input type="text" id="rab-item-name" class="form-input" placeholder="Contoh: Cat Tembok" />
        </div>
        <div class="form-group">
          <label class="form-label">Qty</label>
          <input type="number" id="rab-item-qty" class="form-input" value="1" min="1" />
        </div>
        <div class="form-group">
          <label class="form-label">Harga Satuan (Rp)</label>
          <input type="number" id="rab-item-price" class="form-input" value="0" min="0" />
        </div>
        <div style="display:flex;align-items:flex-end;">
          <button class="btn btn-primary btn-full" onclick="addRabItem()">+ Tambah</button>
        </div>
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
      <button class="btn btn-primary" onclick="submitRAB()">Ajukan RAB ke Supervisor</button>
    </div>
  </div>
</div>
<!-- ===== TOAST ===== -->
<div id="toast-container"></div>

<script src="../assets/js/main.js"></script>
<script>
// ============================================================
// ADMIN DASHBOARD — JavaScript
// Setara dengan: seluruh logic di app/admin/page.tsx
// ============================================================
window.BASE_URL = '<?= BASE_URL ?>';
const ADMIN_NAME = <?= json_encode($userName) ?>;
const ADMIN_USERNAME = <?= json_encode($userLogin) ?>;
const CURRENT_ROLE = <?= json_encode($userRole) ?>;
const API_BASE   = '<?= BASE_URL ?>/api/';

// --- STATE ---
let allRequests = [];
let allUsers    = [];
let allEmployees= [];
let picPendingRequests = [];
let currentView = 'dashboard';
let currentPage = 1;
let itemsPerPage = 10;

// RAB state
let rabItems = [];
let currentRequestId = null;
let currentRequestNote = '';

// PIC Mapping for highlighting
const PIC_MAP = {
  'Vehicle': ['198605082025211053'], // Alfi Dwi Nugroho
  'Item':    ['198902222025211044'], // Indra Septian
  'Zoom':    ['198902222025211044'], // Indra Septian
  'Room':    ['199008092025212052', '198902222025211044'], // Lastiah, Indra
  'Repair':  ['198605082025211053', '197212162014091003'] // Alfi, Agus Sujadi
};

// Admin Calendar State
window._adminCalYear     = new Date().getFullYear();
window._adminCalMonth    = new Date().getMonth();
window._adminCalSelected = null;

// ===== LOAD ALL DATA =====
async function loadAllData(silent = false) {
  try {
    const [vehicles, rooms, zooms, repairs, items, users, employees] = await Promise.all([
      api(API_BASE + 'requests.php?action=get_vehicle'),
      api(API_BASE + 'requests.php?action=get_room'),
      api(API_BASE + 'requests.php?action=get_zoom'),
      api(API_BASE + 'requests.php?action=get_repair'),
      api(API_BASE + 'requests.php?action=get_item'),
      api(API_BASE + 'users.php?action=get_all'),
      api(API_BASE + 'users.php?action=get_employees'),
    ]);

    // Normalize (same as before)
    const norm = (data, type) => data.map(item => ({
      id: item.id,
      type,
      applicant_name: item.applicant_name,
      applicant_unit: item.applicant_unit,
      status: item.status || 'pending',
      purpose: item.purpose || item.issue_description || '-',
      note: item.note || '',
      details: type === 'Vehicle' ? (VEHICLE_MAP[item.vehicle_id] || item.vehicle_id) :
               type === 'Room'    ? `${ROOM_MAP[item.room_id] || item.room_id} (${item.participants||0} org)` :
               type === 'Zoom'    ? item.zoom_account_id :
               type === 'Repair'  ? `${item.location_detail}: ${item.issue_description}` :
               type === 'Item'    ? `${item.item_name} (${item.item_quantity})` : '-',
      date_start: type === 'Item' ? item.loan_date : type === 'Repair' ? item.incident_date : item.date_start,
      raw_time_start: item.time_start || item.incident_time || '',
      raw_time_end:   item.time_end   || '',
      raw_date_end:   item.return_date || item.date_end || '',
      driver_name:    item.driver_name || '',
      created_at:     item.created_at,
      vehicle_id:     item.vehicle_id || '',
      zoom_account_id: item.zoom_account_id || '',
      request_type:    item.request_type   || '',
      special_needs:   item.special_needs  || '',
      participants:    item.participants   || '',
      room_id:         item.room_id        || '',
      location_detail: item.location_detail || '',
      item_name:       item.item_name      || '',
    }));

    const statusWeight = {
      'pending': 1,
      'verified': 2,
      'in-progress': 3,
      'waiting_manager_fmd': 4,
      'waiting_manager_fad': 5,
      'waiting_ppk': 6,
      'waiting_bod': 7,
      'approved_waiting_fund': 8,
      'approved': 9,
      'ready_for_user': 10,
      'completed': 11,
      'returned': 12,
      'rejected': 13
    };

    allRequests = [
      ...norm(vehicles || [], 'Vehicle'),
      ...norm(rooms    || [], 'Room'),
      ...norm(zooms    || [], 'Zoom'),
      ...norm(repairs  || [], 'Repair'),
      ...norm(items    || [], 'Item'),
    ].sort((a,b) => new Date(b.created_at) - new Date(a.created_at));

    allUsers = Array.isArray(users) ? users : [];
    allEmployees = Array.isArray(employees) ? employees : [];

    const isManagerFMD = (CURRENT_ROLE === 'managerFMD' || ADMIN_USERNAME === '197707072025211067');
    
    const picPending = allRequests.filter(r => {
      if (r.status === 'pending') {
        const allowed = PIC_MAP[r.type] || [];
        return allowed.length === 0 || allowed.includes(ADMIN_USERNAME);
      }
      if (r.status === 'waiting_manager_fmd') return isManagerFMD;
      if (r.status === 'verified' && r.type === 'Repair') return isManagerFMD;
      if (r.status === 'waiting_manager_fad' && CURRENT_ROLE === 'managerFAD') return true;
      if (r.status === 'waiting_ppk' && CURRENT_ROLE === 'ppk') return true;
      if (r.status === 'waiting_bod' && CURRENT_ROLE === 'bod') return true;
      if (r.status === 'approved_waiting_fund' && CURRENT_ROLE === 'bendahara') return true;
      return false;
    });

    const picTrack = allRequests.filter(r => {
      if (r.status === 'approved' || r.status === 'ready_for_user') {
        const allowed = PIC_MAP[r.type] || [];
        return allowed.length === 0 || allowed.includes(ADMIN_USERNAME);
      }
      return false;
    });

    picPendingRequests = picPending;
    const pendingCount = picPending.length;
    const trackCount = picTrack.length;
    
    const pendingBadge = document.getElementById('pending_count');
    if (pendingBadge) {
        pendingBadge.textContent = pendingCount || '';
        pendingBadge.style.display = pendingCount > 0 ? 'flex' : 'none';
    }

    const trackBadge = document.getElementById('track_count');
    if (trackBadge) {
        trackBadge.textContent = trackCount || '';
        trackBadge.style.display = trackCount > 0 ? 'flex' : 'none';
    }

    const topNotif = document.getElementById('notif-badge');
    const headerCount = document.getElementById('notif-header-count');
    if (topNotif) {
      if (pendingCount > 0) {
        topNotif.textContent = pendingCount;
        topNotif.style.display = 'flex';
        if (headerCount) headerCount.textContent = `${pendingCount} Baru`;
      } else {
        topNotif.style.display = 'none';
        if (headerCount) headerCount.textContent = `0 Baru`;
      }
    }
    
    renderNotifDropdown();

    // IF SILENT: Don't refresh the whole view, but if in management/track, refresh table only
    if (silent) {
        if (currentView === 'request_management' && !document.getElementById('req-search')?.value) {
            const status = document.getElementById('req-status-filter')?.value || 'all';
            const type   = document.getElementById('req-type-filter')?.value   || 'all';
            if (status === 'all' && type === 'all') {
                filterRequests(true); // true to preserve current page
            }
        } else if (currentView === 'track_reports' && !document.getElementById('track-search')?.value) {
            const type = document.getElementById('track-type')?.value || 'all';
            const status = document.getElementById('track-status')?.value || 'all';
            if (type === 'all' && status === 'all') {
                filterTrack();
            }
        }
    } else {
        renderCurrentView();
    }
  } catch(err) {
    console.error(err);
    if (!silent) Toast.error('Gagal memuat data dari server.');
  }
}

// ===== SWITCH VIEW =====
let previousView = 'dashboard';
let currentReqTab = 'my_tasks';
function switchView(viewId, initialStatus = 'all') {
  window._initialReqStatus = initialStatus;
  if (currentView && currentView !== 'detail_pengajuan' && currentView !== 'profile') {
    previousView = currentView;
  }
  currentView = viewId;
  currentPage = 1;
  document.querySelectorAll('.nav-item').forEach(el => el.classList.toggle('active', el.dataset.view === viewId));
  const titles = {
    dashboard:'Dashboard',
    request_management:'Manajemen Pengajuan',
    track_reports:'Track Pengajuan',
    user_management:'Manajemen User',
    analytics:'Analitik',
    profile:'Profil'
  };
  const titleEl = document.getElementById('page-title');
  if (titleEl) titleEl.textContent = titles[viewId] || viewId;
  renderCurrentView();
}

function renderCurrentView() {
  const container = document.getElementById('view-container');
  switch(currentView) {
    case 'dashboard':
      container.innerHTML = renderDashboard();
      setTimeout(renderAdminCalendar, 50);
      break;
    case 'request_management': container.innerHTML = renderRequestManagement();  break;
    case 'track_reports':      container.innerHTML = renderTrackReports();       break;
    case 'user_management':    container.innerHTML = renderUserManagement();     break;
    case 'analytics':          container.innerHTML = renderAnalytics();          break;
    case 'profile':            container.innerHTML = renderProfile();            break;
    case 'detail_pengajuan':   container.innerHTML = renderDetailPengajuan();    break;
    default:                   container.innerHTML = `<div class="page-header"><h1>${currentView}</h1></div>`;
  }
}

// ===== RENDER VIEWS =====

// --- DASHBOARD ---
function renderDashboard() {
  const pending   = allRequests.filter(r => r.status === 'pending' || r.status === 'waiting_manager_fmd').length;
  const approved  = allRequests.filter(r => r.status === 'approved').length;
  const completed = allRequests.filter(r => ['completed','returned'].includes(r.status)).length;
  const rejected  = allRequests.filter(r => r.status === 'rejected').length;
  const total     = allRequests.length;

  const recentPending = allRequests.filter(r => r.status === 'pending' || r.status === 'waiting_manager_fmd').slice(0, 5);

  return `
  <div class="page-header">
    <h1>Dashboard Admin</h1>
    <p>Overview sistem pengajuan fasilitas SEAMEO BIOTROP</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card border-left-amber" style="cursor:pointer;" onclick="switchView('request_management')">
      <div class="stat-label">Menunggu Tindakan</div>
      <div class="stat-value" style="color:var(--color-amber-600);">${pending}</div>
      <div class="stat-sub">Pengajuan pending</div>
    </div>
    <div class="stat-card border-left-emerald" style="cursor:pointer;" onclick="switchView('request_management')">
      <div class="stat-label">Disetujui</div>
      <div class="stat-value" style="color:var(--color-emerald-600);">${approved}</div>
      <div class="stat-sub">Sedang berjalan</div>
    </div>
    <div class="stat-card border-left-blue">
      <div class="stat-label">Selesai</div>
      <div class="stat-value" style="color:var(--color-blue-600);">${completed}</div>
      <div class="stat-sub">Completed/Returned</div>
    </div>
    <div class="stat-card border-left-red">
      <div class="stat-label">Ditolak</div>
      <div class="stat-value" style="color:var(--color-red-600);">${rejected}</div>
      <div class="stat-sub">Rejected</div>
    </div>
  </div>

  <div class="grid-2 mb-4" style="gap:1.5rem;">
    <div class="card">
      <div class="card-header"><div class="card-title">Navigasi Utama</div><div class="card-desc">Akses cepat menu administratif</div></div>
      <div class="card-body grid-2-sm" style="gap:1rem;">
        ${[
          {id:'request_management', icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>', label:'Manajemen Pengajuan', desc:'Proses & verifikasi berkas', status: 'all'},
          {id:'request_management', icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>', label:'Pengajuan Pending', desc:'Tindak lanjuti pengajuan', status: 'pending'},
          {id:'track_reports', icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.98"/></svg>', label:'Track Pengajuan', desc:'Lihat riwayat & status'},
          {id:'user_management', icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>', label:'Manajemen User', desc:'Kelola akun & pegawai'},
          {id:'analytics', icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', label:'Laporan & Statistik', desc:'Analisis data bulanan'},
        ].map(m => `
          <div class="stat-card" style="display:flex; align-items:center; gap:1rem; cursor:pointer; padding:1rem; border:1px solid var(--color-slate-200); transition:all 0.2s;" 
               onclick="switchView('${m.id}', '${m.status || 'all'}')" 
               onmouseover="this.style.borderColor='var(--color-blue-500)'; this.style.background='var(--color-blue-50)';" 
               onmouseout="this.style.borderColor='var(--color-slate-200)'; this.style.background='white';">
            <div style="background:${m.status === 'pending' ? 'var(--color-amber-50)' : 'var(--color-blue-50)'}; color:${m.status === 'pending' ? 'var(--color-amber-600)' : 'var(--color-blue-600)'}; width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
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

    <div class="card">
      <div class="card-header">
        <div class="card-title">Kalender Pengajuan</div>
        <div class="card-desc">Ringkasan seluruh layanan BIOTROP</div>
      </div>
      <div class="card-body" style="padding:0.75rem;">
        <!-- Navigasi Bulan -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
          <button onclick="adminCalPrevMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8249;</button>
          <div id="admin-cal-title" style="font-weight:700;font-size:0.9rem;color:var(--color-slate-800);"></div>
          <button onclick="adminCalNextMonth()" class="btn btn-ghost btn-sm" style="padding:0.25rem 0.6rem;font-size:1rem;">&#8250;</button>
        </div>

        <!-- Header hari -->
        <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;font-size:0.65rem;font-weight:700;color:var(--color-slate-400);margin-bottom:0.3rem;">
          <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
        </div>

        <!-- Grid kalender -->
        <div id="admin-cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;"></div>

        <!-- Legenda -->
        <div style="display:flex;gap:0.8rem;margin-top:0.75rem;font-size:0.7rem;color:var(--color-slate-500);flex-wrap:wrap;">
          <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#16a34a;"></div>Disetujui</div>
          <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;"></div>Pending</div>
          <div style="display:flex;align-items:center;gap:0.3rem;"><div style="width:8px;height:2px;background:#7c3aed;"></div>PIC Task</div>
        </div>

        <!-- Detail tanggal yang dipilih -->
        <div id="admin-cal-detail" style="margin-top:0.75rem;"></div>
      </div>
    </div>
  </div>
  </div>`;
}

// --- REQUEST MANAGEMENT ---
function renderRequestManagement() {
  const initStatus = window._initialReqStatus || 'all';
  window._initialReqStatus = 'all'; // Reset after use

  let baseData = currentReqTab === 'my_tasks' ? picPendingRequests : allRequests;
  let filtered = baseData;
  if (initStatus !== 'all') {
    filtered = filtered.filter(r => r.status === initStatus);
  } else {
    if (currentReqTab === 'all_active') {
      const activeStatuses = ['pending','verified','in-progress','waiting_manager_fmd','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
      filtered = filtered.filter(r => activeStatuses.includes(r.status));
    }
  }

  const html = `
  <div class="page-header">
    <h1>Manajemen Pengajuan</h1>
    <p>Kelola dan proses semua pengajuan yang masuk</p>
  </div>
  
  <div style="display:flex; gap:0.5rem; margin-bottom:1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
    <button class="btn ${currentReqTab === 'my_tasks' ? 'btn-primary' : 'btn-outline'}" onclick="setReqTab('my_tasks')" style="border-radius: 9999px;">
      Tugas Saya (Membutuhkan Tindakan)
    </button>
    <button class="btn ${currentReqTab === 'all_active' ? 'btn-primary' : 'btn-outline'}" onclick="setReqTab('all_active')" style="border-radius: 9999px;">
      Semua Pengajuan Aktif
    </button>
  </div>

  <div class="card">
    <div class="card-header">
      <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
        <div class="search-wrap" style="flex:1;min-width:200px;">
          <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" class="form-input" id="req-search" placeholder="Cari nama, tipe, detail..." oninput="filterRequests()" style="padding-left:2.5rem;" />
        </div>
        <select class="form-select" id="req-type-filter" onchange="filterRequests()" style="width:160px;">
          <option value="all">Semua Tipe</option>
          <option value="Vehicle">Kendaraan</option>
          <option value="Room">Ruangan</option>
          <option value="Zoom">Zoom</option>
          <option value="Repair">Perbaikan</option>
          <option value="Item">Barang</option>
        </select>
        <select class="form-select" id="req-status-filter" onchange="filterRequests()" style="width:160px;">
          <option value="all" ${initStatus === 'all' ? 'selected' : ''}>Semua Status</option>
          <option value="pending" ${initStatus === 'pending' ? 'selected' : ''}>Pending PIC</option>
          <option value="waiting_manager_fmd" ${initStatus === 'waiting_manager_fmd' ? 'selected' : ''}>Waiting Manager FMD</option>
          <option value="verified" ${initStatus === 'verified' ? 'selected' : ''}>Verified (Review Teknisi)</option>
          <option value="in-progress" ${initStatus === 'in-progress' ? 'selected' : ''}>In-Progress</option>
          <option value="approved" ${initStatus === 'approved' ? 'selected' : ''}>Approved</option>
          <option value="ready_for_user" ${initStatus === 'ready_for_user' ? 'selected' : ''}>Ready for User</option>
          <option value="completed" ${initStatus === 'completed' ? 'selected' : ''}>Completed</option>
          <option value="returned" ${initStatus === 'returned' ? 'selected' : ''}>Returned</option>
          <option value="rejected" ${initStatus === 'rejected' ? 'selected' : ''}>Rejected</option>
        </select>
      </div>
    </div>
    <div class="table-wrap table-stack-mobile">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipe</th>
            <th>Pemohon</th>
            <th>Detail</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="req-table-body">
          ${renderRequestRows(filtered, 1)}
        </tbody>
      </table>
    </div>
    <div style="padding:1rem;" id="req-pagination"></div>
  </div>`;

  setTimeout(() => updateReqPagination(filtered), 50);
  return html;
}

function setReqTab(tab) {
  currentReqTab = tab;
  currentPage = 1;
  const searchEl = document.getElementById('req-search');
  if (searchEl) searchEl.value = '';
  const typeEl = document.getElementById('req-type-filter');
  if (typeEl) typeEl.value = 'all';
  const statusEl = document.getElementById('req-status-filter');
  if (statusEl) statusEl.value = 'all';
  renderCurrentView();
}

function filterRequests(preservePage = false) {
  const search = (document.getElementById('req-search')?.value || '').toLowerCase();
  const type   = document.getElementById('req-type-filter')?.value || 'all';
  const status = document.getElementById('req-status-filter')?.value || 'all';
  
  let filtered = currentReqTab === 'my_tasks' ? picPendingRequests : allRequests;
  if (status !== 'all') {
    filtered = filtered.filter(r => r.status === status);
  } else {
    if (currentReqTab === 'all_active') {
      const activeStatuses = ['pending','verified','in-progress','waiting_manager_fmd','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund'];
      filtered = filtered.filter(r => activeStatuses.includes(r.status));
    }
  }
  if (type !== 'all') filtered = filtered.filter(r => r.type === type);
  if (search) filtered = filtered.filter(r =>
    r.applicant_name.toLowerCase().includes(search) ||
    r.type.toLowerCase().includes(search) ||
    r.details.toLowerCase().includes(search)
  );
  if (!preservePage) currentPage = 1;
  document.getElementById('req-table-body').innerHTML = renderRequestRows(filtered, currentPage);
  updateReqPagination(filtered);
}

function renderRequestRows(data, page) {
  const start = (page - 1) * itemsPerPage;
  const paged = data.slice(start, start + itemsPerPage);
  if (paged.length === 0) return `<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Tidak ada data</td></tr>`;

  return paged.map(r => `
    <tr>
      <td data-label="ID" style="font-size:.78rem;color:var(--color-slate-400);">#${r.id}</td>
      <td data-label="Tipe"><span style="font-size:.78rem;font-weight:600;padding:.15rem .5rem;background:var(--color-slate-100);border-radius:var(--radius-sm);">${r.type}</span></td>
      <td data-label="Pemohon">
        <div style="font-weight:600;font-size:.875rem;">${r.applicant_name}</div>
        <div style="font-size:.75rem;color:var(--color-slate-400);">${r.applicant_unit}</div>
      </td>
      <td data-label="Detail" style="font-size:.82rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${r.details}">${r.details}</td>
      <td data-label="Tanggal" style="font-size:.82rem;">${formatDate(r.date_start)}</td>
      <td data-label="Status">${getStatusBadge(r.status)}</td>
      <td data-label="Aksi">
        <button class="btn btn-outline btn-sm" onclick="openDetailView(${r.id}, '${r.type}', 'tinjau')">Tinjau</button>
      </td>
    </tr>
  `).join('');
}

function updateReqPagination(data) {
  const container = document.getElementById('req-pagination');
  if (!container) return;
  const total = data.length;
  const totalPages = Math.ceil(total / itemsPerPage);

  let html = `<div style="display:flex;align-items:center;gap:.5rem;justify-content:space-between;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:1.5rem;">
      <span style="font-size:.78rem;color:var(--color-slate-400);">Menampilkan ${Math.min(itemsPerPage, total)} dari ${total} data</span>
      ${renderRowsSelector('changeReqRows')}
    </div>
    <div class="pagination">`;
  html += `<button class="pag-btn" onclick="goReqPage(${currentPage-1})" ${currentPage===1?'disabled':''}>‹</button>`;
  for(let i=1;i<=totalPages;i++) {
    if(i===1 || i===totalPages || Math.abs(i-currentPage) <= 1) {
      html += `<button class="pag-btn ${i===currentPage?'active':''}" onclick="goReqPage(${i})">${i}</button>`;
    } else if(Math.abs(i-currentPage) === 2) {
      html += `<span style="padding:0 4px;color:#94a3b8">…</span>`;
    }
  }
  html += `<button class="pag-btn" onclick="goReqPage(${currentPage+1})" ${currentPage===totalPages?'disabled':''}>›</button>`;
  html += `</div></div>`;
  container.innerHTML = html;
}

function changeReqRows(val) {
  itemsPerPage = parseInt(val);
  currentPage = 1;
  filterRequests();
}

function goReqPage(page) {
  currentPage = page;
  filterRequests(true);
}

// --- TRACK REPORTS ---
function renderTrackReports() {
  const all = allRequests;
  const html = `
  <div class="page-header">
    <h1>Track Pengajuan</h1>
    <p>Riwayat dan status semua pengajuan</p>
  </div>
  <div class="card">
    <div class="card-header">
      <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
        <div class="search-wrap" style="flex:1;min-width:200px;">
          <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" class="form-input" id="track-search" placeholder="Cari nama pemohon atau tipe..." oninput="filterTrack()" style="padding-left:2.5rem;" />
        </div>
        <select class="form-select" id="track-type" onchange="filterTrack()" style="width:140px;">
          <option value="all">Semua Tipe</option>
          <option value="Vehicle">Kendaraan</option>
          <option value="Room">Ruangan</option>
          <option value="Zoom">Zoom</option>
          <option value="Repair">Perbaikan</option>
          <option value="Item">Barang</option>
        </select>
        <select class="form-select" id="track-status" onchange="filterTrack()" style="width:160px;">
          <option value="all">Semua Status</option>
          <option value="pending">Pending PIC</option>
          <option value="waiting_manager_fmd">Waiting Manager FMD</option>
          <option value="verified">Verified (Review Teknisi)</option>
          <option value="in-progress">In-Progress</option>
          <option value="waiting_ppk">Waiting PPK</option>
          <option value="waiting_bod">Waiting BOD</option>
          <option value="approved_waiting_fund">Approved Waiting Fund</option>
          <option value="approved">Approved</option>
          <option value="ready_for_user">Ready for User</option>
          <option value="completed">Completed</option>
          <option value="returned">Returned</option>
          <option value="rejected">Rejected</option>
          <option value="canceled">Canceled</option>
        </select>
      </div>
    </div>
    <div class="table-wrap table-stack-mobile">
      <table>
        <thead><tr><th>ID</th><th>Tipe</th><th>Pemohon</th><th>Tanggal Buat</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody id="track-table-body">
          ${renderTrackRows(all, 1)}
        </tbody>
      </table>
    </div>
    <div style="padding:1rem;" id="track-pagination"></div>
  </div>`;
  setTimeout(() => updateTrackPagination(all), 50);
  return html;
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

function renderTrackRows(data, page) {
  const start = (page - 1) * itemsPerPage;
  const paged = data.slice(start, start + itemsPerPage);
  if (paged.length === 0) return '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Data tidak ditemukan</td></tr>';

  return paged.map(r => `
    <tr>
      <td data-label="ID" style="font-size:.78rem;color:var(--color-slate-400);">#${r.id}</td>
      <td data-label="Tipe"><span style="font-size:.78rem;font-weight:600;">${r.type}</span></td>
      <td data-label="Pemohon">
        <div style="font-weight:600;font-size:.875rem;">${r.applicant_name}</div>
        <div style="font-size:.75rem;color:var(--color-slate-400);">${r.applicant_unit}</div>
      </td>
      <td data-label="Tanggal Buat" style="font-size:.82rem;">${formatDate(r.created_at, true)}</td>
      <td data-label="Status">${getStatusBadge(r.status)}</td>
      <td data-label="Aksi"><button class="btn btn-ghost btn-sm" onclick="openDetailView(${r.id}, '${r.type}', 'track')">Detail</button></td>
    </tr>`).join('');
}

function getFilteredTrackRequests() {
  const search = (document.getElementById('track-search')?.value || '').toLowerCase();
  const type   = document.getElementById('track-type')?.value || 'all';
  const status = document.getElementById('track-status')?.value || 'all';
  
  let data = allRequests;
  
  if (type !== 'all')   data = data.filter(r => r.type === type);
  if (status !== 'all') data = data.filter(r => r.status === status);
  if (search)         data = data.filter(r => 
    r.applicant_name.toLowerCase().includes(search) || 
    r.type.toLowerCase().includes(search) ||
    (r.details && r.details.toLowerCase().includes(search))
  );
  return data;
}

function filterTrack() {
  const data = getFilteredTrackRequests();
  currentPage = 1;
  document.getElementById('track-table-body').innerHTML = renderTrackRows(data, 1);
  updateTrackPagination(data);
}

function updateTrackPagination(data) {
  const container = document.getElementById('track-pagination');
  if (!container) return;
  const total = data.length;
  const totalPages = Math.ceil(total / itemsPerPage);

  let html = `<div style="display:flex;align-items:center;gap:.5rem;justify-content:space-between;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:1.5rem;">
      <span style="font-size:.78rem;color:var(--color-slate-400);">Menampilkan ${Math.min(itemsPerPage, total)} dari ${total} data</span>
      ${renderRowsSelector('changeTrackRows')}
    </div>
    <div class="pagination">`;
  html += `<button class="pag-btn" onclick="goTrackPage(${currentPage-1})" ${currentPage===1?'disabled':''}>‹</button>`;
  for(let i=1;i<=totalPages;i++) {
    if(i===1 || i===totalPages || Math.abs(i-currentPage) <= 1) {
      html += `<button class="pag-btn ${i===currentPage?'active':''}" onclick="goTrackPage(${i})">${i}</button>`;
    } else if(Math.abs(i-currentPage) === 2) {
      html += `<span style="padding:0 4px;color:#94a3b8">…</span>`;
    }
  }
  html += `<button class="pag-btn" onclick="goTrackPage(${currentPage+1})" ${currentPage===totalPages?'disabled':''}>›</button>`;
  html += `</div></div>`;
  container.innerHTML = html;
}

function changeTrackRows(val) {
  itemsPerPage = parseInt(val);
  currentPage = 1;
  filterTrack();
}

function goTrackPage(page) {
  currentPage = page;
  const data = getFilteredTrackRequests();
  document.getElementById('track-table-body').innerHTML = renderTrackRows(data, page);
  updateTrackPagination(data);
}

// --- USER MANAGEMENT ---
function renderUserManagement() {
  const users = allUsers;
  const html = `
  <div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
      <div><h1>Manajemen User</h1><p>Kelola akun pengguna sistem</p></div>
      <button class="btn btn-primary" onclick="openAddUser()">+ Tambah User</button>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <div class="search-wrap" style="max-width:300px;">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" class="form-input" id="user-search" placeholder="Cari nama atau NIP..." oninput="filterUsers()" style="padding-left:2.5rem;" />
      </div>
    </div>
    <div class="table-wrap table-stack-mobile">
      <table>
        <thead><tr><th>Nama</th><th>NIP/NIK</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr></thead>
        <tbody id="user-table-body">
          ${renderUserRows(users, 1)}
        </tbody>
      </table>
    </div>
    <div style="padding:1rem;" id="user-pagination"></div>
  </div>`;
  setTimeout(() => updateUserPagination(users), 50);
  return html;
}

function renderUserRows(data, page) {
  const start = (page - 1) * itemsPerPage;
  const paged = data.slice(start, start + itemsPerPage);
  if (paged.length === 0) return '<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--color-slate-400);">User tidak ditemukan</td></tr>';

  return paged.map(u => `
    <tr>
      <td data-label="Nama" style="font-weight:600;">${u.full_name}</td>
      <td data-label="NIP/NIK" style="font-family:monospace;font-size:.82rem;">${u.nip_nik || '-'}</td>
      <td data-label="Role"><span class="badge ${u.role === 'admin' ? 'badge-approved' : u.role === 'supervisor' ? 'badge-verified' : 'badge-pending'}">${u.role}</span></td>
      <td data-label="Dibuat" style="font-size:.78rem;color:var(--color-slate-400);">${formatDate(u.created_at)}</td>
      <td data-label="Aksi">
        <div style="display:flex;gap:.4rem;justify-content:flex-end;">
          <button class="btn btn-outline btn-sm" onclick="openEditUser(${u.id})">Edit</button>
          <button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id})">Hapus</button>
        </div>
      </td>
    </tr>`).join('');
}

function filterUsers() {
  const search = (document.getElementById('user-search')?.value || '').toLowerCase();
  let data = allUsers;
  if (search) {
    data = data.filter(u => 
      u.full_name.toLowerCase().includes(search) || 
      u.username.toLowerCase().includes(search)
    );
  }
  currentPage = 1;
  document.getElementById('user-table-body').innerHTML = renderUserRows(data, 1);
  updateUserPagination(data);
}

function updateUserPagination(data) {
  const container = document.getElementById('user-pagination');
  if (!container) return;
  const total = data.length;
  const totalPages = Math.ceil(total / itemsPerPage);

  let html = `<div style="display:flex;align-items:center;gap:.5rem;justify-content:space-between;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:1.5rem;">
      <span style="font-size:.78rem;color:var(--color-slate-400);">Menampilkan ${Math.min(itemsPerPage, total)} dari ${total} user</span>
      ${renderRowsSelector('changeUserRows')}
    </div>
    <div class="pagination">`;
  html += `<button class="pag-btn" onclick="goUserPage(${currentPage-1})" ${currentPage===1?'disabled':''}>‹</button>`;
  for(let i=1;i<=totalPages;i++) {
    if(i===1 || i===totalPages || Math.abs(i-currentPage) <= 1) {
      html += `<button class="pag-btn ${i===currentPage?'active':''}" onclick="goUserPage(${i})">${i}</button>`;
    } else if(Math.abs(i-currentPage) === 2) {
      html += `<span style="padding:0 4px;color:#94a3b8">…</span>`;
    }
  }
  html += `<button class="pag-btn" onclick="goUserPage(${currentPage+1})" ${currentPage===totalPages?'disabled':''}>›</button>`;
  html += `</div></div>`;
  container.innerHTML = html;
}

function changeUserRows(val) {
  itemsPerPage = parseInt(val);
  currentPage = 1;
  filterUsers();
}

function goUserPage(page) {
  currentPage = page;
  const search = (document.getElementById('user-search')?.value || '').toLowerCase();
  let data = allUsers;
  if (search) {
    data = data.filter(u => 
      u.full_name.toLowerCase().includes(search) || 
      u.username.toLowerCase().includes(search)
    );
  }
  document.getElementById('user-table-body').innerHTML = renderUserRows(data, page);
  updateUserPagination(data);
}

// --- ANALYTICS ---
function renderAnalytics() {
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  const currentMonth = document.getElementById('stat-month')?.value || months[new Date().getMonth()];
  const currentYear = document.getElementById('stat-year')?.value || new Date().getFullYear();

  const html = `
  <div class="page-header mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan & Statistik</h1>
    <p class="text-muted">Analisis data pengajuan dan unduh dokumen laporan (.pdf)</p>
  </div>

  <div class="grid-dashboard-top mb-4" style="display: grid; grid-template-columns: 3fr 1fr; gap: 1.5rem;">
    <!-- Content Row -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
      <!-- Total Volume Card -->
      <div class="stat-card border-left-blue shadow h-100" style="padding: 0.75rem 1rem; background: #fff; border-radius: 0.35rem; border-left: 4px solid #4e73df;">
          <div class="card-body" style="padding: 0;">
              <div class="row no-gutters align-items-center" style="display:flex; justify-content:space-between; align-items:center;">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold-stats text-primary-stats text-uppercase mb-1" style="font-size: 0.65rem; color: #4e73df;">Total Volume</div>
                      <div id="kpi-volume" class="font-weight-bold-stats text-gray-800" style="font-size: 1.15rem;">0</div>
                  </div>
                  <div class="col-auto" style="opacity: 0.3;">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4e73df" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  </div>
              </div>
          </div>
      </div>

      <!-- Success Rate Card -->
      <div class="stat-card border-left-emerald shadow h-100" style="padding: 0.75rem 1rem; background: #fff; border-radius: 0.35rem; border-left: 4px solid #1cc88a;">
          <div class="card-body" style="padding: 0;">
              <div class="row no-gutters align-items-center" style="display:flex; justify-content:space-between; align-items:center;">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold-stats text-uppercase mb-1" style="font-size: 0.65rem; color: #1cc88a;">Tingkat Selesai</div>
                      <div id="kpi-success" class="font-weight-bold-stats text-gray-800" style="font-size: 1.15rem;">0%</div>
                  </div>
                  <div class="col-auto" style="opacity: 0.3;">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1cc88a" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                  </div>
              </div>
          </div>
      </div>

      <!-- Failed Rate Card -->
      <div class="stat-card shadow h-100" style="padding: 0.75rem 1rem; background: #fff; border-radius: 0.35rem; border-left: 4px solid #e74a3b;">
          <div class="card-body" style="padding: 0;">
              <div class="row no-gutters align-items-center" style="display:flex; justify-content:space-between; align-items:center;">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold-stats text-uppercase mb-1" style="font-size: 0.65rem; color: #e74a3b;">Batal/Ditolak</div>
                      <div id="kpi-failed" class="font-weight-bold-stats text-gray-800" style="font-size: 1.15rem;">0%</div>
                  </div>
                  <div class="col-auto" style="opacity: 0.3;">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e74a3b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                  </div>
              </div>
          </div>
      </div>

      <!-- Active Users Card -->
      <div class="stat-card shadow h-100" style="padding: 0.75rem 1rem; background: #fff; border-radius: 0.35rem; border-left: 4px solid #8b5cf6;">
          <div class="card-body" style="padding: 0;">
              <div class="row no-gutters align-items-center" style="display:flex; justify-content:space-between; align-items:center;">
                  <div class="col mr-2">
                      <div class="text-xs font-weight-bold-stats text-uppercase mb-1" style="font-size: 0.65rem; color: #8b5cf6;">Pemohon Unik</div>
                      <div id="kpi-users" class="font-weight-bold-stats text-gray-800" style="font-size: 1.15rem;">0</div>
                  </div>
                  <div class="col-auto" style="opacity: 0.3;">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                  </div>
              </div>
          </div>
      </div>
    </div>

    <!-- Filter Row -->
    <div class="card card-shadow h-100">
      <div class="card-header-stats">
          <h6 class="m-0 font-weight-bold-stats text-primary-stats" style="font-size: 0.85rem;">Filter Statistik</h6>
      </div>
      <div class="card-body" style="padding: 0.75rem;">
        <div style="display:flex; flex-direction:column; gap:0.5rem;">
          <div>
            <label class="form-label" style="font-size:0.7rem; margin-bottom:0.2rem;">Tipe Fasilitas</label>
            <select class="form-select" id="stat-type" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; height: auto;">
              <option value="all">Semua Tipe</option>
              <option value="Vehicle">Kendaraan</option>
              <option value="Room">Ruangan</option>
              <option value="Zoom">Zoom</option>
              <option value="Repair">Perbaikan</option>
              <option value="Item">Barang</option>
            </select>
          </div>
          <div>
            <label class="form-label" style="font-size:0.7rem; margin-bottom:0.2rem;">Status</label>
            <select class="form-select" id="stat-status" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; height: auto;">
              <option value="all">Semua Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="completed">Completed</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <div>
              <label class="form-label" style="font-size:0.7rem; margin-bottom:0.2rem;">Bulan</label>
              <select class="form-select" id="stat-month" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; height: auto;">
                <option value="all">Semua</option>
                ${['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'].map(m => `<option value="${m}" ${m === currentMonth ? 'selected' : ''}>${m.substring(0,3)}</option>`).join('')}
              </select>
            </div>
            <div>
              <label class="form-label" style="font-size:0.7rem; margin-bottom:0.2rem;">Tahun</label>
              <select class="form-select" id="stat-year" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; height: auto;">
                ${[2024, 2025, 2026].map(y => `<option value="${y}" ${y == currentYear ? 'selected' : ''}>${y}</option>`).join('')}
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <style>
    @media (max-width: 992px) {
      .grid-dashboard-top { grid-template-columns: 1fr !important; }
      .stats-grid { grid-template-columns: 1fr 1fr !important; }
    }
  </style>

  <style>
    .grid-charts-row1 {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .grid-charts-row2 {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    @media (max-width: 1200px) {
      .grid-charts-row1, .grid-charts-row2 { grid-template-columns: 1fr; }
    }
    .heatmap-table {
      width: 100%; border-collapse: collapse; font-size: 0.75rem; text-align: center;
    }
    .heatmap-table th, .heatmap-table td {
      border: 1px solid #e2e8f0; padding: 4px;
    }
    .heatmap-table th { background: #f8fafc; color: #475569; font-weight: 600; }
  </style>

  <!-- Charts Row 1 -->
  <div class="grid-charts-row1">
    <div class="card card-shadow h-100">
        <div class="card-header-stats py-3">
            <h6 class="m-0 font-weight-bold-stats text-primary-stats">Tren Volume Pengajuan Harian</h6>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="dailyVolumeChart"></canvas>
            </div>
        </div>
    </div>
    <div class="card card-shadow h-100">
        <div class="card-header-stats py-3">
            <h6 class="m-0 font-weight-bold-stats text-primary-stats">Proporsi Tipe Fasilitas</h6>
        </div>
        <div class="card-body" style="display:flex; justify-content:center; align-items:center;">
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="channelChart"></canvas>
            </div>
        </div>
    </div>
    <div class="card card-shadow h-100">
        <div class="card-header-stats py-3">
            <h6 class="m-0 font-weight-bold-stats text-primary-stats">Top 10 Pemohon Teraktif</h6>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="topUsersChart"></canvas>
            </div>
        </div>
    </div>
  </div>

  <!-- Charts Row 2 -->
  <div class="grid-charts-row2">
    <div class="card card-shadow h-100">
        <div class="card-header-stats py-3">
            <h6 class="m-0 font-weight-bold-stats text-primary-stats">Waktu Sibuk Pengajuan (Heatmap)</h6>
        </div>
        <div class="card-body">
            <div id="peakTrafficHeatmap" style="overflow-x:auto;"></div>
        </div>
    </div>
    <div class="card card-shadow h-100">
        <div class="card-header-stats py-3">
            <h6 class="m-0 font-weight-bold-stats text-primary-stats">Top 10 Item/Fasilitas Terpopuler</h6>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="topItemsChart"></canvas>
            </div>
        </div>
    </div>
  </div>

  <!-- Export Section (Dipindah ke bawah) -->
  <div class="card card-shadow mb-4">
    <div class="card-header-stats d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold-stats text-primary-stats">Pusat Unduh Laporan (PDF)</h6>
      <span class="badge badge-pill badge-primary-stats" style="font-size: 0.7rem; padding: 0.3rem 0.8rem;">Sesuai Filter Periode</span>
    </div>
    <div class="card-body">
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem;">
        <div class="export-tile" onclick="exportPDF('Vehicle')">
          <div class="tile-icon icon-vehicle"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v7c0 .6.4 1 1 1h1"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg></div>
          <div class="tile-label">Kendaraan</div>
        </div>
        <div class="export-tile" onclick="exportPDF('Room')">
          <div class="tile-icon icon-room"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
          <div class="tile-label">Ruangan</div>
        </div>
        <div class="export-tile" onclick="exportPDF('Zoom')">
          <div class="tile-icon icon-zoom"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg></div>
          <div class="tile-label">Virtual (Zoom)</div>
        </div>
        <div class="export-tile" onclick="exportPDF('Repair')">
          <div class="tile-icon icon-repair"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
          <div class="tile-label">Perbaikan</div>
        </div>
        <div class="export-tile" onclick="exportPDF('Item')">
          <div class="tile-icon icon-item"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><polyline points="3.29 7l8.71 5 8.71-5"/><line x1="12" y1="22" x2="12" y2="12"/></svg></div>
          <div class="tile-label">Barang</div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .export-tile {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 0.75rem;
      padding: 1.25rem 1rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s;
    }
    .export-tile:hover {
      background: #fff;
      transform: translateY(-3px);
      box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
      border-color: #4e73df;
    }
    .tile-icon {
      width: 42px;
      height: 42px;
      margin: 0 auto 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }
    .tile-label { font-size: 0.8rem; font-weight: 700; color: #475569; }
    
    .icon-vehicle { background: rgba(37,99,235,0.1); color: #2563eb; }
    .icon-room    { background: rgba(5,150,105,0.1); color: #059669; }
    .icon-zoom    { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .icon-repair  { background: rgba(220,38,38,0.1); color: #dc2626; }
    .icon-item    { background: rgba(217,119,6,0.1); color: #d97706; }
  </style>
  `;
  setTimeout(initAnalyticsListeners, 50);
  return html;
}
window.exportPDF = function(subType) {
  const monthName = document.getElementById('stat-month')?.value;
  const yearStr = document.getElementById('stat-year')?.value;
  const selectedStatus = document.getElementById('stat-status')?.value;
  
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  const monthIdx = months.indexOf(monthName);
  const year = parseInt(yearStr);

  // Filter Data
  const filtered = allRequests.filter(r => {
    if (r.type !== subType) return false;
    const dStr = r.created_at || r.date_start;
    if (!dStr) return false;
    const d = new Date(dStr);
    const mMatch = monthName === 'all' || d.getMonth() === monthIdx;
    const yMatch = d.getFullYear() === year;
    const sMatch = selectedStatus === 'all' || r.status === selectedStatus;
    return mMatch && yMatch && sMatch;
  });

  if (filtered.length === 0) {
    Toast.error(`Tidak ada data ${subType} untuk periode ${monthName} ${year}.`);
    return;
  }

  // Generate PDF
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  
  // Header
  doc.setFontSize(22);
  doc.setTextColor(22, 163, 74); // emerald-600
  doc.setFont('inter', 'bold');
  doc.text('SEAMEO BIOTROP', 105, 20, null, 'center');
  
  doc.setFontSize(10);
  doc.setTextColor(100, 116, 139); // slate-500
  doc.setFont('inter', 'normal');
  doc.text('Jl. Raya Tajur Km. 6, Bogor, Jawa Barat, Indonesia', 105, 27, null, 'center');
  
  doc.setDrawColor(226, 232, 240); // slate-200
  doc.line(15, 35, 195, 35);

  // Report Title
  doc.setFontSize(16);
  doc.setTextColor(15, 23, 42); // slate-900
  doc.setFont('inter', 'bold');
  const typeLabels = { 'Vehicle': 'Kendaraan', 'Room': 'Ruangan', 'Repair': 'Maintenance/Perbaikan', 'Item': 'Peminjaman Barang', 'Zoom': 'Virtual (Zoom)' };
  doc.text(`LAPORAN PENGGUNAAN FASILITAS: ${typeLabels[subType] || subType}`, 105, 48, null, 'center');
  
  doc.setFontSize(11);
  doc.setFont('inter', 'normal');
  doc.setTextColor(100, 116, 139);
  doc.text(`Periode: ${monthName} ${year}`, 105, 54, null, 'center');

  // Table
  const tableData = filtered.map((r, i) => [
    i + 1,
    (r.created_at || r.date_start) ? new Date((r.created_at || r.date_start).includes(' ') ? (r.created_at || r.date_start).replace(' ', 'T') : (r.created_at || r.date_start)).toLocaleDateString('id-ID') : '-',
    r.applicant_name,
    r.applicant_unit,
    r.status.toUpperCase(),
    r.details
  ]);

  doc.autoTable({
    startY: 65,
    head: [['No', 'Tanggal', 'Pengusul', 'Unit', 'Status', 'Keterangan/Detail']],
    body: tableData,
    theme: 'grid',
    headStyles: { fillColor: [22, 163, 74], textColor: 255, fontStyle: 'bold' },
    styles: { fontSize: 9, cellPadding: 3 },
    columnStyles: {
      0: { cellWidth: 10 },
      1: { cellWidth: 25 },
      2: { cellWidth: 35 },
      3: { cellWidth: 30 },
      4: { cellWidth: 25 },
      5: { cellWidth: 'auto' }
    }
  });

  // Footer / Signature (Bottom right)
  const finalY = doc.lastAutoTable.finalY + 20;
  doc.setFontSize(10);
  doc.setTextColor(0);
  doc.text('Dicetak oleh sistem pada: ' + new Date().toLocaleString('id-ID'), 15, doc.internal.pageSize.height - 10);
  
  doc.save(`Laporan_${subType}_${monthName}_${year}.pdf`);
  Toast.success('Laporan PDF berhasil diunduh.');
}

let chartInstances = {};

function initAnalyticsListeners() {
  const t = document.getElementById('stat-type');
  const s = document.getElementById('stat-status');
  const m = document.getElementById('stat-month');
  const y = document.getElementById('stat-year');
  if(!m || !y) return;
  
  [t, s, m, y].forEach(el => el?.addEventListener('change', updateAnalyticsDashboard));
  
  // Register Chart.js defaults
  if (typeof Chart !== 'undefined') {
      Chart.defaults.font.family = 'Inter, sans-serif';
      Chart.defaults.color = '#858796';
  }

  updateAnalyticsDashboard(); 
}

function updateAnalyticsDashboard() {
  const type = document.getElementById('stat-type')?.value;
  const status = document.getElementById('stat-status')?.value;
  const monthName = document.getElementById('stat-month')?.value;
  const year = parseInt(document.getElementById('stat-year')?.value);

  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  const monthIdx = months.indexOf(monthName);
  
  let filtered = allRequests.filter(r => {
    const dStr = r.created_at || r.date_start;
    if (!dStr) return false;
    const d = new Date(dStr);
    const mMatch = monthName === 'all' || d.getMonth() === monthIdx;
    return mMatch && d.getFullYear() === year && (type === 'all' || r.type === type) && (status === 'all' || r.status === status);
  });

  // 1. Calculate KPIs
  const totalVolume = filtered.length;
  
  let successCount = 0;
  let failedCount = 0;
  let usersSet = new Set();

  filtered.forEach(r => {
      if (['approved', 'ready_for_user', 'completed'].includes(r.status)) successCount++;
      if (['rejected', 'canceled'].includes(r.status)) failedCount++;
      if (r.applicant_name) usersSet.add(r.applicant_name.toLowerCase().trim());
  });

  const successRate = totalVolume > 0 ? ((successCount / totalVolume) * 100).toFixed(1) : 0;
  const failedRate = totalVolume > 0 ? ((failedCount / totalVolume) * 100).toFixed(1) : 0;
  const activeUsers = usersSet.size;

  document.getElementById('kpi-volume').textContent = totalVolume.toLocaleString('id-ID');
  document.getElementById('kpi-success').textContent = successRate + '%';
  document.getElementById('kpi-failed').textContent = failedRate + '%';
  document.getElementById('kpi-users').textContent = activeUsers.toLocaleString('id-ID');

  if (typeof Chart === 'undefined') return;

  // 2. Prepare Data for Daily Volume Chart
  const dailyCountsByType = { 'Vehicle': {}, 'Room': {}, 'Zoom': {}, 'Repair': {}, 'Item': {} };
  const allDaysSet = new Set();
  
  filtered.forEach(r => {
      const dtStr = r.created_at || r.date_start;
      const safeDtStr = (dtStr && dtStr.includes(' ')) ? dtStr.replace(' ', 'T') : dtStr;
      const d = new Date(safeDtStr);
      const key = `${d.getFullYear()}/${(d.getMonth()+1).toString().padStart(2, '0')}/${d.getDate().toString().padStart(2, '0')}`;
      if (dailyCountsByType[r.type]) {
          dailyCountsByType[r.type][key] = (dailyCountsByType[r.type][key] || 0) + 1;
          allDaysSet.add(key);
      }
  });

  const sortedDays = Array.from(allDaysSet).sort((a,b) => new Date(a) - new Date(b));
  const dailyLabels = sortedDays.map(k => {
      const [y, m, d] = k.split('/');
      return `${d}/${m}`;
  });

  const typeConfig = {
      'Vehicle': { label: 'Kendaraan', color: '#2563eb' },
      'Room': { label: 'Ruangan', color: '#059669' },
      'Zoom': { label: 'Zoom', color: '#8b5cf6' },
      'Repair': { label: 'Perbaikan', color: '#dc2626' },
      'Item': { label: 'Barang', color: '#d97706' }
  };

  const datasets = [];
  if (type === 'all') {
      Object.keys(typeConfig).forEach(t => {
          const data = sortedDays.map(k => dailyCountsByType[t][k] || 0);
          if (data.some(v => v > 0)) {
              datasets.push({
                  label: typeConfig[t].label,
                  data: data,
                  borderColor: typeConfig[t].color,
                  backgroundColor: 'transparent',
                  borderWidth: 2,
                  fill: false,
                  tension: 0.3,
                  pointRadius: 3,
                  pointBackgroundColor: typeConfig[t].color
              });
          }
      });
  } else {
      const data = sortedDays.map(k => dailyCountsByType[type][k] || 0);
      datasets.push({
          label: typeConfig[type]?.label || 'Volume',
          data: data,
          borderColor: '#4e73df',
          backgroundColor: 'rgba(78, 115, 223, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.3,
          pointRadius: 3,
          pointBackgroundColor: '#4e73df'
      });
  }

  // Render Daily Volume Chart
  if (chartInstances.dailyVolume) chartInstances.dailyVolume.destroy();
  const ctxDaily = document.getElementById('dailyVolumeChart')?.getContext('2d');
  if (ctxDaily && dailyLabels.length > 0) {
      chartInstances.dailyVolume = new Chart(ctxDaily, {
          type: 'line',
          data: {
              labels: dailyLabels,
              datasets: datasets
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { 
                  legend: { 
                      display: type === 'all', 
                      position: 'top',
                      labels: { boxWidth: 12, usePointStyle: true, pointStyle: 'circle' }
                  } 
              },
              scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
          }
      });
  } else if (ctxDaily) {
      ctxDaily.clearRect(0, 0, ctxDaily.canvas.width, ctxDaily.canvas.height);
  }

  // 3. Prepare Data for Channel Chart (Proportion by Type)
  const typeCounts = {};
  filtered.forEach(r => { typeCounts[r.type] = (typeCounts[r.type] || 0) + 1; });
  const typeLabels = Object.keys(typeCounts);
  const typeData = Object.values(typeCounts);
  const typeColors = {
      'Vehicle': '#2563eb',
      'Room': '#059669',
      'Zoom': '#8b5cf6',
      'Repair': '#dc2626',
      'Item': '#d97706'
  };

  // Render Channel Chart
  if (chartInstances.channel) chartInstances.channel.destroy();
  const ctxChannel = document.getElementById('channelChart')?.getContext('2d');
  if (ctxChannel && typeLabels.length > 0) {
      chartInstances.channel = new Chart(ctxChannel, {
          type: 'doughnut',
          data: {
              labels: typeLabels.map(l => ({'Vehicle':'Kendaraan','Room':'Ruangan','Zoom':'Zoom','Repair':'Perbaikan','Item':'Barang'}[l] || l)),
              datasets: [{
                  data: typeData,
                  backgroundColor: typeLabels.map(l => typeColors[l] || '#858796'),
                  borderWidth: 1
              }]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { position: 'right' } }
          }
      });
  }

  // 4. Prepare Data for Top Users Chart
  const userCounts = {};
  filtered.forEach(r => {
      if (r.applicant_name) {
          const name = r.applicant_name.trim();
          userCounts[name] = (userCounts[name] || 0) + 1;
      }
  });
  const topUsers = Object.entries(userCounts).sort((a,b) => b[1] - a[1]).slice(0, 10);

  // Render Top Users Chart
  if (chartInstances.topUsers) chartInstances.topUsers.destroy();
  const ctxTopUsers = document.getElementById('topUsersChart')?.getContext('2d');
  if (ctxTopUsers && topUsers.length > 0) {
      chartInstances.topUsers = new Chart(ctxTopUsers, {
          type: 'bar',
          data: {
              labels: topUsers.map(u => u[0].substring(0, 15) + (u[0].length>15?'...':'')),
              datasets: [{
                  label: 'Total Pengajuan',
                  data: topUsers.map(u => u[1]),
                  backgroundColor: '#36b9cc',
                  borderRadius: 4
              }]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              indexAxis: 'y',
              plugins: { legend: { display: false } },
              scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
          }
      });
  }

  // 5. Prepare & Render Peak Traffic Heatmap
  const heatmapContainer = document.getElementById('peakTrafficHeatmap');
  if (heatmapContainer) {
      const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
      const hours = Array.from({length: 12}, (_, i) => i + 7); // 07:00 to 18:00
      
      const heatData = {};
      days.forEach(d => heatData[d] = {});
      
      let maxHeat = 0;
      filtered.forEach(r => {
          const dtStr = r.created_at || r.date_start;
          if (!dtStr) return;
          // Format "YYYY-MM-DD HH:mm:ss" -> "YYYY-MM-DDTHH:mm:ss" untuk memaksa browser membacanya sebagai Waktu Lokal, bukan UTC
          const safeDtStr = dtStr.includes(' ') ? dtStr.replace(' ', 'T') : dtStr;
          const d = new Date(safeDtStr);
          if (isNaN(d)) return;
          const day = days[d.getDay()];
          const hr = d.getHours();
          if (hr >= 7 && hr <= 18) {
              heatData[day][hr] = (heatData[day][hr] || 0) + 1;
              if (heatData[day][hr] > maxHeat) maxHeat = heatData[day][hr];
          }
      });

      if (maxHeat === 0) {
          heatmapContainer.innerHTML = '<div style="text-align:center; padding: 2rem; color: #858796;">Tidak ada data pada jam kerja</div>';
      } else {
          let heatHtml = `<table class="heatmap-table">
            <thead>
              <tr>
                <th>Hari / Jam</th>
                ${hours.map(h => `<th>${h.toString().padStart(2,'0')}:00</th>`).join('')}
              </tr>
            </thead>
            <tbody>`;
          
          const displayDays = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
          displayDays.forEach(day => {
              heatHtml += `<tr><td style="font-weight:bold; background:#f8fafc; color:#475569;">${day}</td>`;
              hours.forEach(hr => {
                  const val = heatData[day][hr] || 0;
                  const intensity = maxHeat > 0 ? val / maxHeat : 0;
                  const color = `rgba(78, 115, 223, ${Math.max(0.05, intensity)})`;
                  const fontColor = intensity > 0.5 ? '#fff' : '#858796';
                  heatHtml += `<td style="background-color:${color}; color:${fontColor}; font-weight: 600;" title="${day} ${hr}:00 - ${val} Pengajuan">${val > 0 ? val : ''}</td>`;
              });
              heatHtml += `</tr>`;
          });
          heatHtml += `</tbody></table>`;
          heatmapContainer.innerHTML = heatHtml;
      }
  }

  // 6. Prepare Data for Top Items Chart
  const itemCounts = {};
  filtered.forEach(r => {
      let itemName = '';
      if (r.type === 'Vehicle' && r.vehicle_id) {
          if (typeof VEHICLE_MAP !== 'undefined' && VEHICLE_MAP[r.vehicle_id]) {
              itemName = VEHICLE_MAP[r.vehicle_id];
          } else {
              return; // Abaikan kendaraan yang belum di-assign atau data lama
          }
      } else if (r.type === 'Room' && r.room_id) {
          itemName = (typeof ROOM_MAP !== 'undefined' && ROOM_MAP[r.room_id]) ? ROOM_MAP[r.room_id] : `Ruang ${r.room_id}`;
      } else if (r.type === 'Zoom' && r.zoom_account_id) {
          itemName = `Akun Zoom ${r.zoom_account_id}`;
      } else if (r.type === 'Item' && r.item_name) {
          itemName = r.item_name.trim();
      }
      
      if (itemName) {
          itemCounts[itemName] = (itemCounts[itemName] || 0) + 1;
      }
  });

  const topItems = Object.entries(itemCounts).sort((a,b) => b[1] - a[1]).slice(0, 10);

  // Render Top Items Chart
  if (chartInstances.topItems) chartInstances.topItems.destroy();
  const ctxTopItems = document.getElementById('topItemsChart')?.getContext('2d');
  if (ctxTopItems) {
      if (topItems.length > 0) {
          chartInstances.topItems = new Chart(ctxTopItems, {
              type: 'bar',
              data: {
                  labels: topItems.map(u => u[0].substring(0, 25) + (u[0].length>25?'...':'')),
                  datasets: [{
                      label: 'Total Penggunaan',
                      data: topItems.map(u => u[1]),
                      backgroundColor: '#1cc88a',
                      borderRadius: 4
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: { legend: { display: false } },
                  scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
              }
          });
      } else {
          // Clear canvas if no data
          ctxTopItems.clearRect(0, 0, ctxTopItems.canvas.width, ctxTopItems.canvas.height);
          ctxTopItems.font = "14px Inter";
          ctxTopItems.fillStyle = "#858796";
          ctxTopItems.textAlign = "center";
          ctxTopItems.fillText("Tidak ada data", ctxTopItems.canvas.width/2, ctxTopItems.canvas.height/2);
      }
  }
}

// --- PROFILE ---
function renderProfile() {
  return `
  <div class="page-header d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
  </div>

  <div class="grid-2" style="gap: 1.5rem;">
    <!-- Profile Card -->
    <div class="card card-shadow mb-4">
      <div class="card-body text-center" style="padding: 2.5rem 1.5rem;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: #4e73df; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; margin: 0 auto 1.5rem; border: 4px solid #f8f9fc; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
          ${ADMIN_NAME.charAt(0).toUpperCase()}
        </div>
        <h5 class="font-weight-bold-stats text-gray-800 mb-1" style="font-size: 1.25rem;">${ADMIN_NAME}</h5>
        <div class="text-xs font-weight-bold-stats text-primary-stats text-uppercase mb-3" style="font-size: 0.75rem;">Administrator System</div>
        <div style="display:flex; justify-content:center; gap:0.5rem; margin-top: 1rem;">
          <button class="btn btn-sm btn-primary shadow-sm" style="font-size: 0.75rem; padding: 0.5rem 1rem;">Edit Profil</button>
        </div>
      </div>
    </div>

    <!-- Details Card -->
    <div class="card card-shadow mb-4">
      <div class="card-header-stats">
        <h6 class="m-0 font-weight-bold-stats text-primary-stats">Informasi Akun</h6>
      </div>
      <div class="card-body">
        <div style="display:grid; gap: 1.25rem;">
          <div>
            <label style="display:block; font-size: 0.7rem; font-weight: 800; color: #b7b9cc; text-transform: uppercase; margin-bottom: 0.25rem;">NIP/NIK (Login Identity)</label>
            <div style="font-weight: 700; color: #5a5c69; font-size: 0.9rem;">${ADMIN_USERNAME}</div>
          </div>
          <div style="height: 1px; background: #eaecf4;"></div>
          <div>
            <label style="display:block; font-size: 0.7rem; font-weight: 800; color: #b7b9cc; text-transform: uppercase; margin-bottom: 0.25rem;">Nama Lengkap</label>
            <div style="font-weight: 700; color: #5a5c69; font-size: 0.9rem;">${ADMIN_NAME}</div>
          </div>
          <div style="height: 1px; background: #eaecf4;"></div>
          <div>
            <label style="display:block; font-size: 0.7rem; font-weight: 800; color: #b7b9cc; text-transform: uppercase; margin-bottom: 0.25rem;">Jabatan</label>
            <div style="font-weight: 700; color: #5a5c69; font-size: 0.9rem;">Administrator Utama</div>
          </div>
        </div>
        <div style="margin-top: 2rem; background: #f8f9fc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e3e6f0;">
          <p style="font-size: 0.75rem; color: #858796; margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            Untuk mengubah password atau data diri, silakan hubungi tim IT SEAMEO BIOTROP.
          </p>
        </div>
      </div>
    </div>
  </div>
  `;
}

// ===== TIMELINE LOGIC =====
function parseLogDate(dateStr) {
  try {
    let clean = dateStr.replace(/[\[\]]/g, '').trim();
    // Fix time separator 14.02 -> 14:02
    clean = clean.replace('.', ':');
    const stdTimestamp = Date.parse(clean);
    if (!isNaN(stdTimestamp)) return new Date(stdTimestamp).toISOString();
    return new Date().toISOString();
  } catch (e) {
    return new Date().toISOString();
  }
}

function getTimelineEvents(req) {
  const events = [];
  // 1. Created Event
  events.push({
    id: 'created',
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
      // Format: [timestamp] [role] - [status]: [message]
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
        else if (status === 'canceled') { title = 'Canceled'; color = '#6b7280'; icon = 'x-circle'; }
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
        // Format: [timestamp] [user]: [message]
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

// ===== NOTIFICATION DROPDOWN =====
function toggleNotifDropdown(e) {
  e.stopPropagation();
  const dropdown = document.getElementById('notif-dropdown');
  dropdown.classList.toggle('open');
}

function renderNotifDropdown() {
  const container = document.getElementById('notif-list');
  if (!container) return;

  if (picPendingRequests.length === 0) {
    container.innerHTML = `<div class="notif-empty">Tidak ada pengajuan baru yang perlu diproses.</div>`;
    return;
  }

  container.innerHTML = picPendingRequests.map(r => {
    // Extract date info (e.g., "29 Jan")
    const safeCreated = (r.created_at && r.created_at.includes(' ')) ? r.created_at.replace(' ', 'T') : r.created_at;
    const d = new Date(safeCreated);
    const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    
    return `
      <div class="notif-item" onclick="openDetailView(${r.id}, '${r.type}', 'tinjau')">
        <div class="notif-item-top">
          <span class="notif-type-badge">${r.type}</span>
          <span class="notif-date">${dateStr}</span>
        </div>
        <div class="notif-title">${r.details}</div>
        <div class="notif-subtitle">Oleh: ${r.applicant_name}</div>
      </div>
    `;
  }).join('');
}

// Close dropdown on click outside
document.addEventListener('click', (e) => {
  const dropdown = document.getElementById('notif-dropdown');
  if (dropdown && dropdown.classList.contains('open')) {
    if (!dropdown.contains(e.target) && !document.getElementById('notification-area').contains(e.target)) {
      dropdown.classList.remove('open');
    }
  }
});

// ===== OPEN DETAIL VIEW =====
let currentDetailReq = null;
let currentDetailMode = 'tinjau';
function openDetailView(id, type, mode = 'tinjau') {
  // Konversi id ke Number untuk menghindari mismatch String vs Number dari JSON
  const numId = Number(id);
  const req = allRequests.find(r => Number(r.id) === numId && r.type === type);
  if (!req) {
    Toast.error(`Request #${id} (${type}) tidak ditemukan. Coba refresh halaman.`);
    console.warn('openDetailView: not found', id, type, allRequests.slice(0,3));
    return;
  }
  currentRequestId   = numId;
  currentRequestNote = req.note || '';
  currentDetailReq   = req;
  currentDetailMode  = mode;

  // Switch ke detail view menggantikan modal
  switchView('detail_pengajuan');

  // Load RAB jika tipe Repair
  if (type === 'Repair') {
    setTimeout(() => loadRABView(numId), 50);
  }
}

function renderDetailPengajuan() {
  // Mode 'tinjau' = dari Manajemen Pengajuan → tampilkan form pemrosesan
  // Mode lainnya (misal 'track') = dari Track Pengajuan → tampilkan reporting detail
  if (currentDetailMode === 'tinjau') {
    return renderDetailPengajuanTinjau();
  }
  return renderDetailPengajuanTrack();
}

function renderDetailPengajuanTinjau() {
  const req = currentDetailReq;
  if (!req) return `<div class="page-header"><h1>Tidak ada data</h1></div>`;

  const isFinal = ['completed','returned','rejected','canceled'].includes(req.status);
  const isActiveWorkflow = ['pending','waiting_manager_fmd','approved','ready_for_user','in-progress'].includes(req.status);

  // ── Jadwal baris ──
  const ds = formatDate(req.date_start);
  const de = req.raw_date_end ? ` s/d ${formatDate(req.raw_date_end)}` : '';
  const ts = req.raw_time_start || '';
  const te = req.raw_time_end   || '';
  const timeStr = (ts || te) ? `${ts}${te ? ' - ' + te : ''}` : '(Seharian)';
  const jadwalVal = `${ds}${de}<br><span style="font-size:.75rem;color:#6b7280;">Jam: ${timeStr}</span>`;

  // ── Category label ──
  const catLabel = { Vehicle:'Kendaraan Dinas', Room:'Ruangan', Zoom:'Zoom Meeting', Repair:'Perbaikan Fasilitas', Item:'Peminjaman Barang' }[req.type] || req.type;

  // ── Bagian assign kendaraan ──
  const vehicleSection = (req.type === 'Vehicle' && req.status === 'pending') ? `
    <div style="background:#fff7ed; border:1px solid #ffedd5; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#9a3412; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 11l1.5-4.5h11L19 11M3 11h18v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5M7 16v-2M17 16v-2"/></svg>
        Pilih Kendaraan (Wajib)
      </div>
      <div class="form-group" style="margin-bottom:1.5rem">
        <select id="assign-vehicle" class="form-select" style="background:#fff;">
          <option value="">Pilih Kendaraan Dinas...</option>
          ${ALL_VEHICLES.map(v => `<option value="${v.id}"${req.vehicle_id === v.id ? ' selected' : ''}>${v.name}</option>`).join('')}
        </select>
      </div>

      <div style="font-weight:700; color:#9a3412; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Set Driver (Wajib)
      </div>
      <div class="form-group" style="margin-bottom:0">
        <select id="assign-driver" class="form-select" style="background:#fff;">
          <option value="">Pilih Driver dari Data Pegawai...</option>
          ${(Array.isArray(allEmployees) ? allEmployees : []).filter(emp => emp.position && (String(emp.position).toLowerCase().includes('driver') || String(emp.position).toLowerCase().includes('pengemudi'))).map(emp => `<option value="${emp.full_name}"${req.driver_name?.includes(emp.full_name) ? ' selected' : ''}>${emp.full_name} - ${emp.position}</option>`).join('')}
        </select>
      </div>
      <div style="font-size:0.75rem; color:#ea580c; font-style:italic; margin-top:1rem;">* Kendaraan dan Driver wajib dipilih sebelum melakukan verifikasi ke Manager.</div>
    </div>` : '';

  // ── Bagian assign Zoom ──
  const zoomSection = (req.type === 'Zoom' && req.status === 'pending') ? `
    <div style="background:#ecfeff; border:1px solid #cffafe; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#155e75; margin-bottom:0.75rem;">Link / Host Key (Optional)</div>
      <div class="form-group" style="margin-bottom:0">
        <input type="text" id="assign-zoom" class="form-input" style="background:#fff; border-color:#a5f3fc;" placeholder="Contoh: https://zoom.us/j/..., Host Key: 123456" />
      </div>
    </div>` : '';

  // ── Bagian assign Item ──
  const itemSection = (req.type === 'Item' && req.status === 'pending') ? `
    <div style="background:#faf5ff; border:1px solid #f3e8ff; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#6b21a8; margin-bottom:0.75rem;">Asset ID / Note (Optional)</div>
      <div class="form-group" style="margin-bottom:0">
        <input type="text" id="assign-item" class="form-input" style="background:#fff; border-color:#d8b4fe;" placeholder="Contoh: PROJ-001, Kondisi Baik" />
      </div>
    </div>` : '';

  // ── Bagian assign Room ──
  const roomSection = (req.type === 'Room' && req.status === 'pending') ? `
    <div style="background:#f0fdf4; border:1px solid #dcfce7; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#166534; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
        Plotting / Ubah Ruangan
      </div>
      <div class="form-group" style="margin-bottom:0">
        <select id="assign-room" class="form-select" style="background:#fff;">
          <option value="">Pilih Ruangan...</option>
          ${ALL_ROOMS.map(r => `<option value="${r.id}"${req.room_id === r.id ? ' selected' : ''}>${r.name}</option>`).join('')}
        </select>
        <div style="font-size:0.75rem; color:#15803d; font-style:italic; margin-top:0.5rem;">* Anda dapat mengubah ruangan yang dipilih user jika diperlukan.</div>
      </div>
    </div>` : '';

  // ── Strict Role-Based Workflow Logic ──
  // NOTE: All users on admin/index.php have CURRENT_ROLE='admin' (from login redirect).
  // Access control MUST be based on ADMIN_USERNAME (which = NIK) or CURRENT_ROLE for specific non-admin roles.
  const PIC_MAP = {
    'Vehicle': ['198605082025211053'],
    'Item':    ['198902222025211044'],
    'Zoom':    ['198902222025211044'],
    'Room':    ['199008092025212052', '198902222025211044'],
    'Repair':  ['198605082025211053', '197212162014091003'] // Alfi, Agus Sujadi
  };
  const SUPER_ADMIN_NIKS = ['000000000000000000'];

  const allowedPICs  = PIC_MAP[req.type] || [];
  // isPIC: matches by NIK for all types including Repair
  const isPIC = allowedPICs.includes(ADMIN_USERNAME);

  const MANAGER_FMD_NIK = '197707072025211067';
  const isManagerFMD = (CURRENT_ROLE === 'managerFMD' || ADMIN_USERNAME === MANAGER_FMD_NIK);
  // isSuperAdmin: specific NIKs only — NOT just any 'admin' role
  const isSuperAdmin = SUPER_ADMIN_NIKS.includes(ADMIN_USERNAME);

  // Helper: reminder box
  function reminderBox(icon, title, msg) {
    return `<div style="background:#f8fafc;border:1px solid #e2e8f0;padding:1.1rem 1.25rem;border-radius:0.6rem;display:flex;gap:0.75rem;align-items:flex-start;">
      <span style="font-size:1.4rem;line-height:1;">${icon}</span>
      <div><div style="font-weight:700;font-size:.875rem;color:#1e293b;margin-bottom:.25rem;">${title}</div><div style="font-size:.82rem;color:#64748b;line-height:1.5;">${msg}</div></div>
    </div>`;
  }

  let actionBtns  = '';
  let picMessage  = '';

  if (isFinal) {
    // No action needed for final states
  } else if (req.status === 'pending') {
    // ── STAGE 1: Pending → PIC Forward to Manager FMD ──
    if (isPIC || isSuperAdmin) {
      if (req.type === 'Vehicle') {
        actionBtns = `<div style="display:flex;flex-direction:column;gap:.5rem;">
          <button class="btn btn-primary btn-full" onclick="doVehicleAssign(${req.id},'waiting_manager_fmd')">✓ Tersedia — Teruskan ke Manager FMD</button>
          <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','rejected')">✕ Tidak Tersedia — Tolak</button>
        </div>`;
      } else if (req.type === 'Room') {
        actionBtns = `<div style="display:flex;flex-direction:column;gap:.5rem;">
          <button class="btn btn-primary btn-full" onclick="doRoomApprove(${req.id},'waiting_manager_fmd')">✓ Tersedia — Plotting & Teruskan ke Manager FMD</button>
          <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','rejected')">✕ Tidak Tersedia — Tolak</button>
        </div>`;
      } else {
        actionBtns = `<div style="display:flex;gap:.5rem;">
          <button class="btn btn-primary" style="flex:1;" onclick="doApproveRequest(${req.id},'${req.type}','waiting_manager_fmd')">✓ Tersedia — Teruskan ke Manager FMD</button>
          <button class="btn btn-danger" style="flex:1;" onclick="updateStatus(${req.id},'${req.type}','rejected')">✕ Tidak Tersedia — Tolak</button>
        </div>`;
      }
    } else {
      picMessage = reminderBox('🔔', 'Menunggu Tindakan PIC',
        `Pengajuan ini perlu diverifikasi ketersediaannya oleh PIC ${req.type}. Jika tersedia, PIC akan meneruskan ke Manager FMD untuk persetujuan.`);
    }

  } else if (req.status === 'waiting_manager_fmd') {
    // ── STAGE 2: Manager FMD Approve / Reject ──
    if (isManagerFMD || isSuperAdmin) {
      actionBtns = `<div style="display:flex;gap:.5rem;">
        <button class="btn btn-success btn-full" onclick="updateStatus(${req.id},'${req.type}','approved')">✓ Setujui (Approved)</button>
        <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','rejected')">✕ Tolak</button>
      </div>`;
    } else if (isPIC) {
      picMessage = reminderBox('⏳', 'Menunggu Keputusan Manager FMD',
        'Pengajuan telah diteruskan dan sedang dalam antrian persetujuan Manager FMD. Anda akan mendapat notifikasi setelah keputusan diberikan.');
    } else {
      picMessage = reminderBox('🔒', 'Akses Terbatas',
        'Tahap ini hanya dapat diproses oleh Manager FMD. Silakan tunggu hasilnya.');
    }

  } else if (req.status === 'approved') {
    // ── STAGE 3: Approved → PIC Check & Recheck ──
    if (isPIC || isSuperAdmin) {
      const checkLabel = req.type === 'Item' ? 'Laporan Check: Barang Siap Diserahkan' : 'Buat Laporan Pengecekan';
      let btnAction = `onclick="updateStatus(${req.id},'${req.type}','ready_for_user')"`;
      if (req.type === 'Room') {
          btnAction = `onclick="openRoomChecklistModal(${req.id})"`;
      }

      actionBtns = `<div style="display:flex;flex-direction:column;gap:.75rem;">
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;padding:.9rem 1rem;border-radius:.5rem;font-size:.82rem;color:#065f46;">
          <strong>✅ Disetujui Manager FMD.</strong><br>Lakukan persiapan & pemeriksaan kebutuhan pengajuan ini. Klik tombol di bawah jika semua sudah siap dan sedang dalam pelaksanaan.
        </div>
        <button class="btn btn-primary btn-full" ${btnAction}>📋 ${checkLabel}</button>
        <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','canceled')">✕ Batalkan Pengajuan</button>
      </div>`;
    } else if (isManagerFMD) {
      picMessage = reminderBox('✅', 'Pengajuan Telah Disetujui',
        'Anda telah menyetujui pengajuan ini. Menunggu PIC melakukan check & recheck persiapan sebelum penyerahan/pelaksanaan.');
    } else {
      picMessage = reminderBox('⏳', 'Menunggu PIC: Check & Recheck',
        'Pengajuan disetujui. PIC sedang menyiapkan dan melakukan pemeriksaan akhir sebelum penyerahan/pelaksanaan.');
    }

  } else if (req.status === 'ready_for_user') {
    // ── STAGE 4: Ready for User → Completed ──
    if (isPIC || isSuperAdmin) {
      const doneLabel = req.type === 'Item' ? 'Konfirmasi Barang Telah Dikembalikan' : 'Konfirmasi Selesai — Permintaan Terpenuhi';
      actionBtns = `<div style="display:flex;flex-direction:column;gap:.75rem;">
        <div style="background:#eff6ff;border:1px solid #bfdbfe;padding:.9rem 1rem;border-radius:.5rem;font-size:.82rem;color:#1e40af;">
          <strong>📋 Status: Ready for User.</strong><br>Pastikan pengajuan telah siap dan selesai digunakan. Klik tombol di bawah untuk menandai bahwa pengajuan telah tuntas (Completed).
        </div>
        <button class="btn btn-success btn-full" onclick="updateStatus(${req.id},'${req.type}','${req.type === 'Item' ? 'returned' : 'completed'}')">
          ✓ ${doneLabel}
        </button>
        <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','canceled')">✕ Batalkan Pengajuan</button>
      </div>`;
    } else {
      picMessage = reminderBox('📋', 'Siap Digunakan (Ready for User)',
        'Pengajuan telah disiapkan oleh PIC dan berstatus Ready for User. PIC akan menyelesaikan permintaan ini setelah penggunaan selesai.');
    }

  } else if (req.type === 'Repair') {
    // ── REPAIR WORKFLOW ──
    if (req.status === 'pending' && (isPIC || isSuperAdmin)) {
      actionBtns = `<div style="display:flex;flex-direction:column;gap:.5rem;">
        <button class="btn btn-primary btn-full" onclick="openRABModal()" style="background:#4f46e5;">📄 Verifikasi & Buat RAB</button>
        <button class="btn btn-primary btn-full" onclick="updateStatus(${req.id},'Repair','in-progress')" style="background:#2563eb;">🔧 Proses Internal (Tanpa RAB)</button>
        <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'Repair','rejected')">✕ Tolak</button>
      </div>`;
    } else if (req.status === 'verified' && (isManagerFMD || isSuperAdmin)) {
      actionBtns = `<div style="display:flex;gap:.5rem;">
        <button class="btn btn-success btn-full" onclick="handleApproveRAB(${req.id})">✓ Approve RAB</button>
        <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'Repair','rejected')">✕ Tolak</button>
      </div>`;
    } else if (req.status === 'waiting_bod' && (CURRENT_ROLE === 'bod' || isSuperAdmin)) {
      actionBtns = `<button class="btn btn-success btn-full" onclick="updateStatus(${req.id},'Repair','waiting_ppk')">✓ Approve (ke PPK)</button>`;
    } else if (req.status === 'waiting_ppk' && (CURRENT_ROLE === 'ppk' || isSuperAdmin)) {
      actionBtns = `<button class="btn btn-success btn-full" onclick="updateStatus(${req.id},'Repair','waiting_manager_fad')">✓ Approve (ke Manager FAD)</button>`;
    } else if (req.status === 'waiting_manager_fad' && (CURRENT_ROLE === 'managerFAD' || isSuperAdmin)) {
      actionBtns = `<button class="btn btn-success btn-full" onclick="updateStatus(${req.id},'Repair','approved_waiting_fund')">✓ Approve (Cairkan Dana)</button>`;
    } else if (req.status === 'approved_waiting_fund' && (CURRENT_ROLE === 'bendahara' || isSuperAdmin)) {
      actionBtns = `<div style="background:#fffbeb;border:1px solid #fef3c7;padding:1rem;border-radius:.5rem;">
        <label class="form-label" style="font-weight:700;color:#92400e;">Pilih Pekerja / Staff:</label>
        <select id="assign-worker" class="form-select" style="margin-bottom:1rem;">
          <option value="">Pilih Pekerja...</option>
          ${allEmployees.map(e => `<option value="${e.full_name}">${e.full_name} (${e.position})</option>`).join('')}
        </select>
        <button class="btn btn-warning btn-full" onclick="doDisburseRepair(${req.id})">💰 Cairkan Dana & Mulai Kerja</button>
      </div>`;
    } else if (req.status === 'in-progress' && (isPIC || isSuperAdmin)) {
      actionBtns = `<button class="btn btn-primary btn-full" onclick="updateStatus(${req.id},'Repair','completed')">✓ Tandai Selesai</button>`;
    } else if (!isFinal) {
      picMessage = reminderBox('⏳', 'Menunggu Proses',
        `Pengajuan sedang dalam tahap: <b>${getStatusLabel(req.status)}</b>. Tidak ada tindakan yang tersedia untuk peran Anda saat ini.`);
    }
  }

  return `
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 1.5rem;">
      <div style="display:flex; align-items:center;">
        <button class="btn btn-outline btn-sm" onclick="switchView(previousView || 'dashboard')" style="margin-right:1rem;">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.2rem; vertical-align: middle;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Kembali
        </button>
        <h1 style="margin:0; font-size: 1.5rem; font-weight: 700; color: #111827;">Detail Pengajuan</h1>
      </div>
      <div style="background:#eff6ff; color:#1e40af; padding:0.25rem 0.75rem; border-radius:0.25rem; font-size:0.875rem; font-weight:500; border: 1px solid #bfdbfe;">
        Anda login sebagai: ${ADMIN_NAME}
      </div>
    </div>

    <div class="grid-2" style="gap:1.5rem; align-items:start;">
      <!-- KARTU KIRI: INFO PENGAJUAN -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">Info Pengajuan #${req.id}</div>
          <div class="card-desc">Informasi lengkap dari database</div>
        </div>
        <div class="card-body">
          <div class="grid-2" style="gap:1rem; font-size:0.875rem;">
            <div>
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Pemohon:</span>
              ${req.applicant_name} <br>
              <span style="color:var(--color-slate-500); font-size:0.8rem;">(${req.applicant_unit})</span>
            </div>
            <div>
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Tipe & Status:</span>
              <span style="display:inline-block; padding:0.15rem 0.5rem; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:0.25rem; font-size:0.75rem; font-weight:600; margin-right:0.5rem;">${req.type}</span>
              ${getStatusBadge(req.status)}
            </div>
            <div style="grid-column:1/3;">
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Item / Lokasi:</span>
              <div style="font-weight:500; font-size:1.1rem; color:var(--color-blue-700);">
                ${req.type === 'Vehicle' ? (ALL_VEHICLES.find(v => v.id === req.vehicle_id)?.name || req.details) : (ROOM_MAP[req.room_id] || req.details)}
                ${req.type === 'Vehicle' && req.driver_name ? ` <span style="font-size:0.85rem;color:#4b5563;">(Driver: ${req.driver_name})</span>` : ''}
              </div>
            </div>
            <div style="grid-column:1/3;">
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Waktu / Tanggal:</span>
              ${jadwalVal}
            </div>
            <div style="grid-column:1/3; background:#f8fafc; padding:0.75rem 1rem; border-radius:0.5rem; border:1px solid #f1f5f9;">
              ${req.type === 'Zoom' ? `
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                  <div><span style="font-weight:600;color:#475569;font-size:0.8rem;display:block;margin-bottom:0.15rem;">Nama Kegiatan:</span><span style="color:#1e293b;line-height:1.5;">${req.request_type || '-'}</span></div>
                  <div><span style="font-weight:600;color:#475569;font-size:0.8rem;display:block;margin-bottom:0.15rem;">Jumlah Peserta:</span><span style="color:#1e293b;">${req.participants || '-'} orang</span></div>
                  <div><span style="font-weight:600;color:#475569;font-size:0.8rem;display:block;margin-bottom:0.15rem;">Permintaan Tambahan:</span><span style="color:#1e293b;">${req.special_needs || '-'}</span></div>
                </div>
              ` : `
                <span style="font-weight:600; display:block; margin-bottom:0.25rem; color:#475569;">Keperluan / Masalah:</span>
                <span style="color:#1e293b; line-height:1.5;">${req.purpose || '-'}</span>
              `}
            </div>
            ${req.note ? `
            <div style="grid-column:1/3; background:#fefce8; padding:0.75rem 1rem; border-radius:0.5rem; border:1px solid #fef08a;">
              <span style="font-weight:600; color:#854d0e; display:block; margin-bottom:0.25rem;">Catatan / Riwayat:</span>
              <div style="white-space:pre-wrap; font-family:monospace; font-size:0.75rem; color:#374151; margin-top:0.25rem;">${req.note}</div>
            </div>` : ''}
          </div>
        </div>
      </div>

      <!-- KARTU KANAN: TINDAKAN PENGELOLA -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">Tindakan Pengelola</div>
          <div class="card-desc">Tindakan yang tersedia untuk peran Anda</div>
        </div>
        <div class="card-body">
          ${isFinal ? `
            <div style="background:#f3f4f6; padding:1.5rem 1rem; text-align:center; border-radius:0.5rem; color:#6b7280; border:1px solid #e5e7eb;">
              Pengajuan ini telah selesai/ditutup.
              <br>
              <span style="font-style:italic; font-size:0.75rem; margin-top:0.5rem; display:inline-block;">Status final: ${getStatusLabel(req.status)}</span>
            </div>
          ` : `
            <div style="display:flex; flex-direction:column; gap:1.25rem;">
              ${actionBtns ? `
              <div>
                <label class="form-label" style="font-weight:600; margin-bottom:0.5rem;">Catatan Proses <span style="font-size:.78rem;font-weight:400;color:#94a3b8;">(opsional)</span></label>
                <textarea id="admin-note" class="form-textarea" placeholder="Tulis catatan atau keterangan tambahan..." style="min-height:80px;"></textarea>
              </div>` : ''}
              
              ${picMessage}
              
              ${vehicleSection}
              ${roomSection}
              ${zoomSection}
              ${itemSection}

              ${actionBtns ? `<div style="display:flex; flex-direction:column; gap:0.5rem;">${actionBtns}</div>` : ''}
            </div>
          `}
        </div>
      </div>
    </div>
  `;
}

function renderDetailPengajuanTrack() {
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
  // ── Jadwal baris ──
  const ds = formatDate(req.date_start);
  const de = req.raw_date_end ? ` s/d ${formatDate(req.raw_date_end)}` : '';
  const ts = req.raw_time_start || '';
  const te = req.raw_time_end   || '';
  const timeStr = (ts || te) ? `${ts}${te ? ' - ' + te : ''}` : '(Seharian)';
  const jadwalVal = `${ds}${de}<div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">${timeStr}</div>`;

  // ── Category label ──
  const catLabel = { Vehicle:'Kendaraan Dinas', Room:'Ruangan', Zoom:'Zoom Meeting', Repair:'Perbaikan Fasilitas', Item:'Peminjaman Barang' }[req.type] || req.type;

  const type_lower = (req.type || '').toLowerCase();
  const detail = type_lower === 'vehicle' ? (ALL_VEHICLES.find(v => String(v.id) === String(req.vehicle_id))?.name || req.details || 'Menunggu Plotting') :
                 type_lower === 'room'    ? (ROOM_MAP[req.room_id] || req.room_id || req.details) :
                 type_lower === 'zoom'    ? (req.zoom_account_id || req.details) :
                 type_lower === 'repair'  ? (req.location_detail || req.details) :
                 type_lower === 'item'    ? (req.item_name || req.details) : (req.details || '-');

  // ── Timeline Section ──
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

  // ── RAB section ──
  const rabSection = req.type === 'Repair'
    ? `<div class="tv-row" id="rab-view-container"><div class="tv-label">RAB</div><div class="tv-value" style="color:#9ca3af;font-size:.82rem;">⏳ Memuat data RAB...</div></div>`
    : '';

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
      ${req.type === 'Zoom' ? `
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
    </div>
    
    <div style="padding: 1rem 1.25rem; background: #f8fafc; border-top: 1px solid #e5e7eb; font-weight: 700; color: #4b5563; font-size: 0.9rem;">
      Riwayat Permohonan
    </div>
    
    ${timelineHtml}
  </div>`;
}

function buildDetailFooter(req) {
  const isFinal = ['completed','returned','rejected','canceled'].includes(req.status);
  if (isFinal) {
    return `<span style="color:var(--color-slate-400);font-size:.875rem;">Pengajuan ini telah selesai/ditutup.</span>
            <button class="btn btn-outline" onclick="switchView(previousView || 'dashboard')">Tutup</button>`;
  }

  let btns = `<button class="btn btn-outline" onclick="switchView(previousView || 'dashboard')">Tutup</button>`;

  if (req.status === 'pending') {
    // Buttons removed as requested to keep Track View clean
  } else if (['approved','in-progress'].includes(req.status)) {
    // Actions only in Tinjau mode
  }

  return btns;
}

async function loadRABView(requestId) {
  const container = document.getElementById('rab-view-container');
  if (!container) return;
  const data  = await api(API_BASE + `requests.php?action=get_repair_budget&request_id=${requestId}`);
  const items = Array.isArray(data) ? data : (data.data || []);

  if (!items.length) {
    container.innerHTML = `
      <div class="tv-label">RAB</div>
      <div class="tv-value" style="font-size:.82rem; color:#94a3b8;">
        Belum ada rincian RAB yang diajukan.
      </div>`;
    return;
  }

  const total = items.reduce((s, i) => s + parseFloat(i.total_price || 0), 0);
  container.innerHTML = `
    <div class="tv-label">Rincian RAB</div>
    <div class="tv-value" style="padding:.5rem .85rem;">
      <div class="rab-table-wrap">
        <table>
          <thead><tr><th>Item</th><th style="text-align:right">Qty</th><th style="text-align:right">Harga</th><th style="text-align:right">Total</th></tr></thead>
          <tbody>
            ${items.map(i => `<tr>
              <td style="font-size:.82rem;">${i.item_name}</td>
              <td style="text-align:right;font-size:.82rem;">${i.quantity}</td>
              <td style="text-align:right;font-size:.82rem;">${formatRupiah(i.unit_price)}</td>
              <td style="text-align:right;font-size:.82rem;font-weight:600;">${formatRupiah(i.total_price)}</td>
            </tr>`).join('')}
            <tr class="rab-total-row">
              <td colspan="3" style="text-align:right;padding-right:1rem;">Total:</td>
              <td style="text-align:right;color:var(--color-blue-600);">${formatRupiah(total)}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>`;
}

// ===== UPDATE STATUS =====
async function updateStatus(id, type, newStatus) {
  const note     = document.getElementById('admin-note')?.value || '';
  const prevNote = currentRequestNote;
  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'update_status', id, type, status: newStatus, note, prev_note: prevNote
  });
  if (res.success) {
    Toast.success(`Status berhasil diubah menjadi: ${newStatus}`);
    // Reload semua data dari server agar database & UI selalu sinkron
    await loadAllData();
    switchView(previousView || 'dashboard');
  } else {
    Toast.error(res.message || 'Gagal update status.');
  }
}

async function handleApproveRAB(id) {
  // We need to know the total amount to decide the next status
  const data = await api(API_BASE + `requests.php?action=get_repair_budget&request_id=${id}`);
  const items = Array.isArray(data) ? data : (data.data || []);
  const total = items.reduce((s, i) => s + parseFloat(i.total_price || 0), 0);

  let nextStatus = 'waiting_manager_fad'; 
  if (total > 50000000) nextStatus = 'waiting_bod';
  else if (total > 20000000) nextStatus = 'waiting_ppk';
  else nextStatus = 'waiting_manager_fad';

  const noteEl = document.getElementById('admin-note');
  if (noteEl) {
    noteEl.value = `RAB Approved. Total: ${formatRupiah(total)}. ` + (noteEl.value || '');
  }
  
  await updateStatus(id, 'Repair', nextStatus);
}

async function doDisburseRepair(id) {
  const worker = document.getElementById('assign-worker')?.value || '';
  if (!worker) {
    Toast.error('Pilih pekerja/staff terlebih dahulu!');
    return;
  }
  const noteEl = document.getElementById('admin-note');
  if (noteEl) {
    noteEl.value = `Dana dicairkan. Pekerja: ${worker}. ` + (noteEl.value || '');
  }
  await updateStatus(id, 'Repair', 'in-progress');
}

async function doApproveRequest(id, type, targetStatus = 'approved') {
  let noteAppend = '';
  if (type === 'Zoom') {
    const info = document.getElementById('assign-zoom')?.value;
    if (info) noteAppend = `Zoom Info: ${info}. `;
  } else if (type === 'Item') {
    const info = document.getElementById('assign-item')?.value;
    if (info) noteAppend = `Asset Info: ${info}. `;
  }
  
  const noteEl    = document.getElementById('admin-note');
  const rawNote   = noteEl?.value || '';
  
  let finalNote = noteAppend ? (noteAppend + rawNote) : rawNote;
  
  if (targetStatus === 'waiting_manager_fmd' && !rawNote.trim()) {
    let detailVal = type;
    if (type === 'Zoom') {
      detailVal = currentDetailReq.zoom_account_id || 'Akun Zoom';
    } else if (type === 'Item') {
      detailVal = currentDetailReq.item_name || 'Barang';
    }
    const defaultMsg = `${detailVal} tersedia, diteruskan kepada Manager FMD untuk approval permohonan`;
    finalNote = noteAppend ? (noteAppend + defaultMsg) : defaultMsg;
  }

  if (noteEl) noteEl.value = finalNote;
  await updateStatus(id, type, targetStatus);
}

async function doVehicleAssign(id, targetStatus = 'approved') {
  const vehicleId  = document.getElementById('assign-vehicle')?.value || '';
  const driverName = document.getElementById('assign-driver')?.value  || '';
  const noteEl     = document.getElementById('admin-note');
  const rawNote    = noteEl?.value || '';

  if (!vehicleId || !driverName) {
    Toast.error('Pilih kendaraan dinas dan driver!');
    return;
  }

  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'update_vehicle_assignment', id, vehicle_id: vehicleId, driver_name: driverName
  });
  if (!res.success) { Toast.error(res.message); return; }

  const vName = ALL_VEHICLES.find(v => v.id === vehicleId)?.name || vehicleId;
  if (noteEl) {
    if (targetStatus === 'waiting_manager_fmd' && !rawNote.trim()) {
      noteEl.value = `${vName} tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: ${driverName}`;
    } else {
      noteEl.value = `Vehicle: ${vName}. Driver: ${driverName}. ${rawNote}`;
    }
  }
  await updateStatus(id, 'Vehicle', targetStatus);
}

async function doRoomApprove(id, targetStatus = 'approved') {
  const roomId = document.getElementById('assign-room')?.value || '';
  const noteEl = document.getElementById('admin-note');
  const rawNote = noteEl?.value || '';

  if (!roomId) {
    Toast.error('Pilih ruangan yang akan ditetapkan!');
    return;
  }

  // Update room assignment first
  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'update_room_assignment', id, room_id: roomId
  });
  if (!res.success) { Toast.error(res.message); return; }

  const rName = ROOM_MAP[roomId] || roomId;
  if (noteEl) {
    if (targetStatus === 'waiting_manager_fmd' && !rawNote.trim()) {
      noteEl.value = `${rName} tersedia, diteruskan kepada Manager FMD untuk approval permohonan`;
    } else {
      noteEl.value = `Ruangan: ${rName}. ${rawNote}`;
    }
  }
  await updateStatus(id, 'Room', targetStatus);
}

async function approveRABtoSupervisor(id) {
  const res = await apiPost(API_BASE + 'requests.php', { action: 'approve_repair_budget', request_id: id });
  if (res.success) {
    Toast.success(res.message);
    switchView(previousView || 'dashboard');
    await loadAllData();
  } else {
    Toast.error(res.message);
  }
}

// ===== RAB MODAL =====
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
  document.getElementById('rab-item-name').value = '';
  document.getElementById('rab-item-qty').value  = '1';
  document.getElementById('rab-item-price').value = '0';
  renderRABTable();
}

function removeRabItem(id) {
  rabItems = rabItems.filter(i => i.id !== id);
  renderRABTable();
}

function renderRABTable() {
  const tbody = document.getElementById('rab-table-body');
  if (!tbody) return;
  if (!rabItems.length) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--color-slate-400);padding:1.5rem;">Belum ada item</td></tr>`;
    document.getElementById('rab-total').textContent = 'Rp 0';
    return;
  }
  let total = 0;
  tbody.innerHTML = rabItems.map(i => {
    const t = i.quantity * i.unitPrice;
    total += t;
    return `<tr>
      <td>${i.itemName}</td>
      <td style="text-align:right;">${i.quantity}</td>
      <td style="text-align:right;">${formatRupiah(i.unitPrice)}</td>
      <td style="text-align:right;font-weight:600;">${formatRupiah(t)}</td>
      <td><button class="btn btn-danger btn-sm" onclick="removeRabItem(${i.id})">✕</button></td>
    </tr>`;
  }).join('');
  document.getElementById('rab-total').textContent = formatRupiah(total);
}

async function submitRAB() {
  if (!rabItems.length) { Toast.error('Minimal isi 1 item RAB.'); return; }
  if (!currentRequestId) { Toast.error('Tidak ada request terpilih.'); return; }

  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'submit_repair_budget',
    request_id: currentRequestId,
    items: JSON.stringify(rabItems)
  });
  if (res.success) {
    Toast.success('RAB berhasil diajukan!');
    Modal.close('modal-rab');
    switchView(previousView || 'dashboard');
    await loadAllData();
  } else {
    Toast.error(res.message);
  }
}

// ===== USER MANAGEMENT =====
window.openAddUser = function() {
  const title = document.getElementById('modal-user-title');
  const id    = document.getElementById('edit-user-id');
  const form  = document.getElementById('user-form');
  const hint  = document.getElementById('pw-hint');
  const empSelect = document.getElementById('user-employee-id');
  
  if(title) title.textContent = 'Tambah User';
  if(id) id.value = '';
  if(form) form.reset();
  if(hint) hint.textContent = '(wajib isi)';

  // Populate Employees
  if (empSelect) {
    empSelect.innerHTML = '<option value="">-- Pilih Karyawan --</option>';
    // Only show employees that don't have an account (except in Edit mode)
    const existingEmpIds = allUsers.map(u => String(u.employee_id));
    allEmployees.forEach(e => {
        if (!existingEmpIds.includes(String(e.id))) {
            empSelect.innerHTML += `<option value="${e.id}">${e.full_name} (${e.nip_nik})</option>`;
        }
    });
    empSelect.disabled = false;
  }

  Modal.open('modal-user');
};

window.onEmployeeSelect = function(el) {
    const empId = el.value;
    const emp = allEmployees.find(e => String(e.id) === String(empId));
    if (emp) {
        document.getElementById('user-fullname').value = emp.full_name;
    }
};

window.openEditUser = function(id) {
  const user = allUsers.find(u => Number(u.id) === Number(id));
  if (!user) {
    Toast.error('Data user tidak ditemukan');
    return;
  }
  
  const title = document.getElementById('modal-user-title');
  const idEl  = document.getElementById('edit-user-id');
  const name  = document.getElementById('user-fullname');
  const empSelect = document.getElementById('user-employee-id');
  const role  = document.getElementById('user-role');
  const pw    = document.getElementById('user-password');
  const hint  = document.getElementById('pw-hint');

  if(title) title.textContent = 'Edit User';
  if(idEl)  idEl.value = user.id;
  if(name)  name.value = user.full_name;
  if(role)  role.value = user.role;
  if(pw)    pw.value = '';
  if(hint)  hint.textContent = '(kosongkan jika tidak diubah)';

  if (empSelect) {
      empSelect.innerHTML = `<option value="${user.employee_id}">${user.full_name} (${user.nip_nik || '???'})</option>`;
      empSelect.value = user.employee_id;
      empSelect.disabled = true; // Cannot change employee for existing account
  }
  
  Modal.open('modal-user');
};

window.submitUserForm = async function() {
  const id       = document.getElementById('edit-user-id')?.value;
  const full_name= document.getElementById('user-fullname')?.value;
  const emp_id   = document.getElementById('user-employee-id')?.value;
  const role     = document.getElementById('user-role')?.value;
  const password = document.getElementById('user-password')?.value;

  if(!full_name || !emp_id || !role) {
    Toast.error('Mohon lengkapi data wajib.');
    return;
  }

  const action = id ? 'update' : 'add';
  const res = await apiPost(API_BASE + 'users.php', { action, id, full_name, employee_id: emp_id, role, password });
  if (res.success) {
    Toast.success(res.message);
    Modal.close('modal-user');
    await loadAllData();
  } else {
    Toast.error(res.message);
  }
};

window.deleteUser = async function(id) {
  if (!await confirmAction('Yakin ingin menghapus user ini secara permanen?')) return;
  const res = await apiPost(API_BASE + 'users.php', { action: 'delete', id });
  if (res.success) {
    Toast.success(res.message);
    await loadAllData();
  } else {
    Toast.error(res.message);
  }
};

// ===== ADMIN CALENDAR FUNCTIONS =====
window.renderAdminCalendar = function() {
  renderAdminCalendarGrid();
};

window.adminCalPrevMonth = function() {
  window._adminCalMonth--;
  if (window._adminCalMonth < 0) { window._adminCalMonth = 11; window._adminCalYear--; }
  renderAdminCalendarGrid();
  window._adminCalSelected = null;
  const det = document.getElementById('admin-cal-detail');
  if (det) det.innerHTML = '';
};

window.adminCalNextMonth = function() {
  window._adminCalMonth++;
  if (window._adminCalMonth > 11) { window._adminCalMonth = 0; window._adminCalYear++; }
  renderAdminCalendarGrid();
  window._adminCalSelected = null;
  const det = document.getElementById('admin-cal-detail');
  if (det) det.innerHTML = '';
};

window.renderAdminCalendarGrid = function() {
  const yr   = window._adminCalYear;
  const mo   = window._adminCalMonth;
  const grid  = document.getElementById('admin-cal-grid');
  const title = document.getElementById('admin-cal-title');
  if (!grid || !title) return;

  const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  title.textContent = `${BULAN[mo]} ${yr}`;

  const occupiedStatuses = ['approved','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','completed','returned'];
  const pendingStatuses  = ['pending'];
  const activeStatuses   = [...occupiedStatuses, ...pendingStatuses];

  const dayMap = {};
  allRequests.filter(r => activeStatuses.includes(r.status)).forEach(r => {
    if (!r.date_start) return;
    const start = new Date(r.date_start);
    const end   = r.raw_date_end ? new Date(r.raw_date_end) : new Date(r.date_start);
    
    // Check if current user is PIC
    const isPIC = (PIC_MAP[r.type] || []).includes(ADMIN_USERNAME);

    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
      if (d.getMonth() !== mo || d.getFullYear() !== yr) continue;
      const key = `${yr}-${String(mo+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
      if (!dayMap[key]) dayMap[key] = { approved:[], pending:[], picCount: 0 };
      if (occupiedStatuses.includes(r.status)) dayMap[key].approved.push(r);
      else if (pendingStatuses.includes(r.status)) dayMap[key].pending.push(r);
      
      if (isPIC) dayMap[key].picCount++;
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
    const isSelected = dateStr === window._adminCalSelected;

    let dots = '';
    let picHighlight = '';
    if (ev) {
      if (ev.approved.length) dots += `<div style="width:4px;height:4px;border-radius:50%;background:#16a34a;display:inline-block;margin:0 1px;"></div>`;
      if (ev.pending.length)  dots += `<div style="width:4px;height:4px;border-radius:50%;background:#f59e0b;display:inline-block;margin:0 1px;"></div>`;
      if (ev.picCount > 0)    picHighlight = `<div style="position:absolute; bottom:2px; left:20%; right:20%; height:2px; background:#7c3aed; border-radius:1px;"></div>`;
    }

    const bgColor   = isSelected ? 'var(--color-emerald-600)' : isToday ? '#ecfdf5' : ev ? 'var(--color-slate-50)' : 'transparent';
    const textColor = isSelected ? '#fff'    : isToday ? 'var(--color-emerald-700)' : 'var(--color-slate-700)';
    const border    = isToday && !isSelected ? '1.5px solid var(--color-emerald-500)' : isSelected ? 'none' : '1px solid transparent';

    html += `
      <div onclick="showAdminDayDetail('${dateStr}')" style="
        padding:0.25rem 0; text-align:center; cursor:pointer; position:relative;
        border-radius:0.4rem; background:${bgColor}; border:${border}; transition:all .12s;
        min-height: 38px;
      " onmouseover="if('${dateStr}'!==window._adminCalSelected)this.style.background='var(--color-slate-100)'"
         onmouseout="if('${dateStr}'!==window._adminCalSelected)this.style.background='${ev ? 'var(--color-slate-50)' : 'transparent'}'">
        <div style="font-size:0.75rem;font-weight:${isToday||isSelected?'700':'500'};color:${textColor};line-height:1.2;">${d}</div>
        <div style="display:flex;justify-content:center;align-items:center;min-height:6px;gap:1px;margin-top:2px;">${dots}</div>
        ${picHighlight}
      </div>`;
  }

  grid.innerHTML = html;
  if (window._adminCalSelected) showAdminDayDetail(window._adminCalSelected, false);
};

window.showAdminDayDetail = function(dateStr, updateGrid = true) {
  window._adminCalSelected = dateStr;
  if (updateGrid) renderAdminCalendarGrid();

  const det = document.getElementById('admin-cal-detail');
  if (!det) return;

  const occupiedStatuses = ['approved','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','completed','returned'];
  const activeStatuses   = [...occupiedStatuses, 'pending'];

  // Requests on this date
  const reqs = allRequests.filter(r => {
    if (!activeStatuses.includes(r.status) || !r.date_start) return false;
    const start = r.date_start;
    const end   = r.raw_date_end || r.date_start;
    return dateStr >= start && dateStr <= end;
  });

  const [yr, mo, dy] = dateStr.split('-');
  const BULAN    = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const dayObj   = new Date(dateStr);
  const dayLabel = `${dayNames[dayObj.getDay()]}, ${parseInt(dy)} ${BULAN[parseInt(mo)-1]} ${yr}`;

  let bookedHtml = '';
  reqs.sort((a,b) => (a.raw_time_start||'').localeCompare(b.raw_time_start||'')).forEach(r => {
    const isPIC = (PIC_MAP[r.type] || []).includes(ADMIN_USERNAME);
    const sColor = r.status === 'pending' ? '#f59e0b' : '#16a34a';
    const tStr   = (r.raw_time_start||'00:00').substring(0,5);
    const label  = r.type.substring(0,3).toUpperCase();
    
    bookedHtml += `
      <div style="display:flex;align-items:center;gap:0.45rem;padding:0.4rem 0;border-bottom:1px solid var(--color-slate-100);cursor:pointer;" onclick="openDetailView(${r.id}, '${r.type}', 'tinjau')">
        <div style="width:32px; height:18px; font-size:0.6rem; font-weight:800; display:flex; align-items:center; justify-content:center; background:${sColor}20; color:${sColor}; border-radius:3px; flex-shrink:0;">${label}</div>
        <div style="flex:1; min-width:0;">
          <div style="font-size:0.75rem; font-weight:${isPIC?'700':'500'}; color:${isPIC?'#7c3aed':'var(--color-slate-700)'}; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            ${r.applicant_name}
          </div>
          <div style="font-size:0.65rem; color:var(--color-slate-500);">${tStr} &bull; ${r.details}</div>
        </div>
        ${isPIC ? `<div style="width:6px; height:6px; border-radius:50%; background:#7c3aed;" title="PIC Task"></div>` : ''}
      </div>`;
  });
  
  if (!bookedHtml) bookedHtml = `<div style="font-size:0.75rem;color:var(--color-slate-400);padding:0.5rem 0;">Tidak ada aktivitas.</div>`;

  det.innerHTML = `
    <div style="background:#fff; border:1px solid var(--color-slate-200); border-radius:0.5rem; overflow:hidden; box-shadow: var(--shadow-sm);">
      <div style="padding:0.5rem 0.75rem; background:var(--color-slate-50); border-bottom:1px solid var(--color-slate-200); display:flex; justify-content:space-between; align-items:center;">
        <div style="font-size:0.75rem; font-weight:700; color:var(--color-slate-700);">${dayLabel}</div>
        <div style="font-size:0.65rem; font-weight:600; color:var(--color-slate-500);">${reqs.length} Req</div>
      </div>
      <div style="padding:0.25rem 0.75rem 0.5rem;">
        ${bookedHtml}
      </div>
    </div>`;
};

// ===== INIT =====
document.addEventListener('DOMContentLoaded', () => {
  Modal.init();
  loadAllData().then(() => switchView('dashboard'));
  
  // Auto Update: Refresh data every 20 seconds
  setInterval(async () => {
    // Only reload if we are not in a detail view to avoid disrupting user input
    if (currentView !== 'detail_pengajuan') {
      await loadAllData(true);
    }
  }, 20000);
});
// ===== CHECKLIST RUANGAN =====
window.openRoomChecklistModal = function(id) {
    currentRequestId = id;
    const form = document.getElementById('form-checklist-ruangan');
    if (form) form.reset();
    Modal.open('modal-checklist-ruangan');
};

window.submitRoomChecklist = async function() {
    const statusVal = document.getElementById('cr-status').value;
    if (!statusVal) {
        Toast.error("Pilih Status Kesiapan terlebih dahulu.");
        return;
    }
    const now = new Date();
    const waktu = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    
    const getChecked = (name) => Array.from(document.querySelectorAll(`input[name="${name}"]:checked`)).map(e => e.value);
    
    let noteText = `[LAPORAN PENGECHECKAN RUANGAN]\n`;
    noteText += `Waktu Pengecekan: ${waktu || '-'}\n`;
    noteText += `Status: ${statusVal}\n`;
    
    noteText += `\nKebersihan: ${getChecked('cr-kebersihan').join(', ') || '-'}`;
    noteText += `\nFasilitas: ${getChecked('cr-fasilitas').join(', ') || '-'}`;
    noteText += `\nListrik: ${getChecked('cr-listrik').join(', ') || '-'}`;
    noteText += `\nPerlengkapan: ${getChecked('cr-perlengkapan').join(', ') || '-'}`;
    noteText += `\nPengaturan: ${getChecked('cr-pengaturan').join(', ') || '-'}`;
    noteText += `\nVideo: ${getChecked('cr-video').join(', ') || '-'}`;
    
    const catatan = document.getElementById('cr-catatan').value;
    if (catatan) noteText += `\n\nCatatan Tambahan: ${catatan}`;
    
    const fileInput = document.getElementById('cr-foto');
    if (fileInput && fileInput.files.length > 5) {
        Toast.error("Maksimal 5 foto.");
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', currentRequestId);
    formData.append('type', 'Room');
    formData.append('status', 'ready_for_user');
    formData.append('note', noteText);
    formData.append('prev_note', currentRequestNote || '');
    
    if (fileInput && fileInput.files.length > 0) {
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('foto_ruangan[]', fileInput.files[i]);
        }
    }

    try {
        const res = await fetch(API_BASE + 'requests.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            Toast.success('Checklist berhasil disimpan. Menunggu penyelesaian.');
            Modal.close('modal-checklist-ruangan');
            switchView(previousView || 'dashboard');
            await loadAllData();
        } else {
            Toast.error(data.message || 'Gagal menyimpan checklist');
        }
    } catch (e) {
        Toast.error("Gagal terhubung ke server");
    }
};
</script>

<!-- Modal Checklist Ruangan -->
<div class="modal-overlay" id="modal-checklist-ruangan">
  <div class="modal" style="max-width: 650px;">
    <div class="modal-header">
      <h3 class="modal-title">Laporan Pengecheckan Ruangan</h3>
      <button class="modal-close-btn">&times;</button>
    </div>
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
      <form id="form-checklist-ruangan" onsubmit="event.preventDefault(); submitRoomChecklist();">
        <div style="font-weight:700; color:#1e293b; margin-top:1rem; margin-bottom:0.5rem; border-bottom:1px solid #e2e8f0; padding-bottom:0.25rem;">Kebersihan dan Kerapihan</div>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-kebersihan" value="Lantai bersih"> Lantai bersih</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-kebersihan" value="Meja bersih dan rapi"> Meja bersih dan rapi</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-kebersihan" value="Kursi tertata rapi"> Kursi tertata rapi</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-kebersihan" value="Tempat sampah kosong"> Tempat sampah kosong</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-kebersihan" value="Tidak ada bau tidak sedap"> Tidak ada bau tidak sedap</label>
        
        <div style="font-weight:700; color:#1e293b; margin-top:1rem; margin-bottom:0.5rem; border-bottom:1px solid #e2e8f0; padding-bottom:0.25rem;">Fasilitas Utama</div>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-fasilitas" value="Proyektor berfungsi"> Proyektor berfungsi</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-fasilitas" value="Layar proyektor siap"> Layar proyektor siap</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-fasilitas" value="TV/Monitor menyala dengan baik"> TV/Monitor menyala dengan baik</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-fasilitas" value="Sound system berfungsi"> Sound system berfungsi</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-fasilitas" value="Microphone tersedia dan berfungsi"> Microphone tersedia dan berfungsi</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-fasilitas" value="Kabel dan konektor lengkap"> Kabel dan konektor lengkap</label>
        
        <div style="font-weight:700; color:#1e293b; margin-top:1rem; margin-bottom:0.5rem; border-bottom:1px solid #e2e8f0; padding-bottom:0.25rem;">Kelistrikan dan Koneksi</div>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-listrik" value="Stop kontak berfungsi"> Stop kontak berfungsi</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-listrik" value="Lampu menyala dengan baik"> Lampu menyala dengan baik</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-listrik" value="AC berfungsi"> AC berfungsi</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-listrik" value="WiFi tersedia dan stabil"> WiFi tersedia dan stabil</label>

        <div style="font-weight:700; color:#1e293b; margin-top:1rem; margin-bottom:0.5rem; border-bottom:1px solid #e2e8f0; padding-bottom:0.25rem;">Perlengkapan Meeting</div>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-perlengkapan" value="Whiteboard tersedia"> Whiteboard tersedia</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-perlengkapan" value="Spidol dan penghapus tersedia"> Spidol dan penghapus tersedia</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-perlengkapan" value="Air minum tersedia (jika diperlukan)"> Air minum tersedia (jika diperlukan)</label>
        
        <div style="font-weight:700; color:#1e293b; margin-top:1rem; margin-bottom:0.5rem; border-bottom:1px solid #e2e8f0; padding-bottom:0.25rem;">Pengaturan Ruangan</div>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-pengaturan" value="Layout sesuai permintaan"> Layout sesuai permintaan (teater/u-shape/classroom)</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-pengaturan" value="Jumlah kursi sesuai kebutuhan"> Jumlah kursi sesuai kebutuhan</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-pengaturan" value="Pencahayaan sesuai"> Pencahayaan sesuai</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-pengaturan" value="Suhu ruang nyaman"> Suhu ruang nyaman</label>

        <div style="font-weight:700; color:#1e293b; margin-top:1rem; margin-bottom:0.5rem; border-bottom:1px solid #e2e8f0; padding-bottom:0.25rem;">Video Pembukaan Kegiatan</div>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-video" value="File Lagu Indonesia Raya tersedia"> File Lagu Indonesia Raya tersedia</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-video" value="File Lagu SEAMEO Colours tersedia"> File Lagu SEAMEO Colours tersedia</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-video" value="Format file audio sesuai standar"> Format file audio sesuai standar</label>
        <label style="display:flex; gap:.5rem; margin-bottom:.5rem;"><input type="checkbox" name="cr-video" value="Lokasi penyimpanan file sudah benar"> Lokasi penyimpanan file sudah benar</label>

        <div class="form-group" style="margin-top:1rem;">
          <label class="form-label" style="font-weight:700;">Catatan Tambahan (kendala atau hal yang perlu diperbaiki)</label>
          <textarea id="cr-catatan" class="form-textarea" rows="3"></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Status Kesiapan</label>
          <select id="cr-status" class="form-select" required>
            <option value="">Pilih Status...</option>
            <option value="Siap digunakan">Siap digunakan</option>
            <option value="Perlu perbaikan">Perlu perbaikan</option>
            <option value="Belum siap">Belum siap</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700;">Foto Ruangan (Maks 5 file, maks 100MB)</label>
          <input type="file" id="cr-foto" class="form-input" accept="image/*" multiple>
        </div>
      </form>
    </div>
    <div class="modal-footer" style="margin-top:0;">
      <button class="btn btn-outline" onclick="Modal.close('modal-checklist-ruangan')">Batal</button>
      <button class="btn btn-primary" onclick="submitRoomChecklist()">Siap - Lanjutkan</button>
    </div>
  </div>
</div>

</body>
</html>
