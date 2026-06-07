<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reception — Layar Informasi SEAMEO BIOTROP</title>
  <meta name="description" content="Layar informasi jadwal penggunaan fasilitas SEAMEO BIOTROP" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css" />
  <link rel="icon" href="<?= BASE_URL ?>/assets/img/logo.png" type="image/png" />
  <style>
    :root {
      --glass-bg: rgba(255, 255, 255, 0.7);
      --glass-border: rgba(255, 255, 255, 0.4);
      --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    html, body { height: 100%; overflow: hidden; margin: 0; padding: 0; }

    body {
      background: radial-gradient(circle at 0% 0%, #f0fdf4 0%, #ffffff 50%, #f0fdf4 100%);
      font-family: 'Inter', system-ui, sans-serif;
    }

    .reception-page { 
      height: 100vh; 
      display: flex; 
      flex-direction: column; 
      position: relative;
    }

    /* Ambient blobs for background aesthetic */
    .blob {
      position: absolute;
      width: 500px;
      height: 500px;
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
      filter: blur(80px);
      border-radius: 50%;
      z-index: -1;
    }
    .blob-1 { top: -100px; left: -100px; }
    .blob-2 { bottom: -100px; right: -100px; }

    /* --- Header --- */
    .recv-header {
      padding: 1rem 3rem;
      display: flex; 
      align-items: center; 
      justify-content: space-between;
      flex-shrink: 0;
      background: var(--glass-bg);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--glass-border);
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
      height: 120px; /* Reduced to match clock area more closely */
    }

    /* --- Left Header (Mockup matched) --- */
    .header-left { display: flex; flex-direction: column; gap: 0.5rem; }
    
    .info-badge {
      background: #ffcc33;
      color: #064e3b;
      font-size: 1.8rem;
      font-weight: 900;
      padding: 0.4rem 2rem;
      border-radius: 0.5rem 1.5rem 0.5rem 0; /* Custom shape style from image */
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      letter-spacing: 0.05em;
      text-transform: uppercase;
      width: fit-content;
    }

    .site-info { display: flex; align-items: center; gap: 1rem; }
    .site-logo { width: 70px; height: 70px; object-fit: contain; }
    .site-titles { display: flex; flex-direction: column; justify-content: center; }
    .site-main-title { font-size: 1.5rem; font-weight: 850; color: #064e3b; letter-spacing: -0.02em; line-height: 1.1; }
    .site-sub-title { font-size: 0.9rem; font-weight: 600; color: #059669; text-transform: uppercase; letter-spacing: 0.05em; }

    .recv-clock-col  { text-align: right; display: flex; flex-direction: column; justify-content: center; }
    .recv-time { 
      font-size: 3.5rem; 
      font-weight: 800; 
      letter-spacing: -0.05em; 
      color: #064e3b; 
      line-height: 1; 
      font-variant-numeric: tabular-nums;
    }
    .recv-date { 
      font-size: 1rem; 
      font-weight: 700; 
      color: #64748b; 
      text-transform: uppercase; 
      letter-spacing: 0.05em; 
      margin-top: 0.4rem; 
    }

    /* --- Body grid --- */
    .recv-body {
      flex: 1; min-height: 0;
      display: grid; grid-template-columns: 1fr 420px;
      gap: 2rem; padding: 2rem 3rem;
      overflow: hidden;
    }

    /* --- Events panel (left) --- */
    .panel { display: flex; flex-direction: column; min-height: 0; }
    .panel-label {
      font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;
      color: #059669; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem;
    }
    .panel-label::after { content:''; flex:1; height:1px; background: linear-gradient(90deg, rgba(16, 185, 129, 0.3), transparent); }
    .events-list { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem; }

    /* Scrollbar Styling */
    .events-list::-webkit-scrollbar { width: 6px; }
    .events-list::-webkit-scrollbar-track { background: transparent; }
    .events-list::-webkit-scrollbar-thumb { background: rgba(5, 150, 105, 0.1); border-radius: 10px; }

    /* Event card */
    .ev-card {
      background: #ffffff;
      border-radius: 1.25rem;
      border: 1px solid var(--color-slate-100);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
      display: flex;
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      animation: fadeSlide 0.5s ease backwards;
      flex-shrink: 0; /* Important: prevent cards from shrinking when many */
      height: 160px;  /* Optimal height to fit approx 4 cards in standard 1080p view */
    }
    @keyframes fadeSlide { from { opacity:0; transform: translateY(15px); } to { opacity:1; transform: translateY(0); } }
    
    .ev-card:hover { 
      transform: translateY(-4px) scale(1.01);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border-color: rgba(16, 185, 129, 0.3);
    }

    .ev-time-col {
      width: 130px; 
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      padding: 1.5rem 1rem; text-align: center;
      background: linear-gradient(180deg, #064e3b 0%, #065f46 100%);
      color: #fff;
      flex-shrink: 0;
    }
    .ev-time-start { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .ev-time-separator { height: 2px; width: 20px; background: rgba(255,255,255,0.3); margin: 0.5rem 0; }
    .ev-time-end   { font-size: 1.1rem; font-weight: 600; opacity: 0.7; }
    .ev-time-day   { font-size: 0.7rem; font-weight: 700; margin-top: 0.75rem; background: rgba(255,255,255,0.15); padding: 0.2rem 0.5rem; border-radius: 4px; }

    .ev-body-col { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .ev-top-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; }
    
    .ev-type-pill {
      display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
      padding: 0.3rem 0.75rem; border-radius: 9999px;
    }
    .ev-type-pill.vehicle { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
    .ev-type-pill.room    { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .ev-type-pill.zoom    { background: #f5f3ff; color: #5b21b6; border: 1px solid #ddd6fe; }
    .ev-type-pill.item    { background: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; }

    .ev-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; line-height: 1.2; margin-bottom: 0.4rem; }
    .ev-sub   { font-size: 0.95rem; font-weight: 600; color: #64748b; }
    .ev-purpose { font-size: 0.875rem; color: #94a3b8; font-style: italic; margin-top: 0.75rem; padding-left: 0.75rem; border-left: 2px solid #e2e8f0; }

    .ev-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; }
    .ev-who { font-size: 0.8rem; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 0.5rem; }
    .ev-who .avatar { width: 24px; height: 24px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800; color: #475569; }

    .ev-status-pill { font-size: 0.7rem; font-weight: 800; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase; }
    .ev-status-pill.approved  { background: #10b981; color: #fff; }
    .ev-status-pill.in-progress { background: #f59e0b; color: #fff; }

    /* --- Right panel --- */
    .right-panels { display: flex; flex-direction: column; gap: 1.5rem; min-height: 0; }
    .mini-panel {
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      border-radius: 1.5rem;
      border: 1px solid var(--glass-border);
      box-shadow: var(--glass-shadow);
      padding: 1.5rem;
      display: flex; flex-direction: column; min-height: 0;
    }
    .mini-panel-title { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #064e3b; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .mini-panel-title svg { color: #059669; }

    /* Upcoming list (Right Small Panel) */
    .mini-list { flex: 1; overflow-y: hidden; display: flex; flex-direction: column; gap: 0.5rem; }
    .mini-item {
      padding: 0.75rem; border-radius: 0.75rem; background: rgba(255,255,255,0.4); 
      border: 1px solid rgba(16, 185, 129, 0.1); display: flex; align-items: center; gap: 0.75rem;
      animation: fadeSlide 0.5s ease backwards;
    }
    .mini-item-date { 
      font-size: 0.7rem; font-weight: 800; color: #059669; background: #d1fae5; 
      padding: 0.2rem 0.4rem; border-radius: 4px; text-align: center; min-width: 45px;
    }
    .mini-item-info { flex: 1; min-width: 0; }
    .mini-item-title { font-size: 0.85rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mini-item-time { font-size: 0.75rem; color: #64748b; font-weight: 600; }

    /* Auto-scroll container */
    #events-list { 
      mask-image: linear-gradient(to bottom, black 90%, transparent 100%);
      -webkit-mask-image: linear-gradient(to bottom, black 90%, transparent 100%);
    }

    /* Ticker (today's events) */
    .recv-ticker {
      background: #022c22; color: #fff;
      padding: 0.85rem 2rem; display: flex; align-items: center; gap: 2rem;
      font-size: 0.9rem; overflow: hidden;
      border-top: 1px solid rgba(16, 185, 129, 0.2);
    }
    .ticker-label { 
      background: #f59e0b; color: #064e3b; 
      padding: 0.25rem 0.85rem; border-radius: 6px; 
      font-weight: 800; font-size: 0.75rem; flex-shrink: 0;
      text-transform: uppercase; letter-spacing: 0.05em;
    }
    .ticker-track { flex: 1; overflow: hidden; display: flex; }
    .ticker-content { white-space: nowrap; animation: ticker-scroll 60s linear infinite; font-weight: 600; letter-spacing: 0.02em; }
    @keyframes ticker-scroll { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

    /* Empty state */
    .empty-state { text-align: center; padding: 4rem 2rem; color: #94a3b8; }
    .empty-state .icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }

    /* Loading */
    .recv-loading { text-align: center; padding: 5rem; }

    @media (max-width: 1200px) { 
      .recv-body { grid-template-columns: 1fr; padding: 1.5rem; } 
      .right-panels { display: none; } 
    }
  </style>
</head>
<body>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="reception-page">

  <!-- HEADER -->
  <header class="recv-header">
    <div class="site-info">
      <img src="<?= BASE_URL ?>/assets/img/logo.png" class="site-logo" alt="BIOTROP">
      <div class="site-titles">
        <div class="site-main-title">SEAMEO BIOTROP</div>
        <div class="site-sub-title">Facility Information Center</div>
      </div>
    </div>
    <div class="recv-clock-col">
      <div class="recv-time" id="recv-time">--:--:--</div>
      <div class="recv-date" id="recv-date">--</div>
    </div>
  </header>

  <!-- BODY -->
  <div class="recv-body">

    <!-- LEFT: Events List -->
    <div class="panel">
      <div class="panel-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>Jadwal Aktif Hari Ini &amp; Mendatang</span>
      </div>
      <div class="events-list" id="events-list">
        <div class="recv-loading">
          <div class="spinner" style="width:3rem;height:3rem;border-color:rgba(16,185,129,.2);border-top-color:#059669; border-width:4px;"></div>
          <p style="margin-top:1.5rem;font-size:1rem;font-weight:600;color:#64748b;">Memperbarui informasi jadwal...</p>
        </div>
      </div>
    </div>

    <!-- RIGHT: Upcoming & Info -->
    <div class="right-panels">
      <!-- Jadwal Mendatang -->
      <div class="mini-panel">
        <div class="mini-panel-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/><path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/></svg>
          Jadwal Esok & Mendatang
        </div>
        <div class="mini-list" id="upcoming-mini-list">
          <div style="color:#94a3b8;font-size:0.8rem;text-align:center;padding:1rem;">Memuat data...</div>
        </div>
      </div>

      <!-- Info Panel -->
      <div class="mini-panel">
        <div class="mini-panel-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          Informasi Unit
        </div>
        <div style="color: #64748b; font-size: 0.85rem; line-height: 1.5;">
          <div style="padding: 0.75rem; background: rgba(255,255,255,0.3); border-radius: 0.75rem; border: 1px dashed rgba(16,185,129,0.3); margin-bottom: 0.75rem;">
            <p style="margin: 0; font-weight: 700; color: #064e3b; font-size: 0.8rem;">Unit Facility Management (FMD)</p>
            <p style="margin: 0; font-size: 0.75rem;">Gedung GWB, Lantai 1</p>
          </div>
          <p style="font-size: 0.75rem; opacity: 0.8; margin: 0;">
            • Harap lapor kedatangan ke Unit FMD<br>
            • Ext. Bantuan: 123 / 124
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- TICKER -->
  <div class="recv-ticker">
    <span class="ticker-label">Headline</span>
    <div class="ticker-track">
      <div class="ticker-content" id="ticker-content">Informasi jadwal penggunaan fasilitas SEAMEO BIOTROP akan diperbarui secara otomatis dalam hitungan detik. Harap perhatikan jadwal untuk kelancaran kegiatan.</div>
    </div>
  </div>
</div>

<script>
const API_BASE = '<?= BASE_URL ?>/api/';

// Helper for icons in pills
const ICONS = {
  Vehicle: '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
  Room: '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
  Zoom: '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M15.6 11.6L22 7v10l-6.4-4.6z"/><rect x="2" y="5" width="12" height="14" rx="2"/></svg>',
  Item: '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>'
};

// ===== CLOCK =====
function updateClock() {
  const now   = new Date();
  const days  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const months= ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  const hh    = String(now.getHours()).padStart(2,'0');
  const mm    = String(now.getMinutes()).padStart(2,'0');
  const ss    = String(now.getSeconds()).padStart(2,'0');
  document.getElementById('recv-time').textContent = `${hh}:${mm}:${ss}`;
  document.getElementById('recv-date').textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
}
setInterval(updateClock, 1000);
updateClock();

// ===== COLOR MAP (type → CSS class) =====
const TYPE_CLASS = { Vehicle: 'vehicle', Room: 'room', Zoom: 'zoom', Item: 'item' };
const TYPE_LABEL = { Vehicle: 'Kendaraan Dinas', Room: 'Penggunaan Ruangan', Zoom: 'Virtual Meeting', Item: 'Peminjaman Barang' };

// ===== FORMAT TIME =====
function fmtTime(t) { return t ? t.slice(0,5) : '--:--'; }
function fmtShortDate(d) {
  if (!d) return '';
  const dt = new Date(d);
  return isNaN(dt) ? d : dt.toLocaleDateString('id-ID', {day:'numeric',month:'short'});
}

// ===== VEHICLE & ROOM MAPS (Sync with main.js) =====
const VEHICLE_MAP = {
  'AVZ001': 'Panther - F 1895 A',
  'INV002': 'Panther - F 1898 A'
};
const ROOM_MAP = {
  'RUANG_STUDIO':   'Ruang Studio (10-12 org)',
  'RUANG_MATOA':    'Ruang Matoa (45-75 org)',
  'RUANG_JATI':     'Ruang Jati (30-45 org)',
  'RUANG_KENARI':   'Ruang Kenari (10-12 org)',
  'RUANG_EBONY':    'Ruang Ebony (10-12 org)',
  'GEDUNG_BUNDAR':  'Gedung Bundar (75-100 org)',
  'MAHONI':         'Mahoni (10-12 org)',
  'RG_DEWAN':       'Rg. Dewan (5-7 org)',
  'RG_PDID':        'Rg. PDID (20-30 org)',
  'RUANG_KMD':      'Ruang Rapat KMD (8-10 orang)',
  'RUANG_HERBARIUM':'Ruang Rapat Herbarium (30-35 org)',
};

// ===== LOAD + RENDER =====
async function loadAndRender() {
  try {
    const response = await fetch(API_BASE + 'reception.php');
    const data = await response.json();
    
    if (!Array.isArray(data)) throw new Error('Invalid data');

    const today = new Date().toISOString().split('T')[0];
    
    // Process data to match UI expectations
    const allEvents = data.map(item => {
      let title = item.sub_title;
      let sub = '';
      
      if (item.type === 'Vehicle') {
        title = VEHICLE_MAP[item.sub_title] || item.sub_title;
        sub   = `Driver: ${item.info_extra || 'TBA'}`;
      } else if (item.type === 'Room') {
        title = ROOM_MAP[item.sub_title] || item.sub_title;
        sub   = `Kapasitas: ${item.info_extra || '0'} orang`;
      } else if (item.type === 'Zoom') {
        title = `Zoom: ${item.sub_title}`;
        sub   = `${item.info_extra || '0'} Partisipan`;
      } else if (item.type === 'Item') {
        title = `Peminjaman: ${item.sub_title}`;
        sub   = `Jumlah: ${item.info_extra || '1'}`;
      }

      return {
        ...item,
        title: title,
        sub: sub,
        who: item.applicant_name,
        dept: item.applicant_unit,
        timeStart: item.time_start.slice(0,5),
        timeEnd: item.time_end.slice(0,5)
      };
    });

    const todayEvents = allEvents.filter(e => e.date_start <= today && (e.date_end >= today || !e.date_end));
    const upcomingEvents = allEvents.filter(e => e.date_start > today);

    renderEventList(todayEvents);
    renderUpcomingMini(upcomingEvents);
    updateTicker(); 

  } catch(err) {
    console.error(err);
    document.getElementById('events-list').innerHTML = `<div class="empty-state"><div class="icon">⚠️</div><p>Gagal memuat data dari database.</p></div>`;
  }
}

function renderUpcomingMini(events) {
  const container = document.getElementById('upcoming-mini-list');
  if (!events.length) {
    container.innerHTML = `<div style="text-align:center;color:#94a3b8;font-size:0.8rem;padding:1rem;">Belum ada jadwal esok hari</div>`;
    return;
  }
  container.innerHTML = events.slice(0, 5).map(e => `
    <div class="mini-item">
      <div class="mini-item-date">${fmtShortDate(e.date_start)}</div>
      <div class="mini-item-info">
        <div class="mini-item-title">${e.title}</div>
        <div class="mini-item-time">${e.timeStart} - ${e.timeEnd}</div>
      </div>
    </div>
  `).join('');
}

function renderEventList(events) {
  const container = document.getElementById('events-list');
  
  // Reset cloned state when data is re-rendered
  delete container.dataset.cloned;
  
  if (!events.length) {
    container.innerHTML = `<div class="empty-state"><div class="icon">✨</div><p style="font-weight:700;color:#64748b;font-size:1.25rem;">Tidak Ada Jadwal Berlangsung</p><p style="font-size:1rem;color:#94a3b8;">Semua fasilitas saat ini tersedia untuk dipesan.</p></div>`;
    return;
  }

  const today = new Date().toISOString().split('T')[0];
  container.innerHTML = events.map((e, idx) => {
    const isToday = e.date_start === today;
    const typeClass = TYPE_CLASS[e.type] || '';
    const avatar = e.who ? e.who.charAt(0).toUpperCase() : '?';

    return `
    <div class="ev-card" style="animation-delay: ${idx * 0.1}s">
      <div class="ev-time-col" style="${!isToday ? 'background: linear-gradient(180deg, #334155 0%, #475569 100%);' : ''}">
        <div class="ev-time-start">${e.timeStart}</div>
        <div class="ev-time-separator"></div>
        <div class="ev-time-end">${e.timeEnd}</div>
        <div class="ev-time-day">${isToday ? '🔴 HARI INI' : fmtShortDate(e.dateStart)}</div>
      </div>
      <div class="ev-body-col">
        <div class="ev-top-row">
          <span class="ev-type-pill ${typeClass}">${ICONS[e.type] || ''} ${TYPE_LABEL[e.type] || e.type}</span>
          <span class="ev-status-pill ${e.status}">${e.status.replace(/-/g,' ')}</span>
        </div>
        <div class="ev-title">${e.title}</div>
        <div class="ev-sub">${e.sub}</div>
        ${e.purpose ? `<div class="ev-purpose">${e.purpose.slice(0,90)}${e.purpose.length>90?'...':''}</div>` : ''}
        
        <div class="ev-footer">
          <div class="ev-who">
            <div class="avatar">${avatar}</div>
            <span>${e.who} <span style="opacity:0.6;font-weight:500;">/ ${e.dept}</span></span>
          </div>
          <svg style="color:#e2e8f0;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </div>
      </div>
    </div>`;
  }).join('');
}

function renderStats(vehicles, rooms, zooms, today, statuses) {
  const v = Array.isArray(vehicles) ? vehicles.filter(r=>statuses.includes(r.status) && r.date_start <= today && (r.date_end >= today || !r.date_end)).length : 0;
  const r = Array.isArray(rooms)    ? rooms.filter(r=>statuses.includes(r.status) && r.date_start === today).length : 0;
  const z = Array.isArray(zooms)    ? zooms.filter(z=>statuses.includes(z.status) && z.date_start === today).length : 0;
  document.getElementById('stat-vehicle').textContent = v;
  document.getElementById('stat-room').textContent    = r;
  document.getElementById('stat-zoom').textContent    = z;
  document.getElementById('stat-total').textContent   = v + r + z;
}


function updateTicker() {
  const ticker = document.getElementById('ticker-content');
  ticker.textContent = 'Harap perhatikan protokol penggunaan fasilitas SEAMEO BIOTROP. Hubungi unit FMD untuk bantuan teknis — Integrated Facility Management System v2.0';
}

// --- AUTO-SCROLL LOGIC ---
let scrollInterval = null;
function startAutoScroll() {
  const container = document.getElementById('events-list');
  if (!container) return;
  
  // Clear any existing interval
  if (scrollInterval) clearInterval(scrollInterval);
  
  // To make it infinitely loop, we clone the children if they overflow
  if (container.scrollHeight > container.clientHeight) {
    // Check if we already cloned (to avoid infinite cloning on refresh)
    const originalChildren = Array.from(container.children);
    if (!container.dataset.cloned) {
        originalChildren.forEach(child => {
          const clone = child.cloneNode(true);
          container.appendChild(clone);
        });
        container.dataset.cloned = 'true';
    }
  }

  const scrollStep = 1.5; // Slightly faster increment
  scrollInterval = setInterval(() => {
    if (container.scrollHeight > container.clientHeight) {
      const halfHeight = container.scrollHeight / 2;
      
      if (container.scrollTop >= halfHeight) {
        // Instant reset to top when reaching the clone
        container.scrollTop = 0;
      } else {
        container.scrollTop += scrollStep;
      }
    }
  }, 30); // ~33fps scroll, faster interval
}

// INIT: load now, then refresh every 1 minute
loadAndRender().then(() => startAutoScroll());
setInterval(loadAndRender, 60000);
</script>
</body>
</html>
