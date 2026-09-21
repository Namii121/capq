/**
 * CarePoint CAPQ - Shared Queue System & State Sync
 * Synchronizes queue actions between Doctor Console, Admin Dashboard, Kiosk, and Live TV Display
 * using browser localStorage and the Web Audio API for chime sounds.
 */

// Initial default state with rich patient medical records
const DEFAULT_QUEUE_STATE = {
  nowCalling: {
    ticket: 'A-102',
    name: 'Juan Carlos Dela Cruz',
    age: 38,
    gender: 'Male',
    phone: '0917-555-0192',
    patientId: 'PT-10492',
    room: 'Room 03',
    department: 'General Medicine',
    doctor: 'Dr. Sarah Santos, MD',
    time: '10:12 AM',
    vitals: { bp: '120/80', temp: '36.8', pulse: '76', spo2: '99', weight: '68' },
    complaint: 'Patient reports dry persistent cough for 4 days, mild sore throat and fatigue. No reported asthma or chest pain. Requesting medical clearance certificate for employment.',
    diagnosis: 'J06.9 - Acute Upper Respiratory Infection (Mild)',
    prescription: '1. Cetirizine 10mg tab - 1 tab once daily at bedtime for 5 days.\n2. Paracetamol 500mg tab - 1 tab every 4-6 hours PRN for fever or sore throat.\n3. Increase oral hydration (2-3L water/day) and adequate rest.'
  },
  rooms: [
    { room: '01', name: 'Triage & Vitals', doctor: 'Nurse Station 1', ticket: 'T-089', next: ['T-090', 'T-091'], color: 'amber' },
    { room: '02', name: 'Pediatrics Clinic', doctor: 'Dr. Patricia Lim, MD', ticket: 'P-045', next: ['P-046', 'P-047'], color: 'blue' },
    { room: '03', name: 'General Medicine', doctor: 'Dr. Sarah Santos, MD', ticket: 'A-102', next: ['A-103', 'A-104'], color: 'teal' },
    { room: '04', name: 'Dental & Oral Care', doctor: 'Dr. Michael Tan, DMD', ticket: 'D-018', next: ['D-019'], color: 'purple' },
    { room: '05', name: 'Pharmacy & Billing', doctor: 'Prescription Counter', ticket: 'B-201', next: ['B-202', 'B-203'], color: 'emerald' }
  ],
  upcomingQueue: [
    {
      ticket: 'A-103',
      name: 'Beatriz Mendoza',
      age: 29,
      gender: 'Female',
      phone: '0922-334-1188',
      patientId: 'PT-10503',
      type: 'Appointment',
      time: '10:30 AM',
      department: 'General Medicine',
      vitals: { bp: '115/75', temp: '36.6', pulse: '72', spo2: '98', weight: '54' },
      complaint: 'Follow-up consultation for complete blood count (CBC) results. Patient reports occasional fatigue and mild dizziness.',
      diagnosis: 'D50.9 - Iron Deficiency Anemia (Mild)',
      prescription: 'Ferrous Sulfate 325mg (65mg elemental iron) - 1 tablet once daily with Vitamin C. Re-check CBC in 30 days.',
      isPriority: false
    },
    {
      ticket: 'P-012',
      name: 'Rosa Flores',
      age: 68,
      gender: 'Female',
      phone: '0918-771-2299',
      patientId: 'PT-09821',
      type: 'Walk-in',
      time: '10:35 AM',
      department: 'General Medicine',
      vitals: { bp: '138/84', temp: '36.5', pulse: '70', spo2: '98', weight: '62' },
      complaint: 'Bilateral knee joint pain, stiffness in the morning for 3 weeks. Senior citizen requesting maintenance arthritis review.',
      diagnosis: 'M17.0 - Primary Bilateral Gonarthrosis (Osteoarthritis)',
      prescription: 'Paracetamol 500mg - 1 tab every 8 hours PRN. Glucosamine Sulfate 500mg daily. Refer to Physical Therapy.',
      isPriority: true
    },
    {
      ticket: 'A-104',
      name: 'Eduardo Rivera',
      age: 45,
      gender: 'Male',
      phone: '0919-445-8821',
      patientId: 'PT-10512',
      type: 'Walk-in',
      time: '10:40 AM',
      department: 'General Medicine',
      vitals: { bp: '142/90', temp: '37.0', pulse: '84', spo2: '97', weight: '82' },
      complaint: 'Routine blood pressure check-up and antihypertensive maintenance prescription renewal.',
      diagnosis: 'I10 - Essential (Primary) Hypertension Stage 1',
      prescription: 'Amlodipine 5mg tab - 1 tablet once daily in the morning. Maintain low-sodium diet and home BP log.',
      isPriority: false
    },
    {
      ticket: 'A-105',
      name: 'Gabriel Ramos',
      age: 34,
      gender: 'Male',
      phone: '0920-551-7733',
      patientId: 'PT-10525',
      type: 'Appointment',
      time: '11:00 AM',
      department: 'General Medicine',
      vitals: { bp: '122/82', temp: '36.7', pulse: '78', spo2: '99', weight: '71' },
      complaint: 'Review of annual executive check-up laboratory results (Fasting blood sugar & lipid profile).',
      diagnosis: 'E78.0 - Pure Hypercholesterolemia (Borderline)',
      prescription: 'Lifestyle modification, dietary counseling. Re-test Lipid Profile in 3 months.',
      isPriority: false
    },
    {
      ticket: 'A-106',
      name: 'Cristina Ocampo',
      age: 22,
      gender: 'Female',
      phone: '0917-112-9900',
      patientId: 'PT-10531',
      type: 'Appointment',
      time: '11:15 AM',
      department: 'General Medicine',
      vitals: { bp: '110/70', temp: '36.9', pulse: '80', spo2: '99', weight: '50' },
      complaint: 'Pruritic erythematous skin rash on bilateral forearms after exposure to household chemical detergent.',
      diagnosis: 'L24.9 - Irritant Contact Dermatitis',
      prescription: 'Hydrocortisone 1% cream - apply thinly to affected areas twice daily for 7 days. Avoid irritants.',
      isPriority: false
    }
  ]
};

