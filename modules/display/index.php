<?php
if (!function_exists('getAppBaseUrl')) {
    @include_once __DIR__ . '/../../config/db.php';
}
$baseUrl = function_exists('getAppBaseUrl') ? getAppBaseUrl() : '/';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-white select-none">
<head>
  <meta charset="UTF-8">
  <base href="<?= $baseUrl ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarePoint Medical - Live Waiting Room Queue Display</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@700;800;900&display=swap" rel="stylesheet">
  
  <!-- Core Stylesheet & Fonts -->
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  
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
        <p id="tv-doctor" class="text-sm font-semibold text-slate-400 mt-1">Dr. Sarah Santos, MD • Attending Physician</p>
      </div>

    </div>

    <!-- Right 6 Cols: MULTI-ROOM QUEUE STATUS BOARD & PREPARING BANNER -->
    <div class="lg:col-span-6 flex flex-col justify-between space-y-3 sm:space-y-3.5">
      
      <!-- PREPARING / ON DECK SPOTLIGHT BANNER -->
      <div id="tv-preparing-banner" class="bg-gradient-to-r from-amber-950/80 via-amber-900/50 to-slate-900 border-2 border-amber-500/80 rounded-2xl p-4 flex items-center justify-between shadow-xl hidden">
        <div class="flex items-center gap-3.5">
          <div class="w-12 h-12 rounded-xl bg-amber-500 text-slate-950 font-mono font-black text-xl flex items-center justify-center shadow-md animate-pulse" id="tv-prep-ticket">
            ---
          </div>
          <div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-black uppercase tracking-wider border border-amber-500/40">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
              ON DECK / PREPARE NOW
            </span>
            <h4 class="text-base font-black text-white mt-0.5" id="tv-prep-name">Patient Name</h4>
          </div>
        </div>
        <div class="text-right">
          <p class="text-xs font-bold text-amber-300 uppercase tracking-wider">PLEASE STAND BY</p>
          <p class="text-[11px] text-slate-400 font-medium">Prepare for Consultation</p>
        </div>
      </div>

      <div class="flex items-center justify-between pb-2 border-b border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400 px-3">
        <span>Consultation Room / Service</span>
        <span class="text-center">Now Serving</span>
        <span class="text-right">Next in Line</span>
      </div>

      <div id="rooms-status-list" class="space-y-3 sm:space-y-3.5">
        <!-- Room cards rendered dynamically via JS -->
      </div>

      <!-- ALL PENDING QUEUE NUMBERS IN LIVE VIEW -->
      <div class="mt-3 bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex-1 flex flex-col min-h-[220px]">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <div class="flex items-center gap-2">
            <i data-lucide="list-ordered" class="w-4 h-4 text-teal-400"></i>
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-200">All Pending Queue Numbers in Line</h3>
          </div>
          <span class="text-[11px] font-mono font-bold px-2.5 py-0.5 rounded-full bg-slate-800 text-teal-400 border border-slate-700" id="tv-pending-count">0 Pending</span>
        </div>
        
        <div id="tv-pending-queue-grid" class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2 overflow-y-auto max-h-[280px] pr-1">
          <!-- Pending ticket cards rendered dynamically via JS -->
        </div>
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
        🔔 Welcome to CarePoint Medical Center. Please prepare your Queue Ticket and valid Photo ID when your number is called. Wear a face mask if experiencing cough or respiratory symptoms. Free Clinic WiFi: <span class="text-teal-300 font-bold">CarePoint_Guest</span> (No password).
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
        const room = state.nowCalling.room_assigned || state.nowCalling.room || 'Consultation Room';
        const dept = state.nowCalling.department || 'General Consultation';
        const doc = state.nowCalling.doctor_name || state.nowCalling.doctor || 'Dr. Sarah Santos, MD';
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
        document.getElementById('tv-doctor').textContent = 'Dr. Sarah Santos, MD • Attending Physician';
        document.getElementById('tv-call-time').textContent = 'SYSTEM IDLE';
      }

      if (state && state.nowPreparing) {
        const prep = state.nowPreparing;
        const pTicket = prep.ticket_number || prep.ticket || '---';
        const pName = prep.patient_name || prep.name || 'Patient';
        document.getElementById('tv-prep-ticket').textContent = pTicket;
        document.getElementById('tv-prep-name').textContent = pName;
        document.getElementById('tv-preparing-banner').classList.remove('hidden');
      } else {
        document.getElementById('tv-preparing-banner').classList.add('hidden');
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
      }

      // Render All Pending Queue Numbers Grid
      const pendingGrid = document.getElementById('tv-pending-queue-grid');
      const pendingCount = document.getElementById('tv-pending-count');
      const upcoming = (state && state.upcomingQueue) ? state.upcomingQueue : [];

      if (pendingCount) {
        pendingCount.textContent = `${upcoming.length} Pending`;
      }

      if (pendingGrid) {
        if (upcoming.length === 0) {
          pendingGrid.innerHTML = `
            <div class="col-span-full text-center py-8 text-slate-500 text-xs font-medium border border-dashed border-slate-800 rounded-xl">
              No pending patients in queue line.
            </div>
          `;
        } else {
          pendingGrid.innerHTML = upcoming.map(item => {
            const ticket = item.ticket || item.ticket_number || '---';
            const name = item.name || item.patient_name || 'Patient';
            const isPrep = item.status === 'PREPARING' || item.isPreparing;
            const isPrio = item.isPriority || item.is_priority;

            const cardBg = isPrep
              ? 'bg-amber-950/40 border-amber-500/60 text-amber-200 shadow-md shadow-amber-500/10'
              : 'bg-slate-950/70 border-slate-800 text-slate-300';

            const badgeBg = isPrep
              ? 'bg-amber-500/20 text-amber-300 border-amber-500/40'
              : 'bg-teal-500/20 text-teal-300 border-teal-500/40';

            const badgeText = isPrep ? 'ON DECK' : 'WAITING';

            return `
              <div class="p-2.5 rounded-xl border ${cardBg} flex flex-col justify-between transition">
                <div class="flex items-center justify-between gap-1 mb-1">
                  <span class="font-mono font-black text-base ${isPrep ? 'text-amber-300' : 'text-teal-300'}">${ticket}</span>
                  <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full border ${badgeBg}">
                    ${badgeText}
                  </span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                  <span class="font-semibold truncate max-w-[120px] text-slate-200">${name}</span>
                  ${isPrio ? '<span class="text-[9px] font-bold text-amber-400 bg-amber-950/80 px-1 py-0.2 rounded border border-amber-500/40">PRIORITY</span>' : ''}
                </div>
              </div>
            `;
          }).join('');
        }
      }

      if (window.lucide) lucide.createIcons();
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
