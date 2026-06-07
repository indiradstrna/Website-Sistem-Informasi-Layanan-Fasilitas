// ============================================================
// assets/js/main.js — Utility Functions & Shared Logic
// Setara dengan: lib/utils.ts + komponen JS dari Next.js
// ============================================================

// ===== TOAST NOTIFICATION =====
// Menggantikan: alert() dan useEffect toasts
const Toast = (() => {
    let container = null;

    function ensureContainer() {
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
    }

    function show(message, type = 'default', duration = 3500) {
        ensureContainer();
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icons = { success: '✓', error: '✕', default: 'ℹ' };
        toast.innerHTML = `<span>${icons[type] || 'ℹ'}</span> ${message}`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideInRight .25s ease reverse';
            setTimeout(() => toast.remove(), 240);
        }, duration);
    }

    return {
        success: (msg) => show(msg, 'success'),
        error: (msg) => show(msg, 'error'),
        info: (msg) => show(msg, 'default'),
    };
})();

// ===== MODAL HELPER =====
// Setara dengan: useState showModal + <Dialog>
const Modal = {
    open(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.add('open'); document.body.style.overflow = 'hidden'; }
    },
    close(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('open'); document.body.style.overflow = ''; }
    },
    // Tutup semua modal saat klik overlay
    init() {
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function (e) {
                if (e.target === this) {
                    Modal.close(this.id);
                }
            });
        });
        document.querySelectorAll('.modal-close-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal-overlay');
                if (modal) Modal.close(modal.id);
            });
        });
    }
};

// ===== BADGE STATUS HELPER =====
// Setara dengan: getStatusBadgeClass() + getStatusLabel()
function getStatusLabel(status) {
    const labelMap = {
        pending: 'Pending PIC',
        verified: 'Verified (Admin)',
        approved: 'Approved',
        rejected: 'Rejected',
        canceled: 'Canceled',
        completed: 'Completed',
        returned: 'Returned',
        'in-progress': 'In Progress',
        waiting_manager_fmd: 'Waiting Manager FMD',
        waiting_manager_fad: 'Waiting Manager FAD',
        waiting_ppk: 'Waiting PPK',
        waiting_bod: 'Waiting BOD',
        approved_waiting_fund: 'Approved Waiting Fund',
        ready_for_user: 'Ready for User',
    };
    return labelMap[status] || status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function getStatusBadge(status) {
    const label = getStatusLabel(status);
    return `<span class="badge badge-${status}">${label}</span>`;
}

// ===== FORMAT DATE (Indonesia) =====
function formatDate(dateStr, includeTime = false) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr;
    const opts = { day: 'numeric', month: 'short', year: 'numeric' };
    if (includeTime) { opts.hour = '2-digit'; opts.minute = '2-digit'; }
    return d.toLocaleDateString('id-ID', opts);
}

// ===== FORMAT CURRENCY =====
function formatRupiah(amount) {
    return 'Rp ' + Number(amount).toLocaleString('id-ID');
}

// ===== API FETCH WRAPPER =====
// Menggantikan: Server Actions (await getSomething())
async function api(url, options = {}) {
    try {
        const res = await fetch(url, options);
        return await res.json();
    } catch (err) {
        console.error('API Error:', err);
        return { success: false, message: 'Terjadi kesalahan jaringan.' };
    }
}

// POST ke API
async function apiPost(url, data = {}) {
    const body = new FormData();
    for (const [k, v] of Object.entries(data)) {
        if (v !== null && v !== undefined) body.append(k, v);
    }
    return api(url, { method: 'POST', body });
}

// ===== PAGINATION HELPER =====
function createPagination(containerId, totalItems, itemsPerPage, currentPage, onPageChange) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    if (totalPages <= 1) { container.innerHTML = ''; return; }

    let html = `<div class="pagination">`;
    html += `<button class="pag-btn" onclick="(${onPageChange})(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>‹</button>`;

    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 1) {
            html += `<button class="pag-btn ${i === currentPage ? 'active' : ''}" onclick="(${onPageChange})(${i})">${i}</button>`;
        } else if (Math.abs(i - currentPage) === 2) {
            html += `<span style="padding:0 4px;color:#94a3b8">…</span>`;
        }
    }

    html += `<button class="pag-btn" onclick="(${onPageChange})(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>›</button>`;
    html += `</div>`;
    container.innerHTML = html;
}

// ===== SEARCH FILTER =====
function filterTable(tableBodyId, searchValue, columns = [0, 1, 2]) {
    const rows = document.querySelectorAll(`#${tableBodyId} tr`);
    const term = searchValue.toLowerCase();
    rows.forEach(row => {
        const cells = [...row.querySelectorAll('td')];
        const match = columns.some(i => {
            const cell = cells[i];
            return cell && cell.textContent.toLowerCase().includes(term);
        });
        row.style.display = match || !term ? '' : 'none';
    });
}

// ===== CONFIRM DIALOG =====
async function confirmAction(message) {
    return window.confirm(message);
}

// ===== LOGOUT =====
function logout() {
    const base = window.BASE_URL || '..';
    window.location.href = base + '/api/logout.php';
}

// (ALL_VEHICLES, VEHICLE_MAP, ALL_ROOMS, ROOM_MAP sekarang di-load dinamis dari DB via layout.php)

// ===== DOM READY =====
document.addEventListener('DOMContentLoaded', () => {
    Modal.init();
});
