<?php
// ============================================================
// includes/layout.php — Fungsi helper untuk render sidebar/layout
// Setara dengan: components/dashboard-layout.tsx
// ============================================================
// Cara pakai: require_once dari setiap halaman dashboard
// Variabel yang harus di-set sebelum include:
//   $pageTitle   string   — judul halaman
//   $activeView  string   — menu aktif
//   $userRole    string   — role dari session
//   $userName    string   — nama user
//   $basePath    string   — '../' atau ''
// ============================================================

// Definisikan BASE_URL berdasarkan basePath sebelum include auth
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

function renderSidebar(string $role, string $activeView, string $userName, string $basePath = '../'): void {
    // ===== MENU CONFIG PER ROLE =====
    // Setara dengan: conditional rendering di dashboard-layout.tsx
    $adminMenus = [
        ['id' => 'dashboard',          'label' => 'Dashboard',            'icon' => 'layout'],
        ['id' => 'request_management', 'label' => 'Manajemen Pengajuan',  'icon' => 'file-text', 'badge' => 'pending_count'],
        ['id' => 'track_reports',      'label' => 'Track Pengajuan',      'icon' => 'history', 'badge' => 'track_count'],
        ['id' => 'user_management',    'label' => 'Manajemen User',       'icon' => 'users'],
        ['id' => 'analytics',          'label' => 'Laporan & Statistik',  'icon' => 'bar-chart-2'],
        ['id' => 'profile',            'label' => 'Profil',               'icon' => 'user'],
    ];

    $userMenus = [
        ['id' => 'dashboard',     'label' => 'Dashboard',     'icon' => 'layout'],
        ['id' => 'vehicle',       'label' => 'Pengajuan Kendaraan Dinas', 'icon' => 'car'],
        ['id' => 'room',          'label' => 'Pengajuan Peminjaman Ruangan',        'icon' => 'building2'],
        ['id' => 'dormitory',     'label' => 'Pengajuan Dormitory',           'icon' => 'building2'],
        ['id' => 'zoom',          'label' => 'Pengajuan Zoom',   'icon' => 'video'],
        ['id' => 'repair',        'label' => 'Pengajuan Perbaikan',      'icon' => 'wrench'],
        ['id' => 'item',          'label' => 'Pengajuan Peminjaman Barang','icon' => 'package'],
        ['id' => 'my_reports',    'label' => 'Riwayat Pengajuan',   'icon' => 'activity'],
        ['id' => 'profile',       'label' => 'Profil',         'icon' => 'user'],
    ];

    if ($_SESSION['role'] === 'admin') {
        if ($role === 'admin') {
            // Kita di Admin Dashboard, tambahkan menu ke User View
            $adminMenus[] = ['id' => 'switch_to_user', 'label' => 'Beralih ke User', 'icon' => 'eye', 'url' => $basePath . 'user/index.php'];
        } else {
            // Kita di User Dashboard, tambahkan menu ke Admin View
            $userMenus[] = ['id' => 'switch_to_admin', 'label' => 'Kembali ke Admin', 'icon' => 'layout', 'url' => $basePath . 'admin/index.php'];
        }
    }


    $supervisorMenus = [
        ['id' => 'dashboard',    'label' => 'Dashboard',            'icon' => 'layout'],
        ['id' => 'verification', 'label' => 'Verifikasi & RAB',     'icon' => 'clipboard-check'],
        ['id' => 'in-progress',  'label' => 'Sedang Dikerjakan',    'icon' => 'hammer'],
        ['id' => 'history',      'label' => 'Riwayat Perbaikan',    'icon' => 'history'],
        ['id' => 'profile',      'label' => 'Profil',               'icon' => 'user'],
    ];

    $menus = match($role) {
        'admin'      => $adminMenus,
        'supervisor' => $supervisorMenus,
        default      => $userMenus,
    };

    $roleLabel = match($role) {
        'admin'      => 'Administrator',
        'supervisor' => 'Supervisor FMD',
        default      => 'Staff / User',
    };
    ?>

<aside class="sidebar" id="sidebar">
  <!-- Nav -->
  <nav class="sidebar-nav">
    <?php $dashboard = array_shift($menus); ?>
    
    <!-- Nav Item - Dashboard -->
    <button class="nav-item <?= $activeView === $dashboard['id'] ? 'active' : '' ?>" data-view="<?= htmlspecialchars($dashboard['id']) ?>" onclick="switchView('<?= htmlspecialchars($dashboard['id']) ?>')">
      <?= getSvgIcon($dashboard['icon']) ?>
      <span><?= htmlspecialchars($dashboard['label']) ?></span>
    </button>

    <hr class="sidebar-divider">

    <div class="sidebar-section-label">Interface</div>

    <?php foreach ($menus as $menu): ?>
      <?php if ($menu['id'] === 'profile' || $menu['id'] === 'my_reports' || strpos($menu['id'], 'switch_') === 0): ?>
        <hr class="sidebar-divider">
        <div class="sidebar-section-label">
            <?php 
                if (strpos($menu['id'], 'switch_') === 0) echo 'System View';
                else if ($menu['id'] === 'profile') echo 'Settings';
                else echo 'Reports';
            ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($menu['url'])): ?>
        <a href="<?= $menu['url'] ?>" class="nav-item <?= $activeView === $menu['id'] ? 'active' : '' ?>" style="text-decoration:none;">
          <?= getSvgIcon($menu['icon']) ?>
          <span><?= htmlspecialchars($menu['label']) ?></span>
        </a>
      <?php else: ?>
        <button class="nav-item <?= $activeView === $menu['id'] ? 'active' : '' ?>" data-view="<?= htmlspecialchars($menu['id']) ?>" onclick="switchView('<?= htmlspecialchars($menu['id']) ?>')">
          <?= getSvgIcon($menu['icon']) ?>
          <span><?= htmlspecialchars($menu['label']) ?></span>
          <?php if (isset($menu['badge'])): ?>
            <span class="nav-badge-count" id="<?= htmlspecialchars($menu['badge']) ?>">...</span>
          <?php endif; ?>
        </button>
      <?php endif; ?>
    <?php endforeach; ?>

  </nav>

  <!-- Sidebar Footer: User Info & Logout -->
  <div class="sidebar-footer">
    <div class="sidebar-user-card">
      <div class="user-avatar">
        <?= strtoupper(mb_substr($userName, 0, 1)) ?>
      </div>
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars($userName) ?></div>
        <div class="user-role"><?= $roleLabel ?></div>
      </div>
    </div>
    <form action="<?= BASE_URL ?>/api/logout.php" method="post" style="margin-top:0.75rem;">
      <button type="submit" class="btn btn-logout btn-full">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span style="font-weight:600;">Keluar</span>
      </button>
    </form>
  </div>
