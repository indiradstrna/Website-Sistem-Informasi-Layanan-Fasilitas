<?php
// ============================================================
// admin/index.php — Dashboard Admin
// Setara dengan: app/admin/page.tsx
// ============================================================

require_once __DIR__ . '/../includes/auth.php';
requireRole('superadmin', 'super admin');
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
      <input type="hidden" id="rab-jenis" value="Tidak memungkinkan dikerjakan sendiri (pihak ketiga/vendor)">
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
      <button class="btn btn-primary" onclick="submitRAB()">Diteruskan ke Manager FMD</button>
    </div>
  </div>
</div>

<!-- ===== MODAL: FORM GUDANG ===== -->
<div class="modal-overlay" id="modal-gudang">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h3 class="modal-title">Form Pengerjaan Sendiri</h3>
      <button class="modal-close modal-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="form-group" style="margin-bottom:1rem;">
        <label class="form-label">Jenis Pengerjaan</label>
        <select id="gudang-jenis" class="form-select" onchange="toggleGudangItems(this.value)">
          <option value="Sparepart tersedia di gudang">Sparepart tersedia di gudang</option>
          <option value="Tidak perlu sparepart (jasa)">Tidak perlu sparepart (jasa)</option>
          <option value="Perlu mengajukan pembelian sparepart (dikerjakan sendiri)">Perlu mengajukan pembelian sparepart (dikerjakan sendiri)</option>
        </select>
      </div>
      <div id="gudang-item-inputs">
        <div class="grid-3" style="margin-bottom:1rem;">
          <div class="form-group" style="grid-column:1/3; position:relative;">
            <label class="form-label">Nama Barang</label>
            <input type="text" id="gudang-item-name" class="form-input" placeholder="Cari nama atau kode barang..." autocomplete="off" />
            <input type="hidden" id="gudang-item-id" />
            <input type="hidden" id="gudang-item-stock" />
            <div id="gudang-item-dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; background:white; border:1px solid var(--border); border-radius:4px; max-height:200px; overflow-y:auto; z-index:1000; box-shadow:0 4px 6px rgba(0,0,0,0.1);"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Jumlah</label>
            <input type="number" id="gudang-item-qty" class="form-input" value="1" min="1" />
          </div>
          <div style="grid-column:1/4; display:flex; justify-content:flex-end;">
            <button class="btn btn-primary" onclick="addGudangItem()">+ Tambah</button>
          </div>
        </div>
        <div class="rab-table-wrap" id="gudang-item-table">
          <table>
            <thead><tr><th>Barang</th><th style="text-align:right">Jumlah</th><th></th></tr></thead>
            <tbody id="gudang-table-body"><tr><td colspan="3" style="text-align:center;color:var(--color-slate-400);padding:1.5rem;">Belum ada barang</td></tr></tbody>
          </table>
        </div>
      </div>
      
      <!-- INTERNAL RAB FORM -->
      <div id="gudang-rab-inputs" style="display:none;">
        <div class="grid-3" style="margin-bottom:1rem;">
          <div class="form-group" style="grid-column:1/3;">
            <label class="form-label">Nama Item</label>
            <input type="text" id="gr-item-name" class="form-input" placeholder="Contoh: Cat Tembok" />
          </div>
          <div class="form-group">
            <label class="form-label">Qty</label>
            <input type="number" id="gr-item-qty" class="form-input" value="1" min="1" />
          </div>
          <div class="form-group">
            <label class="form-label">Harga Satuan (Rp)</label>
            <input type="number" id="gr-item-price" class="form-input" value="0" min="0" />
          </div>
          <div style="display:flex;align-items:flex-end;">
            <button class="btn btn-primary btn-full" onclick="addGrItem()">+ Tambah</button>
          </div>
        </div>
        <div class="rab-table-wrap">
          <table>
            <thead><tr><th>Item</th><th style="text-align:right">Qty</th><th style="text-align:right">Harga</th><th style="text-align:right">Total</th><th></th></tr></thead>
            <tbody id="gr-table-body"><tr><td colspan="5" style="text-align:center;color:var(--color-slate-400);padding:1.5rem;">Belum ada item</td></tr></tbody>
          </table>
        </div>
        <div style="margin-top:1rem;text-align:right;font-weight:700;font-size:1rem;">
          Total RAB: <span id="gr-total" style="color:var(--color-blue-600);">Rp 0</span>
        </div>
      </div>
      <div class="form-group" style="margin-top:1rem;">
        <label class="form-label">Catatan Pengerjaan / Proses Internal</label>
        <textarea id="gudang-note" class="form-input" rows="2" placeholder="Catatan tambahan..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline modal-close-btn">Batal</button>
      <button class="btn btn-primary" onclick="submitGudang()">Diteruskan ke Manager FMD</button>
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
let systemSettings = {};
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
  'Item2':   ['198902222025211044'],
  'Zoom':    ['198902222025211044'], // Indra Septian
  'Room':    ['199008092025212052', '198902222025211044', '16268300055'], // Lastiah, Indra, Dhany
  'Dormitory':['199008092025212052', '198902222025211044', '16268300055'],
  'Repair':  ['198605082025211053', '197212162014091003'] // Alfi, Agus Sujadi
};

// Admin Calendar State
window._adminCalYear     = new Date().getFullYear();
window._adminCalMonth    = new Date().getMonth();
window._adminCalSelected = null;

