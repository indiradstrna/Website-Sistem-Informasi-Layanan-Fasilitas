<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login — SEAMEO BIOTROP Facility Management</title>
  <meta name="description" content="Sistem manajemen fasilitas terpadu SEAMEO BIOTROP. Masuk untuk mengakses dashboard layanan." />
  <link rel="stylesheet" href="assets/css/style.css?v=1.1" />
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
      <img src="assets/img/logo.png" alt="SEAMEO BIOTROP" style="height:44px;object-fit:contain;" onerror="this.style.display='none'" />
    </div>
    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;font-weight:600;color:var(--color-emerald-800);opacity:.8;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      Facility Management Dept.
    </div>
  </header>

  <!-- MAIN -->
  <main class="login-main">
    <div class="login-grid">

      <!-- LEFT: Branding -->
      <div class="login-branding">
        <div class="login-badge">SILATAS - Facility Management Department</div>
        <h1 class="login-title">
          Sistem Informasi<br>
          <span>Layanan &amp; Fasilitas</span>
        </h1>
        <p class="login-subtitle">
          Selamat datang di sistem manajemen fasilitas terpadu. Silakan masukan kredensial Anda untuk melanjutkan ke dashboard layanan.
        </p>
      </div>

      <!-- RIGHT: Login Card -->
      <div>
        <div class="login-card">
          <h2 class="login-card-title">Login Account</h2>
          <p class="login-card-sub">Masukan kredensial Anda untuk melanjutkan</p>

          <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger" style="margin-bottom:1rem;">
              <?php
                $err = htmlspecialchars($_GET['error']);
                echo match($err) {
                  'not_logged_in' => 'Silakan login terlebih dahulu.',
                  'unauthorized'  => 'Anda tidak memiliki akses ke halaman tersebut.',
                  default         => 'Username atau password tidak valid.',
                };
              ?>
            </div>
          <?php endif; ?>

          <div id="login-alert" class="alert alert-danger hidden"></div>

          <form id="login-form">
            <div class="form-group">
              <label class="form-label" for="username">NIP/NIK</label>
              <div class="form-input-icon">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input type="text" id="username" name="username" class="form-input" placeholder="Contoh: 1980..." required autocomplete="username" />
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
              Masuk Aplikasi
            </button>
          </form>

          <div class="login-footer">
            <a href="reception/rooms.php" class="login-reception-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
              Buka Layar Informasi / Reception
            </a>
          </div>
        </div>
      </div>

    </div>
  </main>

  <footer style="text-align:center;padding:1rem;font-size:.75rem;color:var(--color-slate-400);">
    &copy; <?= date('Y') ?> SEAMEO BIOTROP Facility Management. All rights reserved.
  </footer>
</div>

<script src="assets/js/main.js"></script>
<script>
window.BASE_URL = '<?= BASE_URL ?>';
document.getElementById('login-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn       = document.getElementById('login-btn');
  const alertBox  = document.getElementById('login-alert');
  const username  = document.getElementById('username').value;
  const password  = document.getElementById('password').value;

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Memproses...';
  alertBox.classList.add('hidden');

  const res = await apiPost('api/login.php', { username, password });

  if (res.success) {
    btn.innerHTML = '✓ Berhasil! Mengarahkan...';
    window.location.href = res.data?.redirectUrl || 'user/index.php';
  } else {
    alertBox.textContent = res.message || 'Login gagal.';
    alertBox.classList.remove('hidden');
    btn.disabled = false;
    btn.innerHTML = 'Masuk Aplikasi';
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
      eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
      eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
  });
}
</script>

</body>
</html>
