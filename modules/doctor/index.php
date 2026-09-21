<?php
if (!function_exists('getAppBaseUrl')) {
    @include_once __DIR__ . '/../../config/db.php';
}
$baseUrl = function_exists('getAppBaseUrl') ? getAppBaseUrl() : '/';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
  <meta charset="UTF-8">
  <base href="<?= $baseUrl ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarePoint - Doctor Consultation & Queue Station</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;800&display=swap" rel="stylesheet">
  
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
</head>
<body class="min-h-screen flex flex-col font-sans text-slate-800 antialiased bg-slate-100">

  <!-- ========================================================================= -->
  <!-- UNIFIED DEMO SUITE SWITCHER BAR                                           -->
  <!-- ========================================================================= -->
  <div class="bg-slate-900 text-slate-300 px-4 py-2 text-xs border-b border-slate-800 flex-shrink-0">
    <div class="max-w-[1920px] mx-auto flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
        <span class="font-bold text-white uppercase text-[11px]">CarePoint CAPQ:</span>
        <span class="text-slate-400 hidden sm:inline">Doctor Consultation Station • Solo Clinic</span>
      </div>
      <nav class="flex items-center gap-1.5 flex-wrap">
        <a href="index.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> Booking Portal</a>
        <a href="kiosk.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="tablet" class="w-3 h-3"></i> Self Kiosk</a>
        <a href="display.php" target="_blank" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="tv" class="w-3 h-3"></i> Live TV Queue <i data-lucide="external-link" class="w-2.5 h-2.5 ml-0.5"></i></a>
        <a href="doctor.php" class="px-2.5 py-1 rounded bg-teal-600 text-white font-bold flex items-center gap-1"><i data-lucide="stethoscope" class="w-3 h-3"></i> Doctor Console</a>
        <a href="admin.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="shield" class="w-3 h-3"></i> Admin Dashboard</a>
      </nav>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- DOCTOR STATION LOGIN VIEW                                                 -->
  <!-- ========================================================================= -->
  <div id="doctor-login-view" class="flex-1 flex items-center justify-center p-6 bg-slate-900/95 relative overflow-hidden hidden">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full bg-white text-slate-900 rounded-3xl p-8 shadow-2xl border border-slate-200 relative z-10 animate-in fade-in zoom-in-95 duration-200">
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center mx-auto mb-3 shadow-md">
          <i data-lucide="stethoscope" class="w-7 h-7"></i>
        </div>
        <h2 class="text-xl font-black text-slate-900">Doctor Station Login</h2>
        <p class="text-xs text-slate-500 mt-1">Authenticate to access EHR consultation station & live queue dispatch</p>
      </div>

      <!-- Quick 1-Click Demo Selector -->
      <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200">
        <div class="flex items-center gap-2 mb-2 text-teal-700 font-bold text-xs">
          <i data-lucide="zap" class="w-4 h-4 text-teal-600"></i>
          <span>Quick Select Doctor (Demo 1-Click)</span>
        </div>
        <select id="quick-doctor-select" onchange="quickDoctorLogin(this.value)" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
          <option value="">-- Choose Registered Physician Account --</option>
        </select>
      </div>

      <div class="relative flex py-2 items-center mb-5">
        <div class="flex-grow border-t border-slate-200"></div>
        <span class="flex-shrink mx-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Or Enter Login Credentials</span>
        <div class="flex-grow border-t border-slate-200"></div>
      </div>

      <!-- Credentials Form -->
      <form onsubmit="event.preventDefault(); submitDoctorLogin();" class="space-y-4 text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Account Username</label>
          <div class="relative">
            <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
            <input type="text" id="login-username" placeholder="e.g. dr.reyes" required class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Account Password</label>
          <div class="relative">
            <i data-lucide="key-round" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
            <input type="password" id="login-password" placeholder="••••••••" required class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
          </div>
        </div>

        <button type="submit" class="w-full py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition flex items-center justify-center gap-2 mt-2">
          <i data-lucide="log-in" class="w-4 h-4"></i>
          <span>Login to Station</span>
        </button>
      </form>

    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- DOCTOR WORKSTATION VIEW (AUTHENTICATED)                                   -->
  <!-- ========================================================================= -->
  <div id="doctor-workstation" class="flex-1 flex flex-col">

  <!-- DOCTOR WORKSTATION HEADER -->
  <header class="bg-slate-900 text-white px-6 sm:px-8 py-4 border-b border-slate-800 flex flex-wrap items-center justify-between gap-4 flex-shrink-0">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-2xl bg-teal-500/20 text-teal-300 border border-teal-500/30 font-black text-xl flex items-center justify-center" id="header-doc-initials">
        DR
      </div>
      <div>
        <div class="flex items-center gap-2.5">
          <h1 class="text-xl font-bold text-white" id="header-doc-name">Dr. Sarah Santos, MD</h1>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
            ACTIVE CONSULTATION
          </span>
        </div>
        <p class="text-xs text-slate-400 mt-0.5" id="header-doc-room">Consultation Room • Dr. Sarah Santos, MD</p>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <div class="bg-slate-800/80 px-4 py-2 rounded-xl border border-slate-700 text-center">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Waiting in Queue</p>
        <p class="text-lg font-black font-mono text-teal-300" id="doc-waiting-count">5 Patients</p>
      </div>
      <div class="bg-slate-800/80 px-4 py-2 rounded-xl border border-slate-700 text-center">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Completed Today</p>
        <p class="text-lg font-black font-mono text-emerald-400" id="doc-completed-count">0 Served</p>
      </div>
      <button onclick="handleDoctorLogout()" class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition shadow-sm flex items-center gap-1.5 text-xs font-bold" title="Switch Doctor Account">
        <i data-lucide="log-out" class="w-4 h-4 text-rose-400"></i>
        <span class="hidden sm:inline">Logout</span>
      </button>
    </div>
  </header>

  <!-- ACTION CONTROLS TOOLBAR -->
  <div class="bg-white px-6 sm:px-8 py-3.5 border-b border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4 flex-shrink-0">
    
    <div class="flex items-center gap-3">
      <button onclick="handleCallNext()" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-black text-xs shadow-md shadow-teal-600/25 transition flex items-center gap-2">
        <i data-lucide="phone-forwarded" class="w-4 h-4"></i>
        <span>CALL NEXT PATIENT</span>
      </button>

      <button onclick="handlePrepareNext()" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-md shadow-amber-600/25 transition flex items-center gap-2">
        <i data-lucide="bell-ring" class="w-4 h-4"></i>
        <span>PREPARE NEXT</span>
      </button>

      <button onclick="handleRecallChime()" class="px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
        <i data-lucide="volume-2" class="w-4 h-4 text-teal-600"></i>
        <span>Recall / Ring Chime</span>
      </button>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <button onclick="handleComplete()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
        <span>Finish & Save Visit</span>
      </button>

      <button onclick="handleNoShow()" class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-rose-50 border border-rose-200 text-rose-600 font-bold text-xs transition flex items-center gap-1.5">
        <i data-lucide="user-x" class="w-4 h-4"></i>
        <span>No Show / Skip</span>
      </button>

      <button onclick="showToast('Transferred to Clinical Laboratory Station.', 'Patient Transfer')" class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5">
        <i data-lucide="arrow-right-left" class="w-4 h-4"></i>
        <span>Transfer to Lab</span>
      </button>
    </div>

  </div>

  <!-- MAIN DOCTOR WORKSPACE -->
  <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
    
    <!-- Left 7 Cols: Active Consultation Medical Record Sheet -->
    <div class="lg:col-span-7 space-y-6">
      
      <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm relative">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold" id="active-status-badge">
            <span class="w-2 h-2 rounded-full bg-teal-500 animate-ping"></span>
            PATIENT IN ROOM (CONSULTATION ACTIVE)
          </span>
          <span class="text-xs font-mono text-slate-500 font-semibold" id="consult-timer">Time In: --:--</span>
        </div>

        <div class="mt-5 flex items-start gap-5">
          <div class="w-18 h-18 rounded-2xl bg-teal-600 text-white flex flex-col items-center justify-center font-mono shadow-md flex-shrink-0 p-3">
            <span class="text-[10px] font-bold text-teal-200 uppercase tracking-wider">TICKET</span>
            <span class="text-3xl font-black" id="active-ticket-num">---</span>
          </div>

          <div class="flex-1">
            <div class="flex items-center justify-between flex-wrap gap-2">
              <h2 class="text-2xl font-black text-slate-900" id="active-patient-name">No Active Patient in Room</h2>
              <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600" id="active-patient-id">ID: ---</span>
            </div>
            <p class="text-xs text-slate-500 mt-1" id="active-patient-meta">Consultation Room • Dr. Sarah Santos, MD</p>
            <p class="text-xs text-teal-700 font-semibold mt-1" id="active-patient-type">Click "Call Next Patient" when ready</p>
          </div>
        </div>


        <!-- Chief Complaint -->
        <div class="mt-5 pt-4 border-t border-slate-100">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Patient Reason / Chief Complaint</label>
          <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-800 leading-relaxed font-semibold" id="active-complaint">
            "No active consultation session in progress. Click 'Call Next Patient' or select a patient from the queue line."
          </div>
        </div>

        <!-- EHR Consultation Notes & Diagnosis -->
        <div class="mt-4 space-y-3 pt-3 border-t border-slate-100">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1 flex items-center gap-1">
              <i data-lucide="file-text" class="w-3.5 h-3.5 text-teal-600"></i> Clinical Diagnosis & Assessment
            </label>
            <textarea id="doc-diagnosis" placeholder="Enter clinical assessment, diagnostic findings, or physician notes..." rows="2" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition"></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1 flex items-center gap-1">
              <i data-lucide="pill" class="w-3.5 h-3.5 text-teal-600"></i> Prescription & Medical Orders (Rx)
            </label>
            <textarea id="doc-prescription" placeholder="Enter prescribed medication, dosage instructions, or follow-up recommendations..." rows="2" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition"></textarea>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
          <span class="text-xs text-slate-400 flex items-center gap-1.5 font-medium">
            <i data-lucide="check-circle" class="w-4 h-4 text-teal-600"></i> EHR Synced with Central Queue
          </span>
          <div class="flex items-center gap-2">
            <button onclick="handleSaveNotesOnly()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
              <i data-lucide="save" class="w-4 h-4 text-teal-600"></i> Save Notes
            </button>
            <button onclick="handleComplete()" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs transition shadow-md shadow-teal-600/20 flex items-center gap-2">
              <i data-lucide="check-circle-2" class="w-4 h-4"></i>
              <span>Finish & Save Visit</span>
            </button>
          </div>
        </div>

      </div>

    </div>

    <!-- Right 5 Cols: Live Room 3 Queue List -->
    <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
          <div class="flex items-center gap-2">
            <i data-lucide="list-ordered" class="w-5 h-5 text-teal-600"></i>
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900" id="doc-queue-title">Clinic Queue Line</h3>
          </div>
          <span class="text-xs bg-teal-50 border border-teal-200 text-teal-800 px-2.5 py-0.5 rounded-full font-bold" id="doc-queue-badge">Active Station</span>
        </div>

        <p class="text-xs text-slate-500 mb-4" id="doc-queue-subtitle">Click "Call" on any patient below to admit them directly into consultation.</p>

        <div class="space-y-3" id="doctor-queue-container">
          <!-- Rendered via JS -->
        </div>
      </div>

      <div class="mt-6 pt-4 border-t border-slate-100 text-center">
        <button onclick="handleManualRefresh()" class="text-xs text-teal-700 font-bold hover:underline inline-flex items-center gap-1.5">
          <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Refresh Patient List
        </button>
      </div>

    </div>

  </main>
  </div>

  <!-- Scripts -->
  <script src="assets/js/queue-api.js"></script>
  <script>
    lucide.createIcons();

    function renderQueueList() {
      const state = QueueManager.getState();
      const container = document.getElementById('doctor-queue-container');
      container.innerHTML = '';

      if (!state) return;

      const queue = state.upcomingQueue || [];
      const compCount = (typeof state.completedCount !== 'undefined') ? state.completedCount : 0;
      document.getElementById('doc-waiting-count').textContent = `${queue.length} Patients`;
      document.getElementById('doc-completed-count').textContent = `${compCount} Served`;

      if (state.nowCalling && (state.nowCalling.ticket_number || state.nowCalling.ticket)) {
        const p = state.nowCalling;
        const ticketNum = p.ticket_number || p.ticket || '---';
        const name = p.patient_name || p.name || 'Patient';
        const pid = p.patient_id || p.patientId || '---';
        const age = p.age || 35;
        const gender = p.gender || 'Patient';
        const phone = p.phone || 'N/A';
        const type = p.type || 'Walk-in';
        const time = p.time_slot || p.time || 'Active';

        document.getElementById('active-ticket-num').textContent = ticketNum;
        document.getElementById('active-patient-name').textContent = name;
        document.getElementById('active-patient-id').textContent = `ID: ${pid}`;
        document.getElementById('active-patient-meta').textContent = `${age} Y/O • ${gender} • Contact: ${phone}`;
        document.getElementById('active-patient-type').textContent = `Type: ${type} • Slot: ${time}`;
        document.getElementById('consult-timer').textContent = `Time In: ${time}`;
        document.getElementById('active-complaint').textContent = p.complaint ? `"${p.complaint}"` : 'General Medical Consultation';

        // Diagnosis & Prescription prefill
        if (p.diagnosis && !document.getElementById('doc-diagnosis').value) {
          document.getElementById('doc-diagnosis').value = p.diagnosis;
        }
        if (p.prescription && !document.getElementById('doc-prescription').value) {
          document.getElementById('doc-prescription').value = p.prescription;
        }

        document.getElementById('active-status-badge').innerHTML = '<span class="w-2 h-2 rounded-full bg-teal-500 animate-ping"></span> PATIENT IN ROOM (CONSULTATION ACTIVE)';
        document.getElementById('active-status-badge').className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-xs font-bold';
      } else {
        document.getElementById('active-ticket-num').textContent = '---';
        document.getElementById('active-patient-name').textContent = 'No Active Patient in Room';
        document.getElementById('active-patient-id').textContent = 'ID: ---';
        document.getElementById('active-patient-meta').textContent = 'Consultation Room • Dr. Sarah Santos, MD';
        document.getElementById('active-patient-type').textContent = 'Click "Call Next Patient" when ready';
        document.getElementById('consult-timer').textContent = 'Room Available';
        document.getElementById('active-complaint').textContent = 'No active consultation session in progress. Click "Call Next Patient" or select a patient from the queue line.';

        document.getElementById('doc-diagnosis').value = '';
        document.getElementById('doc-prescription').value = '';

        document.getElementById('active-status-badge').innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400"></span> STATION READY (WAITING FOR PATIENT)';
        document.getElementById('active-status-badge').className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold';
      }

      if (queue.length === 0) {
        container.innerHTML = `
          <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
            <i data-lucide="check-circle" class="w-8 h-8 text-teal-500 mx-auto mb-2"></i>
            All scheduled patients for the clinic queue have been served!
          </div>
        `;
        lucide.createIcons();
        return;
      }

      queue.forEach((item) => {
        const row = document.createElement('div');
        const isPriority = item.isPriority || item.is_priority;
        const isPreparing = item.isPreparing || item.status === 'PREPARING';
        const t = item.ticket_number || item.ticket;
        const name = item.patient_name || item.name;

        row.className = isPreparing
          ? 'p-3.5 rounded-2xl border-2 border-amber-400 bg-amber-50 shadow-md flex items-center justify-between transition'
          : (isPriority 
            ? 'p-3.5 rounded-2xl border-2 border-amber-300 bg-amber-50/70 shadow-sm flex items-center justify-between transition hover:bg-amber-50'
            : 'p-3.5 rounded-2xl border border-slate-200 bg-slate-50/60 shadow-sm flex items-center justify-between transition hover:bg-slate-100');

        row.innerHTML = `
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl ${isPreparing ? 'bg-amber-400 text-amber-950 border border-amber-500 font-bold' : (isPriority ? 'bg-amber-200 text-amber-900 border border-amber-300' : 'bg-white text-teal-800 border border-slate-200')} font-mono font-black flex items-center justify-center text-sm shadow-sm flex-shrink-0">
              ${t}
            </div>
            <div>
              <div class="flex items-center gap-1.5 flex-wrap">
                <h4 class="text-xs font-black text-slate-900">${name}</h4>
                ${isPreparing ? '<span class="text-[9px] font-black bg-amber-400 text-amber-950 px-1.5 py-0.5 rounded uppercase flex items-center gap-0.5"><i data-lucide="clock" class="w-2.5 h-2.5"></i> PREPARING</span>' : (isPriority ? '<span class="text-[9px] font-black bg-amber-300 text-amber-950 px-1.5 py-0.5 rounded uppercase">PRIORITY</span>' : '')}
              </div>
              <p class="text-[10px] text-slate-500 mt-0.5">${item.type || 'Walk-in'} • ${item.time_slot || item.time || ''}</p>
            </div>
          </div>
          <div class="flex items-center gap-1.5 flex-shrink-0">
            ${!isPreparing ? `<button onclick="prepareSpecificPatient('${t}', '${name}')" class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-900 font-bold text-xs transition">Prepare</button>` : ''}
            <button onclick="callSpecificPatient('${t}', '${name}')" class="px-3 py-1.5 rounded-xl ${isPriority ? 'bg-amber-500 hover:bg-amber-400 text-slate-950' : 'bg-teal-600 hover:bg-teal-700 text-white'} font-bold text-xs shadow-sm transition">
              Call
            </button>
          </div>
        `;
        container.appendChild(row);
      });

      lucide.createIcons();
    }

    async function handlePrepareNext() {
      const prepared = await QueueManager.prepareNext();
      if (prepared && (prepared.ticket_number || prepared.ticket)) {
        const ticketNum = prepared.ticket_number || prepared.ticket;
        showToast(`Patient ${ticketNum} notified to PREPARE / ON DECK!`, 'Prepare Next');
      } else {
        showToast('No waiting patients in queue to prepare.', 'Notice');
      }
      await QueueManager.syncFromBackend();
      renderQueueList();
    }

    async function prepareSpecificPatient(ticket, name) {
      await QueueManager.preparePatient(ticket);
      showToast(`Patient ${ticket} (${name}) set to PREPARE / ON DECK`, 'Patient Preparing');
      await QueueManager.syncFromBackend();
      renderQueueList();
    }

    async function callSpecificPatient(ticket, name) {
      await QueueManager.callPatient(ticket);
      renderQueueList();
      showToast(`Now Calling ${ticket} into Consultation Room`, 'Patient Called');
    }

    async function handleCallNext() {
      const next = await QueueManager.callNext();
      if (next && (next.ticket_number || next.ticket)) {
        showToast(`Now Calling ${next.ticket_number || next.ticket} into Consultation Room`, 'Patient Called');
      } else {
        showToast('No waiting patients in queue.', 'Queue Empty');
      }
      await QueueManager.syncFromBackend();
      renderQueueList();
    }

    function handleRecallChime() {
      QueueManager.playChime();
      showToast('Calling chime sounded in waiting lounge.', 'Chime Sounded');
    }

    async function handleSaveNotesOnly() {
      const activeTicket = document.getElementById('active-ticket-num').textContent.trim();
      if (!activeTicket || activeTicket === '---') {
        showToast('No active patient selected in consultation room.', 'Notice');
        return;
      }
      const diag = document.getElementById('doc-diagnosis').value.trim();
      const rx = document.getElementById('doc-prescription').value.trim();

      const formData = new FormData();
      formData.append('ticket_number', activeTicket);
      formData.append('diagnosis', diag);
      formData.append('prescription', rx);

      try {
        const res = await fetch(getDoctorApiUrl('save_consultation'), { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || 'Notes saved to patient record.', 'EHR Record Saved');
        } else {
          showToast(json.message || 'Save failed.', 'Error');
        }
      } catch (e) {
        showToast('Could not save notes.', 'Error');
      }
    }

    async function handleComplete() {
      const activeTicket = document.getElementById('active-ticket-num').textContent.trim();
      if (!activeTicket || activeTicket === '---') {
        showToast('No active patient in consultation room to complete.', 'Notice');
        return;
      }
      const diag = document.getElementById('doc-diagnosis').value.trim();
      const rx = document.getElementById('doc-prescription').value.trim();

      const formData = new FormData();
      formData.append('ticket_number', activeTicket);
      formData.append('diagnosis', diag);
      formData.append('prescription', rx);

      try {
        const res = await fetch(getDoctorApiUrl('complete_consultation'), { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || `Visit finished for ${activeTicket}.`, 'Consultation Completed');
          document.getElementById('doc-diagnosis').value = '';
          document.getElementById('doc-prescription').value = '';
          await QueueManager.syncFromBackend();
          renderQueueList();
        } else {
          showToast(json.message || 'No active patient to complete.', 'Notice');
        }
      } catch (e) {
        showToast('Could not complete patient ticket.', 'Error');
      }
    }

    async function handleNoShow() {
      const activeTicket = document.getElementById('active-ticket-num').textContent.trim();
      const formData = new FormData();
      if (activeTicket && activeTicket !== '---') {
        formData.append('ticket_number', activeTicket);
      }
      try {
        const res = await fetch(getDoctorApiUrl('skip_patient'), { method: 'POST', body: formData });
        const json = await res.json();
        showToast(json.message || 'Patient marked as Skipped / No Show.', 'Patient Skipped');
        await QueueManager.syncFromBackend();
        renderQueueList();
      } catch (e) {
        showToast('Could not skip patient ticket.', 'Error');
      }
    }

    let isDoctorLoggedIn = false;
    let currentDoctorRoom = '01';

    function getDoctorApiUrl(action) {
      const isSubdir = window.location.pathname.includes('/modules/doctor/');
      return (isSubdir ? 'actions.php' : 'modules/doctor/actions.php') + '?action=' + action;
    }

    async function checkDoctorSession() {
      try {
        const res = await fetch(getDoctorApiUrl('get_session'));
        const json = await res.json();
        if (json.success && json.data) {
          const active = json.data.activeDoctor;
          const all = json.data.allDoctors || [];

          if (active) {
            isDoctorLoggedIn = true;
            currentDoctorRoom = active.room_number || '01';

            document.getElementById('doctor-login-view').classList.add('hidden');
            document.getElementById('doctor-workstation').classList.remove('hidden');

            const parts = (active.full_name || 'Doctor').split(' ');
            const initials = parts.length > 1 ? (parts[0][0] + parts[parts.length-1][0]).toUpperCase() : parts[0].substring(0,2).toUpperCase();
            document.getElementById('header-doc-initials').textContent = initials;
            document.getElementById('header-doc-name').textContent = active.full_name;
            document.getElementById('header-doc-room').textContent = `Consultation Room • ${active.full_name}`;

            const titleEl = document.getElementById('doc-queue-title');
            if (titleEl) titleEl.textContent = 'Clinic Queue Line';
            const subEl = document.getElementById('doc-queue-subtitle');
            if (subEl) subEl.textContent = 'Click "Call" on any patient below to admit them directly into consultation.';
            const badgeEl = document.getElementById('doc-queue-badge');
            if (badgeEl) badgeEl.textContent = active.department || 'General Consultation';

            // Sync latest queue state from backend and render immediately
            await QueueManager.syncFromBackend();
            renderQueueList();
          } else {
            isDoctorLoggedIn = false;
            document.getElementById('doctor-login-view').classList.remove('hidden');
            document.getElementById('doctor-workstation').classList.add('hidden');

            const sel = document.getElementById('quick-doctor-select');
            if (sel) {
              sel.innerHTML = '<option value="">-- Choose Registered Physician Account --</option>';
              all.forEach(d => {
                const roomStr = d.room_number ? ` (Room ${d.room_number})` : '';
                sel.add(new Option(`${d.full_name}${roomStr} - ${d.department}`, d.id));
              });
            }
          }
        }
      } catch (e) {
        console.error('Check session error:', e);
      }
    }

    async function quickDoctorLogin(doctorId) {
      if (!doctorId) return;
      const formData = new FormData();
      formData.append('doctor_id', doctorId);

      try {
        const res = await fetch(getDoctorApiUrl('login'), { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || 'Logged in successfully', 'Station Access');
          await checkDoctorSession();
        } else {
          showToast(json.message || 'Login failed', 'Error');
        }
      } catch (e) {
        showToast('Server error logging in', 'Error');
      }
    }

    async function submitDoctorLogin() {
      const u = document.getElementById('login-username').value.trim();
      const p = document.getElementById('login-password').value;

      const formData = new FormData();
      formData.append('username', u);
      formData.append('password', p);

      try {
        const res = await fetch(getDoctorApiUrl('login'), { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || 'Logged in successfully', 'Welcome');
          await checkDoctorSession();
        } else {
          showToast(json.message || 'Invalid username or password', 'Login Error');
        }
      } catch (e) {
        showToast('Server connection error', 'Error');
      }
    }

    async function handleDoctorLogout() {
      try {
        await fetch(getDoctorApiUrl('logout'), { method: 'POST' });
        showToast('Doctor session ended', 'Logged Out');
        await checkDoctorSession();
      } catch (e) {
        console.error('Logout error:', e);
      }
    }

    async function handleManualRefresh() {
      await QueueManager.syncFromBackend();
      renderQueueList();
      showToast('Patient queue list refreshed', 'Live Sync');
    }

    checkDoctorSession();

    window.addEventListener('queue_state_updated', () => {
      const ws = document.getElementById('doctor-workstation');
      if (ws && !ws.classList.contains('hidden')) {
        renderQueueList();
      }
    });
  </script>
</body>
</html>