// ===== LOAD ALL DATA =====
async function loadAllData(silent = false) {
  try {
    const [vehicles, rooms, dormitories, zooms, repairs, items, items2, users, employees, settingsRaw] = await Promise.all([
      api(API_BASE + 'requests.php?action=get_vehicle').catch(e => []),
      api(API_BASE + 'requests.php?action=get_room').catch(e => []),
      api(API_BASE + 'requests.php?action=get_dormitory').catch(e => []),
      api(API_BASE + 'requests.php?action=get_zoom').catch(e => []),
      api(API_BASE + 'requests.php?action=get_repair').catch(e => []),
      api(API_BASE + 'requests.php?action=get_item').catch(e => []),
      api(API_BASE + 'requests.php?action=get_item2').catch(e => []),
      api(API_BASE + 'users.php?action=get_all').catch(e => []),
      api(API_BASE + 'users.php?action=get_employees').catch(e => []),
      api(API_BASE + 'settings.php?action=get_all').catch(e => ({data:{}}))
    ]);

    systemSettings = (settingsRaw && settingsRaw.data) ? settingsRaw.data : {};

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
               type === 'Dormitory'? `${item.dormitory_id} (${item.occupant_name||''} - ${item.participants||0} org)` :
               type === 'Zoom'    ? item.zoom_account_id :
               type === 'Repair'  ? `${item.location_detail}: ${item.issue_description}` :
               type === 'Item'    ? `${item.item_name} (${item.item_quantity})` :
               type === 'Item2'   ? `Permintaan Barang (${item.items_json ? (() => { try{ return JSON.parse(item.items_json).length; } catch(e){ return 0; } })() : 0} jenis)` : '-',
      date_start: type === 'Item' ? item.loan_date : type === 'Repair' ? item.incident_date : type === 'Item2' ? (item.created_at ? item.created_at.split(' ')[0] : '') : item.date_start,
      raw_time_start: type === 'Item2' ? (item.created_at ? item.created_at.split(' ')[1].substring(0,5) : '') : (item.time_start || item.incident_time || ''),
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
      dormitory_id:    item.dormitory_id   || '',
      occupant_name:   item.occupant_name  || '',
      location_detail: item.location_detail || '',
      item_name:       item.item_name      || '',
      passenger_name:  item.passenger_name || '',
      departure:       item.departure      || '',
      destination:     item.destination    || '',
      cost_bearer:     item.cost_bearer    || '',
      items_json:      item.items_json     || '',
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
      ...norm(dormitories|| [], 'Dormitory'),
      ...norm(zooms    || [], 'Zoom'),
      ...norm(repairs  || [], 'Repair'),
      ...norm(items    || [], 'Item'),
      ...norm(items2   || [], 'Item2'),
    ].sort((a,b) => new Date(b.created_at) - new Date(a.created_at));

    allUsers = Array.isArray(users) ? users : [];
    allEmployees = Array.isArray(employees) ? employees : [];

    const isManagerFMD = (CURRENT_ROLE === 'managerFMD' || ADMIN_USERNAME === '197707072025211067');
    
    const picPending = allRequests.filter(r => {
      if (['pending', 'approved', 'ready_for_user', 'in-progress'].includes(r.status)) {
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
        if (currentView === 'request_management' && !(document.getElementById('req-search') || {}).value) {
            const status = (document.getElementById('req-status-filter') || {}).value || 'all';
            const type   = (document.getElementById('req-type-filter') || {}).value   || 'all';
            if (status === 'all' && type === 'all') {
                filterRequests(true); // true to preserve current page
            }
        } else if (currentView === 'track_reports' && !(document.getElementById('track-search') || {}).value) {
            const type = (document.getElementById('track-type') || {}).value || 'all';
            const status = (document.getElementById('track-status') || {}).value || 'all';
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
    profile:'Profil',
    master_data: 'Master Data',
    system_settings: 'Pengaturan Sistem',
    crud_requests: 'Kelola Data Pengajuan'
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
    case 'master_data':        container.innerHTML = renderMasterData();         break;
    case 'system_settings':    container.innerHTML = renderSystemSettings();     break;
    case 'analytics':          container.innerHTML = renderAnalytics();          break;
    case 'profile':            container.innerHTML = renderProfile();            break;
    case 'detail_pengajuan':   container.innerHTML = renderDetailPengajuan();    break;
    case 'crud_requests':      container.innerHTML = renderCrudRequests();       break;
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
          <option value="Dormitory">Dormitory</option>
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
  const search = ((document.getElementById('req-search') || {}).value || '').toLowerCase();
  const type   = (document.getElementById('req-type-filter') || {}).value || 'all';
  const status = (document.getElementById('req-status-filter') || {}).value || 'all';
  
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
          <option value="Dormitory">Dormitory</option>
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
          <option value="canceled">Declined / Canceled</option>
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
  const search = ((document.getElementById('track-search') || {}).value || '').toLowerCase();
  const type   = (document.getElementById('track-type') || {}).value || 'all';
  const status = (document.getElementById('track-status') || {}).value || 'all';
  
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
  const total = users.length;
  const adminCount = users.filter(u => u.role === 'admin' || u.role === 'super admin' || u.role === 'superadmin').length;
  const fmdCount = users.filter(u => u.role === 'supervisor').length;
  const userCount = users.filter(u => u.role === 'user').length;
  
  const html = `
  <div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
      <div><h1>Manajemen User</h1><p>Kelola akun pengguna dan hak akses sistem</p></div>
      <button class="btn btn-primary" onclick="openAddUser()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.4rem"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah User
      </button>
    </div>
  </div>

  <div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card border-left-blue">
      <div class="stat-label">Total Users</div>
      <div class="stat-value" style="color:var(--color-blue-600);">${total}</div>
      <div class="stat-sub">Seluruh pengguna terdaftar</div>
    </div>
    <div class="stat-card border-left-emerald">
      <div class="stat-label">Administrator</div>
      <div class="stat-value" style="color:var(--color-emerald-600);">${adminCount}</div>
      <div class="stat-sub">Akses pengelola sistem</div>
    </div>
    <div class="stat-card border-left-amber">
      <div class="stat-label">Supervisor FMD</div>
      <div class="stat-value" style="color:var(--color-amber-600);">${fmdCount}</div>
      <div class="stat-sub">Penyetuju permintaan</div>
    </div>
    <div class="stat-card border-left-slate">
      <div class="stat-label">Staff / User</div>
      <div class="stat-value" style="color:var(--color-slate-600);">${userCount}</div>
      <div class="stat-sub">Pengguna layanan fasilitas</div>
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
      <td data-label="Nama">
        <div style="display:flex;align-items:center;gap:0.8rem;">
            <div class="user-avatar-sm" style="width:32px;height:32px;background:#e2e8f0;color:#334155;display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:700;font-size:0.85rem;">
                ${(u.full_name || u.username || 'U').charAt(0).toUpperCase()}
            </div>
            <div>
                <div style="font-weight:700;color:var(--color-slate-800);">${u.full_name || u.username || 'Tanpa Nama'}</div>
                <div style="font-size:0.75rem;color:var(--color-slate-400);">@${u.username}</div>
            </div>
        </div>
      </td>
      <td data-label="NIP/NIK" style="font-family:monospace;font-size:.82rem;">${u.nip_nik || '-'}</td>
      <td data-label="Role"><span class="badge ${u.role === 'admin' || u.role === 'super admin' || u.role === 'superadmin' ? 'badge-approved' : u.role === 'supervisor' ? 'badge-verified' : 'badge-pending'}">${u.role}</span></td>
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
  const search = ((document.getElementById('user-search') || {}).value || '').toLowerCase();
  let data = allUsers;
  if (search) {
    data = data.filter(u => 
      (u.full_name || '').toLowerCase().includes(search) || 
      (u.nip_nik || '').toLowerCase().includes(search) ||
      (u.role || '').toLowerCase().includes(search)
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
  const search = ((document.getElementById('user-search') || {}).value || '').toLowerCase();
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

// --- CRUD REQUESTS ---
function renderCrudRequests() {
  const html = `
  <div class="page-header mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Data Pengajuan</h1>
    <p class="text-muted">Edit, ubah status, atau hapus pengajuan secara langsung.</p>
  </div>
  <div class="card card-shadow">
    <div class="card-header-stats py-3" style="display:flex; justify-content:space-between; align-items:center;">
      <h6 class="m-0 font-weight-bold-stats text-primary-stats">Daftar Seluruh Pengajuan</h6>
      <div style="display:flex; gap:0.5rem;">
        <input type="text" id="crud-search" class="form-input form-input-sm" placeholder="Cari nama, departemen, dsb..." oninput="filterCrud()" style="width:250px;">
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table-custom">
          <thead>
            <tr>
              <th>ID / Tipe</th>
              <th>Pemohon</th>
              <th>Tujuan / Keperluan</th>
              <th>Status</th>
              <th style="width:120px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody id="crud-table-body">
            <!-- Rendered by JS -->
          </tbody>
        </table>
      </div>
      <div id="crud-pagination" class="pagination" style="margin-top:1rem;"></div>
    </div>
  </div>
  `;
  setTimeout(() => filterCrud(), 50);
  return html;
}

function filterCrud() {
  const search = ((document.getElementById('crud-search') || {}).value || '').toLowerCase();
  let data = allRequests;
  if (search) {
    data = data.filter(r => 
      (r.applicant_name && r.applicant_name.toLowerCase().includes(search)) ||
      (r.applicant_unit && r.applicant_unit.toLowerCase().includes(search)) ||
      (r.purpose && r.purpose.toLowerCase().includes(search)) ||
      (r.type && r.type.toLowerCase().includes(search))
    );
  }
  document.getElementById('crud-table-body').innerHTML = renderCrudRows(data, currentPage);
  updateCrudPagination(data);
}

function renderCrudRows(data, page) {
  const start = (page - 1) * itemsPerPage;
  const paginated = data.slice(start, start + itemsPerPage);
  if (!paginated.length) return '<tr><td colspan="5" style="text-align:center;padding:2rem;">Tidak ada data ditemukan</td></tr>';

  return paginated.map(r => {
    let statusBadge = '';
    const st = r.status || '';
    if (st === 'pending') statusBadge = '<span class="badge" style="background:#fef3c7;color:#d97706;">Pending</span>';
    else if (st === 'approved') statusBadge = '<span class="badge" style="background:#dcfce7;color:#166534;">Approved</span>';
    else if (st === 'rejected' || st === 'cancelled') statusBadge = '<span class="badge" style="background:#fee2e2;color:#b91c1c;">'+st+'</span>';
    else statusBadge = '<span class="badge" style="background:#e0e7ff;color:#3730a3;">'+st+'</span>';

    return `<tr>
      <td><b>#${r.id}</b><br><span style="font-size:0.75rem;color:var(--color-slate-400);">${r.type}</span></td>
      <td>${r.applicant_name}<br><span style="font-size:0.75rem;color:var(--color-slate-400);">${r.applicant_unit}</span></td>
      <td>${r.purpose || '-'}</td>
      <td>${statusBadge}</td>
      <td style="text-align:center;">
        <div style="display:flex; flex-direction:column; gap:0.25rem; align-items:center; width:80px; margin:0 auto;">
          <button class="btn btn-sm btn-outline" style="width:100%; padding:0.25rem;" onclick="openCrudEdit(${r.id}, '${r.type}')">Edit</button>
          <button class="btn btn-sm btn-danger" style="width:100%; padding:0.25rem;" onclick="doCrudDelete(${r.id}, '${r.type}')">Hapus</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function updateCrudPagination(data) {
  const container = document.getElementById('crud-pagination');
  if (!container) return;
  const totalPages = Math.ceil(data.length / itemsPerPage);
  let html = '';
  for (let i = 1; i <= totalPages; i++) {
    html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goCrudPage(${i})">${i}</button>`;
  }
  container.innerHTML = html;
}

function goCrudPage(page) {
  currentPage = page;
  filterCrud();
}

function openCrudEdit(id, type) {
  const req = allRequests.find(r => r.id == id && r.type === type);
  if (!req) return;
  
  document.getElementById('crud-id').value = req.id;
  document.getElementById('crud-type').value = req.type;
  document.getElementById('crud-applicant_name').value = req.applicant_name || '';
  document.getElementById('crud-applicant_unit').value = req.applicant_unit || '';
  document.getElementById('crud-purpose').value = req.purpose || '';
  document.getElementById('crud-status').value = req.status || '';
  document.getElementById('crud-note').value = req.note || '';
  
  document.getElementById('crud-date_start').value = req.date_start || '';
  document.getElementById('crud-date_end').value = req.raw_date_end || '';
  document.getElementById('crud-time_start').value = req.raw_time_start ? req.raw_time_start.substring(0,5) : '';
  document.getElementById('crud-time_end').value = req.raw_time_end ? req.raw_time_end.substring(0,5) : '';

  // Type-specific fields
  document.getElementById('crud-type-specific').innerHTML = renderCrudSpecificFields(req);
  
  Modal.open('modal-crud-edit');
}

function renderCrudSpecificFields(req) {
  let html = '';
  if (req.type === 'Vehicle') {
    html += `
      <div class="form-group"><label class="form-label">Tujuan (Destination)</label><input type="text" id="crud-sp-destination" class="form-input" value="${req.destination || ''}"></div>
      <div class="form-group"><label class="form-label">Vehicle ID</label><input type="text" id="crud-sp-vehicle_id" class="form-input" value="${req.vehicle_id || ''}"></div>
      <div class="form-group"><label class="form-label">Driver Name</label><input type="text" id="crud-sp-driver_name" class="form-input" value="${req.driver_name || ''}"></div>
    `;
  } else if (req.type === 'Room') {
    html += `
      <div class="form-group"><label class="form-label">Room ID</label><input type="text" id="crud-sp-room_id" class="form-input" value="${req.room_id || ''}"></div>
      <div class="form-group"><label class="form-label">Peserta</label><input type="number" id="crud-sp-participants" class="form-input" value="${req.participants || ''}"></div>
      <div class="form-group"><label class="form-label">Kebutuhan Khusus</label><input type="text" id="crud-sp-special_needs" class="form-input" value="${req.special_needs || ''}"></div>
    `;
  } else if (req.type === 'Dormitory') {
    html += `
      <div class="form-group"><label class="form-label">Dormitory ID</label><input type="text" id="crud-sp-dormitory_id" class="form-input" value="${req.dormitory_id || ''}"></div>
      <div class="form-group"><label class="form-label">Nama Penghuni</label><input type="text" id="crud-sp-occupant_name" class="form-input" value="${req.occupant_name || ''}"></div>
      <div class="form-group"><label class="form-label">Peserta</label><input type="number" id="crud-sp-participants" class="form-input" value="${req.participants || ''}"></div>
    `;
  } else if (req.type === 'Zoom') {
    html += `
      <div class="form-group"><label class="form-label">Zoom Account ID</label><input type="text" id="crud-sp-zoom_account_id" class="form-input" value="${req.zoom_account_id || ''}"></div>
      <div class="form-group"><label class="form-label">Peserta</label><input type="number" id="crud-sp-participants" class="form-input" value="${req.participants || ''}"></div>
    `;
  } else if (req.type === 'Repair') {
    html += `
      <div class="form-group"><label class="form-label">Detail Lokasi</label><input type="text" id="crud-sp-location_detail" class="form-input" value="${req.location_detail || ''}"></div>
      <div class="form-group"><label class="form-label">Deskripsi Masalah</label><textarea id="crud-sp-issue_description" class="form-input" rows="2">${req.issue_description || ''}</textarea></div>
    `;
  } else if (req.type === 'Item') {
    html += `
      <div class="form-group"><label class="form-label">Nama Barang</label><input type="text" id="crud-sp-item_name" class="form-input" value="${req.item_name || ''}"></div>
      <div class="form-group"><label class="form-label">Jumlah Barang</label><input type="number" id="crud-sp-item_quantity" class="form-input" value="${req.item_quantity || ''}"></div>
    `;
  }
  return html;
}

async function doCrudSave() {
  const id = document.getElementById('crud-id').value;
  const type = document.getElementById('crud-type').value;
  
  const payload = {
    action: 'superadmin_update_request',
    id: id,
    type: type,
    applicant_name: document.getElementById('crud-applicant_name').value,
    applicant_unit: document.getElementById('crud-applicant_unit').value,
    purpose: document.getElementById('crud-purpose').value,
    status: document.getElementById('crud-status').value,
    note: document.getElementById('crud-note').value,
    date_start: document.getElementById('crud-date_start').value,
    date_end: document.getElementById('crud-date_end').value,
    time_start: document.getElementById('crud-time_start').value,
    time_end: document.getElementById('crud-time_end').value
  };

  // Collect specific fields
  if (type === 'Vehicle') {
    payload.destination = document.getElementById('crud-sp-destination').value;
    payload.vehicle_id = document.getElementById('crud-sp-vehicle_id').value || null;
    payload.driver_name = document.getElementById('crud-sp-driver_name').value;
  } else if (type === 'Room') {
    payload.room_id = document.getElementById('crud-sp-room_id').value || null;
    payload.participants = document.getElementById('crud-sp-participants').value || 0;
    payload.special_needs = document.getElementById('crud-sp-special_needs').value;
  } else if (type === 'Dormitory') {
    payload.dormitory_id = document.getElementById('crud-sp-dormitory_id').value || null;
    payload.occupant_name = document.getElementById('crud-sp-occupant_name').value;
    payload.participants = document.getElementById('crud-sp-participants').value || 0;
  } else if (type === 'Zoom') {
    payload.zoom_account_id = document.getElementById('crud-sp-zoom_account_id').value || null;
    payload.participants = document.getElementById('crud-sp-participants').value || 0;
  } else if (type === 'Repair') {
    payload.location_detail = document.getElementById('crud-sp-location_detail').value;
    payload.issue_description = document.getElementById('crud-sp-issue_description').value;
  } else if (type === 'Item') {
    payload.item_name = document.getElementById('crud-sp-item_name').value;
    payload.item_quantity = document.getElementById('crud-sp-item_quantity').value || 0;
  }

  const res = await apiPost(API_BASE + 'requests.php', payload);
  if (res.success) {
    Toast.success('Data pengajuan berhasil diperbarui!');
    Modal.close('modal-crud-edit');
    await loadAllData();
    filterCrud();
  } else {
    Toast.error(res.message);
  }
}

async function doCrudDelete(id, type) {
  if (!confirm('Apakah Anda yakin ingin HAPUS PERMANEN pengajuan ini? Data yang dihapus tidak bisa dikembalikan!')) return;
  
  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'superadmin_delete_request',
    id: id,
    type: type
  });
  
  if (res.success) {
    Toast.success('Pengajuan berhasil dihapus!');
    await loadAllData();
    filterCrud();
  } else {
    Toast.error(res.message);
  }
}

// --- ANALYTICS ---
function renderAnalytics() {
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  const currentMonth = (document.getElementById('stat-month') || {}).value || months[new Date().getMonth()];
  const currentYear = (document.getElementById('stat-year') || {}).value || new Date().getFullYear();

  const html = `
  <div class="page-header mb-4" style="display:flex; justify-content:space-between; align-items:flex-end;">
    <div>
      <h1 class="h3 mb-0 text-gray-800">Laporan & Statistik</h1>
      <p class="text-muted">Analisis data pengajuan dan unduh dokumen laporan (.csv)</p>
    </div>
    <button class="btn btn-primary" onclick="exportExcel()" style="display:flex; align-items:center;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export Excel
    </button>
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
          <option value="Dormitory">Dormitory</option>
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
        <div class="export-tile" onclick="exportPDF('Dormitory')">
          <div class="tile-icon icon-room"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
          <div class="tile-label">Dormitory</div>
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
  const monthName = (document.getElementById('stat-month') || {}).value;
  const yearStr = (document.getElementById('stat-year') || {}).value;
  const selectedStatus = (document.getElementById('stat-status') || {}).value;
  
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
  const typeLabels = { 'Vehicle': 'Kendaraan', 'Room': 'Ruangan', 'Dormitory': 'Dormitory', 'Repair': 'Maintenance/Perbaikan', 'Item': 'Peminjaman Barang', 'Zoom': 'Virtual (Zoom)' };
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
  
  [t, s, m, y].forEach(el => (el && el.addEventListener)('change', updateAnalyticsDashboard));
  
  // Register Chart.js defaults
  if (typeof Chart !== 'undefined') {
      Chart.defaults.font.family = 'Inter, sans-serif';
      Chart.defaults.color = '#858796';
  }

  updateAnalyticsDashboard(); 
}

function updateAnalyticsDashboard() {
  const type = (document.getElementById('stat-type') || {}).value;
  const status = (document.getElementById('stat-status') || {}).value;
  const monthName = (document.getElementById('stat-month') || {}).value;
  const year = parseInt((document.getElementById('stat-year') || {}).value);

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
  const dailyCountsByType = { 'Vehicle': {}, 'Room': {}, 'Dormitory': {}, 'Zoom': {}, 'Repair': {}, 'Item': {} };
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
          label: (typeConfig[type] || {}).label || 'Volume',
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
  const ctxDailyEl = document.getElementById('dailyVolumeChart'); const ctxDaily = ctxDailyEl ? ctxDailyEl.getContext('2d') : null;
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
  const ctxChannelEl = document.getElementById('channelChart'); const ctxChannel = ctxChannelEl ? ctxChannelEl.getContext('2d') : null;
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
  const ctxTopUsersEl = document.getElementById('topUsersChart'); const ctxTopUsers = ctxTopUsersEl ? ctxTopUsersEl.getContext('2d') : null;
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
  const ctxTopItemsEl = document.getElementById('topItemsChart'); const ctxTopItems = ctxTopItemsEl ? ctxTopItemsEl.getContext('2d') : null;
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
        else if (status === 'canceled') { title = 'Declined / Canceled'; color = '#6b7280'; icon = 'x-circle'; }
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

function checkResourceConflict(currentReq, resourceType, resourceId) {
  if (!resourceId) return false;
  if (resourceType === 'driver' && resourceId === 'TANPA_SUPIR') return false;

  const activeStatuses = ['approved', 'ready_for_user', 'in-progress', 'verified', 'waiting_manager_fad', 'waiting_ppk', 'waiting_bod', 'approved_waiting_fund'];
  
  const curStartStr = currentReq.date_start + 'T' + ((currentReq.raw_time_start || '').substring(0, 5) || '00:00') + ':00';
  const curEndStr = (currentReq.raw_date_end || currentReq.date_start) + 'T' + ((currentReq.raw_time_end || '').substring(0, 5) || '23:59') + ':00';
  
  const curStart = new Date(curStartStr).getTime();
  const curEnd = new Date(curEndStr).getTime();

  for (const r of allRequests) {
    if (r.id === currentReq.id && r.type === currentReq.type) continue;
    if (!activeStatuses.includes(r.status)) continue;
    
    let match = false;
    if (resourceType === 'Vehicle' && r.type === 'Vehicle' && String(r.vehicle_id) === String(resourceId)) match = true;
    if (resourceType === 'driver' && r.type === 'Vehicle' && String(r.driver_name) === String(resourceId)) match = true;
    if (resourceType === 'Room' && r.type === 'Room' && String(r.room_id) === String(resourceId)) match = true;
    if (resourceType === 'Dormitory' && r.type === 'Dormitory' && String(r.dormitory_id) === String(resourceId)) match = true;
    
    if (match) {
      const rStartStr = r.date_start + 'T' + ((r.raw_time_start || '').substring(0, 5) || '00:00') + ':00';
      const rEndStr = (r.raw_date_end || r.date_start) + 'T' + ((r.raw_time_end || '').substring(0, 5) || '23:59') + ':00';
      
      const rStart = new Date(rStartStr).getTime();
      const rEnd = new Date(rEndStr).getTime();
      
      if (rStart < curEnd && rEnd > curStart) {
        return true;
      }
    }
  }
  return false;
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
  const catLabel = { Vehicle:'Kendaraan Dinas', Room:'Ruangan', Zoom:'Zoom Meeting', Repair:'Perbaikan Fasilitas', Item:'Peminjaman Barang', Item2:'Permintaan Barang' }[req.type] || req.type;

  // ── Strict Role-Based Workflow Logic (deklarasi di awal agar bisa digunakan di seluruh fungsi) ──
  const PIC_MAP_LOCAL = {
    'Vehicle': ['198605082025211053'],
    'Item':    ['198902222025211044'],
    'Item2':   ['198902222025211044'],
    'Zoom':    ['198902222025211044'],
    'Room':    ['199008092025212052', '198902222025211044', '16268300055'],
    'Dormitory': ['199008092025212052', '198902222025211044', '16268300055'],
    'Repair':  ['198605082025211053', '197212162014091003']
  };
  const SUPER_ADMIN_NIKS_LOCAL = ['000000000000000000'];
  const allowedPICs  = PIC_MAP_LOCAL[req.type] || [];
  const isPIC        = allowedPICs.includes(ADMIN_USERNAME);
  const MANAGER_FMD_NIK = '197707072025211067';
  const isManagerFMD = (CURRENT_ROLE === 'managerFMD' || ADMIN_USERNAME === MANAGER_FMD_NIK);
  const isSuperAdmin = SUPER_ADMIN_NIKS_LOCAL.includes(ADMIN_USERNAME);

  // ── Bagian assign kendaraan ──
  const vehicleSection = (req.type === 'Vehicle' && (isPIC || isSuperAdmin || isManagerFMD) && !['canceled', 'rejected', 'completed', 'returned'].includes(req.status)) ? `
    <div style="background:#fff7ed; border:1px solid #ffedd5; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#9a3412; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 11l1.5-4.5h11L19 11M3 11h18v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5M7 16v-2M17 16v-2"/></svg>
        Pilih Kendaraan (Wajib)
      </div>
      <div class="form-group" style="margin-bottom:1.5rem">
        <select id="assign-vehicle" class="form-select" style="background:#fff;">
          <option value="">Pilih Kendaraan Dinas...</option>
          <option value="TANPA_KENDARAAN" style="color:#ea580c; font-weight:bold;">[0] Tanpa Kendaraan (Hanya Jasa Driver / Mobil Pribadi)</option>
          ${ALL_VEHICLES.map(v => {
            const conflict = checkResourceConflict(req, 'Vehicle', v.id);
            return `<option value="${v.id}"${req.vehicle_id === v.id ? ' selected' : ''}${conflict ? ' disabled' : ''}>${v.name}${conflict ? ' (Digunakan)' : ''}</option>`;
          }).join('')}
        </select>
      </div>

      <div style="font-weight:700; color:#9a3412; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Set Driver (Wajib)
      </div>
      <div class="form-group" style="margin-bottom:0">
        <select id="assign-driver" class="form-select" style="background:#fff;">
          <option value="">Pilih Driver dari Data Pegawai...</option>
          <option value="TANPA_SUPIR" style="color:#ea580c; font-weight:bold;">[0] Tanpa Supir (Tidak Ada Driver Tersedia)</option>
          ${(Array.isArray(allEmployees) ? allEmployees : []).filter(emp => emp.position && (String(emp.position).toLowerCase().includes('driver') || String(emp.position).toLowerCase().includes('pengemudi'))).map(emp => {
            const conflict = checkResourceConflict(req, 'driver', emp.full_name);
            return `<option value="${emp.full_name}"${(req.driver_name && req.driver_name.includes(emp.full_name)) ? ' selected' : ''}${conflict ? ' disabled' : ''}>${emp.full_name} - ${emp.position}${conflict ? ' (Bertugas)' : ''}</option>`;
          }).join('')}
        </select>
      </div>
      <div style="font-size:0.75rem; color:#ea580c; font-style:italic; margin-top:1rem;">* Kendaraan dan Driver wajib dipilih sebelum melakukan verifikasi ke Manager.</div>
      ${!['pending', 'waiting_manager_fmd'].includes(req.status) ? `
        <button class="btn btn-warning btn-full" style="margin-top:1rem; background-color:#f59e0b; color:#fff;" onclick="doVehicleAssign(${req.id}, '${req.status}')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.25rem;vertical-align:-3px;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          Update Penempatan Kendaraan / Driver
        </button>
      ` : ''}
    </div>` : '';

  // ── Bagian assign Zoom ──
  const zoomSection = (req.type === 'Zoom' && (req.status === 'pending' || (req.status === 'waiting_manager_fmd' && (isManagerFMD || isSuperAdmin)))) ? `
    <div style="background:#ecfeff; border:1px solid #cffafe; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#155e75; margin-bottom:0.75rem;">Link / Host Key (Optional)</div>
      <div class="form-group" style="margin-bottom:0">
        <input type="text" id="assign-zoom" class="form-input" style="background:#fff; border-color:#a5f3fc;" placeholder="Contoh: https://zoom.us/j/..., Host Key: 123456" />
      </div>
    </div>` : '';

  // ── Bagian assign Item ──
  const itemSection = (req.type === 'Item' && (req.status === 'pending' || (req.status === 'waiting_manager_fmd' && (isManagerFMD || isSuperAdmin)))) ? `
    <div style="background:#faf5ff; border:1px solid #f3e8ff; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#6b21a8; margin-bottom:0.75rem;">Asset ID / Note (Optional)</div>
      <div class="form-group" style="margin-bottom:0">
        <input type="text" id="assign-item" class="form-input" style="background:#fff; border-color:#d8b4fe;" placeholder="Contoh: PROJ-001, Kondisi Baik" />
      </div>
    </div>` : '';

  // ── Bagian assign Room ──
  const roomSection = (req.type === 'Room' && (isPIC || isSuperAdmin || isManagerFMD) && !['canceled', 'rejected', 'completed', 'returned'].includes(req.status)) ? `
    <div style="background:#f0fdf4; border:1px solid #dcfce7; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#166534; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
        Plotting / Ubah Ruangan
      </div>
      <div class="form-group" style="margin-bottom:0">
        <select id="assign-room" class="form-select" style="background:#fff;">
          <option value="">Pilih Ruangan...</option>
          ${ALL_ROOMS.map(r => {
            const conflict = checkResourceConflict(req, 'Room', r.id);
            return `<option value="${r.id}"${req.room_id === r.id ? ' selected' : ''}${conflict ? ' disabled' : ''}>${r.name}${conflict ? ' (Digunakan)' : ''}</option>`;
          }).join('')}
        </select>
        <div style="font-size:0.75rem; color:#15803d; font-style:italic; margin-top:0.5rem;">* Anda dapat mengubah ruangan yang dipilih user jika diperlukan.</div>
      </div>
      ${!['pending', 'waiting_manager_fmd'].includes(req.status) ? `
        <button class="btn btn-warning btn-full" style="margin-top:1rem; background-color:#f59e0b; color:#fff;" onclick="doRoomApprove(${req.id}, '${req.status}')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.25rem;vertical-align:-3px;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          Update Penempatan Ruangan
        </button>
      ` : ''}
    </div>` : '';

  // ── Bagian assign Dormitory ──
  const dormitorySection = (req.type === 'Dormitory' && (isPIC || isSuperAdmin || isManagerFMD) && !['canceled', 'rejected', 'completed', 'returned'].includes(req.status)) ? `
    <div style="background:#fdf4ff; border:1px solid #fbcfe8; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
      <div style="font-weight:700; color:#831843; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Plotting / Ubah Dormitory
      </div>
      <div class="form-group" style="margin-bottom:0">
        <select id="assign-dormitory" class="form-select" style="background:#fff;">
          <option value="">Pilih Dormitory...</option>
          ${ALL_DORMITORIES.map(r => {
            const conflict = checkResourceConflict(req, 'Dormitory', r.id);
            return `<option value="${r.id}"${req.dormitory_id === r.id ? ' selected' : ''}${conflict ? ' disabled' : ''}>${r.name}${conflict ? ' (Digunakan)' : ''}</option>`;
          }).join('')}
        </select>
        <div style="font-size:0.75rem; color:#be185d; font-style:italic; margin-top:0.5rem;">* Anda wajib memploting dormitory untuk user.</div>
      </div>
      ${!['pending', 'waiting_manager_fmd'].includes(req.status) ? `
        <button class="btn btn-warning btn-full" style="margin-top:1rem; background-color:#f59e0b; color:#fff;" onclick="doDormitoryApprove(${req.id}, '${req.status}')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.25rem;vertical-align:-3px;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          Update Penempatan Dormitory
        </button>
      ` : ''}
    </div>` : '';

  // NOTE: All users on admin/index.php have CURRENT_ROLE='admin' (from login redirect).
  // Access control MUST be based on ADMIN_USERNAME (which = NIK) or CURRENT_ROLE for specific non-admin roles.
  // (isPIC, isManagerFMD, isSuperAdmin sudah dideklarasikan di atas fungsi ini)

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
  } else if (req.type === 'Repair') {
    // ── REPAIR WORKFLOW ──
    if (req.status === 'pending' && (isPIC || isSuperAdmin)) {
      actionBtns = `
      <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:1.25rem; border-radius:0.5rem; margin-bottom:1rem;">
        <div style="font-weight:700; color:#334155; margin-bottom:0.75rem;">Opsi Penanganan Perbaikan</div>
        
        <div style="margin-bottom:1rem;">
          <div style="font-size:0.875rem; font-weight:600; margin-bottom:0.5rem; color:#475569;">1. Sanggup Dikerjakan Sendiri</div>
          <button class="btn btn-primary btn-full" onclick="openGudangModal(${req.id})" style="background:#059669;">&#x1F6E0; Proses Pengerjaan Sendiri</button>
        </div>

        <div>
          <div style="font-size:0.875rem; font-weight:600; margin-bottom:0.5rem; color:#475569;">2. Tidak Memungkinkan (Pihak Ketiga / Vendor)</div>
          <button class="btn btn-primary btn-full" onclick="openRABModal('Tidak memungkinkan dikerjakan sendiri (pihak ketiga/vendor)')" style="background:#4f46e5;">&#x1F3ED; Teruskan ke Vendor / Pihak Ketiga</button>
        </div>
      </div>`;
    } else if (req.status === 'waiting_manager_fmd' && (isManagerFMD || isSuperAdmin)) {
      actionBtns = `<div style="display:flex;gap:.5rem;flex-direction:column;">
        <button class="btn btn-success btn-full" onclick="handleApproveRAB(${req.id})">✓ Approve RAB / Internal</button>
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
        <button class="btn btn-warning btn-full" onclick="doDisburseRepair(${req.id})" style="margin-bottom:1rem;">💰 Cairkan Dana & Mulai Kerja</button>
        <a href="${BASE_URL}api/print_pum.php?id=${req.id}" target="_blank" class="btn btn-outline btn-full" style="display:block;text-align:center;">🖨️ Cetak PUM (PDF)</a>
      </div>`;
    } else if (req.status === 'in-progress' && (isPIC || isSuperAdmin)) {
      actionBtns = `
        <a href="${BASE_URL}api/print_pum.php?id=${req.id}" target="_blank" class="btn btn-outline btn-full" style="display:block;text-align:center;margin-bottom:1rem;">🖨️ Cetak PUM (PDF)</a>
        <button class="btn btn-primary btn-full" onclick="updateStatus(${req.id},'Repair','completed')">✓ Tandai Selesai</button>
      `;
    } else {
      picMessage = reminderBox('⏳', 'Menunggu Proses',
        `Pengajuan sedang dalam tahap: <b>${getStatusLabel(req.status)}</b>. Tidak ada tindakan yang tersedia untuk peran Anda saat ini.`);
    }

  } else if (req.status === 'pending') {
    // ── STAGE 1: Pending → PIC Forward to Manager FMD ──
    if (isPIC || isSuperAdmin) {
      if (req.type === 'Vehicle') {
        actionBtns = `<div style="display:flex;flex-direction:column;gap:.5rem;">
          <button class="btn btn-primary btn-full" onclick="doVehicleAssign(${req.id},'waiting_manager_fmd')">✓ Teruskan ke Manager FMD</button>
        </div>`;
      } else if (req.type === 'Room') {
        actionBtns = `<div style="display:flex;flex-direction:column;gap:.5rem;">
          <button class="btn btn-primary btn-full" onclick="doRoomApprove(${req.id},'waiting_manager_fmd')">✓ Plotting & Teruskan ke Manager FMD</button>
        </div>`;
      } else if (req.type === 'Dormitory') {
        actionBtns = `<div style="display:flex;flex-direction:column;gap:.5rem;">
          <button class="btn btn-primary btn-full" onclick="doDormitoryApprove(${req.id},'waiting_manager_fmd')">✓ Plotting & Teruskan ke Manager FMD</button>
        </div>`;
      } else {
        actionBtns = `<div style="display:flex;gap:.5rem;">
          <button class="btn btn-primary" style="flex:1;" onclick="doApproveRequest(${req.id},'${req.type}','waiting_manager_fmd')">✓ Teruskan ke Manager FMD</button>
        </div>`;
      }
    } else {
      picMessage = reminderBox('🔔', 'Menunggu Tindakan PIC',
        `Pengajuan ini perlu diverifikasi ketersediaannya oleh PIC ${req.type}. Jika tersedia, PIC akan meneruskan ke Manager FMD untuk persetujuan.`);
    }

  } else if (req.status === 'waiting_manager_fmd') {
    // ── STAGE 2: Manager FMD Approve / Reject ──
    if (isManagerFMD || isSuperAdmin) {
      if (req.type === 'Vehicle') {
        actionBtns = `<div style="display:flex;gap:.5rem;">
          <button class="btn btn-success btn-full" onclick="doVehicleAssign(${req.id},'approved')">✓ Setujui (Approved)</button>
          <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','rejected')">✕ Tolak</button>
        </div>`;
      } else if (req.type === 'Room') {
        actionBtns = `<div style="display:flex;gap:.5rem;">
          <button class="btn btn-success btn-full" onclick="doRoomApprove(${req.id},'approved')">✓ Setujui (Approved)</button>
          <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','rejected')">✕ Tolak</button>
        </div>`;
      } else if (req.type === 'Dormitory') {
        actionBtns = `<div style="display:flex;gap:.5rem;">
          <button class="btn btn-success btn-full" onclick="doDormitoryApprove(${req.id},'approved')">✓ Setujui (Approved)</button>
          <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','rejected')">✕ Tolak</button>
        </div>`;
      } else {
        actionBtns = `<div style="display:flex;gap:.5rem;">
          <button class="btn btn-success btn-full" onclick="doApproveRequest(${req.id},'${req.type}','approved')">✓ Setujui (Approved)</button>
          <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','rejected')">✕ Tolak</button>
        </div>`;
      }
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
      } else if (req.type === 'Zoom') {
          btnAction = `onclick="doApproveRequest(${req.id},'${req.type}','ready_for_user')"`;
      }

      actionBtns = `<div style="display:flex;flex-direction:column;gap:.75rem;">
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;padding:.9rem 1rem;border-radius:.5rem;font-size:.82rem;color:#065f46;">
          <strong>✅ Disetujui Manager FMD.</strong><br>Lakukan persiapan & pemeriksaan kebutuhan pengajuan ini. Klik tombol di bawah jika semua sudah siap dan sedang dalam pelaksanaan.
        </div>
        <button class="btn btn-primary btn-full" ${btnAction}>📋 ${checkLabel}</button>
        <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','canceled')">✕ Tolak / Batalkan (Decline)</button>
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
        <button class="btn btn-danger btn-full" onclick="updateStatus(${req.id},'${req.type}','canceled')">✕ Tolak / Batalkan (Decline)</button>
      </div>`;
    } else {
      picMessage = reminderBox('📋', 'Siap Digunakan (Ready for User)',
        'Pengajuan telah disiapkan oleh PIC dan berstatus Ready for User. PIC akan menyelesaikan permintaan ini setelah penggunaan selesai.');
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
                ${req.type === 'Vehicle' ? (req.vehicle_id === 'TANPA_KENDARAAN' ? 'Tanpa Kendaraan (Hanya Jasa Driver)' : (ALL_VEHICLES.find(v => v.id === req.vehicle_id) || {}).name || req.details) : (ROOM_MAP[req.room_id] || req.details)}
                ${req.type === 'Vehicle' && req.driver_name ? ` <span style="font-size:0.85rem;color:#4b5563;">(Driver: ${req.driver_name === 'TANPA_SUPIR' ? 'Tidak Ada' : req.driver_name})</span>` : ''}
              </div>
            </div>
            <div style="grid-column:1/3;">
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Waktu / Tanggal:</span>
              ${jadwalVal}
            </div>
            ${req.type === 'Dormitory' ? `
            <div style="grid-column:1/3;">
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Penghuni:</span>
              ${req.occupant_name || '-'} <span style="font-size:0.85rem;color:#6b7280;">(${req.participants || '0'} orang)</span>
            </div>` : ''}
            ${req.type === 'Vehicle' ? `
            <div style="grid-column:1/3;">
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Penumpang:</span>
              ${req.passenger_name || '-'} <span style="font-size:0.85rem;color:#6b7280;">(${req.passenger_name ? req.passenger_name.split(',').length : 0} orang)</span>
            </div>
            <div style="grid-column:1/3;">
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Rute Perjalanan:</span>
              ${req.departure || '-'} &rarr; ${req.destination || '-'}
            </div>
            <div style="grid-column:1/3;">
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Pembebanan Biaya:</span>
              ${req.cost_bearer || '-'}
            </div>` : ''}
            ${req.type === 'Item2' ? `
            <div style="grid-column:1/3;">
              <span style="font-weight:600; display:block; margin-bottom:0.25rem;">Daftar Barang:</span>
              <ul style="margin:0; padding-left:1.25rem; font-size:0.85rem; color:#1e293b; line-height:1.5;">
                ${(function(){
                  try {
                    let items = JSON.parse(req.items_json);
                    return items.map(i => `<li><strong>${i.name}</strong> (${i.quantity} unit)</li>`).join('');
                  } catch(e) { return '<li>Format data invalid</li>'; }
                })()}
              </ul>
            </div>` : ''}
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
          ${req.type === 'Repair' && req.status !== 'pending' ? `
          <div id="rab-view-container" style="margin-bottom:1.5rem; background:#fff; border:1px solid #e2e8f0; border-radius:.5rem; padding:1rem;">
            <span style="font-weight:600; color:#475569; display:block; margin-bottom:0.5rem;">RAB / Rincian Biaya:</span>
            <div style="color:#94a3b8; font-size:0.8rem;">&#x23F3; Memuat data RAB...</div>
          </div>` : ''}
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
  const catLabel = { Vehicle:'Kendaraan Dinas', Room:'Ruangan', Zoom:'Zoom Meeting', Repair:'Perbaikan Fasilitas', Item:'Peminjaman Barang', Item2:'Permintaan Barang' }[req.type] || req.type;

  const type_lower = (req.type || '').toLowerCase();
  const detail = type_lower === 'vehicle' ? ((ALL_VEHICLES.find(v => String(v.id) === String(req.vehicle_id)) || {}).name || req.details || 'Menunggu Plotting') :
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
      ${req.type === 'Dormitory' ? `
      <div class="tv-row">
        <div class="tv-label">Penghuni</div>
        <div class="tv-value" style="font-weight:600;">${req.occupant_name || '-'} <span style="font-size:0.85rem;color:#6b7280;font-weight:400;">(${req.participants || '0'} orang)</span></div>
      </div>` : ''}
      ${req.type === 'Vehicle' ? `
      <div class="tv-row">
        <div class="tv-label">Penumpang</div>
        <div class="tv-value" style="font-weight:600;">${req.passenger_name || '-'} <span style="font-size:0.85rem;color:#6b7280;font-weight:400;">(${req.passenger_name ? req.passenger_name.split(',').length : 0} orang)</span></div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Lokasi Awal</div>
        <div class="tv-value" style="font-weight:600;">${req.departure || '-'}</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Lokasi Tujuan</div>
        <div class="tv-value" style="font-weight:600;">${req.destination || '-'}</div>
      </div>` : ''}
      <div class="tv-row">
        <div class="tv-label">Kategori</div>
        <div class="tv-value">${catLabel}</div>
      </div>
      <div class="tv-row">
        <div class="tv-label">Item / Lokasi</div>
        <div class="tv-value tv-item" style="color:#1d4ed8; font-weight:600;">${detail || '-'}</div>
      </div>
      ${req.type === 'Item2' ? `
      <div class="tv-row" style="grid-column:1/3; margin-top:0.5rem;">
        <div class="tv-label" style="margin-bottom:0.5rem;">Daftar Permintaan:</div>
        <div class="tv-value" style="width:100%;">
          <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
            <thead style="background:#f1f5f9; border-bottom:1px solid #e2e8f0;">
              <tr><th style="padding:0.5rem;text-align:left;">Nama Barang</th><th style="padding:0.5rem;text-align:center;">Jumlah</th></tr>
            </thead>
            <tbody>
              ${(function(){
                try {
                  let items = JSON.parse(req.items_json);
                  return items.map(i => `<tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:0.5rem;">${i.name}</td><td style="padding:0.5rem;text-align:center;font-weight:600;">${i.quantity}</td></tr>`).join('');
                } catch(e) { return '<tr><td colspan="2" style="padding:0.5rem;color:red;">Format data invalid</td></tr>'; }
              })()}
            </tbody>
          </table>
        </div>
      </div>` : ''}
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
      <div style="margin-top:1rem; text-align:right;">
        <a href="${BASE_URL}api/print_pum.php?id=${requestId}" target="_blank" class="btn btn-primary" style="display:inline-block; font-size:0.875rem; background:#4f46e5; color:#fff; padding:0.5rem 1rem; border-radius:0.375rem; text-decoration:none;">&#x1F5A8;&#xFE0F; Cetak PUM (PDF)</a>
      </div>
    </div>`;
}

// ===== UPDATE STATUS =====
async function updateStatus(id, type, newStatus) {
  const note     = (document.getElementById('admin-note') || {}).value || '';
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
  else if (total > 0) nextStatus = 'waiting_manager_fad';
  else nextStatus = 'in-progress'; // Jika 0, berarti form gudang / tanpa biaya

  const noteEl = document.getElementById('admin-note');
  if (noteEl) {
    if (total > 0) {
      noteEl.value = `RAB Approved. Total: ${formatRupiah(total)}. ` + (noteEl.value || '');
    } else {
      noteEl.value = `Permintaan Internal / Gudang disetujui. ` + (noteEl.value || '');
    }
  }
  
  await updateStatus(id, 'Repair', nextStatus);
}

async function doDisburseRepair(id) {
  const worker = (document.getElementById('assign-worker') || {}).value || '';
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
    const info = (document.getElementById('assign-zoom') || {}).value;
    if (info) noteAppend = `Zoom Info: ${info}. `;
  } else if (type === 'Item') {
    const info = (document.getElementById('assign-item') || {}).value;
    if (info) noteAppend = `Asset Info: ${info}. `;
  }
  
  const noteEl    = document.getElementById('admin-note');
  const rawNote   = (noteEl || {}).value || '';
  
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
  } else if (targetStatus === 'ready_for_user' && type === 'Zoom' && !rawNote.trim()) {
    finalNote = noteAppend + "Link Zoom telah disiapkan PIC dan siap digunakan.";
  }

  if (noteEl) noteEl.value = finalNote;
  await updateStatus(id, type, targetStatus);
}

async function doVehicleAssign(id, targetStatus = 'approved') {
  const vehicleId  = (document.getElementById('assign-vehicle') || {}).value || '';
  const driverName = (document.getElementById('assign-driver') || {}).value  || '';
  const noteEl     = document.getElementById('admin-note');
  const rawNote    = (noteEl || {}).value || '';

  if (!vehicleId || !driverName) {
    Toast.error('Pilih kendaraan dinas dan driver!');
    return;
  }

  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'update_vehicle_assignment', id, vehicle_id: vehicleId, driver_name: driverName
  });
  if (!res.success) { Toast.error(res.message); return; }

  const vName = vehicleId === 'TANPA_KENDARAAN' ? 'Tanpa Kendaraan (Hanya Jasa Driver)' : (ALL_VEHICLES.find(v => v.id === vehicleId) || {}).name || vehicleId;
  const isChanged = (String(vehicleId) !== String(currentDetailReq.vehicle_id)) || (String(driverName) !== String(currentDetailReq.driver_name));

  if (noteEl) {
    if (targetStatus === 'waiting_manager_fmd' && !rawNote.trim()) {
      noteEl.value = `${vName} tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: ${driverName}`;
    } else if (targetStatus === 'approved' && currentDetailReq.status === 'waiting_manager_fmd') {
      if (isChanged) noteEl.value = `Disetujui dengan perubahan: Kendaraan ${vName}, Driver ${driverName}. ${rawNote}`;
      else noteEl.value = `Vehicle: ${vName}. Driver: ${driverName}. ${rawNote}`;
    } else {
      noteEl.value = `Vehicle: ${vName}. Driver: ${driverName}. ${rawNote}`;
    }
  }
  await updateStatus(id, 'Vehicle', targetStatus);
}

