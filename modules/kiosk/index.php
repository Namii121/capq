<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-white select-none">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarePoint - Patient Self-Check-In Kiosk</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;800&display=swap" rel="stylesheet">
  
  <!-- Core Stylesheet & Fonts -->
  <link rel="stylesheet" href="assets/css/style.css">
  
  <!-- Tailwind CSS Configuration (must be defined before CDN script for first-load rendering) -->
  <script>
    tailwind = {
      config: {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
              mono: ['JetBrains Mono', 'monospace'],
            },
            colors: {
              brand: {
                50: '#f0fdfa',
                100: '#ccfbf1',
                500: '#14b8a6',
                600: '#0d9488',
                700: '#0f766e',
                800: '#115e59',
                900: '#134e4a',
              }
            }
          }
        }
      }
    };
  </script>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
    button { touch-action: manipulation; }
    .kiosk-card {
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .kiosk-card:active {
      transform: scale(0.985);
    }
    .touch-btn {
      touch-action: manipulation;
      transition: all 0.15s ease;
    }
    .touch-btn:active {
      transform: scale(0.94);
      background-color: #0d9488;
      color: white;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col font-sans bg-slate-950 text-white antialiased overflow-x-hidden">

  <!-- ========================================================================= -->
  <!-- UNIFIED DEMO SUITE SWITCHER BAR                                           -->
  <!-- ========================================================================= -->
  <div class="bg-slate-900 text-slate-300 px-4 py-2 text-xs border-b border-slate-800 flex-shrink-0">
    <div class="max-w-[1920px] mx-auto flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
        <span class="font-bold text-white uppercase text-[11px]">CarePoint CAPQ:</span>
        <span class="text-slate-400 hidden sm:inline">Self-Service Check-In Kiosk (Station 01)</span>
      </div>
      <nav class="flex items-center gap-1.5 flex-wrap">
        <a href="index.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> Booking Portal</a>
        <a href="kiosk.php" class="px-2.5 py-1 rounded bg-teal-600 text-white font-bold flex items-center gap-1"><i data-lucide="tablet" class="w-3 h-3"></i> Self Kiosk</a>
        <a href="display.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="tv" class="w-3 h-3"></i> Live TV Queue</a>
        <a href="doctor.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="stethoscope" class="w-3 h-3"></i> Doctor Console</a>
        <a href="admin.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="shield" class="w-3 h-3"></i> Admin Dashboard</a>
      </nav>
    </div>
  </div>

  <!-- KIOSK TOP BAR -->
  <header class="px-6 sm:px-8 py-5 border-b border-slate-800/80 flex items-center justify-between flex-shrink-0">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-2xl bg-teal-500 text-slate-950 flex items-center justify-center font-bold shadow-lg shadow-teal-500/20">
        <i data-lucide="activity" class="w-7 h-7 text-slate-950"></i>
      </div>
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white uppercase">CarePoint Medical Center</h1>
        <p class="text-[11px] sm:text-xs text-teal-400 font-bold tracking-wider">TOUCHSCREEN SELF-SERVICE CHECK-IN KIOSK</p>
      </div>
    </div>

    <div class="text-right">
      <p class="text-2xl sm:text-3xl font-black font-mono text-white tracking-wider" id="kiosk-clock">10:45 AM</p>
      <p class="text-[11px] sm:text-xs text-slate-400">Please tap a card below to begin</p>
    </div>
  </header>

  <!-- MAIN TOUCH SCREEN BODY -->
  <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-8 sm:py-10 flex flex-col justify-center">
    
    <div class="text-center mb-8 sm:mb-10">
      <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight">Welcome! How can we assist you today?</h2>
      <p class="text-sm sm:text-base text-slate-400 mt-2">Select your check-in pathway to receive your official clinic queue pass</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 items-stretch">
      
      <!-- Option A: Existing Appointment -->
      <div class="kiosk-card bg-gradient-to-b from-slate-900 to-slate-900/90 border-2 border-slate-700 hover:border-teal-400 rounded-3xl p-6 sm:p-8 flex flex-col justify-between shadow-2xl relative overflow-hidden group">
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div>
          <div class="w-14 h-14 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center mb-5 group-hover:scale-105 transition">
            <i data-lucide="calendar-check" class="w-8 h-8"></i>
          </div>

          <span class="text-xs font-bold text-teal-400 tracking-wider uppercase">Scheduled Patients</span>
          <h3 class="text-2xl font-black text-white mt-1 mb-2">I Have an Appointment</h3>
          <p class="text-xs sm:text-sm text-slate-400 leading-relaxed mb-5">
            If you booked ahead online or with our staff, enter your 4-digit or 6-digit booking code to activate your ticket.
          </p>

          <div class="space-y-3">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Appointment Code</label>
            <div class="flex gap-2">
              <input type="text" id="kiosk-ref-input" placeholder="CP-8921" value="CP-8921" readonly class="flex-1 bg-slate-950 border-2 border-slate-700 rounded-2xl px-4 py-3 text-lg uppercase font-mono tracking-widest text-teal-300 text-center font-bold focus:outline-none focus:border-teal-400">
              <button onclick="handleAppointmentCheckin()" class="px-5 py-3 rounded-2xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs sm:text-sm transition flex items-center gap-1.5 shadow-lg shadow-teal-500/20">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Check-in</span>
              </button>
            </div>

            <div class="pt-2">
              <div class="grid grid-cols-3 gap-1.5 max-w-[280px] mx-auto text-xs font-bold">
                <button type="button" onclick="appendKey('1')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">1</button>
                <button type="button" onclick="appendKey('2')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">2</button>
                <button type="button" onclick="appendKey('3')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">3</button>
                <button type="button" onclick="appendKey('4')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">4</button>
                <button type="button" onclick="appendKey('5')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">5</button>
                <button type="button" onclick="appendKey('6')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">6</button>
                <button type="button" onclick="appendKey('7')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">7</button>
                <button type="button" onclick="appendKey('8')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">8</button>
                <button type="button" onclick="appendKey('9')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">9</button>
                <button type="button" onclick="clearKey()" class="touch-btn p-2.5 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs">Clear</button>
                <button type="button" onclick="appendKey('0')" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm">0</button>
                <button type="button" onclick="backspaceKey()" class="touch-btn p-2.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 text-xs flex items-center justify-center">⌫</button>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
          <span class="flex items-center gap-1.5">
            <i data-lucide="qr-code" class="w-4 h-4 text-teal-400"></i>
            QR Optical Scanner Active
          </span>
          <span class="text-teal-400 font-bold">Fast-Track Pass &rarr;</span>
        </div>
      </div>

      <!-- Option B: Walk-in Patient -->
      <div class="kiosk-card bg-gradient-to-b from-slate-900 to-slate-900/90 border-2 border-slate-700 hover:border-blue-400 rounded-3xl p-6 sm:p-8 flex flex-col justify-between shadow-2xl relative overflow-hidden group">
        <div class="absolute -right-12 -top-12 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div>
          <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center mb-5 group-hover:scale-105 transition">
            <i data-lucide="users" class="w-8 h-8"></i>
          </div>

          <span class="text-xs font-bold text-blue-400 tracking-wider uppercase">Direct Consultation</span>
          <h3 class="text-2xl font-black text-white mt-1 mb-2">Walk-In Patient</h3>
          <p class="text-xs sm:text-sm text-slate-400 leading-relaxed mb-5">
            No appointment? Tap your required clinic specialty below to instantly generate your queue ticket slip.
          </p>

          <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
            <button onclick="issueKioskTicket('General Medicine', 'A', 'Dr. Sarah Santos, MD')" class="touch-btn p-3.5 sm:p-4 rounded-2xl bg-slate-950 border-2 border-slate-800 hover:border-teal-400 text-left transition flex flex-col justify-between">
              <span class="text-sm font-bold text-white">General Care</span>
              <span class="text-[11px] text-teal-400 mt-1 font-mono">~15m wait (3 ahead)</span>
            </button>

            <button onclick="issueKioskTicket('Pediatrics', 'P', 'Dr. Patricia Lim, MD')" class="touch-btn p-3.5 sm:p-4 rounded-2xl bg-slate-950 border-2 border-slate-800 hover:border-teal-400 text-left transition flex flex-col justify-between">
              <span class="text-sm font-bold text-white">Pediatrics</span>
              <span class="text-[11px] text-teal-400 mt-1 font-mono">~10m wait (1 ahead)</span>
            </button>

            <button onclick="issueKioskTicket('Dental Clinic', 'D', 'Dr. Michael Tan, DMD')" class="touch-btn p-3.5 sm:p-4 rounded-2xl bg-slate-950 border-2 border-slate-800 hover:border-teal-400 text-left transition flex flex-col justify-between">
              <span class="text-sm font-bold text-white">Dental Care</span>
              <span class="text-[11px] text-teal-400 mt-1 font-mono">~25m wait (4 ahead)</span>
            </button>

            <button onclick="issueKioskTicket('Triage & Vitals', 'T', 'Nurse Station 1')" class="touch-btn p-3.5 sm:p-4 rounded-2xl bg-slate-950 border-2 border-slate-800 hover:border-teal-400 text-left transition flex flex-col justify-between">
              <span class="text-sm font-bold text-white">Triage & Lab</span>
              <span class="text-[11px] text-teal-400 mt-1 font-mono">~5m wait (0 ahead)</span>
            </button>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
          <span class="flex items-center gap-1.5">
            <i data-lucide="printer" class="w-4 h-4 text-blue-400"></i>
            Thermal Dispenser Ready
          </span>
          <span class="text-blue-400 font-bold">Tap Specialty to Print &rarr;</span>
        </div>
      </div>

    </div>

    <!-- Priority Lane Banner -->
    <div class="mt-6 sm:mt-8 p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-500/10 via-amber-500/15 to-transparent border border-amber-500/30 flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-3 sm:gap-4">
        <div class="w-11 h-11 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0">
          <i data-lucide="accessibility" class="w-6 h-6"></i>
        </div>
        <div>
          <h4 class="text-sm sm:text-base font-bold text-white">Need Senior Citizen or PWD Priority Assistance?</h4>
          <p class="text-xs text-amber-200/80">Seniors (60+), Persons with Disabilities, and Expecting Mothers may press the button for assisted intake.</p>
        </div>
      </div>
      <button onclick="issuePriorityTicket()" class="px-5 py-2.5 sm:py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider transition shadow-lg shadow-amber-500/20">
        Request Priority Pass
      </button>
    </div>

  </main>

  <!-- Ticket Print Modal -->
  <div id="kiosk-ticket-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-white text-slate-900 rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center animate-in zoom-in-95 duration-200">
      
      <div class="border-b-2 border-dashed border-slate-300 pb-4 mb-4">
        <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center mx-auto mb-2 font-bold">
          <i data-lucide="activity" class="w-5 h-5"></i>
        </div>
        <h3 class="text-base font-black tracking-tight uppercase">CarePoint Medical Center</h3>
        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Self-Service Queue Ticket</p>
      </div>

      <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">YOUR NUMBER</p>
      <h2 id="slip-number" class="text-6xl font-black font-mono text-teal-600 my-2">A-108</h2>
      <p id="slip-dept" class="text-base font-extrabold text-slate-900">General Medicine</p>
      <p id="slip-doctor" class="text-xs text-slate-500">Dr. Sarah Santos, MD • Room 03</p>

      <div class="bg-slate-100 rounded-2xl p-4 my-4 text-xs space-y-1.5 text-left font-medium">
        <div class="flex justify-between">
          <span class="text-slate-500">Patients Ahead:</span>
          <span class="font-bold text-slate-800" id="slip-ahead">3 Persons</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Estimated Wait:</span>
          <span class="font-bold text-teal-700" id="slip-wait">~15 Minutes</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Time Issued:</span>
          <span class="font-mono text-slate-700" id="slip-time">10:45 AM</span>
        </div>
      </div>

      <div class="w-20 h-20 bg-slate-900 text-teal-300 mx-auto rounded-xl flex items-center justify-center mb-3">
        <i data-lucide="qr-code" class="w-14 h-14"></i>
      </div>
      <p class="text-[10px] text-slate-500">Please watch the Waiting Room TV Display for your number.</p>

      <div class="mt-5 flex gap-2">
        <button onclick="window.print()" class="flex-1 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
          <i data-lucide="printer" class="w-4 h-4"></i> Print Receipt
        </button>
        <button onclick="closeKioskModal()" class="py-3 px-5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
          Done
        </button>
      </div>

    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/js/queue-api.js"></script>
  <script>
    lucide.createIcons();

    function updateClock() {
      const now = new Date();
      document.getElementById('kiosk-clock').textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    function appendKey(num) {
      const input = document.getElementById('kiosk-ref-input');
      if (input.value.length < 8) {
        input.value += num;
      }
    }
    function clearKey() {
      document.getElementById('kiosk-ref-input').value = 'CP-';
    }
    function backspaceKey() {
      const input = document.getElementById('kiosk-ref-input');
      if (input.value.length > 3) {
        input.value = input.value.slice(0, -1);
      }
    }

    async function issueKioskTicket(dept, prefix, doc) {
      try {
        const data = await QueueManager.addPatient({
          name: `Walk-in Patient`,
          department: dept,
          reason: `${dept} Self Check-In`,
          isPriority: false
        });

        const ticket = data ? data.ticket_number : `${prefix}-105`;
        const nowTime = data ? data.time : new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const ahead = (data && typeof data.patients_ahead !== 'undefined') ? data.patients_ahead : 0;
        const wait = (data && typeof data.est_wait_min !== 'undefined') ? data.est_wait_min : 0;
        const room = data ? (data.room_assigned || 'Room 03') : 'Room 03';
        const doctor = data ? (data.doctor_name || doc) : doc;

        document.getElementById('slip-number').textContent = ticket;
        document.getElementById('slip-dept').textContent = dept;
        document.getElementById('slip-doctor').textContent = `${doctor} • ${room}`;
        document.getElementById('slip-ahead').textContent = `${ahead} Person${ahead === 1 ? '' : 's'} Ahead`;
        document.getElementById('slip-wait').textContent = wait > 0 ? `~${wait} Minutes` : 'Proceed Immediately';
        document.getElementById('slip-time').textContent = nowTime;

        QueueManager.playChime();
        document.getElementById('kiosk-ticket-modal').classList.remove('hidden');
      } catch (e) {
        showToast('Check-in failed', 'Kiosk Error');
      }
    }

    async function issuePriorityTicket() {
      try {
        const data = await QueueManager.addPatient({
          name: `Priority Patient`,
          department: 'General Medicine',
          reason: 'Priority Lane Check-In',
          isPriority: true
        });

        const ticket = data ? data.ticket_number : 'P-015';
        const nowTime = data ? data.time : new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        document.getElementById('slip-number').textContent = ticket;
        document.getElementById('slip-dept').textContent = 'General Medicine (Priority Lane)';
        document.getElementById('slip-doctor').textContent = 'Dr. Sarah Santos, MD • Room 03';
        document.getElementById('slip-ahead').textContent = '0 (Next Priority)';
        document.getElementById('slip-wait').textContent = 'Proceed Immediately';
        document.getElementById('slip-time').textContent = nowTime;

        QueueManager.playChime();
        document.getElementById('kiosk-ticket-modal').classList.remove('hidden');
      } catch (e) {
        showToast('Priority check-in failed', 'Kiosk Error');
      }
    }

    async function handleAppointmentCheckin() {
      const code = document.getElementById('kiosk-ref-input').value.trim();
      if (!code || code === 'CP-') {
        showToast('Please enter your appointment reference code.', 'Code Required');
        return;
      }
      try {
        const res = await fetch(`modules/kiosk/actions.php?action=checkin_appointment&code=${encodeURIComponent(code)}`);
        const json = await res.json();
        if (json.success && json.data) {
          const d = json.data;
          const t = d.ticket_number;
          const dept = d.department;
          const doc = d.doctor_name;
          const room = d.room_assigned;
          const ahead = d.patients_ahead;
          const wait = d.est_wait_min;
          const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

          document.getElementById('slip-number').textContent = t;
          document.getElementById('slip-dept').textContent = `${dept} (${d.status === 'CALLING' ? 'PROCEED NOW' : 'Checked-in'})`;
          document.getElementById('slip-doctor').textContent = `${doc} • ${room}`;
          document.getElementById('slip-ahead').textContent = `${ahead} Person${ahead === 1 ? '' : 's'} Ahead`;
          document.getElementById('slip-wait').textContent = wait > 0 ? `~${wait} Minutes` : 'Proceed Immediately';
          document.getElementById('slip-time').textContent = nowTime;

          QueueManager.playChime();
          document.getElementById('kiosk-ticket-modal').classList.remove('hidden');
          QueueManager.syncFromBackend();
          showToast(json.message || `Check-in successful for ${t}!`, 'Appointment Validated');
        } else {
          // Trap invalid or already processed code
          showToast(json.message || `Invalid Appointment Code '${code}'.`, 'Check-In Rejected');
        }
      } catch (e) {
        showToast('Could not validate appointment code.', 'Server Error');
      }
    }

    function closeKioskModal() {
      document.getElementById('kiosk-ticket-modal').classList.add('hidden');
    }
  </script>
</body>
</html>
