# Dokumentasi API SILATAS

Dokumentasi ini menjelaskan berbagai endpoint API yang tersedia pada sistem SILATAS. Semua API menggunakan format response JSON dan membutuhkan Session (kecuali disebutkan berbeda).

**Base URL**: `http://<domain_anda>/silatas/api/`

---

## Standar Response (JSON)
Secara umum, API akan mengembalikan response dengan struktur berikut:
```json
{
  "success": true,
  "message": "Pesan sukses atau error",
  "data": { ... } // Opsional, jika ada data tambahan
}
```

---

## 1. Authentication (`login.php`)

Digunakan untuk proses otentikasi user (login).
- **URL**: `login.php`
- **Method**: `POST`
- **Parameter**:
  - `username` (string): NIP/NIK karyawan
  - `password` (string): Password akun
- **Response Sukses**:
  ```json
  {
    "success": true,
    "message": "Login berhasil.",
    "redirectUrl": "admin/index.php"
  }
  ```

---

## 2. Public API (`reception.php`)

Endpoint ini terbuka untuk umum (tidak butuh login). Biasanya digunakan untuk Layar Informasi (Reception TV Display).
Hanya menampilkan pengajuan yang berstatus **approved** atau **in-progress** pada hari ini dan yang akan datang.
- **URL**: `reception.php`
- **Method**: `GET`
- **Response Sukses**:
  ```json
  [
    {
      "id": "1",
      "type": "Vehicle",
      "applicant_name": "John Doe",
      "applicant_unit": "IT",
      "date_start": "2026-09-22",
      "time_start": "08:00:00",
      "date_end": "2026-09-22",
      "time_end": "17:00:00",
      "purpose": "Kunjungan Dinas",
      "status": "approved",
      "sub_title": "B 1234 CD",
      "info_extra": "Driver: Ali"
    },
    ...
  ]
  ```

---

## 3. User Management (`users.php`)

Digunakan untuk mengelola data user. Sebagian besar aksi memerlukan role Admin/Superadmin.
- **URL**: `users.php`
- **Method**: `POST` / `GET`
- **Parameter Wajib**: `action`

| Action | Method | Parameter Tambahan | Keterangan | Role |
|---|---|---|---|---|
| `get_all` | GET/POST | - | Mendapatkan semua user | Admin |
| `get_employees` | GET/POST | - | Mendapatkan semua karyawan dari tabel employees | Admin |
| `add` | POST | `full_name`, `password`, `role`, `employee_id` | Menambah user baru | Admin |
| `update` | POST | `id`, `full_name`, `role`, `password` (opsional) | Mengupdate data user | Admin |
| `delete` | POST | `id` | Menghapus user berdasarkan ID | Admin |
| `update_profile`| POST | `telegram_chat_id`, `whatsapp_number` | Update profil user yg sedang login | All Users |
| `change_password`| POST | `old_password`, `new_password` | Ubah password user yg sedang login | All Users |

---

## 4. Master Data (`master_data.php`)

Digunakan untuk mengelola data master seperti Kendaraan (Vehicle), Ruangan (Room), dan Asrama (Dormitory).
- **URL**: `master_data.php`
- **Method**: `POST`
- **Role Wajib**: Admin / Superadmin
- **Parameter Wajib**: `action` dan `type` (`vehicle`, `room`, `dormitory`)

| Action | Parameter Tambahan | Keterangan |
|---|---|---|
| `add` | `id`, `name` | Menambahkan master data baru |
| `edit` | `id`, `name` | Mengupdate nama master data |
| `delete`| `id` | Menghapus data. Akan gagal jika data sudah digunakan di riwayat pengajuan |

---

## 5. System Settings (`settings.php`)

Digunakan untuk mengatur konfigurasi sistem (contoh: notifikasi, batas pengajuan).
- **URL**: `settings.php`
- **Method**: `POST` / `GET`
- **Role Wajib**: Admin / Superadmin
- **Parameter Wajib**: `action`

| Action | Method | Parameter Tambahan | Keterangan |
|---|---|---|---|
| `get_all` | GET/POST | - | Mendapatkan seluruh pengaturan (Admin) |
| `update_settings`| POST | `settings` (array key-value) | Menyimpan perubahan pengaturan (Hanya Superadmin) |

---

## 6. Request Management (`requests.php`)

Ini adalah API utama untuk semua jenis pengajuan (Kendaraan, Ruangan, Asrama, Zoom, Perbaikan, Barang).
- **URL**: `requests.php`
- **Method**: `POST` / `GET`
- **Catatan**: Ada fitur Debounce (3 detik) untuk mencegah double-submit data.
- **Parameter Wajib**: `action`

### a. GET Actions (Mengambil Data)
| Action | Keterangan |
|---|---|
| `get_vehicle`, `get_room`, `get_dormitory`, `get_zoom`, `get_repair`, `get_item`, `get_item2` | Mengambil semua pengajuan (Untuk Admin) |
| `get_vehicle_by_user`, `get_room_by_user`, dll. | Mengambil pengajuan milik user yang sedang login (Untuk User) |
| `get_driver_tasks` | Mengambil tugas khusus untuk Driver (Sopir) yang sedang login |
| `search_inventory_items` | Pencarian barang (parameter `q`) |

### b. Submit Actions (Membuat Pengajuan Baru)
Gunakan `action` dengan awalan `submit_` + `jenis pengajuan`. Contoh:
- `submit_vehicle`: Pengajuan kendaraan
- `submit_room`: Pengajuan ruangan
- `submit_dormitory`: Pengajuan asrama
- `submit_zoom`: Pengajuan zoom
- `submit_repair`: Pengajuan perbaikan
- `submit_item`: Pengajuan peminjaman barang (item_loan_requests)
- `submit_item2`: Pengajuan pengadaan barang (item_requests)

*(Parameter tambahan menyesuaikan dengan field masing-masing form pada aplikasi frontend)*