async function doRoomApprove(id, targetStatus = 'approved') {
  const roomId = (document.getElementById('assign-room') || {}).value || '';
  const noteEl = document.getElementById('admin-note');
  const rawNote = (noteEl || {}).value || '';

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
  const isChanged = String(roomId) !== String(currentDetailReq.room_id);

  if (noteEl) {
    if (targetStatus === 'waiting_manager_fmd' && !rawNote.trim()) {
      noteEl.value = `${rName} tersedia, diteruskan kepada Manager FMD untuk approval permohonan`;
    } else if (targetStatus === 'approved' && currentDetailReq.status === 'waiting_manager_fmd') {
      if (isChanged) noteEl.value = `Disetujui dengan perubahan: Ruangan ${rName}. ${rawNote}`;
      else noteEl.value = `Ruangan: ${rName}. ${rawNote}`;
    } else {
      noteEl.value = `Ruangan: ${rName}. ${rawNote}`;
    }
  }
  await updateStatus(id, 'Room', targetStatus);
}

async function doDormitoryApprove(id, targetStatus = 'approved') {
  const dormitoryId = (document.getElementById('assign-dormitory') || {}).value || '';
  const noteEl = document.getElementById('admin-note');
  const rawNote = (noteEl || {}).value || '';

  if (!dormitoryId) {
    Toast.error('Pilih dormitory yang akan ditetapkan!');
    return;
  }

  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'update_dormitory_assignment', id, dormitory_id: dormitoryId
  });
  if (!res.success) { Toast.error(res.message); return; }

  const dName = DORMITORY_MAP[dormitoryId] || dormitoryId;
  if (noteEl) {
    if (targetStatus === 'waiting_manager_fmd' && !rawNote.trim()) {
      noteEl.value = `${dName} tersedia, diteruskan kepada Manager FMD untuk approval permohonan`;
    } else {
      noteEl.value = `Dormitory: ${dName}. ${rawNote}`;
    }
  }
  await updateStatus(id, 'Dormitory', targetStatus);
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
function openRABModal(jenis = 'Tidak memungkinkan dikerjakan sendiri (pihak ketiga/vendor)') {
  rabItems = [];
  const el = document.getElementById('rab-jenis');
  if (el) el.value = jenis;
  renderRABTable();
  Modal.open('modal-rab');
}