</aside>

<?php
} // end renderSidebar()

// ===== SVG ICONS (inline) =====
// Setara dengan: lucide-react icons
function getSvgIcon(string $name): string {
    $paths = [
        'layout'          => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'file-text'       => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
        'history'         => '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.98"/>',
        'users'           => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'trending-up'     => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
        'bar-chart-2'     => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
        'user'            => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'car'             => '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'building2'       => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/>',
        'video'           => '<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>',
        'wrench'          => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'package'         => '<line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        'activity'        => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
        'clipboard-check' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'hammer'          => '<path d="m15 12-8.5 8.5c-.83.83-2.17.83-3 0 0 0 0 0 0 0a2.12 2.12 0 0 1 0-3L12 9"/><path d="M17.64 15 22 10.64"/><path d="m20.91 11.7-1.25-1.25c-.6-.6-.93-1.4-.93-2.25v-.86L16.01 4.6a5.56 5.56 0 0 0-3.94-1.64H9l.92.82A6.18 6.18 0 0 1 12 8.4v1.56l2 2h2.47l2.26 1.91"/>',
        'search'          => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'arrow-left'      => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
        'plus'            => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'trash'           => '<polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
        'edit'            => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
        'check-circle'    => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        'x-circle'        => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
        'alert-circle'    => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
        'printer'         => '<polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
        'wallet'          => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'eye'             => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'calendar'        => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'bell'            => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
    ];
    $path = $paths[$name] ?? '<circle cx="12" cy="12" r="10"/>';
    return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

// ===== HTML HEAD & COMMON TOPBAR =====
function renderPageHead(string $title, string $basePath = '../'): void {
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title) ?> — BIOTROP Facility System</title>
  <meta name="description" content="<?= htmlspecialchars($title) ?> - SEAMEO BIOTROP Facility Management System" />
  <link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css?v=1.1" />
  <link rel="icon" href="<?= $basePath ?>assets/img/logo.png" type="image/png" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <?php
    global $conn;
    $masterVehicles = [];
    $masterRooms = [];
    $masterDormitories = [];
    if ($conn) {
        $resV = $conn->query("SELECT id, name FROM master_vehicles ORDER BY id ASC");
        if ($resV) { while($row = $resV->fetch_assoc()) $masterVehicles[] = $row; }
        
        $resR = $conn->query("SELECT id, name FROM master_rooms ORDER BY id ASC");
        if ($resR) { while($row = $resR->fetch_assoc()) $masterRooms[] = $row; }

        $resD = $conn->query("SELECT id, name FROM master_dormitories ORDER BY id ASC");
        if ($resD) { while($row = $resD->fetch_assoc()) $masterDormitories[] = $row; }
    }
  ?>
  <script>
    const ALL_VEHICLES = <?= json_encode($masterVehicles) ?>;
    const VEHICLE_MAP = Object.fromEntries(ALL_VEHICLES.map(v => [v.id, v.name]));
    
    const ALL_ROOMS = <?= json_encode($masterRooms) ?>;
    const ROOM_MAP = Object.fromEntries(ALL_ROOMS.map(r => [r.id, r.name]));

    const ALL_DORMITORIES = <?= json_encode($masterDormitories) ?>;
    const DORMITORY_MAP = Object.fromEntries(ALL_DORMITORIES.map(r => [r.id, r.name]));
  </script>
</head>
<body>
<div class="top-bar"></div>
<?php
} // end renderPageHead()
