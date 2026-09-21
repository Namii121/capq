<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-white select-none">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarePoint Medical - Live Waiting Room Queue Display</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@700;800;900&display=swap" rel="stylesheet">
  
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
    @keyframes pulse-call {
      0%, 100% { transform: scale(1); box-shadow: 0 0 35px rgba(20, 184, 166, 0.25); }
      50% { transform: scale(1.015); box-shadow: 0 0 60px rgba(20, 184, 166, 0.45); }
    }
    .pulse-calling {
      animation: pulse-call 2s infinite ease-in-out;
    }
    
    @keyframes flash-beacon {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }
    .flash-beacon {
      animation: flash-beacon 1.2s infinite ease-in-out;
    }

    @keyframes marquee {
      0% { transform: translateX(100%); }
      100% { transform: translateX(-100%); }
    }
    .animate-marquee {
      display: inline-block;
      white-space: nowrap;
      animation: marquee 32s linear infinite;
    }

    @keyframes wave-ripple {
      0% { transform: scale(0.95); opacity: 0.8; }
      50% { transform: scale(1.05); opacity: 0.3; }
      100% { transform: scale(0.95); opacity: 0.8; }
    }
    .calling-wave {
      animation: wave-ripple 1.8s infinite ease-in-out;
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
        <span class="text-slate-400 hidden sm:inline">Waiting Lounge TV Display</span>
      </div>
      <nav class="flex items-center gap-1.5 flex-wrap">
        <a href="index.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> Booking Portal</a>
        <a href="kiosk.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="tablet" class="w-3 h-3"></i> Self Kiosk</a>
        <a href="display.php" class="px-2.5 py-1 rounded bg-teal-600 text-white font-bold flex items-center gap-1"><i data-lucide="tv" class="w-3 h-3"></i> Live TV Queue</a>
        <a href="doctor.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="stethoscope" class="w-3 h-3"></i> Doctor Console</a>
        <a href="admin.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="shield" class="w-3 h-3"></i> Admin Dashboard</a>
      </nav>
    </div>
  </div>

  <!-- TV DISPLAY HEADER: LOGO, CONTROLS & LIVE CLOCK -->
  <header class="bg-slate-900/90 px-6 sm:px-8 py-4 border-b border-slate-800 flex flex-wrap items-center justify-between gap-4 flex-shrink-0">
    <div class="flex items-center gap-3 sm:gap-4">
      <div class="w-12 h-12 rounded-2xl bg-teal-500 text-slate-950 flex items-center justify-center font-bold shadow-lg shadow-teal-500/20">
        <i data-lucide="activity" class="w-7 h-7 text-slate-950"></i>
      </div>
      <div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white uppercase leading-tight">CarePoint Medical Center</h1>
        <p class="text-[11px] sm:text-xs font-bold tracking-widest text-teal-400 uppercase">Live Patient Calling & Queue Board</p>
      </div>
    </div>

    <div class="flex items-center gap-4 sm:gap-6">
      <div class="flex items-center gap-2">
        <button onclick="testChimeSound()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-teal-300 text-xs font-bold transition flex items-center gap-2" title="Click to test waiting room chime">
          <i data-lucide="volume-2" class="w-4 h-4 text-teal-400"></i>
          <span id="audio-status-label">Test Chime</span>
        </button>

        <button onclick="simulateNextCall()" class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-teal-600/20">
          <i data-lucide="phone-forwarded" class="w-3.5 h-3.5"></i>
          <span>Simulate Call</span>
        </button>

        <button onclick="toggleFullscreen()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white transition" title="Toggle Fullscreen TV Mode">
          <i data-lucide="maximize" class="w-4 h-4" id="fullscreen-icon"></i>
        </button>
      </div>

      <div class="text-right border-l border-slate-800 pl-4 sm:pl-6">
        <p class="text-2xl sm:text-3xl font-black font-mono tracking-widest text-white leading-none" id="tv-clock">10:48:32 AM</p>
        <p class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-1" id="tv-date">Thursday, September 17, 2026</p>
      </div>
    </div>
  </header>

  <!-- MAIN TV VIEWPORT -->
  <main class="flex-1 max-w-[1920px] w-full mx-auto p-4 sm:p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-stretch">
    
    <!-- Left 6 Cols: HUGE NOW CALLING SPOTLIGHT -->
    <div class="lg:col-span-6 bg-gradient-to-br from-teal-950/90 via-slate-900 to-slate-900 border-2 border-teal-500/70 rounded-3xl p-6 sm:p-8 flex flex-col justify-between shadow-2xl relative pulse-calling min-h-[440px]">
      
      <div class="absolute -right-16 -top-16 w-64 h-64 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

      <div class="flex items-center justify-between pb-3 border-b border-teal-500/30">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-teal-500/20 border border-teal-500/40 text-teal-300 text-xs font-black uppercase tracking-widest">
          <span class="w-2.5 h-2.5 rounded-full bg-teal-400 flash-beacon"></span>
          Now Calling / Now Serving
        </span>
        <span class="text-xs font-mono text-teal-300/80 font-bold" id="tv-call-time">SYSTEM IDLE</span>
      </div>

      <div class="my-auto text-center py-6 sm:py-8 relative">
        <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-slate-400 mb-2">TICKET NUMBER</p>
        <h2 id="tv-ticket" class="text-7xl sm:text-8xl xl:text-9xl font-black font-mono tracking-tight text-teal-300 drop-shadow-[0_4px_35px_rgba(20,184,166,0.45)] leading-none">
          ---
        </h2>
        <p id="tv-patient-name" class="text-xl sm:text-2xl font-black text-white mt-3 tracking-wide">No Active Ticket</p>
      </div>

      <div class="bg-slate-950/90 rounded-2xl p-5 sm:p-6 border-2 border-slate-800 text-center shadow-inner">
        <p class="text-xs font-black uppercase tracking-widest text-teal-400 mb-1 flex items-center justify-center gap-1.5">
          <i data-lucide="arrow-right-circle" class="w-4 h-4"></i>
          PLEASE PROCEED TO
        </p>
        <h3 id="tv-room" class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight">WAITING FOR NEXT PATIENT</h3>
        <p id="tv-doctor" class="text-sm font-semibold text-slate-400 mt-1">Consultation Rooms Available</p>
      </div>

    </div>

    <!-- Right 6 Cols: MULTI-ROOM QUEUE STATUS BOARD -->
    <div class="lg:col-span-6 flex flex-col justify-between space-y-3 sm:space-y-3.5">
      
      <div class="flex items-center justify-between pb-2 border-b border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400 px-3">
        <span>Consultation Room / Service</span>
        <span class="text-center">Now Serving</span>
        <span class="text-right">Next in Line</span>
      </div>

      <div id="rooms-status-list" class="space-y-3 sm:space-y-3.5">
        <!-- Room cards rendered dynamically via JS -->
      </div>

    </div>

  </main>

  <!-- TV FOOTER MARQUEE -->
  <footer class="bg-slate-900 px-6 py-3.5 border-t border-slate-800 flex items-center gap-4 text-xs flex-shrink-0">
    <div class="px-3 py-1.5 rounded-lg bg-teal-500/20 text-teal-300 font-black uppercase tracking-wider flex items-center gap-2 flex-shrink-0">
      <i data-lucide="megaphone" class="w-4 h-4 text-teal-400"></i>
      <span>ANNOUNCEMENT</span>
    </div>
    <div class="overflow-hidden whitespace-nowrap text-slate-300 flex-1">
      <p class="animate-marquee inline-block font-semibold">
        🔔 Welcome to CarePoint Medical Center. Please prepare your Queue Ticket and valid Photo ID when your number is called. Wear a face mask if experiencing cough or respiratory symptoms. Free Clinic WiFi: <span class="text-teal-300 font-bold">CarePoint_Guest</span> (No password). If you require immediate emergency assistance, notify the Triage Desk in Room 1 immediately.
      </p>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="assets/js/queue-api.js"></script>
  <script>
    lucide.createIcons();

    function updateClock() {
      const now = new Date();
      document.getElementById('tv-clock').textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
      document.getElementById('tv-date').textContent = now.toLocaleDateString([], { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    function syncDisplayWithState(state) {
      if (!state) state = QueueManager.getState();

      if (state && state.nowCalling) {
        const t = state.nowCalling.ticket_number || state.nowCalling.ticket || '---';
        const name = state.nowCalling.patient_name || state.nowCalling.name || 'Patient';
        const room = state.nowCalling.room_assigned || state.nowCalling.room || 'Room 03';
        const dept = state.nowCalling.department || 'General Medicine';
        const doc = state.nowCalling.doctor_name || state.nowCalling.doctor || 'Attending Physician';
        const time = state.nowCalling.time_slot || state.nowCalling.time || '';

        document.getElementById('tv-ticket').textContent = t;
        document.getElementById('tv-patient-name').textContent = name;
        document.getElementById('tv-room').textContent = `${room.toUpperCase()} • ${dept.toUpperCase()}`;
        document.getElementById('tv-doctor').textContent = `${doc} • Attending Physician`;
        document.getElementById('tv-call-time').textContent = time ? `CALLED AT ${time}` : 'NOW SERVING';
      } else {
        document.getElementById('tv-ticket').textContent = '---';
        document.getElementById('tv-patient-name').textContent = 'No Active Ticket';
        document.getElementById('tv-room').textContent = 'WAITING FOR NEXT PATIENT';
        document.getElementById('tv-doctor').textContent = 'Consultation Rooms Available';
        document.getElementById('tv-call-time').textContent = 'SYSTEM IDLE';
      }

      // Render Rooms Status Board
      const roomsContainer = document.getElementById('rooms-status-list');
      if (roomsContainer && state && state.rooms && state.rooms.length > 0) {
        roomsContainer.innerHTML = state.rooms.map(r => {
          const roomNum = r.room_number || r.room || '01';
          const roomName = r.room_name || r.name || 'Clinic Room';
          const doctorName = r.doctor_name || r.doctor || 'Staff';
          const currentTicket = (r.current_ticket && r.current_ticket !== '---') ? r.current_ticket : (r.ticket && r.ticket !== '---' ? r.ticket : '---');
          
          let next1 = '--';
          let next2 = '--';
          if (Array.isArray(r.next) && r.next.length > 0) {
            next1 = r.next[0] || '--';
            if (r.next.length > 1) next2 = r.next[1] || '--';
          }

          const isBusy = currentTicket !== '---';
          const bgClass = isBusy ? 'bg-teal-950/50 border-2 border-teal-500/70 shadow-lg shadow-teal-500/15' : 'bg-slate-900/90 border border-slate-800';

          return `
            <div class="${bgClass} rounded-2xl p-3.5 sm:p-4 flex items-center justify-between transition">
              <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-teal-300 font-mono font-black text-base sm:text-lg">
                  ${roomNum}
                </div>
                <div>
                  <h4 class="text-sm sm:text-base font-bold text-white">${roomName}</h4>
                  <p class="text-[11px] text-slate-400">${doctorName}</p>
                </div>
              </div>
              <div class="text-center">
                <span class="font-mono text-2xl sm:text-3xl font-black ${isBusy ? 'text-teal-300' : 'text-slate-500'}">${currentTicket}</span>
              </div>
              <div class="text-right">
                <span class="font-mono text-xs text-slate-300 font-bold block">${next1}</span>
                <span class="font-mono text-[10px] text-slate-500">${next2}</span>
              </div>
            </div>
          `;
        }).join('');
        if (window.lucide) lucide.createIcons();
      }
    }

    syncDisplayWithState();

    window.addEventListener('queue_state_updated', (e) => {
      syncDisplayWithState(e.detail);
    });

    function testChimeSound() {
      QueueManager.playChime();
      document.getElementById('audio-status-label').textContent = 'Chime Played 🔔';
      setTimeout(() => {
        document.getElementById('audio-status-label').textContent = 'Test Chime';
      }, 2000);
    }

    function simulateNextCall() {
      QueueManager.callNext();
    }

    function toggleFullscreen() {
      const icon = document.getElementById('fullscreen-icon');
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
          console.warn('Fullscreen error:', err);
        });
        icon.setAttribute('data-lucide', 'minimize');
      } else {
        document.exitFullscreen();
        icon.setAttribute('data-lucide', 'maximize');
      }
      lucide.createIcons();
    }
  </script>
</body>
</html>