function addRabItem() {
  const nameEl = document.getElementById('rab-item-name'); const name = (nameEl ? nameEl.value : '').trim();
  const qty   = parseInt((document.getElementById('rab-item-qty') || {}).value || '1');
  const price = parseFloat((document.getElementById('rab-item-price') || {}).value || '0');
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

  const jenis = (document.getElementById('rab-jenis') || {}).value || '';

  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'submit_repair_budget',
    request_id: currentRequestId,
    jenis: jenis,
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

// ===== GUDANG MODAL =====
let gudangItems = [];

function toggleGudangItems(val) {
  const inputs = document.getElementById('gudang-item-inputs');
  const rabInputs = document.getElementById('gudang-rab-inputs');
  
  if (val === 'Perlu mengajukan pembelian sparepart (dikerjakan sendiri)') {
    inputs.style.display = 'none';
    rabInputs.style.display = 'block';
  } else if (val === 'Tidak perlu sparepart (jasa)') {
    inputs.style.display = 'none';
    rabInputs.style.display = 'none';
  } else {
    inputs.style.display = 'block';
    rabInputs.style.display = 'none';
  }
}

function openGudangModal(id) {
  gudangItems = [];
  grItems = [];
  document.getElementById('gudang-note').value = '';
  document.getElementById('gudang-jenis').value = 'Sparepart tersedia di gudang';
  toggleGudangItems('Sparepart tersedia di gudang');
  renderGudangTable();
  renderGrTable();
  Modal.open('modal-gudang');
}

function setupGudangSearchDropdown() {
  const input = document.getElementById('gudang-item-name');
  const dropdown = document.getElementById('gudang-item-dropdown');
  const stockInput = document.getElementById('gudang-item-stock');
  const idInput = document.getElementById('gudang-item-id');
  if (!input || !dropdown) return;
  let timeout = null;

  input.addEventListener('input', function() {
    clearTimeout(timeout);
    if (idInput) idInput.value = ''; // clear id if user types manually
    const q = this.value.trim();
    if(q.length < 2) {
      dropdown.style.display = 'none';
      return;
    }
    
    timeout = setTimeout(async () => {
      try {
        const res = await fetch(API_BASE + `requests.php?action=search_inventory_items&q=${encodeURIComponent(q)}`);
        const items = await res.json();
        
        dropdown.innerHTML = '';
        if(items.length === 0) {
          dropdown.innerHTML = '<div style="padding: 0.5rem 1rem; color: #64748b;">Barang tidak ditemukan</div>';
        } else {
          items.forEach(item => {
            const div = document.createElement('div');
            div.style.cssText = 'padding: 0.5rem 1rem; cursor: pointer; border-bottom: 1px solid #eee; transition: background 0.2s;';
            div.onmouseover = () => div.style.background = '#f1f5f9';
            div.onmouseout = () => div.style.background = 'white';
            div.textContent = `${item.item_code} - ${item.name} (Stok: ${item.stock})`;
            div.onclick = () => {
              input.value = item.name;
              if (idInput) idInput.value = item.id;
              if (stockInput) stockInput.value = item.stock;
              const qtyInput = document.getElementById('gudang-item-qty');
              if (qtyInput) qtyInput.max = item.stock;
              dropdown.style.display = 'none';
            };
            dropdown.appendChild(div);
          });
        }
        dropdown.style.display = 'block';
      } catch (err) {
        console.error(err);
      }
    }, 300);
  });
  
  document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });
}
setupGudangSearchDropdown();