// Queue Manager Helper
const QueueManager = {
  // Get active state from localStorage or defaults
  getState() {
    try {
      const stored = localStorage.getItem('carepoint_queue_state');
      if (stored) {
        return JSON.parse(stored);
      }
    } catch (e) {
      console.warn('LocalStorage unavailable:', e);
    }
    return DEFAULT_QUEUE_STATE;
  },

  // Save state and trigger event
  saveState(state) {
    try {
      localStorage.setItem('carepoint_queue_state', JSON.stringify(state));
      window.dispatchEvent(new CustomEvent('queue_state_updated', { detail: state }));
    } catch (e) {
      console.warn('LocalStorage save failed:', e);
    }
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

  // Call a patient to a room (supporting full patient object)
  callPatient(ticket, name, room = 'Room 03', doctor = 'Dr. Sarah Santos, MD') {
    const state = this.getState();
    const existingPatient = state.upcomingQueue.find(p => p.ticket === ticket);

    const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    if (existingPatient) {
      state.nowCalling = {
        ...existingPatient,
        room,
        doctor,
        time: nowTime
      };
      // Remove from upcoming
      state.upcomingQueue = state.upcomingQueue.filter(item => item.ticket !== ticket);
    } else {
      state.nowCalling = {
        ticket,
        name,
        age: 35,
        gender: 'Adult',
        phone: '09XX-XXX-XXXX',
        patientId: 'PT-' + Math.floor(Math.random() * 9000 + 1000),
        room,
        department: 'General Medicine',
        doctor,
        time: nowTime,
        vitals: { bp: '120/80', temp: '36.7', pulse: '76', spo2: '99', weight: '65' },
        complaint: 'Routine medical consultation and check-up.',
        diagnosis: 'Z00.0 - General Medical Examination',
        prescription: 'Maintain balanced diet, regular exercise, and adequate hydration.'
      };
      state.upcomingQueue = state.upcomingQueue.filter(item => item.ticket !== ticket);
    }

    // Update room 3 ticket
    const r3 = state.rooms.find(r => r.room === '03');
    if (r3) {
      r3.ticket = ticket;
    }

    this.saveState(state);
    this.playChime();
    return state.nowCalling;
  },

  // Call next in line
  callNext() {
    const state = this.getState();
    if (state.upcomingQueue.length > 0) {
      const nextPatient = state.upcomingQueue[0];
      return this.callPatient(nextPatient.ticket, nextPatient.name);
    } else {
      const randomNum = Math.floor(Math.random() * 80) + 110;
      const newTicket = `A-${randomNum}`;
      return this.callPatient(newTicket, 'Walk-in Patient');
    }
  },

  // Add new walk-in or booked patient to queue
  addPatient(patient) {
    const state = this.getState();
    const formattedPatient = {
      ticket: patient.ticket,
      name: patient.name,
      age: patient.age || 35,
      gender: patient.gender || 'Female',
      phone: patient.phone || '09XX-XXX-XXXX',
      patientId: 'PT-' + Math.floor(Math.random() * 9000 + 1000),
      type: patient.type || 'Walk-in',
      time: patient.time || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      department: patient.department || 'General Medicine',
      vitals: patient.vitals || { bp: '120/80', temp: '36.8', pulse: '76', spo2: '98', weight: '60' },
      complaint: patient.reason || 'General Consultation',
      diagnosis: 'Pending Physician Evaluation',
      prescription: 'Pending Physician Evaluation',
      isPriority: !!patient.isPriority
    };

    // Priority patients jump ahead of regular patients
    if (formattedPatient.isPriority) {
      const firstNonPriorityIdx = state.upcomingQueue.findIndex(p => !p.isPriority);
      if (firstNonPriorityIdx !== -1) {
        state.upcomingQueue.splice(firstNonPriorityIdx, 0, formattedPatient);
      } else {
        state.upcomingQueue.push(formattedPatient);
      }
    } else {
      state.upcomingQueue.push(formattedPatient);
    }

    this.saveState(state);
    return formattedPatient;
  },

  // Reset to demo defaults
  resetDefaults() {
    this.saveState(DEFAULT_QUEUE_STATE);
  }
};

// Listen for cross-tab storage changes
window.addEventListener('storage', (e) => {
  if (e.key === 'carepoint_queue_state') {
    window.dispatchEvent(new CustomEvent('queue_state_updated', {
      detail: JSON.parse(e.newValue || '{}')
    }));
  }
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
