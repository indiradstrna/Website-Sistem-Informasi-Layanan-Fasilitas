<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Room Info — SEAMEO BIOTROP</title>
  <meta name="description" content="Layar informasi jadwal penggunaan fasilitas SEAMEO BIOTROP" />
  <link rel="stylesheet" href="../assets/css/style.css?v=1.1" />
  <link rel="icon" href="../assets/img/logo.png" type="image/png" />
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
      height: 120px;
    }

    .site-info { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; gap: 0.25rem; }
    .site-logo { height: 95px; width: auto; object-fit: contain; }

    .site-titles { display: flex; flex-direction: column; justify-content: center; }
    .site-sub-title { font-size: 0.95rem; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1; margin-top: -5px; }


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
    .events-list::-webkit-scrollbar { width: 0; }

    /* Event card */
    .ev-card {
      background: #ffffff;
      border-radius: 1.25rem;
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
      display: flex;
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      animation: fadeSlide 0.5s ease backwards;
      flex-shrink: 0;
      height: 160px;
    }
    @keyframes fadeSlide { from { opacity:0; transform: translateY(15px); } to { opacity:1; transform: translateY(0); } }
    
    .ev-card:hover { 
      transform: translateY(-4px) scale(1.01);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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
      padding: 0.3rem 0.75rem; border-radius: 9999px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
    }

    .ev-title { font-size: 1.15rem; font-weight: 850; color: #1e293b; line-height: 1.1; margin-bottom: 0.2rem; }


    .ev-sub   { font-size: 0.8rem; font-weight: 500; color: #64748b; }
    .ev-purpose { font-size: 1.15rem; color: #0f172a; font-weight: 800; margin-top: 0.5rem; padding-left: 0.75rem; border-left: 3px solid #10b981; line-height: 1.3; }


    .ev-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; }
    .ev-who { font-size: 0.75rem; font-weight: 600; color: #64748b; display: flex; align-items: center; gap: 0.4rem; }
    .ev-who .avatar { width: 22px; height: 22px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 800; color: #475569; }


    .ev-status-pill { font-size: 0.7rem; font-weight: 800; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase; }
    .ev-status-pill.approved  { background: #10b981; color: #fff; }

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

    .mini-list { flex: 1; overflow-y: hidden; display: flex; flex-direction: column; gap: 0.5rem; }
    .mini-item {
      padding: 0.75rem; border-radius: 0.75rem; background: rgba(255,255,255,0.4); 
      border: 1px solid rgba(16, 185, 129, 0.1); display: flex; align-items: center; gap: 0.75rem;
    }
    .mini-item-date { 
      font-size: 0.7rem; font-weight: 800; color: #059669; background: #d1fae5; 
      padding: 0.2rem 0.4rem; border-radius: 4px; text-align: center; min-width: 45px;
    }
    .mini-item-info { flex: 1; min-width: 0; }
    .mini-item-title { font-size: 0.85rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mini-item-time { font-size: 0.75rem; color: #64748b; font-weight: 600; }

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

    .empty-state { text-align: center; padding: 4rem 2rem; color: #94a3b8; }
    .empty-state .icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }

    @media (max-width: 1200px) { 
        html, body { height: auto; overflow: visible; }
        .reception-page { height: auto; min-height: 100vh; overflow: visible; }
        .recv-header { height: auto; flex-direction: column; gap: 1rem; padding: 1.5rem; text-align: center; }
        .recv-clock-col { text-align: center; }
        .recv-time { font-size: 2.5rem; }
        .recv-description { display: none; } /* Shrink header more on mobile if needed */
        .recv-body { grid-template-columns: 1fr; padding: 1rem; gap: 1rem; height: auto; overflow: visible; } 
        .events-list { 
            max-height: 580px; /* Approx 3 cards height on mobile */
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            gap: 1rem; 
            position: relative;
        }
        .right-panels { order: 2; width: 100%; display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem; }

        .ev-card { height: auto; flex-direction: column; }
        .ev-time-col { width: 100%; padding: 1rem; flex-direction: row; gap: 1rem; justify-content: center; }
        .ev-time-separator { width: 10px; height: 2px; margin: 0; }
        #events-list { mask-image: none; -webkit-mask-image: none; }
        
        .mini-panel { padding: 1.25rem; border-radius: 1rem; height: auto; }
        .mini-panel-title { font-size: 0.75rem; margin-bottom: 0.75rem; }
        .mini-item { gap: 1rem; padding: 0.85rem; }
        .mini-item-title { font-size: 0.9rem; white-space: normal; }
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
      <img src="../assets/img/logo.png" class="site-logo" alt="BIOTROP">
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
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Jadwal Penggunaan Ruangan</span>
      </div>
      <div class="events-list" id="events-list">
        <div style="text-align:center;padding:5rem;">Memuat informasi ruangan...</div>
      </div>
    </div>

    <!-- RIGHT: Upcoming & Info -->
    <div class="right-panels">
      <div class="mini-panel">
        <div class="mini-panel-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Mendatang
        </div>
        <div class="mini-list" id="upcoming-mini-list">
          <div style="color:#94a3b8;font-size:0.8rem;text-align:center;padding:1rem;">Memuat data...</div>
        </div>
      </div>

      <div class="mini-panel">
        <div class="mini-panel-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          Informasi Bantuan
        </div>
        <div style="color: #64748b; font-size: 0.85rem; line-height: 1.5;">
          <p style="margin: 0;"><b>Unit FMD</b></p>
          <p style="margin-top: 0.5rem; font-size: 0.75rem; opacity: 0.8;">
            PIC Ruangan : Lastiah
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- TICKER -->
  <div class="recv-ticker">
    <span class="ticker-label">INFO</span>
    <div class="ticker-track">
      <div class="ticker-content" id="ticker-content">Harap perhatikan protokol penggunaan fasilitas SEAMEO BIOTROP. Hubungi unit FMD untuk bantuan teknis — Integrated Facility Management System v2.0</div>
    </div>
  </div>
</div>

<script>
// ===== MAPS =====
const ROOM_MAP = {
  'RUANG_STUDIO':   'Ruang Studio',
  'RUANG_MATOA':    'Ruang Matoa',
  'RUANG_JATI':     'Ruang Jati',
  'RUANG_KENARI':   'Ruang Kenari',
  'RUANG_EBONY':    'Ruang Ebony',
  'GEDUNG_BUNDAR':  'Gedung Bundar',
  'MAHONI':         'Mahoni',
  'RG_DEWAN':       'Rg. Dewan',
  'RG_PDID':        'Rg. PDID',
  'RUANG_KMD':      'Ruang Rapat KMD',
  'RUANG_HERBARIUM':'Ruang Rapat Herbarium',
};

const API_BASE = '<?= BASE_URL ?>/api/';

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

function fmtShortDate(d) {
  if (!d) return '';
  const dt = new Date(d);
  return isNaN(dt) ? d : dt.toLocaleDateString('id-ID', {day:'numeric',month:'short'});
}

// ===== LOAD + RENDER =====
// ===== LOAD + RENDER =====
let lastDataHash = "";
async function loadAndRender() {
  try {
    const response = await fetch(API_BASE + 'reception.php');
    let data = await response.json();
    
    // Remove Auto-fallback to Dummy Data if API is empty or fails
    if (!Array.isArray(data)) {
        throw new Error("Invalid API Response");
    }

    const localNow = new Date();
    const today = `${localNow.getFullYear()}-${String(localNow.getMonth() + 1).padStart(2, '0')}-${String(localNow.getDate()).padStart(2, '0')}`;

    // Flicker Prevention: Check if data actually changed
    const currentHash = JSON.stringify(data) + "_" + today;
    if (currentHash === lastDataHash) {
        // Update "NOW" indicators without re-rendering everything
        const curH = localNow.getHours();
        const curM = localNow.getMinutes();
        const curTotal = curH * 60 + curM;
        document.querySelectorAll('.ev-card').forEach(card => {
           const timeCol = card.querySelector('.ev-time-col');
           const dayLabel = card.querySelector('.ev-time-day');
           const [sh, sm] = card.querySelector('.ev-time-start').textContent.split(':').map(Number);
           const [eh, em] = card.querySelector('.ev-time-end').textContent.split(':').map(Number);
           const startTotal = sh * 60 + sm;
           const endTotal = eh * 60 + em;
           
           if (curTotal >= startTotal && curTotal <= endTotal) {
               if (dayLabel.textContent !== '🔴 SEKARANG') {
                   dayLabel.textContent = '🔴 SEKARANG';
                   dayLabel.style.background = ''; // reset style
                   dayLabel.style.color = '';
               }
           }
        });
        return; 
    }
    lastDataHash = currentHash;
    const roomData = data.filter(item => item.type === 'Room');

    
    const allEvents = roomData.map(item => ({
      ...item,
      title: ROOM_MAP[item.sub_title] || item.sub_title,
      sub: `Jumlah peserta: ${item.info_extra || '0'} orang`,
      who: item.applicant_name || 'Admin',
      dept: item.applicant_unit || 'Internal',
      timeStart: (item.time_start || '00:00').slice(0,5),
      timeEnd: (item.time_end || '00:00').slice(0,5)
    }));

    const todayEvents = allEvents.filter(e => e.date_start <= today && (e.date_end >= today || !e.date_end));
    const upcomingEvents = allEvents.filter(e => e.date_start > today);

    renderEventList(todayEvents);
    renderUpcomingMini(upcomingEvents);

  } catch(err) {
    console.error('API Error:', err);
    const container = document.getElementById('events-list');
    container.innerHTML = `<div class="empty-state"><p style="font-weight:700;color:#ef4444;font-size:1.25rem;">Gagal Memuat Data</p><p style="font-size:1rem;color:#94a3b8;">Terjadi kesalahan saat mengambil jadwal dari server.</p></div>`;
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
  
  if (!events.length) {
    container.innerHTML = `<div class="empty-state"><p style="font-weight:700;color:#64748b;font-size:1.25rem;">Tidak Ada Jadwal Ruangan</p><p style="font-size:1rem;color:#94a3b8;">Semua ruangan saat ini tersedia.</p></div>`;
    return;
  }

  const now = new Date();
  const curTotal = now.getHours() * 60 + now.getMinutes();

  const html = events.map((e, idx) => {
    // Dynamic NOW check
    let isActuallyNow = false;
    const [sh, sm] = e.timeStart.split(':').map(Number);
    const [eh, em] = e.timeEnd.split(':').map(Number);
    const startTotal = sh * 60 + sm;
    const endTotal = eh * 60 + em;
    
    if (curTotal >= startTotal && curTotal <= endTotal) {
        isActuallyNow = true;
    }

    const statusTag = isActuallyNow ? '<div class="ev-time-day">🔴 SEKARANG</div>' : '<div class="ev-time-day" style="background:rgba(0,0,0,0.1); color:#475569;">HARI INI</div>';

    return `
    <div class="ev-card" style="animation-delay: ${idx * 0.1}s">
      <div class="ev-time-col">
        <div class="ev-time-start">${e.timeStart}</div>
        <div class="ev-time-separator"></div>
        <div class="ev-time-end">${e.timeEnd}</div>
        ${statusTag}
      </div>
      <div class="ev-body-col">
        <div class="ev-top-row">
          <span class="ev-type-pill">Room Request</span>
          <span class="ev-status-pill approved">Approved</span>
        </div>
        <div class="ev-title"><b>${e.title}</b></div>
        <div class="ev-sub">${e.sub || ''}</div>
        ${e.purpose ? `<div class="ev-purpose">${e.purpose}</div>` : ''}
        
        <div class="ev-footer">
          <div class="ev-who">
            <div class="avatar">${(e.who || '?').charAt(0)}</div>
            <span>${e.who} <span style="opacity:0.6;font-weight:500;">/ ${e.dept}</span></span>
          </div>
        </div>
      </div>
    </div>`;
  }).join('');

    // Instant Loop Logic: Clone content if it overflows
    container.innerHTML = html;
    setTimeout(() => {
        if (container.scrollHeight > container.clientHeight) {
            container.innerHTML = html + html;
        }
    }, 100);
}


// --- AUTO-SCROLL LOGIC ---
let scrollInterval = null;
function startAutoScroll() {
  const container = document.getElementById('events-list');
  if (!container) return;
  if (scrollInterval) clearInterval(scrollInterval);
  
  const scrollStep = 1;
  scrollInterval = setInterval(() => {
    if (container.scrollHeight > container.clientHeight) {
      const halfHeight = container.scrollHeight / 2;
      if (container.scrollTop >= halfHeight) {
          container.scrollTop = 1; 
      } else {
          container.scrollTop += scrollStep;
      }
    }
  }, 40);
}

loadAndRender().then(() => startAutoScroll());

setInterval(async () => {
    await loadAndRender();
}, 60000);


</script>
</body>
</html>