function addGudangItem() {
  const nameEl2 = document.getElementById('gudang-item-name'); const name = (nameEl2 ? nameEl2.value : '').trim();
  const idInput = document.getElementById('gudang-item-id'); const itemId = idInput ? idInput.value : '';
  const qty  = parseInt((document.getElementById('gudang-item-qty') || {}).value || '1');
  if (!name || qty <= 0) { Toast.error('Mohon lengkapi data barang gudang'); return; }
  gudangItems.push({ id: Date.now(), itemId: itemId, itemName: name, quantity: qty });
  document.getElementById('gudang-item-name').value = '';
  if (idInput) idInput.value = '';
  document.getElementById('gudang-item-qty').value  = '1';
  renderGudangTable();
}

function removeGudangItem(id) {
  gudangItems = gudangItems.filter(i => i.id !== id);
  renderGudangTable();
}

function renderGudangTable() {
  const tbody = document.getElementById('gudang-table-body');
  if (!tbody) return;
  if (!gudangItems.length) {
    tbody.innerHTML = `<tr><td colspan="3" style="text-align:center;color:var(--color-slate-400);padding:1.5rem;">Belum ada barang</td></tr>`;
    return;
  }
  tbody.innerHTML = gudangItems.map(i => {
    return `<tr>
      <td>${i.itemName}</td>
      <td style="text-align:right;">${i.quantity}</td>
      <td><button class="btn btn-danger btn-sm" onclick="removeGudangItem(${i.id})">✕</button></td>
    </tr>`;
  }).join('');
}

