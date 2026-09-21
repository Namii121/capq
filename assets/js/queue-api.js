/**
 * CarePoint CAPQ - Shared Native PHP & MySQL Queue API Layer
 * Replaces localStorage with asynchronous fetch() calls to module action handlers.
 */

// Global State Cache
let cachedQueueState = {
  nowCalling: null,
  rooms: [],
  upcomingQueue: []
};

const QueueManager = {
  // Get active cached state
  getState() {
    return cachedQueueState;
  },

  // Sound Synthesizer (Clinic Chime 🔔 Ding-Dong)
  playChime() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) return;
      const ctx = new AudioCtx();

      // Ding (High tone E5: ~659Hz)
      const osc1 = ctx.createOscillator();
      const gain1 = ctx.createGain();
      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(659.25, ctx.currentTime);
      gain1.gain.setValueAtTime(0.35, ctx.currentTime);
      gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.85);
      osc1.connect(gain1);
      gain1.connect(ctx.destination);
      osc1.start(ctx.currentTime);
      osc1.stop(ctx.currentTime + 0.85);

      // Dong (Low tone C5: ~523Hz)
      const osc2 = ctx.createOscillator();
      const gain2 = ctx.createGain();
      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(523.25, ctx.currentTime + 0.38);
      gain2.gain.setValueAtTime(0.4, ctx.currentTime + 0.38);
      gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 1.3);
      osc2.connect(gain2);
      gain2.connect(ctx.destination);
      osc2.start(ctx.currentTime + 0.38);
      osc2.stop(ctx.currentTime + 1.3);
    } catch (e) {
      console.log('Chime sound error or muted:', e);
    }
  },

  // Fetch live queue state from display module action
  async syncFromBackend() {
    try {
      const res = await fetch('modules/display/actions.php?action=get_live_state');
      const json = await res.json();
      if (json.success && json.data) {
        cachedQueueState = json.data;
        window.dispatchEvent(new CustomEvent('queue_state_updated', { detail: cachedQueueState }));
      }
    } catch (err) {
      console.warn('Backend sync warning:', err);
    }
  },

  // Call next patient via Doctor module action
  async callNext() {
    try {
      const res = await fetch('modules/doctor/actions.php?action=call_next', { method: 'POST' });
      const json = await res.json();
      if (json.success && json.data) {
        cachedQueueState.nowCalling = json.data;
        this.playChime();
        await this.syncFromBackend();
        return json.data;
      }
    } catch (err) {
      console.error('Call next patient error:', err);
    }
  },

  // Call specific patient
  async callPatient(ticketNumber) {
    try {
      const res = await fetch(`modules/doctor/actions.php?action=call_patient&ticket=${encodeURIComponent(ticketNumber)}`, { method: 'POST' });
      const json = await res.json();
      if (json.success) {
        this.playChime();
        await this.syncFromBackend();
        return json.data;
      }
    } catch (err) {
      console.error('Call patient error:', err);
    }
  },

  // Add patient via Kiosk module check-in
  async addPatient(patient) {
    try {
      const formData = new FormData();
      formData.append('name', patient.name || 'Walk-in Patient');
      formData.append('phone', patient.phone || '09XX-XXX-XXXX');
      formData.append('department', patient.department || 'General Medicine');
      formData.append('complaint', patient.reason || patient.complaint || 'General Consultation');
      if (patient.isPriority) formData.append('is_priority', '1');

      const res = await fetch('modules/kiosk/actions.php?action=checkin', {
        method: 'POST',
        body: formData
      });
      const json = await res.json();
      if (json.success) {
        await this.syncFromBackend();
        return json.data;
      }
    } catch (err) {
      console.error('Kiosk check-in error:', err);
    }
  },

  // Reset demo state via Admin module action
  async resetDefaults() {
    try {
      await fetch('modules/admin/actions.php?action=reset_queue', { method: 'POST' });
      await this.syncFromBackend();
    } catch (err) {
      console.error('Reset queue error:', err);
    }
  }
};

// Auto-polling sync every 3 seconds
setInterval(() => {
  QueueManager.syncFromBackend();
}, 3000);

// Initial immediate sync
document.addEventListener('DOMContentLoaded', () => {
  QueueManager.syncFromBackend();
});

// Toast notification helper with SweetAlert2 integration
function showToast(message, title = 'Notification', iconType = null) {
  if (typeof Swal !== 'undefined') {
    let icon = iconType || 'info';
    const lowerTitle = (title || '').toLowerCase();
    const lowerMsg = (message || '').toLowerCase();
    
    if (!iconType) {
      if (lowerTitle.includes('error') || lowerTitle.includes('fail') || lowerMsg.includes('error') || lowerMsg.includes('fail')) {
        icon = 'error';
      } else if (lowerTitle.includes('success') || lowerTitle.includes('created') || lowerTitle.includes('deleted') || lowerTitle.includes('registered') || lowerTitle.includes('dispatched') || lowerTitle.includes('finished') || lowerTitle.includes('called')) {
        icon = 'success';
      } else if (lowerTitle.includes('warn') || lowerTitle.includes('require')) {
        icon = 'warning';
      }
    }

    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3500,
      timerProgressBar: true,
      customClass: {
        popup: 'swal2-carepoint-toast font-sans shadow-xl rounded-2xl'
      },
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });

    Toast.fire({
      icon: icon,
      title: title,
      text: message
    });
    return;
  }

  let toast = document.getElementById('system-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'system-toast';
    toast.className = 'fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-teal-500/40 flex items-center gap-3 transition-all duration-300 transform translate-y-24 opacity-0 pointer-events-none';
    toast.innerHTML = `
      <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center flex-shrink-0">
        <i data-lucide="bell" class="w-4 h-4"></i>
      </div>
      <div>
        <p class="text-[10px] uppercase font-bold text-teal-400 tracking-wider" id="toast-title">${title}</p>
        <p class="text-xs font-semibold text-white" id="toast-text">${message}</p>
      </div>
    `;
    document.body.appendChild(toast);
    if (window.lucide) lucide.createIcons();
  }

  document.getElementById('toast-title').textContent = title;
  document.getElementById('toast-text').textContent = message;

  toast.classList.remove('opacity-0', 'translate-y-24');
  toast.classList.add('opacity-100', 'translate-y-0');

  setTimeout(() => {
    toast.classList.remove('opacity-100', 'translate-y-0');
    toast.classList.add('opacity-0', 'translate-y-24');
  }, 3500);
}
