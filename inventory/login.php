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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Inventaris Aset</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/globals.css">
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
      color: #334155;
    }
    .login-wrapper {
      width: 100%;
      max-width: 420px;
      padding: 2rem;
    }
    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 1.5rem;
      padding: 2.5rem 2rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      text-align: center;
    }
    .login-logo {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
    }
    .login-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0.5rem;
    }
    .login-subtitle {
      font-size: 0.875rem;
      color: #64748b;
      margin-bottom: 2rem;
    }
    .form-group {
      text-align: left;
      margin-bottom: 1.25rem;
    }
    .form-label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: #475569;
    }
    .form-input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #e2e8f0;
      border-radius: 0.75rem;
      font-size: 1rem;
      transition: all 0.2s;
      background: #f8fafc;
      box-sizing: border-box;
      font-family: 'Outfit', sans-serif;
    }
    .form-input:focus {
      outline: none;
      border-color: #3b82f6;
      background: #ffffff;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    .btn-submit {
      width: 100%;
      padding: 0.875rem;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
      border: none;
      border-radius: 0.75rem;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
      font-family: 'Outfit', sans-serif;
      margin-top: 1rem;
    }
    .btn-submit:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
    }
    .btn-submit:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
    }
    .error-msg {
      color: #ef4444;
      font-size: 0.875rem;
      margin-top: 1rem;
      display: none;
      background: #fef2f2;
      padding: 0.75rem;
      border-radius: 0.5rem;
      border: 1px solid #fca5a5;
    }
  </style>
</head>
<body>

  <div class="login-wrapper">
    <div class="login-card">
      <div class="login-logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
          <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
          <line x1="12" y1="22.08" x2="12" y2="12"></line>
        </svg>
      </div>
      <h1 class="login-title">Inventaris Aset</h1>
      <p class="login-subtitle">Masuk khusus Admin Gudang</p>
      
      <form id="loginForm" onsubmit="handleLogin(event)">
        <div class="form-group">
          <label class="form-label">ID Karyawan</label>
          <input type="text" id="employee_id" class="form-input" required placeholder="Masukkan ID">
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" id="password" class="form-input" required placeholder="Masukkan Password">
        </div>
        
        <div id="error-alert" class="error-msg"></div>
        
        <button type="submit" id="btn-login" class="btn-submit">Masuk Sistem</button>
      </form>
      <div style="margin-top:1.5rem; font-size:0.8rem; color:#94a3b8;">
        Kembali ke <a href="../index.php" style="color:#3b82f6; text-decoration:none;">Login Utama</a>
      </div>
    </div>
  </div>

  <script>
    async function handleLogin(e) {
      e.preventDefault();
      const empId = document.getElementById('employee_id').value.trim();
      const pass = document.getElementById('password').value.trim();
      const btn = document.getElementById('btn-login');
      const err = document.getElementById('error-alert');
      
      err.style.display = 'none';
      btn.disabled = true;
      btn.textContent = 'Memproses...';

      try {
        const formData = new FormData();
        formData.append('action', 'login');
        formData.append('employee_id', empId);
        formData.append('password', pass);
        
        const res = await fetch('api.php', { method: 'POST', body: formData });
        const json = await res.json();
        
        if (json.success) {
          window.location.href = 'index.php';
        } else {
          err.textContent = json.message || 'Login gagal.';
          err.style.display = 'block';
          btn.disabled = false;
          btn.textContent = 'Masuk Sistem';
        }
      } catch (error) {
        err.textContent = 'Terjadi kesalahan jaringan.';
        err.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Masuk Sistem';
      }
    }
  </script>
</body>
</html>