// ===== INTERNAL RAB LOGIC =====
let grItems = [];

function renderGrTable() {
  const tbody = document.getElementById('gr-table-body');
  const totalEl = document.getElementById('gr-total');
  if (!tbody || !totalEl) return;
  
  if (!grItems.length) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--color-slate-400);padding:1.5rem;">Belum ada item</td></tr>`;
    totalEl.innerText = 'Rp 0';
    return;
  }
  
  let totalAll = 0;
  tbody.innerHTML = grItems.map(i => {
    const sum = i.quantity * i.unitPrice;
    totalAll += sum;
    return `<tr>
      <td>${i.itemName}</td>
      <td style="text-align:right;">${i.quantity}</td>
      <td style="text-align:right;">Rp ${i.unitPrice.toLocaleString('id-ID')}</td>
      <td style="text-align:right;font-weight:600;">Rp ${sum.toLocaleString('id-ID')}</td>
      <td style="text-align:right;">
        <button class="btn btn-danger btn-sm" onclick="removeGrItem(${i.id})">✕</button>
      </td>
    </tr>`;
  }).join('');
  
  totalEl.innerText = `Rp ${totalAll.toLocaleString('id-ID')}`;
}

function addGrItem() {
  const nameEl3 = document.getElementById('gr-item-name'); const name = (nameEl3 ? nameEl3.value : '').trim();
  const qty   = parseInt((document.getElementById('gr-item-qty') || {}).value || '1');
  const price = parseFloat((document.getElementById('gr-item-price') || {}).value || '0');
  if (!name || price <= 0 || qty <= 0) { Toast.error('Mohon lengkapi data item RAB'); return; }
  grItems.push({ id: Date.now(), itemName: name, quantity: qty, unitPrice: price });
  document.getElementById('gr-item-name').value = '';
  document.getElementById('gr-item-qty').value  = '1';
  document.getElementById('gr-item-price').value = '0';
  renderGrTable();
}

