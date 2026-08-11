<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warehouse_admin') {
    header("Location: login.php");
    exit;
}

$userName = $_SESSION['full_name'];
$employeeId = $_SESSION['employee_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Inventaris Aset</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/globals.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --primary: #16a34a;
      --primary-dark: #15803d;
      --accent: #f59e0b;
      --bg-color: #f1f5f9;
      --surface: #ffffff;
      --text: #1e293b;
      --text-light: #64748b;
      --text-white: #ffffff;
      --border: #e2e8f0;
      --danger: #ef4444;
      --success: #10b981;
      --warning: #f59e0b;
      --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      --shadow-sm: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    
    *, *::before, *::after {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: var(--bg-color);
      color: var(--text);
      margin: 0;
      min-height: 100vh;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
    }

    /* ================= ICTBConference-style Header ================= */
    .header {
      position: sticky;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      background: rgba(255, 255, 255, 0.97);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 0;
      transition: var(--transition);
      border-bottom: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 1.5rem;
      height: 64px;
    }

    /* Logo */
    .navbar-left, .navbar-right {
      display: flex;
      align-items: center;
      height: 100%;
    }
    .custom-logo {
      display: flex;
      align-items: center;
      text-decoration: none;
      gap: 0.75rem;
      margin-right: 2rem;
    }
    .custom-logo-icon {
      width: 36px; height: 36px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      transition: var(--transition);
    }
    .custom-logo-text {
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--text);
      letter-spacing: -0.01em;
      transition: var(--transition);
    }

    /* ================= Nav Links (ICTBConference-style) ================= */
    .nav-links {
      display: flex;
      gap: 0;
      list-style: none;
      margin: 0;
      padding: 0;
      height: 100%;
    }
    .nav-links > li {
      position: relative;
      display: flex;
      align-items: center;
      height: 100%;
    }
    .nav-link {
      font-size: 12px;
      font-weight: 600;
      color: var(--text);
      transition: var(--transition);
      text-transform: uppercase;
      letter-spacing: 0.8px;
      position: relative;
      cursor: pointer;
      padding: 0 14px;
      display: flex;
      align-items: center;
      gap: 6px;
      height: 100%;
      text-decoration: none;
      white-space: nowrap;
    }
    .nav-link svg {
      width: 16px;
      height: 16px;
      opacity: 0.7;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 14px;
      right: 14px;
      height: 2px;
      background: var(--primary);
      transition: var(--transition);
      transform: scaleX(0);
      transform-origin: center;
    }
    .nav-link:hover::after,
    .nav-link.active::after {
      transform: scaleX(1);
    }
    .nav-link:hover,
    .nav-link.active {
      color: var(--primary);
    }

    /* ================= Dropdown Menu (ICTBConference-style) ================= */
    .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      background-color: var(--surface);
      min-width: 240px;
      box-shadow: var(--shadow-sm);
      opacity: 0;
      visibility: hidden;
      transform: translateY(8px);
      transition: var(--transition);
      padding: 8px 0;
      z-index: 100;
      border-top: 3px solid var(--primary);
      border-radius: 0 0 8px 8px;
      list-style: none;
    }
    .dropdown-menu::before {
      content: '';
      position: absolute;
      top: -15px;
      left: 0;
      width: 100%;
      height: 15px;
      background: transparent;
    }
    .has-dropdown:hover > .dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .dropdown-menu li {
      display: block;
      width: 100%;
      position: relative;
    }
    .dropdown-menu li a,
    .dropdown-menu li .dropdown-item {
      display: block;
      padding: 10px 20px;
      color: var(--text-light);
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      transition: var(--transition);
      cursor: pointer;
      text-decoration: none;
      white-space: nowrap;
    }
    .dropdown-menu li a:hover,
    .dropdown-menu li .dropdown-item:hover {
      background-color: var(--bg-color);
      color: var(--primary);
      padding-left: 25px;
    }
    .dropdown-menu li .dropdown-item.active {
      color: var(--primary);
      background-color: #eff6ff;
    }

    /* Nested Sub-Dropdown (level 2+) */
    .dropdown-menu .has-sub-dropdown {
      position: relative;
    }
    .sub-dropdown-menu {
      position: absolute;
      top: -3px;
      left: 100%;
      background-color: var(--surface);
      min-width: 220px;
      box-shadow: var(--shadow-sm);
      opacity: 0;
      visibility: hidden;
      transform: translateX(8px);
      transition: var(--transition);
      padding: 8px 0;
      z-index: 101;
      border-top: 3px solid var(--primary);
      border-radius: 0 0 8px 8px;
      list-style: none;
    }
    .dropdown-menu .has-sub-dropdown:hover > .sub-dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateX(0);
    }
    .sub-dropdown-menu li a,
    .sub-dropdown-menu li .dropdown-item {
      padding: 10px 20px;
    }

    /* Last dropdown align right */
    .nav-links > li:nth-last-child(-n+2) > .dropdown-menu {
      left: auto;
      right: 0;
    }

    /* ================= Nav Right (User Area + UAKPB) ================= */
    .nav-right-area {
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }
    .uakpb-filter {
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    .uakpb-filter label {
      font-weight: 600;
      font-size: 0.8rem;
      color: var(--text-light);
      white-space: nowrap;
    }
    .uakpb-filter select {
      width: 180px;
      padding: 6px 10px;
      border: 1px solid var(--border);
      border-radius: 6px;
      font-family: inherit;
      font-size: 0.8rem;
      background: var(--surface);
    }
    .user-profile-container {
      position: relative;
      cursor: pointer;
    }
    .user-profile {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.25rem;
      border-radius: 2rem;
      transition: var(--transition);
    }
    .user-profile:hover {
      background: #f1f5f9;
    }
    .user-avatar {
      width: 34px; height: 34px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700;
      font-size: 0.85rem;
    }
    .user-info {
      text-align: right;
    }
    .user-info .user-name {
      font-weight: 600;
      font-size: 0.8rem;
      color: var(--text);
      white-space: nowrap;
    }
    .user-info .user-role {
      font-size: 0.65rem;
      color: var(--text-light);
      white-space: nowrap;
    }
    .user-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      margin-top: 0.5rem;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 0.75rem;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      min-width: 150px;
      padding: 0.5rem;
      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);
      transition: var(--transition);
      z-index: 1000;
    }
    .user-profile-container:hover .user-dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .btn-logout {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.85rem;
      color: var(--danger);
      text-decoration: none;
      font-weight: 600;
      padding: 0.5rem 0.75rem;
      border-radius: 0.5rem;
      transition: var(--transition);
    }
    .btn-logout:hover {
      background: #fef2f2;
    }

    /* ================= Mobile Menu ================= */
    .mobile-menu-btn {
      display: none;
      font-size: 24px;
      cursor: pointer;
      color: var(--text);
      transition: var(--transition);
      background: none;
      border: none;
      padding: 4px;
    }

    /* ================= Main Content ================= */
    .main-content {
      padding: 2rem;
      width: 100%;
      max-width: 1400px;
      margin: 0 auto;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      box-sizing: border-box;
    }
    .content-area {
      display: none;
    }
    .content-area.active {
      display: block;
      animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Cards & Grids */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    .stat-card {
      background: var(--surface);
      padding: 1.5rem;
      border-radius: 1rem;
      border: 1px solid var(--border);
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .stat-icon.blue { background: #dbeafe; color: var(--primary); }
    .stat-icon.red { background: #fee2e2; color: var(--danger); }
    .stat-icon.green { background: #d1fae5; color: var(--success); }
    .stat-icon.yellow { background: #fef3c7; color: var(--warning); }
    
    .stat-details h3 { font-size: 0.875rem; color: var(--text-light); margin: 0 0 0.25rem 0; }
    .stat-details p { font-size: 1.5rem; font-weight: 700; color: var(--text); margin: 0; }

    /* Tables */
    .card {
      background: var(--surface);
      border-radius: 1rem;
      border: 1px solid var(--border);
      padding: 1.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    .card-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin: 0;
    }
    .table-responsive {
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
    }
    th {
      font-weight: 600;
      color: var(--text-light);
      background: #f8fafc;
    }
    tr:hover { background: #f8fafc; }
    .badge {
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 500;
    }
    .badge.in { background: #d1fae5; color: var(--success); }
    .badge.out { background: #fee2e2; color: var(--danger); }
    
    /* Forms */
    .form-group {
      margin-bottom: 1rem;
    }
    .form-label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
    }
    .form-input, .form-select {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: 0.5rem;
      font-family: inherit;
      box-sizing: border-box;
    }
    .form-input:focus, .form-select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .btn {
      padding: 0.75rem 1.5rem;
      border-radius: 0.5rem;
      font-weight: 500;
      border: none;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      justify-content: center;
    }
    .btn-primary { background: var(--primary); color: white; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }
    .btn-danger { background: var(--danger); color: white; }
    .btn-danger:hover { background: #dc2626; }
    
    /* Search Select */
    .search-select-wrapper {
      position: relative;
    }
    .search-select-dropdown {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid var(--border);
      border-radius: 0.5rem;
      max-height: 200px;
      overflow-y: auto;
      z-index: 10;
      display: none;
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .search-select-item {
      padding: 0.75rem;
      cursor: pointer;
      border-bottom: 1px solid #f1f5f9;
    }
    .search-select-item:hover {
      background: #f8fafc;
    }
    
    /* Pagination */
    .pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid var(--border);
    }
    .pagination-info {
      font-size: 0.875rem;
      color: var(--text-light);
    }
    .pagination-controls {
      display: flex;
      gap: 0.5rem;
    }
    .btn-page {
      padding: 0.5rem 0.75rem;
      border: 1px solid var(--border);
      background: white;
      border-radius: 0.25rem;
      cursor: pointer;
      font-size: 0.875rem;
    }
    .btn-page:hover:not(:disabled) {
      background: #f1f5f9;
    }
    .btn-page:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
    
    /* Modal */
    .modal-overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s;
      backdrop-filter: blur(4px);
    }
    .modal-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }
    .modal-content {
      background: var(--surface);
      border-radius: 1rem;
      width: 100%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
      padding: 2rem;
      position: relative;
      transform: translateY(-20px);
      transition: transform 0.2s;
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
    }
    .modal-overlay.active .modal-content {
      transform: translateY(0);
    }
    .modal-close {
      position: absolute;
      top: 1.5rem;
      right: 1.5rem;
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--text-light);
      transition: color 0.2s;
    }
    .modal-close:hover {
      color: var(--danger);
    }
    
    .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      color: var(--text);
      letter-spacing: -0.02em;
    }

    /* ================= Responsive ================= */
    @media (max-width: 1100px) {
      .uakpb-filter { display: none; }
    }
    @media (max-width: 960px) {
      .mobile-menu-btn {
        display: block;
        margin-left: 10px;
      }
      .nav-links {
        display: none;
        position: fixed;
        top: 64px;
        left: 0;
        right: 0;
        background: var(--surface);
        flex-direction: column;
        padding: 20px 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        gap: 0;
        border-top: 1px solid var(--border);
        max-height: calc(100vh - 64px);
        overflow-y: auto;
        z-index: 999;
      }
      .nav-links.active {
        display: flex;
      }
      .nav-links > li {
        width: 100%;
        height: auto;
        flex-direction: column;
        align-items: stretch;
      }
      .nav-link {
        padding: 12px 0;
        font-size: 13px;
        height: auto;
      }
      .nav-link::after {
        display: none;
      }
      .nav-links.active .dropdown-menu,
      .nav-links.active .sub-dropdown-menu {
        position: static;
        box-shadow: none;
        border-top: none;
        border-radius: 0;
        padding-left: 15px;
        display: none;
        opacity: 1;
        visibility: visible;
        transform: none;
        margin-top: 0;
        background: transparent;
        min-width: unset;
      }
      .nav-links.active .has-dropdown:hover > .dropdown-menu,
      .nav-links.active .has-sub-dropdown:hover > .sub-dropdown-menu {
        display: block;
      }
      .nav-links.active .dropdown-menu::before {
        display: none;
      }
      .nav-links.active .dropdown-menu li a,
      .nav-links.active .dropdown-menu li .dropdown-item,
      .nav-links.active .sub-dropdown-menu li a,
      .nav-links.active .sub-dropdown-menu li .dropdown-item {
        padding: 8px 10px;
        font-size: 11px;
      }
      .nav-right-area {
        gap: 0.5rem;
      }
      .user-info { display: none; }
    }

    @media (max-width: 600px) {
      .custom-logo-text { display: none; }
      .nav-container { padding: 0 1rem; }
      .user-avatar { width: 30px; height: 30px; }
    }
  </style>
</head>
<body>

  <!-- Header (ICTBConference Style) -->
  <header class="header" id="header">
    <div class="nav-container">
      <!-- Logo -->
      <div class="navbar-left">
        <a href="#" class="custom-logo" onclick="switchTab('dashboard', document.querySelector('.nav-link')); return false;">
          <div class="custom-logo-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
              <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
              <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
          </div>
          <span class="custom-logo-text">Inventaris</span>
        </a>

        <!-- Desktop Navigation -->
        <ul class="nav-links" id="nav-links">
          <li>
            <a class="nav-link active" onclick="switchTab('dashboard', this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
              DASHBOARD
            </a>
          </li>
          <li class="has-dropdown">
            <a class="nav-link">REFERENSI ▾</a>
            <ul class="dropdown-menu">
              <li><div class="dropdown-item" onclick="switchTab('master', this, 'Tabel Barang')">Tabel Barang</div></li>
              <li><div class="dropdown-item" onclick="switchTab('uakpb', this, 'Tabel UAKPB')">Tabel UAKPB</div></li>
            </ul>
          </li>
          <li class="has-dropdown">
            <a class="nav-link">TRANSAKSI ▾</a>
            <ul class="dropdown-menu">
              <li class="has-sub-dropdown">
                <div class="dropdown-item">Persediaan Masuk ▸</div>
                <ul class="sub-dropdown-menu">
                  <li><div class="dropdown-item" onclick="switchTab('inbound', this, 'Saldo Awal')">Saldo Awal</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('inbound', this, 'Pembelian')">Pembelian</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('inbound', this, 'Transfer Masuk')">Transfer Masuk</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('inbound', this, 'Hibah Masuk')">Hibah Masuk</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('inbound', this, 'Perolehan Lainnya')">Perolehan Lainnya</div></li>
                </ul>
              </li>
              <li class="has-sub-dropdown">
                <div class="dropdown-item">Persediaan Keluar ▸</div>
                <ul class="sub-dropdown-menu">
                  <li><div class="dropdown-item" onclick="switchTab('outbound', this, 'Pemakaian')">Pemakaian</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('outbound', this, 'Transfer Keluar')">Transfer Keluar</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('outbound', this, 'Hibah Keluar')">Hibah Keluar</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('outbound', this, 'Usang')">Usang</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('outbound', this, 'Rusak')">Rusak</div></li>
                </ul>
              </li>
              <li class="has-sub-dropdown">
                <div class="dropdown-item">Lainnya ▸</div>
                <ul class="sub-dropdown-menu">
                  <li><div class="dropdown-item" onclick="switchTab('koreksi', this, 'Koreksi')">Koreksi</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('development', this, 'Hasil Opname Fisik')">Hasil Opname Fisik</div></li>
                  <li><div class="dropdown-item" onclick="switchTab('development', this, 'Penghapusan Usang/Rusak')">Penghapusan</div></li>
                </ul>
              </li>
            </ul>
          </li>
          <li class="has-dropdown">
            <a class="nav-link">LAPORAN ▾</a>
            <ul class="dropdown-menu">
              <li><div class="dropdown-item" onclick="switchTab('development', this, 'Buku Persediaan')">Buku Persediaan</div></li>
              <li><div class="dropdown-item" onclick="switchTab('development', this, 'Laporan Persediaan')">Laporan Persediaan</div></li>
              <li><div class="dropdown-item" onclick="switchTab('laporan-rincian', this, 'Laporan Rincian Persediaan')">Lap. Rincian Persediaan</div></li>
              <li><div class="dropdown-item" onclick="switchTab('development', this, 'Laporan Posisi Persediaan di Neraca')">Posisi Neraca</div></li>
              <li><div class="dropdown-item" onclick="switchTab('history', this, 'Riwayat Transaksi')">Riwayat Transaksi</div></li>
            </ul>
          </li>
          <li>
            <a class="nav-link" onclick="switchTab('development', this, 'Utility')">UTILITY</a>
          </li>
        </ul>
      </div>
      
      <!-- Right Side -->
      <div class="navbar-right">
        <div class="nav-right-area">
          <div class="uakpb-filter">
            <label for="global-uakpb-filter">UAKPB:</label>
            <select id="global-uakpb-filter" onchange="onGlobalUakpbChange()">
              <option value="all">-- Semua --</option>
            </select>
          </div>
          <div class="user-profile-container">
            <div class="user-profile">
              <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($userName) ?></div>
                <div class="user-role">Admin Gudang</div>
              </div>
              <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
            </div>
            <div class="user-dropdown">
              <a href="logout.php" class="btn-logout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
              </a>
            </div>
          </div>
        </div>

        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" id="mobile-menu-btn" onclick="document.getElementById('nav-links').classList.toggle('active')">
          ☰
        </button>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="main-content">

    <!-- DASHBOARD VIEW -->
    <div id="view-dashboard" class="content-area active">
      <h2 class="page-title">Dashboard Inventaris</h2>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg></div>
          <div class="stat-details">
            <h3>Total Jenis Barang</h3>
            <p id="stat-total-items">0</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>
          <div class="stat-details">
            <h3>Stok Menipis</h3>
            <p id="stat-low-stock">0</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg></div>
          <div class="stat-details">
            <h3>Barang Masuk (Hari Ini)</h3>
            <p id="stat-today-in">0</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg></div>
          <div class="stat-details">
            <h3>Barang Keluar (Hari Ini)</h3>
            <p id="stat-today-out">0</p>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Transaksi Terbaru</h3>
          <button class="btn btn-primary" onclick="switchTab('history', null, 'Riwayat Transaksi')" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Lihat Semua</button>
        </div>
        <div class="table-responsive">
          <table id="table-dashboard-tx">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Tipe</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <!-- Injected via JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MASTER VIEW -->
    <div id="view-master" class="content-area">
      <h2 class="page-title">Master Stok Barang</h2>
      <div class="card">
        <div class="card-header">
          <div>
            <input type="text" id="search-master" class="form-input" style="max-width: 300px; display:inline-block; margin-right: 1rem;" placeholder="Cari nama atau kode barang..." oninput="onSearchMaster()">
          </div>
          <button class="btn btn-primary" onclick="openAddMasterModal()">+ Tambah Barang Baru</button>
        </div>
        <div class="table-responsive">
          <table id="table-master">
            <thead>
              <tr>
                <th>Kode</th>
                <th>Kategori</th>
                <th>Nama Barang</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Min Stok</th>
              </tr>
            </thead>
            <tbody>
              <!-- Injected via JS -->
            </tbody>
          </table>
        </div>
        <div class="pagination" id="master-pagination">
          <div class="pagination-info" id="master-page-info">Menampilkan 0-0 dari 0 data</div>
          <div class="pagination-controls">
            <button class="btn-page" id="btn-prev-page" onclick="changeMasterPage(-1)">Sebelumnya</button>
            <button class="btn-page" id="btn-next-page" onclick="changeMasterPage(1)">Selanjutnya</button>
          </div>
        </div>
      </div>
    </div>

    <!-- UAKPB VIEW -->
    <div id="view-uakpb" class="content-area">
      <h2 class="page-title">Daftar Kuasa Pengguna Barang</h2>
      <div class="card">
        <div class="table-responsive">
          <table id="table-uakpb">
            <thead>
              <tr>
                <th>ID</th>
                <th>Kode Lokasi</th>
                <th>Uraian UAKPB</th>
              </tr>
            </thead>
            <tbody>
              <!-- Injected via JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TRANSACTION LIST VIEW -->
    <div id="view-transaction-list" class="content-area">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="page-title" style="margin-bottom:0;"><span id="tx-list-title">Persediaan Masuk</span> <span id="tx-list-subtitle" style="font-weight:400; font-size:1.2rem; color:var(--text-light); margin-left:0.5rem;"></span></h2>
        <div>
          <button class="btn" style="border: 1px solid var(--border); background: white; margin-right: 0.5rem;" onclick="openCetakModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:text-bottom; margin-right:4px;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak RTH
          </button>
          <button class="btn btn-primary" onclick="openTransactionForm()">+ Tambah Dokumen</button>
        </div>
      </div>
      <div class="card">
        <div class="table-responsive">
          <table id="table-tx-list" style="width: 100%; border-collapse: collapse;">
            <thead>
              <tr style="border-bottom: 1px solid var(--border);">
                <th style="padding: 1rem; text-align: left;">No. Dokumen</th>
                <th style="padding: 1rem; text-align: left;">Tgl Dokumen</th>
                <th style="padding: 1rem; text-align: left;">No. Bukti</th>
                <th style="padding: 1rem; text-align: right;">Total Item</th>
                <th style="padding: 1rem; text-align: right;">Total Harga (Rp)</th>
              </tr>
            </thead>
            <tbody id="tx-list-tbody">
              <!-- Injected via JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TRANSACTION FORM VIEW -->
    <div id="view-transaction-form" class="content-area">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="page-title" style="margin-bottom:0;">Form Tambah Dokumen <span id="tx-form-type-label" style="font-weight:400; font-size:1.2rem; color:var(--text-light);"></span></h2>
        <button class="btn" onclick="closeTransactionForm()" style="border:1px solid var(--border); background:white;">Kembali</button>
      </div>
      
      <div class="card" style="margin-bottom: 1rem; background: #f8fafc;">
        <h3 style="margin-top:0; font-size:1.1rem; border-bottom:1px solid var(--border); padding-bottom:0.5rem; margin-bottom:1rem;">Header Dokumen</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
          <div class="form-group" style="flex: 1; min-width: 250px;">
            <label class="form-label">No. Dokumen</label>
            <input type="text" id="tx-form-nodok" class="form-input" readonly style="background:#e2e8f0; font-weight:bold; color:var(--primary);">
            <small style="color:var(--text-light); display:block; margin-top:0.25rem;">(Dihasilkan otomatis)</small>
          </div>
          <div class="form-group" style="flex: 1; min-width: 200px;" id="koreksi-radio-group" style="display:none;">
            <label class="form-label">Jenis Transaksi Persediaan</label>
            <div style="display:flex; gap:1rem; align-items:center; height: 42px;">
              <label style="cursor:pointer;"><input type="radio" name="koreksi_type" value="in" checked onchange="handleKoreksiTypeChange()"> Masuk</label>
              <label style="cursor:pointer;"><input type="radio" name="koreksi_type" value="out" onchange="handleKoreksiTypeChange()"> Keluar</label>
            </div>
          </div>
          <div class="form-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">No. Bukti/BAST</label>
            <input type="text" id="tx-form-nobukti" class="form-input" placeholder="Misal: BAST/01/2026">
          </div>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
          <div class="form-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Tanggal Dokumen</label>
            <input type="date" id="tx-form-tgldok" class="form-input" required>
          </div>
          <div class="form-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Tanggal Buku</label>
            <input type="date" id="tx-form-tglbuku" class="form-input" required>
          </div>
          <div class="form-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Keterangan / Akun</label>
            <input type="text" id="tx-form-keterangan" class="form-input" placeholder="Tujuan/Keterangan mutasi">
          </div>
        </div>
      </div>

      <div class="card" style="margin-bottom: 1rem;">
        <h3 style="margin-top:0; font-size:1.1rem; border-bottom:1px solid var(--border); padding-bottom:0.5rem; margin-bottom:1rem;">Detail Barang</h3>
        
        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
          <div class="form-group search-select-wrapper" style="flex: 2; min-width: 300px;">
            <label class="form-label">Cari Nama/Kode Barang</label>
            <input type="text" id="tx-item-search" class="form-input" placeholder="Ketik nama atau kode barang..." autocomplete="off">
            <input type="hidden" id="tx-item-id">
            <input type="hidden" id="tx-item-name">
            <input type="hidden" id="tx-item-code">
            <input type="hidden" id="tx-item-stock">
            <input type="hidden" id="tx-item-location-id">
            <div id="tx-item-dropdown" class="search-select-dropdown"></div>
          </div>
          <div class="form-group" style="flex: 1; min-width: 150px;">
            <label class="form-label">Jumlah <span id="tx-stock-label" style="font-size:0.8rem; color:var(--danger);"></span></label>
            <input type="number" id="tx-item-qty" class="form-input" min="1">
          </div>
          <div class="form-group" style="flex: 1; min-width: 150px;">
            <label class="form-label">Harga Satuan (Rp)</label>
            <input type="number" id="tx-item-price" class="form-input" min="0" value="0">
          </div>
          <div class="form-group" style="flex: 1; display:flex; align-items:flex-end;">
            <button class="btn btn-primary" type="button" onclick="addTxItem()" style="width:100%;">Tambahkan</button>
          </div>
        </div>
        
        <div class="table-responsive" style="margin-top: 1rem;">
          <table id="table-tx-items" style="border: 1px solid var(--border);">
            <thead style="background: var(--surface);">
              <tr>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="tx-items-tbody">
              <!-- Added items will appear here -->
              <tr id="tx-empty-row"><td colspan="6" style="text-align:center; color:var(--text-light);">Belum ada barang ditambahkan.</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div style="text-align:right;">
        <button class="btn btn-primary" onclick="submitTransaction()" style="padding: 0.75rem 2rem; font-size: 1.1rem; background: var(--success); color: white; border:none;" id="btn-submit-tx">Simpan Dokumen</button>
      </div>
    </div>

    <!-- HISTORY VIEW -->
    <div id="view-history" class="content-area">
      <h2 class="page-title">Riwayat Transaksi</h2>
      <div class="card">
        <div class="table-responsive">
          <table id="table-history">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Tipe</th>
                <th>Sub-Tipe</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Referensi</th>
                <th>PIC</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody>
              <!-- Injected via JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- LAPORAN RINCIAN VIEW -->
    <div id="view-laporan-rincian" class="content-area">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="page-title" style="margin-bottom:0;">Laporan Rincian Persediaan UAKPB</h2>
      </div>
      <div class="card" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
        <form id="form-cetak-laporan" target="_blank" action="print_laporan.php" method="GET" onsubmit="return appendGlobalLocationId(this)">
          <input type="hidden" name="location_id" id="print-laporan-loc">
          
          <div style="border: 1px solid var(--border); padding: 1rem; display: flex; gap: 1.5rem; justify-content: center; margin-bottom: 2rem; background: #f8fafc; border-radius: 0.5rem;">
            <label style="cursor:pointer; display:flex; align-items:center; gap:0.5rem;">
              <input type="radio" name="periode_type" value="sd_tanggal" onchange="toggleLaporanPeriode()" checked> S/D Tanggal
            </label>
            <label style="cursor:pointer; display:flex; align-items:center; gap:0.5rem;">
              <input type="radio" name="periode_type" value="semester" onchange="toggleLaporanPeriode()"> Semester
            </label>
            <label style="cursor:pointer; display:flex; align-items:center; gap:0.5rem;">
              <input type="radio" name="periode_type" value="tahun" onchange="toggleLaporanPeriode()"> Tahun
            </label>
          </div>
          
          <!-- Periode Inputs -->
          <div id="lap-sd-tanggal" class="form-group" style="margin-bottom: 2rem;">
            <label class="form-label">Sampai Dengan Tanggal</label>
            <input type="date" name="sd_tanggal_val" class="form-input" style="max-width: 250px;">
          </div>
          
          <div id="lap-semester" class="form-group" style="margin-bottom: 2rem; display: none; gap: 1rem;">
            <div style="flex: 1; max-width: 200px;">
              <label class="form-label">Semester</label>
              <select name="semester_val" class="form-select">
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
              </select>
            </div>
            <div style="flex: 1; max-width: 200px;">
              <label class="form-label">Tahun</label>
              <input type="number" name="semester_tahun_val" class="form-input" value="2026" min="2000" max="2099">
            </div>
          </div>
          
          <div id="lap-tahun" class="form-group" style="margin-bottom: 2rem; display: none;">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun_val" class="form-input" value="2026" min="2000" max="2099" style="max-width: 200px;">
          </div>
          
          <hr style="border: 0; border-top: 1px solid var(--border); margin: 2rem 0;">
          
          <!-- Signature Dates -->
          <div style="display: flex; gap: 2rem; margin-bottom: 2rem;">
            <div class="form-group" style="flex: 1;">
              <label class="form-label">Tanggal Isi</label>
              <input type="date" name="tanggal_isi" class="form-input">
            </div>
            <div class="form-group" style="flex: 1;">
              <label class="form-label">Tanggal Setuju</label>
              <input type="date" name="tanggal_setuju" class="form-input">
            </div>
          </div>
          
          <div style="display: flex; gap: 1rem; justify-content: center;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1.1rem;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:bottom; margin-right:8px;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
              Cetak
            </button>
            <button type="button" class="btn" style="padding: 0.75rem 2rem; font-size: 1.1rem; border:1px solid var(--border); background:white;">Keluar</button>
          </div>
          
        </form>
      </div>
    </div>

    <!-- DEVELOPMENT VIEW -->
    <div id="view-development" class="content-area">
      <h2 class="page-title" id="dev-title">Sedang Dikembangkan</h2>
      <div class="card" style="text-align: center; padding: 4rem 2rem;">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.5" style="margin-bottom: 1rem;"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        <h3 style="font-size: 1.5rem; color: var(--text); margin-bottom: 0.5rem;">Fitur Belum Tersedia</h3>
        <p style="color: var(--text-light); max-width: 400px; margin: 0 auto;">Fitur ini masih dalam tahap pengembangan dan akan segera hadir pada update berikutnya.</p>
      </div>
    </div>

  </main>

  <!-- CETAK RTH MODAL -->
  <div class="modal-overlay" id="cetak-modal">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
      <h3 style="margin-top:0; color: var(--primary); font-size: 1.5rem; margin-bottom: 0.5rem;">Cetak Register Transaksi Harian</h3>
      <p style="color: var(--text-light); margin-bottom: 2rem; font-size: 0.95rem;">Pilih periode transaksi yang ingin Anda cetak.</p>
      
      <form id="form-cetak" target="_blank" action="print_rth.php" method="GET" onsubmit="return appendGlobalLocationId(this)">
        <input type="hidden" name="location_id" id="print-rth-loc">
        <input type="hidden" name="type" id="cetak-type">
        <input type="hidden" name="subtype" id="cetak-subtype">
        
        <div style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 1.5rem;">
          <label style="cursor:pointer; font-size:1.1rem; display:flex; align-items:center; gap:0.5rem;">
            <input type="radio" name="period_type" value="month" checked onchange="toggleCetakPeriod()"> Bulan
          </label>
          <label style="cursor:pointer; font-size:1.1rem; display:flex; align-items:center; gap:0.5rem;">
            <input type="radio" name="period_type" value="year" onchange="toggleCetakPeriod()"> Tahun
          </label>
        </div>
        
        <div class="form-group" id="cetak-month-group" style="margin-bottom: 1.5rem;">
          <select name="month" class="form-select" style="font-size: 1.1rem; padding: 0.75rem;">
            <option value="01">Januari</option>
            <option value="02">Februari</option>
            <option value="03">Maret</option>
            <option value="04">April</option>
            <option value="05">Mei</option>
            <option value="06">Juni</option>
            <option value="07">Juli</option>
            <option value="08">Agustus</option>
            <option value="09">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
          </select>
        </div>
        
        <div class="form-group" id="cetak-year-group" style="margin-bottom: 1.5rem; display:none;">
          <select name="year" class="form-select" style="font-size: 1.1rem; padding: 0.75rem;">
            <option value="2026">2026</option>
            <option value="2025">2025</option>
            <option value="2024">2024</option>
          </select>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
          <button type="button" class="btn" style="flex:1; border:1px solid var(--border); background:white;" onclick="closeCetakModal()">Batal</button>
          <button type="submit" class="btn btn-primary" style="flex:1;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:text-bottom; margin-right:4px;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Sekarang
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ADD MASTER MODAL -->
  <div class="modal-overlay" id="add-master-modal">
    <div class="modal-content">
      <button class="modal-close" onclick="closeAddMasterModal()">&times;</button>
      <h3 style="margin-top:0; margin-bottom: 1.5rem; font-size:1.25rem;">Tambah Master Barang Baru</h3>
      
      <form id="form-add-master" onsubmit="submitAddMasterForm(event)">
        <div class="form-group">
          <label class="form-label">Bidang (Level 1)</label>
          <select id="sel-bidang" class="form-select" onchange="loadKelompok()" required>
            <option value="">-- Pilih Bidang --</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Kelompok (Level 2)</label>
          <select id="sel-kelompok" class="form-select" onchange="loadSkel()" disabled required>
            <option value="">-- Pilih Kelompok --</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Sub-Kelompok (Level 3)</label>
          <select id="sel-skel" class="form-select" onchange="loadSskel()" disabled required>
            <option value="">-- Pilih Sub-Kelompok --</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Sub-Sub-Kelompok (Level 4)</label>
          <select id="sel-sskel" class="form-select" onchange="generateCodePrefix()" disabled required>
            <option value="">-- Pilih Sub-Sub-Kelompok --</option>
          </select>
        </div>
        
        <div class="form-group">
          <label class="form-label">Kode Barang (Otomatis)</label>
          <input type="text" id="new-item-code" class="form-input" readonly style="background:#f1f5f9; color: var(--text-light);" placeholder="Prefix kode akan muncul di sini">
        </div>
        
        <div class="form-group">
          <label class="form-label">Nama Barang</label>
          <input type="text" id="new-item-name" class="form-input" required placeholder="Masukkan nama barang">
        </div>
        <div class="form-group">
          <label class="form-label">Satuan</label>
          <input type="text" id="new-item-unit" class="form-input" placeholder="Misal: buah, unit, pak">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:1rem;">Simpan Barang</button>
      </form>
    </div>
  </div>

  <script>
    let masterItems = [];
    let historyTx = [];

    // TABS LOGIC
    function toggleAccordion(el) {
      el.classList.toggle('expanded');
      const submenu = el.nextElementSibling;
      if (submenu && submenu.classList.contains('nav-submenu')) {
        submenu.classList.toggle('open');
      }
    }
    
    function toggleSubmenu(id, el) {
      document.getElementById(id).classList.toggle('open');
      el.classList.toggle('expanded');
    }

    // STATE
    let txType = 'in'; // 'in', 'out', or 'koreksi'
    let currentFormTxType = 'in'; // 'in' or 'out' (actual type for DB)
    let txSubtype = '';
    let txItems = [];

    function switchTab(tabId, el, subtype = '') {
      document.querySelectorAll('.nav-link, .dropdown-item').forEach(n => n.classList.remove('active'));
      if (el) el.classList.add('active');
      // Close mobile menu if open
      document.getElementById('nav-links').classList.remove('active');
      document.querySelectorAll('.content-area').forEach(c => c.classList.remove('active'));
        // TRANSAKSI
      if (tabId === 'inbound' || tabId === 'outbound' || tabId === 'koreksi') {
        document.getElementById('view-transaction-list').classList.add('active');
        document.getElementById('tx-list-title').textContent = 
          tabId === 'inbound' ? 'Persediaan Masuk' : 
          tabId === 'outbound' ? 'Persediaan Keluar' : 'Persediaan Koreksi';
        document.getElementById('tx-list-subtitle').textContent = subtype ? `- ${subtype}` : '';
        
        txType = tabId === 'inbound' ? 'in' : tabId === 'outbound' ? 'out' : 'koreksi';
        txSubtype = subtype || (tabId === 'koreksi' ? 'Koreksi' : '');
        currentFormTxType = txType === 'out' ? 'out' : 'in';
        
        loadDocumentList();
        return;
      }

        if (tabId === 'uakpb') {
          loadUakpbList();
        }

        document.getElementById('view-' + tabId).classList.add('active');

      if (tabId === 'dashboard') loadDashboard();
      if (tabId === 'master') loadMaster();
      if (tabId === 'history') loadHistory();
      
      // LAPORAN RINCIAN
      if (tabId === 'laporan-rincian') {
        document.getElementById('view-laporan-rincian').classList.add('active');
        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="sd_tanggal_val"]').value = today;
        document.querySelector('input[name="tanggal_isi"]').value = today;
        document.querySelector('input[name="tanggal_setuju"]').value = today;
        return;
      }
      
      if (tabId === 'development') {
        document.getElementById('dev-title').textContent = subtype ? subtype : 'Sedang Dikembangkan';
      }
    }

    // DASHBOARD
    async function loadDashboard() {
      try {
        const formData = new FormData();
        formData.append('action', 'get_dashboard_stats');
        formData.append('location_id', document.getElementById('global-uakpb-filter').value);
        const res = await fetch('api.php', { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          document.getElementById('stat-total-items').textContent = json.data.totalItems;
          document.getElementById('stat-low-stock').textContent = json.data.lowStock;
          document.getElementById('stat-today-in').textContent = json.data.todayIn;
          document.getElementById('stat-today-out').textContent = json.data.todayOut;
        }

        // load brief history for dashboard
        const formData2 = new FormData();
        formData2.append('action', 'get_transactions');
        const res2 = await fetch('api.php?limit=5', { method: 'POST', body: formData2 });
        const txs = await res2.json();
        
        const tbody = document.querySelector('#table-dashboard-tx tbody');
        tbody.innerHTML = '';
        txs.forEach(tx => {
          const typeBadge = tx.type === 'in' 
            ? `<span class="badge in">Masuk</span>` 
            : `<span class="badge out">Keluar</span>`;
          tbody.innerHTML += `
            <tr>
              <td>${new Date(tx.created_at).toLocaleString('id-ID')}</td>
              <td>${typeBadge}</td>
              <td><b>${tx.item_code}</b> - ${tx.item_name}</td>
              <td>${tx.quantity}</td>
              <td>${tx.note || '-'}</td>
            </tr>
          `;
        });
      } catch (err) {
        console.error(err);
      }
    }

    // MASTER ITEMS
    let currentPageMaster = 1;
    const itemsPerPage = 20;

    async function loadUakpbList() {
      try {
        const formData = new FormData();
        formData.append('action', 'get_uakpb');
        const res = await fetch('api.php', { method: 'POST', body: formData });
        const items = await res.json();
        
        const tbody = document.querySelector('#table-uakpb tbody');
        tbody.innerHTML = '';
        if (items.length === 0) {
          tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Tidak ada data UAKPB.</td></tr>';
          return;
        }
        
        items.forEach(it => {
          tbody.innerHTML += `
            <tr>
              <td>${it.id}</td>
              <td>${it.code}</td>
              <td>${it.name}</td>
            </tr>
          `;
        });
      } catch (e) {
        console.error(e);
      }
    }

    async function loadMaster() {
      try {
        const formData = new FormData();
        formData.append('action', 'get_items');
        formData.append('location_id', document.getElementById('global-uakpb-filter').value);
        const res = await fetch('api.php', { method: 'POST', body: formData });
        masterItems = await res.json();
        currentPageMaster = 1;
        renderMasterTable();
      } catch (err) {
        console.error(err);
      }
    }

    function onSearchMaster() {
      currentPageMaster = 1; // Reset to page 1 on search
      renderMasterTable();
    }

    function changeMasterPage(delta) {
      currentPageMaster += delta;
      renderMasterTable();
    }

    function renderMasterTable() {
      const q = document.getElementById('search-master').value.toLowerCase();
      const tbody = document.querySelector('#table-master tbody');
      tbody.innerHTML = '';
      
      // 1. Filter
      const filtered = masterItems.filter(item => 
        (item.name && item.name.toLowerCase().includes(q)) || 
        (item.item_code && item.item_code.toLowerCase().includes(q)) ||
        (item.category_name && item.category_name.toLowerCase().includes(q))
      );

      // 2. Paginate
      const totalItems = filtered.length;
      const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
      if (currentPageMaster > totalPages) currentPageMaster = totalPages;
      if (currentPageMaster < 1) currentPageMaster = 1;

      const startIndex = (currentPageMaster - 1) * itemsPerPage;
      const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
      const paginatedItems = filtered.slice(startIndex, endIndex);

      // 3. Render Table
      if (paginatedItems.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 2rem;">Tidak ada data ditemukan</td></tr>`;
      } else {
        let htmlStr = '';
        paginatedItems.forEach(i => {
          htmlStr += `
            <tr>
              <td>${i.item_code}</td>
              <td>${i.category_name || '-'}</td>
              <td>${i.name}</td>
              <td><b>${i.stock}</b></td>
              <td>${i.unit || '-'}</td>
              <td>${i.min_stock || 0}</td>
            </tr>
          `;
        });
        tbody.innerHTML = htmlStr;
      }

      // 4. Update Pagination UI
      document.getElementById('master-page-info').textContent = 
        `Menampilkan ${totalItems === 0 ? 0 : startIndex + 1}-${endIndex} dari ${totalItems} data`;
      
      document.getElementById('btn-prev-page').disabled = currentPageMaster === 1;
      document.getElementById('btn-next-page').disabled = currentPageMaster === totalPages;
    }

    // HISTORY
    async function loadHistory() {
      try {
        const formData = new FormData();
        formData.append('action', 'get_transactions');
        formData.append('location_id', document.getElementById('global-uakpb-filter').value);
        const res = await fetch('api.php?limit=500', { method: 'POST', body: formData });
        historyTx = await res.json();
        
        const tbody = document.querySelector('#table-history tbody');
        let htmlStr = '';
        historyTx.forEach(tx => {
          const typeBadge = tx.type === 'in' 
            ? `<span class="badge in">Masuk</span>` 
            : `<span class="badge out">Keluar</span>`;
          htmlStr += `
            <tr>
              <td>${new Date(tx.created_at).toLocaleString('id-ID')}</td>
              <td>${typeBadge}</td>
              <td>${tx.transaction_subtype || '-'}</td>
              <td>${tx.item_code}</td>
              <td>${tx.item_name}</td>
              <td><b>${tx.quantity}</b></td>
              <td>${tx.reference_id || '-'}</td>
              <td>${tx.user_name}</td>
              <td>${tx.note || '-'}</td>
            </tr>
          `;
        });
        tbody.innerHTML = htmlStr;
      } catch (err) {
        console.error(err);
      }
    }

    // FORM TRANSACTIONS (MASTER-DETAIL)
    function openTransactionForm() {
        document.getElementById('view-transaction-list').classList.remove('active');
        document.getElementById('view-transaction-form').classList.add('active');
        document.getElementById('tx-form-type-label').textContent = `- ${txSubtype}`;
        
        // Reset Form
        document.getElementById('tx-form-tgldok').value = new Date().toISOString().split('T')[0];
        document.getElementById('tx-form-tglbuku').value = new Date().toISOString().split('T')[0];
        document.getElementById('tx-form-nobukti').value = '';
        document.getElementById('tx-form-keterangan').value = '';
        
        // Koreksi Mode
        if (txType === 'koreksi') {
          document.getElementById('koreksi-radio-group').style.display = 'block';
          document.querySelector('input[name="koreksi_type"][value="in"]').checked = true;
          currentFormTxType = 'in';
        } else {
          document.getElementById('koreksi-radio-group').style.display = 'none';
          currentFormTxType = txType;
        }
        
        // Reset items
        txItems = [];
        renderTxItems();
        
        // Reset Price Input Styles
        resetPriceInputStyle();
        
        // Auto generate No Dok
        generateDocNumber();
    }
    
    function resetPriceInputStyle() {
        const priceInput = document.getElementById('tx-item-price');
        priceInput.removeAttribute('readonly');
        priceInput.style.background = '';
        priceInput.style.color = '';
        priceInput.style.fontWeight = '';
        priceInput.value = '';
    }

    function handleKoreksiTypeChange() {
        currentFormTxType = document.querySelector('input[name="koreksi_type"]:checked').value;
        txItems = [];
        renderTxItems();
        resetPriceInputStyle();
        document.getElementById('tx-item-qty').max = '';
        document.getElementById('tx-item-qty').value = '';
        document.getElementById('tx-item-name').value = '';
        document.getElementById('tx-item-search').value = '';
        generateDocNumber();
    }

    async function generateDocNumber(overrideLocationId = null) {
        try {
          const locId = overrideLocationId || document.getElementById('global-uakpb-filter').value;
          const formData = new FormData();
          formData.append('action', 'generate_doc_number');
          formData.append('type', currentFormTxType);
          formData.append('location_id', locId);
          const res = await fetch('api.php', { method: 'POST', body: formData });
          const json = await res.json();
          if (json.success) {
            document.getElementById('tx-form-nodok').value = json.data;
          }
        } catch (err) {
          console.error('Failed to generate doc number');
        }
    }

    function closeTransactionForm() {
      document.getElementById('view-transaction-form').classList.remove('active');
      document.getElementById('view-transaction-list').classList.add('active');
    }

    function setupSearchDropdown() {
      const input = document.getElementById('tx-item-search');
      const dropdown = document.getElementById('tx-item-dropdown');
      let timeout = null;

      input.addEventListener('input', function() {
        clearTimeout(timeout);
        const q = this.value.trim();
        if(q.length < 2) {
          dropdown.style.display = 'none';
          return;
        }
        
        timeout = setTimeout(async () => {
          let searchLocId = document.getElementById('global-uakpb-filter').value;
          if (typeof txItems !== 'undefined' && txItems.length > 0) {
              searchLocId = txItems[0].locationId;
          }
          const res = await fetch(`api.php?action=search_items&q=${encodeURIComponent(q)}&location_id=${searchLocId}`);
          const items = await res.json();
          
          dropdown.innerHTML = '';
          if(items.length === 0) {
            dropdown.innerHTML = '<div style="padding: 0.5rem; color: #64748b;">Barang tidak ditemukan</div>';
          } else {
            items.forEach(item => {
              const div = document.createElement('div');
              div.className = 'dropdown-item';
              div.textContent = `${item.item_code} - ${item.name} (Stok: ${item.stock})`;
              div.onclick = () => {
                input.value = item.name;
                document.getElementById('tx-item-id').value = item.id;
                document.getElementById('tx-item-name').value = item.name;
                document.getElementById('tx-item-code').value = item.item_code;
                document.getElementById('tx-item-stock').value = item.stock;
                document.getElementById('tx-item-location-id').value = item.location_id;
                
                document.getElementById('tx-stock-label').textContent = `(Stok: ${item.stock})`;
                if(currentFormTxType === 'out') {
                  document.getElementById('tx-item-qty').max = item.stock;
                  
                  const priceInput = document.getElementById('tx-item-price');
                  priceInput.value = item.last_price || 0;
                  priceInput.setAttribute('readonly', 'readonly');
                  priceInput.style.background = '#f1f5f9';
                  priceInput.style.color = 'var(--danger)';
                  priceInput.style.fontWeight = 'bold';
                } else {
                  resetPriceInputStyle();
                }
                
                dropdown.style.display = 'none';

                // Regenerate doc number if it starts with DEFAULT
                const currentDoc = document.getElementById('tx-form-nodok').value;
                if (currentDoc.startsWith('DEFAULT')) {
                    generateDocNumber(item.location_id);
                }
              };
              dropdown.appendChild(div);
            });
          }
          dropdown.style.display = 'block';
        }, 300);
      });

      document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
          dropdown.style.display = 'none';
        }
      });
    }

    setupSearchDropdown();

    function addTxItem() {
      const id = document.getElementById('tx-item-id').value;
      const name = document.getElementById('tx-item-name').value;
      const code = document.getElementById('tx-item-code').value;
      const stock = parseInt(document.getElementById('tx-item-stock').value || 0);
      const qty = parseInt(document.getElementById('tx-item-qty').value || 0);
      const price = parseFloat(document.getElementById('tx-item-price').value || 0);
      const locId = document.getElementById('tx-item-location-id').value;
      
      if(!id || qty <= 0) {
        Swal.fire('Peringatan', 'Silakan pilih barang dan masukkan jumlah minimal 1.', 'warning');
        return;
      }
      
      if(txItems.length > 0 && txItems[0].locationId !== locId) {
        Swal.fire('Peringatan', 'Satu dokumen hanya boleh berisi barang dari UAKPB yang sama.', 'warning');
        return;
      }
      
      if(txType === 'out' && qty > stock) {
        Swal.fire('Peringatan', `Jumlah keluar melebihi stok saat ini (${stock}).`, 'warning');
        return;
      }
      
      // Check if already in list
      const existing = txItems.find(i => i.id === id);
      if (existing) {
        if(txType === 'out' && (existing.qty + qty) > stock) {
           Swal.fire('Peringatan', `Total jumlah keluar melebihi stok saat ini (${stock}).`, 'warning');
           return;
        }
        existing.qty += qty;
        existing.price = price; // update price to latest typed
      } else {
        txItems.push({ id, code, name, qty, price, locationId: locId });
      }
      
      // Reset input
      document.getElementById('tx-item-search').value = '';
      document.getElementById('tx-item-id').value = '';
      document.getElementById('tx-item-location-id').value = '';
      document.getElementById('tx-item-qty').value = '';
      document.getElementById('tx-item-price').value = '0';
      document.getElementById('tx-stock-label').textContent = '';
      
      renderTxItems();
    }
    
    function removeTxItem(idx) {
      txItems.splice(idx, 1);
      renderTxItems();
    }

    function renderTxItems() {
      const tbody = document.getElementById('tx-items-tbody');
      tbody.innerHTML = '';
      if(txItems.length === 0) {
        tbody.innerHTML = '<tr id="tx-empty-row"><td colspan="6" style="text-align:center; color:var(--text-light);">Belum ada barang ditambahkan.</td></tr>';
        return;
      }
      
      txItems.forEach((item, idx) => {
        const total = item.qty * item.price;
        tbody.innerHTML += `
          <tr>
            <td>${item.code}</td>
            <td>${item.name}</td>
            <td>${item.qty}</td>
            <td>Rp ${item.price.toLocaleString('id-ID')}</td>
            <td>Rp ${total.toLocaleString('id-ID')}</td>
            <td>
              <button type="button" class="btn" style="background:var(--danger); color:white; padding:0.25rem 0.5rem; font-size:0.75rem;" onclick="removeTxItem(${idx})">Hapus</button>
            </td>
          </tr>
        `;
      });
    }

    async function submitTransaction() {
      if(txItems.length === 0) {
        Swal.fire('Peringatan', 'Tambahkan minimal 1 barang ke dalam dokumen.', 'warning');
        return;
      }
      
      const doc_number = document.getElementById('tx-form-nodok').value;
      const doc_date = document.getElementById('tx-form-tgldok').value;
      const book_date = document.getElementById('tx-form-tglbuku').value;
      const reference_doc = document.getElementById('tx-form-nobukti').value;
      const notes = document.getElementById('tx-form-keterangan').value;
      
      const formData = new FormData();
      formData.append('action', 'save_document');
      formData.append('type', currentFormTxType);
      formData.append('transaction_subtype', txSubtype);
      formData.append('doc_number', doc_number);
      formData.append('doc_date', doc_date);
      formData.append('book_date', book_date);
      formData.append('reference_doc', reference_doc);
      formData.append('notes', notes);
      formData.append('items', JSON.stringify(txItems));
      formData.append('location_id', document.getElementById('global-uakpb-filter').value);
      
      try {
        const btn = document.getElementById('btn-submit-tx');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
        
        const res = await fetch('api.php', { method: 'POST', body: formData });
        const json = await res.json();
        
        if (json.success) {
          Swal.fire('Berhasil!', 'Dokumen berhasil disimpan.', 'success').then(() => {
            closeTransactionForm();
            loadDocumentList();
            loadDashboard(); // Refresh stats
          });
        } else {
          Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
        }
      } catch (err) {
        Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
      } finally {
        const btn = document.getElementById('btn-submit-tx');
        btn.disabled = false;
        btn.textContent = 'Simpan Dokumen';
      }
    }

    async function loadDocumentList() {
      const tbody = document.getElementById('tx-list-tbody');
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 2rem;">Memuat daftar dokumen...</td></tr>';
      
      const formData = new FormData();
      formData.append('action', 'get_documents');
      formData.append('type', txType);
      formData.append('subtype', txSubtype);
      formData.append('location_id', document.getElementById('global-uakpb-filter').value);
      
      try {
        const res = await fetch('api.php', { method: 'POST', body: formData });
        const docs = await res.json();
        
        tbody.innerHTML = '';
        if (docs.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 2rem; color:var(--text-light);">Belum ada riwayat dokumen untuk tipe ini.</td></tr>';
          return;
        }
        
        let htmlStr = '';
        docs.forEach(doc => {
          const tgl = new Date(doc.doc_date).toLocaleDateString('id-ID');
          const totalRp = parseFloat(doc.total_price).toLocaleString('id-ID');
          htmlStr += `
            <tr style="border-bottom: 1px solid var(--border);">
              <td style="padding: 1rem; color: var(--primary); font-weight: 500;">${doc.doc_number || '-'}</td>
              <td style="padding: 1rem;">${tgl}</td>
              <td style="padding: 1rem;">${doc.reference_doc || '-'}</td>
              <td style="padding: 1rem; text-align: right;">${doc.item_count} Item</td>
              <td style="padding: 1rem; text-align: right;">Rp ${totalRp}</td>
            </tr>
          `;
        });
        tbody.innerHTML = htmlStr;
      } catch (err) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 2rem; color:var(--danger);">Gagal memuat daftar dokumen.</td></tr>';
      }
    }

    // CETAK MODAL LOGIC
    function openCetakModal() {
      document.getElementById('cetak-modal').classList.add('active');
      document.getElementById('cetak-type').value = txType;
      document.getElementById('cetak-subtype').value = txSubtype;
      
      // Select current month automatically
      const currentMonth = new Date().getMonth() + 1;
      document.querySelector(`select[name="month"]`).value = currentMonth.toString().padStart(2, '0');
    }
    
    function closeCetakModal() {
      document.getElementById('cetak-modal').classList.remove('active');
    }
    
    function toggleCetakPeriod() {
      const type = document.querySelector('input[name="period_type"]:checked').value;
      if (type === 'month') {
        document.getElementById('cetak-month-group').style.display = 'block';
        document.getElementById('cetak-year-group').style.display = 'none';
      } else {
        document.getElementById('cetak-month-group').style.display = 'none';
        document.getElementById('cetak-year-group').style.display = 'block';
      }
    }

    // LAPORAN PERIODE TOGGLE
    function toggleLaporanPeriode() {
      const type = document.querySelector('input[name="periode_type"]:checked').value;
      document.getElementById('lap-sd-tanggal').style.display = 'none';
      document.getElementById('lap-semester').style.display = 'none';
      document.getElementById('lap-tahun').style.display = 'none';
      
      if (type === 'sd_tanggal') {
        document.getElementById('lap-sd-tanggal').style.display = 'block';
      } else if (type === 'semester') {
        document.getElementById('lap-semester').style.display = 'flex';
      } else if (type === 'tahun') {
        document.getElementById('lap-tahun').style.display = 'block';
      }
    }

    // MODAL ADD MASTER LOGIC
    function openAddMasterModal() {
      document.getElementById('add-master-modal').classList.add('active');
      loadBidang();
    }
    
    function closeAddMasterModal() {
      document.getElementById('add-master-modal').classList.remove('active');
      document.getElementById('form-add-master').reset();
      document.getElementById('sel-kelompok').disabled = true;
      document.getElementById('sel-skel').disabled = true;
      document.getElementById('sel-sskel').disabled = true;
    }

    async function loadBidang() {
      const res = await fetch('api.php?action=get_bmn_bid');
      const data = await res.json();
      const sel = document.getElementById('sel-bidang');
      sel.innerHTML = '<option value="">-- Pilih Bidang --</option>';
      data.forEach(d => {
        sel.innerHTML += `<option value="${d.code}">${d.short_code} ${d.name}</option>`;
      });
    }

    async function loadKelompok() {
      const bid = document.getElementById('sel-bidang').value;
      const sel = document.getElementById('sel-kelompok');
      sel.innerHTML = '<option value="">-- Pilih Kelompok --</option>';
      sel.disabled = true;
      document.getElementById('sel-skel').disabled = true;
      document.getElementById('sel-sskel').disabled = true;
      document.getElementById('new-item-code').value = '';

      if (bid) {
        const res = await fetch(`api.php?action=get_bmn_kel&parent=${bid}`);
        const data = await res.json();
        data.forEach(d => {
          sel.innerHTML += `<option value="${d.code}">${d.short_code} ${d.name}</option>`;
        });
        sel.disabled = false;
      }
    }

    async function loadSkel() {
      const kel = document.getElementById('sel-kelompok').value;
      const sel = document.getElementById('sel-skel');
      sel.innerHTML = '<option value="">-- Pilih Sub-Kelompok --</option>';
      sel.disabled = true;
      document.getElementById('sel-sskel').disabled = true;
      document.getElementById('new-item-code').value = '';

      if (kel) {
        const res = await fetch(`api.php?action=get_bmn_skel&parent=${kel}`);
        const data = await res.json();
        data.forEach(d => {
          sel.innerHTML += `<option value="${d.code}">${d.short_code} ${d.name}</option>`;
        });
        sel.disabled = false;
      }
    }

    async function loadSskel() {
      const skel = document.getElementById('sel-skel').value;
      const sel = document.getElementById('sel-sskel');
      sel.innerHTML = '<option value="">-- Pilih Sub-Sub-Kelompok --</option>';
      sel.disabled = true;
      document.getElementById('new-item-code').value = '';

      if (skel) {
        const res = await fetch(`api.php?action=get_bmn_sskel&parent=${skel}`);
        const data = await res.json();
        data.forEach(d => {
          sel.innerHTML += `<option value="${d.code}">${d.short_code} ${d.name}</option>`;
        });
        sel.disabled = false;
      }
    }

    function generateCodePrefix() {
      const sskel = document.getElementById('sel-sskel').value;
      if (sskel) {
        document.getElementById('new-item-code').value = sskel + " (Otomatis digenerate 6 digit terakhir)";
      } else {
        document.getElementById('new-item-code').value = '';
      }
    }

    async function submitAddMasterForm(e) {
      e.preventDefault();
      const sskel = document.getElementById('sel-sskel').value;
      const name = document.getElementById('new-item-name').value;
      const unit = document.getElementById('new-item-unit').value;
      const locId = document.getElementById('global-uakpb-filter').value;

      if (!sskel) {
        Swal.fire('Peringatan', 'Silakan lengkapi pilihan klasifikasi hingga Sub-Sub-Kelompok.', 'warning');
        return;
      }
      
      if (locId === 'all') {
        Swal.fire('Peringatan', 'Harap pilih UAKPB spesifik (misal: ATK LAMA) di bagian atas layar sebelum menambah barang baru.', 'warning');
        return;
      }

      const formData = new FormData();
      formData.append('action', 'add_master_item');
      formData.append('sskel_code', sskel);
      formData.append('name', name);
      formData.append('unit', unit);
      formData.append('location_id', locId);

      try {
        const res = await fetch('api.php', { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          Swal.fire('Berhasil!', json.message, 'success');
          closeAddMasterModal();
          loadMaster(); // Refresh table
          loadDashboard(); // Refresh stats
        } else {
          Swal.fire('Gagal', json.message, 'error');
        }
      } catch (err) {
        Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
      }
    }

    async function initGlobalUakpb() {
      try {
        const formData = new FormData();
        formData.append('action', 'get_uakpb');
        const res = await fetch('api.php', { method: 'POST', body: formData });
        const items = await res.json();
        
        const sel = document.getElementById('global-uakpb-filter');
        items.forEach(it => {
          sel.innerHTML += `<option value="${it.id}">${it.name}</option>`;
        });
      } catch(e) {
        console.error('Error fetching global uakpb', e);
      }
    }

    function onGlobalUakpbChange() {
      if (document.getElementById('view-dashboard').classList.contains('active')) loadDashboard();
      if (document.getElementById('view-master').classList.contains('active')) loadMaster();
      if (document.getElementById('view-transaction-list').classList.contains('active')) loadDocumentList();
      if (document.getElementById('view-history').classList.contains('active')) loadHistory();
    }

    function appendGlobalLocationId(form) {
      const locId = document.getElementById('global-uakpb-filter').value;
      const input = form.querySelector('input[name="location_id"]');
      if (input) {
        input.value = locId;
      }
      return true;
    }

    // Init
    window.onload = async () => {
      await initGlobalUakpb();
      loadDashboard();
    };
  </script>
</body>
</html>
