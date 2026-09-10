<?php
session_start();
require_once __DIR__ . '/../config.php';

// Jika sudah login sebagai warehouse_admin, langsung redirect
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'warehouse_admin') {
    header("Location: " . BASE_URL . "/inventory/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Inventaris Aset</title>
  <meta name="description" content="Sistem manajemen inventaris terpadu SEAMEO BIOTROP. Masuk untuk mengakses dashboard layanan." />
  <link rel="stylesheet" href="../assets/css/style.css?v=1.1" />
  <link rel="icon" href="<?= BASE_URL ?>/assets/img/logo.png" type="image/png" />
</head>
<body>

<!-- Background dot pattern -->
<div class="login-bg-pattern"></div>

<!-- Top gradient bar -->
<div class="top-bar"></div>

<!-- ===== LOGIN PAGE ===== -->
<div class="login-page" style="padding-top:6px;">

  <!-- NAVBAR -->
  <header class="login-navbar" style="position:sticky;top:6px;z-index:50;">
    <div style="display:flex;align-items:center;">
      <img src="../assets/img/logo.png" alt="SEAMEO BIOTROP" style="height:44px;object-fit:contain;" onerror="this.style.display='none'" />
    </div>
    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;font-weight:600;color:var(--color-emerald-800);opacity:.8;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
      Inventory Management
    </div>
  </header>

  <!-- MAIN -->
  <main class="login-main">
    <div class="login-grid">

      <!-- LEFT: Branding -->
      <div class="login-branding">
        <div class="login-badge">SILATAS - Inventory Management</div>
        <h1 class="login-title">
          Sistem Inventaris<br>
          <span>Aset & Barang</span>
        </h1>
        <p class="login-subtitle">
          Selamat datang di modul manajemen inventaris terpadu. Silakan masukan kredensial Admin Gudang Anda untuk melanjutkan.
        </p>
      </div>

      <!-- RIGHT: Login Card -->
      <div>
        <div class="login-card">
          <h2 class="login-card-title">Login Admin Gudang</h2>
          <p class="login-card-sub">Masukan kredensial Anda untuk melanjutkan</p>

          <div id="login-alert" class="alert alert-danger hidden"></div>

          <form id="login-form">
            <div class="form-group">
              <label class="form-label" for="employee_id">ID Karyawan</label>
              <div class="form-input-icon">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input type="text" id="employee_id" name="employee_id" class="form-input" placeholder="Masukkan ID..." required autocomplete="username" />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="password">Password</label>
              <div class="form-input-icon" style="position:relative;">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password" style="padding-right: 2.5rem;" />
                <button type="button" id="toggle-pwd" tabindex="-1" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; color: var(--color-slate-400); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                  <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
            </div>

            <button type="submit" id="login-btn" class="btn btn-primary btn-full btn-lg" style="margin-top:0.5rem;">
              Masuk Sistem
            </button>
          </form>

          <div class="login-footer">
            <a href="../index.php" class="login-reception-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
              Kembali ke Login Utama
            </a>
          </div>
        </div>
      </div>

    </div>
  </main>

  <footer style="text-align:center;padding:1rem;font-size:.75rem;color:var(--color-slate-400);">
    &copy; <?= date('Y') ?> SEAMEO BIOTROP. All rights reserved.
  </footer>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn       = document.getElementById('login-btn');
  const alertBox  = document.getElementById('login-alert');
  const empId     = document.getElementById('employee_id').value;
  const password  = document.getElementById('password').value;

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Memproses...';
  alertBox.classList.add('hidden');

  try {
    const formData = new FormData();
    formData.append('action', 'login');
    formData.append('employee_id', empId);
    formData.append('password', password);
    
    const res = await fetch('api.php', { method: 'POST', body: formData });
    const json = await res.json();
    
    if (json.success) {
      btn.innerHTML = '✓ Berhasil! Mengarahkan...';
      window.location.href = 'index.php';
    } else {
      alertBox.textContent = json.message || 'Login gagal.';
      alertBox.classList.remove('hidden');
      btn.disabled = false;
      btn.innerHTML = 'Masuk Sistem';
    }
  } catch (error) {
    alertBox.textContent = 'Terjadi kesalahan jaringan.';
    alertBox.classList.remove('hidden');
    btn.disabled = false;
    btn.innerHTML = 'Masuk Sistem';
  }
});

const togglePwd = document.getElementById('toggle-pwd');
const pwdInput = document.getElementById('password');
const eyeIcon = document.getElementById('eye-icon');

if (togglePwd && pwdInput) {
  togglePwd.addEventListener('click', () => {
    const type = pwdInput.getAttribute('type') === 'password' ? 'text' : 'password';
    pwdInput.setAttribute('type', type);
    
    if (type === 'text') {
      eyeIcon.innerHTML = `
        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
        <line x1="1" y1="1" x2="23" y2="23"></line>
      `;
    } else {
      eyeIcon.innerHTML = `
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
        <circle cx="12" cy="12" r="3"></circle>
      `;
    }
  });
}
</script>
</body>
</html>