function removeGrItem(id) {
  grItems = grItems.filter(i => i.id !== id);
  renderGrTable();
}

async function submitGudang() {
  if (!currentRequestId) { Toast.error('Tidak ada request terpilih.'); return; }

  const jenis = (document.getElementById('gudang-jenis') || {}).value || '';
  const noteEl2 = document.getElementById('gudang-note'); let note = (noteEl2 ? noteEl2.value : '').trim() || '';
  
  // If Beli Sparepart, submit as RAB
  if (jenis === 'Perlu mengajukan pembelian sparepart (dikerjakan sendiri)') {
    if (!grItems.length) { Toast.error('Minimal isi 1 item RAB.'); return; }
    
    const res = await apiPost(API_BASE + 'requests.php', {
      action: 'submit_repair_budget',
      request_id: currentRequestId,
      jenis: jenis,
      items: JSON.stringify(grItems),
      note: note
    });
    
    if (res.success) {
      Toast.success('RAB Internal berhasil diajukan!');
      Modal.close('modal-gudang');
      switchView(previousView || 'dashboard');
      await loadAllData();
    } else {
      Toast.error(res.message);
    }
    return;
  }

  if (jenis === 'Sparepart tersedia di gudang' && gudangItems.length > 0) {
    const itemsStr = gudangItems.map(i => `${i.quantity}x ${i.itemName}`).join(', ');
    note = `[Internal: ${jenis}] Permintaan Barang: ${itemsStr}. ${note}`;
  } else {
    note = `[Internal: ${jenis}] ${note}`;
  }

  // Update status to waiting_manager_fmd with the note
  const res = await apiPost(API_BASE + 'requests.php', {
    action: 'update_status', 
    id: currentRequestId, 
    type: 'Repair', 
    status: 'waiting_manager_fmd', 
    note: note, 
    prev_note: currentRequestNote,
    gudang_items: JSON.stringify(jenis === 'Sparepart tersedia di gudang' ? gudangItems : [])
  });

  if (res.success) {
    Toast.success('Permintaan Gudang berhasil diajukan!');
    Modal.close('modal-gudang');
    switchView(previousView || 'dashboard');
    await loadAllData();
  } else {
    Toast.error(res.message || 'Gagal update status.');
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
  const id       = (document.getElementById('edit-user-id') || {}).value;
  const full_name= (document.getElementById('user-fullname') || {}).value;
  const emp_id   = (document.getElementById('user-employee-id') || {}).value;
  const role     = (document.getElementById('user-role') || {}).value;
  const password = (document.getElementById('user-password') || {}).value;

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

function exportExcel() {
  const month = (document.getElementById('stat-month') || {}).value || '';
  const year = (document.getElementById('stat-year') || {}).value || new Date().getFullYear();
  window.open(API_BASE + `export_excel.php?month=${month}&year=${year}`, '_blank');
}

// ===== UTILITIES =====
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

// --- MASTER DATA ---
let currentMasterTab = 'vehicle';
function setMasterTab(tab) {
    currentMasterTab = tab;
    renderCurrentView();
}

function renderMasterData() {
    let dataList = [];
    let title = '';
    
    if (currentMasterTab === 'vehicle') {
        dataList = typeof ALL_VEHICLES !== 'undefined' ? ALL_VEHICLES : [];
        title = 'Kendaraan Dinas';
    } else if (currentMasterTab === 'room') {
        dataList = typeof ALL_ROOMS !== 'undefined' ? ALL_ROOMS : [];
        title = 'Ruangan';
    } else if (currentMasterTab === 'dormitory') {
        dataList = typeof ALL_DORMITORIES !== 'undefined' ? ALL_DORMITORIES : [];
        title = 'Dormitory';
    }
    
    const rows = dataList.map((item, index) => {
        const idSafe = String(item.id).replace(/'/g, "\\'");
        const nameSafe = String(item.name).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        return `
        <tr>
            <td data-label="No" style="width:50px; text-align:center;">${index + 1}</td>
            <td data-label="Nama" style="font-weight:600; color:var(--color-slate-800);">${item.name} <span style="font-size:0.7rem; color:var(--color-slate-400); font-weight:normal; margin-left:8px;">(${item.id})</span></td>
            <td data-label="Aksi" style="text-align:right;">
                <button class="btn btn-outline btn-sm" onclick="openEditMasterData('${idSafe}', '${nameSafe}')" style="margin-right:4px;">Edit</button>
                <button class="btn btn-outline btn-sm" onclick="deleteMasterData('${idSafe}')" style="color:var(--color-red-500); border-color:var(--color-red-200);">Hapus</button>
            </td>
        </tr>
    `}).join('');
    
    const noData = '<tr><td colspan="3" style="text-align:center;padding:2rem;color:var(--color-slate-400);">Tidak ada data</td></tr>';

    return `
    <div class="page-header">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div><h1>Master Data</h1><p>Kelola data referensi fasilitas</p></div>
            <button class="btn btn-primary" onclick="openAddMasterData()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.4rem"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Data
            </button>
        </div>
    </div>
    
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="tab-nav" style="border-bottom:1px solid #e2e8f0; display:flex; gap:1.5rem; padding:0 1.5rem; overflow-x:auto;">
            <button class="tab-btn ${currentMasterTab === 'vehicle' ? 'active' : ''}" onclick="setMasterTab('vehicle')" style="padding:1rem 0; border:none; background:transparent; font-weight:600; cursor:pointer; color:${currentMasterTab === 'vehicle' ? 'var(--color-emerald-600)' : 'var(--color-slate-500)'}; border-bottom:${currentMasterTab === 'vehicle' ? '2px solid var(--color-emerald-600)' : '2px solid transparent'}; white-space:nowrap;">Kendaraan Dinas</button>
            <button class="tab-btn ${currentMasterTab === 'room' ? 'active' : ''}" onclick="setMasterTab('room')" style="padding:1rem 0; border:none; background:transparent; font-weight:600; cursor:pointer; color:${currentMasterTab === 'room' ? 'var(--color-emerald-600)' : 'var(--color-slate-500)'}; border-bottom:${currentMasterTab === 'room' ? '2px solid var(--color-emerald-600)' : '2px solid transparent'}; white-space:nowrap;">Ruangan</button>
            <button class="tab-btn ${currentMasterTab === 'dormitory' ? 'active' : ''}" onclick="setMasterTab('dormitory')" style="padding:1rem 0; border:none; background:transparent; font-weight:600; cursor:pointer; color:${currentMasterTab === 'dormitory' ? 'var(--color-emerald-600)' : 'var(--color-slate-500)'}; border-bottom:${currentMasterTab === 'dormitory' ? '2px solid var(--color-emerald-600)' : '2px solid transparent'}; white-space:nowrap;">Dormitory</button>
        </div>
        <div class="table-wrap table-stack-mobile">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px; text-align:center;">No</th>
                        <th>Nama ${title}</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows || noData}
                </tbody>
            </table>
        </div>
    </div>
    `;
}

// --- SYSTEM SETTINGS ---
function renderSystemSettings() {
    const s = systemSettings || {};
    const appName = (s.APP_NAME && s.APP_NAME.setting_value) || 'SILATAS';
    const teleToken = (s.TELE_TOKEN && s.TELE_TOKEN.setting_value) || '';
    const teleGroupId = (s.TELE_GROUP_ID && s.TELE_GROUP_ID.setting_value) || '';
    const fonnteToken = (s.FONNTE_TOKEN && s.FONNTE_TOKEN.setting_value) || '';
    const isMaintenance = (s.MAINTENANCE_MODE && s.MAINTENANCE_MODE.setting_value) === '1';

    return `
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div><h1>Pengaturan Sistem</h1><p>Konfigurasi parameter operasional dan notifikasi aplikasi</p></div>
        <button class="btn btn-primary" onclick="saveSystemSettings()">Simpan Pengaturan</button>
    </div>
    
    <div class="settings-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem;">
        
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid #f1f5f9; padding: 1.25rem;">
                <h3 style="margin:0; font-size:1rem; font-weight:700; color:var(--color-slate-800); display:flex; align-items:center; gap:0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--color-blue-500)"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    Identitas & Operasional
                </h3>
            </div>
            <div style="padding:1.25rem;">
                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label" style="font-weight:600;">Nama Aplikasi</label>
                    <input type="text" id="sys-app-name" class="form-input" value="${appName}">
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <div>
                        <div style="font-weight:600; color:var(--color-slate-800);">Maintenance Mode</div>
                        <div style="font-size:0.75rem; color:var(--color-slate-500);">Tutup akses untuk pemeliharaan (Hanya Superadmin yg bisa akses)</div>
                    </div>
                    <label class="toggle-switch" style="position:relative; display:inline-block; width:44px; height:24px;">
                        <input type="checkbox" id="sys-maintenance" ${isMaintenance ? 'checked' : ''} onchange="const s = this.nextElementSibling.querySelector('span'); if(this.checked){this.nextElementSibling.style.backgroundColor='var(--color-emerald-500)'; s.style.left='23px'; Toast.success('Maintenance diaktifkan');} else {this.nextElementSibling.style.backgroundColor='#cbd5e1'; s.style.left='3px'; Toast.success('Maintenance dinonaktifkan');}" style="opacity:0; width:0; height:0;">
                        <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:${isMaintenance ? 'var(--color-emerald-500)' : '#cbd5e1'}; border-radius:34px; transition:.4s;">
                            <span style="position:absolute; content:''; height:18px; width:18px; left:${isMaintenance ? '23px' : '3px'}; bottom:3px; background-color:white; border-radius:50%; transition:.4s; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid #f1f5f9; padding: 1.25rem;">
                <h3 style="margin:0; font-size:1rem; font-weight:700; color:var(--color-slate-800); display:flex; align-items:center; gap:0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--color-emerald-500)"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Integrasi WhatsApp
                </h3>
            </div>
            <div style="padding:1.25rem;">
                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label" style="font-weight:600;">Token Fonnte</label>
                    <div style="font-size:0.75rem; color:var(--color-slate-500); margin-bottom:0.25rem;">Digunakan untuk mengirim notifikasi persetujuan via WhatsApp</div>
                    <input type="text" id="sys-fonnte-token" class="form-input" value="${fonnteToken}">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid #f1f5f9; padding: 1.25rem;">
                <h3 style="margin:0; font-size:1rem; font-weight:700; color:var(--color-slate-800); display:flex; align-items:center; gap:0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--color-blue-400)"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    Integrasi Telegram
                </h3>
            </div>
            <div style="padding:1.25rem;">
                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label" style="font-weight:600;">Telegram Bot Token</label>
                    <input type="text" id="sys-tele-token" class="form-input" value="${teleToken}">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight:600;">Telegram Group ID</label>
                    <input type="text" id="sys-tele-group" class="form-input" value="${teleGroupId}">
                </div>
            </div>
        </div>
        
    </div>
    
    <div style="margin-top:2rem;">
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid #f1f5f9; padding: 1.25rem;">
                <h3 style="margin:0; font-size:1rem; font-weight:700; color:var(--color-slate-800); display:flex; align-items:center; gap:0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--color-slate-500)"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Informasi Environment
                </h3>
            </div>
            <div style="padding:1.25rem;">
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed #e2e8f0; padding-bottom:0.75rem; margin-bottom:0.75rem;">
                    <span style="color:var(--color-slate-500); font-size:0.85rem;">Versi SILATAS</span>
                    <span style="font-weight:600; font-size:0.85rem; color:var(--color-slate-800);">v1.2.0 (Production)</span>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed #e2e8f0; padding-bottom:0.75rem; margin-bottom:0.75rem;">
                    <span style="color:var(--color-slate-500); font-size:0.85rem;">PHP Version</span>
                    <span style="font-weight:600; font-size:0.85rem; color:var(--color-slate-800);">8.1.10</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--color-slate-500); font-size:0.85rem;">Database Engine</span>
                    <span style="font-weight:600; font-size:0.85rem; color:var(--color-slate-800);">MySQL / MariaDB</span>
                </div>
            </div>
        </div>
        
    </div>
    `;
}
async function saveSystemSettings() {
    const btn = document.querySelector('.page-header .btn');
    if (btn) {
        btn.textContent = 'Menyimpan...';
        btn.disabled = true;
    }
    
    const data = {
        APP_NAME: document.getElementById('sys-app-name').value,
        TELE_TOKEN: document.getElementById('sys-tele-token').value,
        TELE_GROUP_ID: document.getElementById('sys-tele-group').value,
        FONNTE_TOKEN: document.getElementById('sys-fonnte-token').value,
        MAINTENANCE_MODE: document.getElementById('sys-maintenance').checked ? '1' : '0'
    };
    
    const formData = new FormData();
    formData.append('action', 'update_settings');
    for (const key in data) {
        formData.append(`settings[${key}]`, data[key]);
    }

    try {
        const response = await fetch(API_BASE + 'settings.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            Toast.success('Pengaturan berhasil disimpan');
            // update local cache
            if (!systemSettings) systemSettings = {};
            for (const key in data) {
                if (!systemSettings[key]) systemSettings[key] = {};
                systemSettings[key].setting_value = data[key];
            }
        } else {
            Toast.error(result.message || 'Gagal menyimpan pengaturan');
        }
    } catch (e) {
        console.error(e);
        Toast.error('Terjadi kesalahan jaringan');
    } finally {
        if (btn) {
            btn.textContent = 'Simpan Pengaturan';
            btn.disabled = false;
        }
    }
}

// ===== MASTER DATA CRUD =====
function openAddMasterData() {
    document.getElementById('md-action').value = 'add';
    document.getElementById('md-type').value = currentMasterTab;
    document.getElementById('md-id').value = '';
    document.getElementById('md-id').readOnly = false;
    document.getElementById('md-name').value = '';
    
    let titleType = currentMasterTab === 'vehicle' ? 'Kendaraan' : (currentMasterTab === 'room' ? 'Ruangan' : 'Dormitory');
    document.getElementById('md-modal-title').innerText = 'Tambah Data ' + titleType;
    Modal.open('modal-master-data');
}

function openEditMasterData(id, name) {
    document.getElementById('md-action').value = 'edit';
    document.getElementById('md-type').value = currentMasterTab;
    document.getElementById('md-id').value = id;
    document.getElementById('md-id').readOnly = true; // ID cannot be changed to prevent FK issues
    document.getElementById('md-name').value = name;
    
    let titleType = currentMasterTab === 'vehicle' ? 'Kendaraan' : (currentMasterTab === 'room' ? 'Ruangan' : 'Dormitory');
    document.getElementById('md-modal-title').innerText = 'Edit Data ' + titleType;
    Modal.open('modal-master-data');
}

async function submitMasterData() {
    const action = document.getElementById('md-action').value;
    const type = document.getElementById('md-type').value;
    const id = document.getElementById('md-id').value.trim();
    const name = document.getElementById('md-name').value.trim();
    
    if (!id || !name) {
        Toast.error('ID dan Nama wajib diisi');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', action);
    formData.append('type', type);
    formData.append('id', id);
    formData.append('name', name);
    
    try {
        const response = await fetch(API_BASE + 'master_data.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            Toast.success(result.message);
            Modal.close('modal-master-data');
            // Refresh to get the new list from PHP injection (easiest way since it's global PHP state)
            setTimeout(() => location.reload(), 1500);
        } else {
            Toast.error(result.message);
        }
    } catch(err) {
        console.error(err);
        Toast.error('Terjadi kesalahan jaringan');
    }
}

async function deleteMasterData(id) {
    if (!confirm(`Anda yakin ingin menghapus data dengan ID ${id}? Data tidak dapat dihapus jika sudah pernah digunakan dalam pengajuan.`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('type', currentMasterTab);
    formData.append('id', id);
    
    try {
        const response = await fetch(API_BASE + 'master_data.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            Toast.success(result.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            Toast.error(result.message);
        }
    } catch(err) {
        console.error(err);
        Toast.error('Terjadi kesalahan jaringan');
    }
}

</script>

<!-- Modal Master Data -->
<div class="modal-overlay" id="modal-master-data">
  <div class="modal" style="max-width: 400px;">
    <div class="modal-header">
      <h3 class="modal-title" id="md-modal-title">Kelola Master Data</h3>
      <button class="modal-close-btn" onclick="Modal.close('modal-master-data')">&times;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="md-action">
      <input type="hidden" id="md-type">
      <div class="form-group" style="margin-bottom:1rem;">
        <label class="form-label" style="font-weight:600;">ID / Kode (Unik)</label>
        <div style="font-size:0.75rem; color:var(--color-slate-500); margin-bottom:0.25rem;">Gunakan format unik tanpa spasi (cth: R001, V012)</div>
        <input type="text" id="md-id" class="form-input" placeholder="Masukkan ID">
      </div>
      <div class="form-group" style="margin-bottom:1.5rem;">
        <label class="form-label" style="font-weight:600;">Nama Fasilitas</label>
        <input type="text" id="md-name" class="form-input" placeholder="Masukkan Nama/Deskripsi Lengkap">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="Modal.close('modal-master-data')">Batal</button>
      <button class="btn btn-primary" onclick="submitMasterData()">Simpan Data</button>
    </div>
  </div>
</div>

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

<!-- ===== MODAL CRUD EDIT (SUPERADMIN) ===== -->
<div class="modal-overlay" id="modal-crud-edit">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h3 class="modal-title">Edit Data Pengajuan</h3>
      <button class="modal-close modal-close-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="crud-id">
      <input type="hidden" id="crud-type">
      
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Nama Pemohon</label>
          <input type="text" id="crud-applicant_name" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Unit/Departemen</label>
          <input type="text" id="crud-applicant_unit" class="form-input">
        </div>
      </div>
      
      <div class="form-group">
        <label class="form-label">Tujuan / Keperluan (Purpose)</label>
        <textarea id="crud-purpose" class="form-input" rows="2"></textarea>
      </div>
      
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Status</label>
          <select id="crud-status" class="form-select">
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="waiting_manager_fmd">Waiting Manager FMD</option>
            <option value="waiting_manager_fad">Waiting Manager FAD</option>
            <option value="waiting_ppk">Waiting PPK</option>
            <option value="waiting_bod">Waiting BOD</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="cancelled">Cancelled</option>
            <option value="in-progress">In Progress</option>
            <option value="ready_for_user">Ready for User</option>
            <option value="completed">Completed</option>
            <option value="returned">Returned</option>
            <option value="approved_waiting_fund">Approved Waiting Fund</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Catatan (Note)</label>
          <input type="text" id="crud-note" class="form-input">
        </div>
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Tanggal Mulai</label>
          <input type="date" id="crud-date_start" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Waktu Mulai</label>
          <input type="time" id="crud-time_start" class="form-input">
        </div>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Tanggal Selesai</label>
          <input type="date" id="crud-date_end" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Waktu Selesai</label>
          <input type="time" id="crud-time_end" class="form-input">
        </div>
      </div>

      <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--color-slate-200);">
        <h4 style="font-size:0.9rem; color:var(--color-slate-600); margin-bottom:1rem;">Kolom Spesifik Tipe Pengajuan</h4>
        <div id="crud-type-specific" class="grid-2"></div>
      </div>

    </div>
    <div class="modal-footer">
      <button class="btn btn-outline modal-close-btn">Batal</button>
      <button class="btn btn-primary" onclick="doCrudSave()">Simpan Perubahan</button>
    </div>
  </div>
</div>

<?php if (empty($session['whatsapp_number'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: 'Nomor WhatsApp Belum Diisi!',
        text: 'Untuk menerima notifikasi status pengajuan, harap melengkapi nomor WhatsApp Anda di menu Profil.',
        icon: 'info',
        confirmButtonText: 'Lengkapi Sekarang',
        showCancelButton: true,
        cancelButtonText: 'Nanti Saja'
    }).then((result) => {
        if (result.isConfirmed && typeof renderView === 'function') {
            renderView('profile');
            document.querySelectorAll('.sidebar-item').forEach(el => el.classList.remove('active'));
            const pMenu = document.getElementById('menu-profile');
            if (pMenu) pMenu.classList.add('active');
        }
    });
});
</script>
<?php endif; ?>

</body>
</html>
