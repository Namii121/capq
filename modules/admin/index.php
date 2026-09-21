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
  <title>CarePoint - Central Clinic Administration Dashboard</title>
  
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
        <span class="text-slate-400 hidden sm:inline">Central Operations & Reception Dashboard</span>
      </div>
      <nav class="flex items-center gap-1.5 flex-wrap">
        <a href="index.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> Booking Portal</a>
        <a href="kiosk.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="tablet" class="w-3 h-3"></i> Self Kiosk</a>
        <a href="display.php" target="_blank" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="tv" class="w-3 h-3"></i> Live TV Queue <i data-lucide="external-link" class="w-2.5 h-2.5 ml-0.5"></i></a>
        <a href="doctor.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="stethoscope" class="w-3 h-3"></i> Doctor Console</a>
        <a href="admin.php" class="px-2.5 py-1 rounded bg-teal-600 text-white font-bold flex items-center gap-1"><i data-lucide="shield" class="w-3 h-3"></i> Admin Dashboard</a>
      </nav>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- ADMIN DASHBOARD LOGIN VIEW                                                -->
  <!-- ========================================================================= -->
  <div id="admin-login-view" class="flex-1 flex items-center justify-center p-6 bg-slate-900/95 relative overflow-hidden hidden">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full bg-white text-slate-900 rounded-3xl p-8 shadow-2xl border border-slate-200 relative z-10 animate-in fade-in zoom-in-95 duration-200">
      <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center mx-auto mb-3 shadow-md">
          <i data-lucide="shield-check" class="w-7 h-7"></i>
        </div>
        <h2 class="text-xl font-black text-slate-900">Admin Dashboard Login</h2>
        <p class="text-xs text-slate-500 mt-1">Authenticate to access central queue management & roster administration</p>
      </div>

      <!-- Quick Demo Login Button -->
      <button onclick="quickAdminDemoLogin()" class="w-full mb-5 py-2.5 px-4 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 text-teal-800 font-bold text-xs flex items-center justify-center gap-2 transition shadow-sm">
        <i data-lucide="zap" class="w-4 h-4 text-teal-600"></i>
        <span>Quick Demo Login (Clinic Operations Admin)</span>
      </button>

      <div class="relative flex py-2 items-center mb-5">
        <div class="flex-grow border-t border-slate-200"></div>
        <span class="flex-shrink mx-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Or Enter Admin Credentials</span>
        <div class="flex-grow border-t border-slate-200"></div>
      </div>

      <!-- Credentials Form -->
      <form onsubmit="event.preventDefault(); submitAdminLogin();" class="space-y-4 text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Admin Username</label>
          <div class="relative">
            <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
            <input type="text" id="admin-login-username" placeholder="admin" value="admin" required class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Admin Password</label>
          <div class="relative">
            <i data-lucide="key-round" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
            <input type="password" id="admin-login-password" placeholder="••••••••" value="admin123" required class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
          </div>
        </div>

        <button type="submit" class="w-full py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition flex items-center justify-center gap-2 mt-2">
          <i data-lucide="log-in" class="w-4 h-4"></i>
          <span>Access Admin Console</span>
        </button>
      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- ADMIN DASHBOARD VIEW (AUTHENTICATED)                                      -->
  <!-- ========================================================================= -->
  <div id="admin-dashboard-view" class="flex-1 flex flex-col">

  <!-- ADMIN TOP BAR -->
  <header class="bg-slate-900 text-white px-6 sm:px-8 py-4 border-b border-slate-800 flex flex-wrap items-center justify-between gap-4 flex-shrink-0">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-2xl bg-teal-500 text-slate-950 flex items-center justify-center font-bold">
        <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
      </div>
      <div>
        <h1 class="text-lg font-bold text-white">Clinic Operations Management Console</h1>
        <p class="text-xs text-slate-400" id="admin-header-subtitle">CarePoint Main Outpatient Facility • Reception & Queue Dispatch</p>
      </div>
    </div>

    <!-- Quick Action Controls -->
    <div class="flex items-center gap-3 flex-wrap">
      <button onclick="openWalkinModal()" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition flex items-center gap-1.5">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        <span>Register Walk-in Patient</span>
      </button>

      <button onclick="openBroadcastModal()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 text-xs font-semibold transition flex items-center gap-1.5">
        <i data-lucide="megaphone" class="w-4 h-4 text-teal-400"></i>
        <span>Broadcast Notice</span>
      </button>

      <button onclick="exportCSV()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 text-xs font-semibold transition flex items-center gap-1.5">
        <i data-lucide="download" class="w-4 h-4"></i>
        <span>Export Log</span>
      </button>

      <button onclick="handleAdminLogout()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center gap-1.5" title="Logout Admin Session">
        <i data-lucide="log-out" class="w-4 h-4 text-rose-400"></i>
        <span>Logout</span>
      </button>
    </div>
  </header>

  <!-- MAIN ADMIN DASHBOARD BODY -->
  <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8">
    
    <!-- 1. Real-time KPI Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      
      <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Registered Today</p>
          <h3 class="text-3xl font-black text-slate-900 mt-1 font-mono" id="admin-total-stat">0</h3>
          <p class="text-[11px] text-teal-600 font-semibold mt-1">Today's Total Patient Intake</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center">
          <i data-lucide="calendar-check" class="w-6 h-6"></i>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Waiting in Queue</p>
          <h3 class="text-3xl font-black text-blue-600 mt-1 font-mono" id="admin-waiting-stat">0</h3>
          <p class="text-[11px] text-slate-500 font-medium mt-1">Across 5 departments</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center">
          <i data-lucide="users" class="w-6 h-6"></i>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Avg. Wait Time</p>
          <h3 class="text-3xl font-black text-amber-600 mt-1 font-mono" id="admin-avg-stat">0 <span class="text-sm font-normal text-slate-500">mins</span></h3>
          <p class="text-[11px] text-emerald-600 font-semibold mt-1">Under clinic target</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center">
          <i data-lucide="clock" class="w-6 h-6"></i>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Completed Consults</p>
          <h3 class="text-3xl font-black text-emerald-600 mt-1 font-mono" id="admin-completed-stat">0</h3>
          <p class="text-[11px] text-slate-500 font-medium mt-1">Consultations Finished</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
          <i data-lucide="check-circle-2" class="w-6 h-6"></i>
        </div>
      </div>

    </div>

    <!-- 2. Master Queue & Appointment Management Panel -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
      
      <!-- Filter Bar -->
      <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
        
        <div class="flex-1 max-w-md">
          <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-4 top-3.5"></i>
            <input type="text" id="admin-search" oninput="filterTable()" placeholder="Search patient name, phone, ticket #..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-4 py-2.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
          </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
          <select id="filter-status" onchange="filterTable()" class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-700 focus:outline-none">
            <option value="">All Statuses</option>
            <option value="WAITING">Waiting</option>
            <option value="CALLING">In Consultation</option>
            <option value="COMPLETED">Completed</option>
            <option value="Priority">Priority Patients</option>
          </select>

          <button onclick="syncAdminTable()" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition" title="Refresh data">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
          </button>
        </div>

      </div>

      <!-- Master Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="admin-table">
          <thead>
            <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 bg-slate-50/50">
              <th class="py-3.5 px-6">Ticket</th>
              <th class="py-3.5 px-6">Patient Name & Demographics</th>
              <th class="py-3.5 px-6">Type</th>
              <th class="py-3.5 px-6">Intake Complaint</th>
              <th class="py-3.5 px-6">Check-in Time</th>
              <th class="py-3.5 px-6">Status</th>
              <th class="py-3.5 px-6 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="admin-table-body">
            <!-- Dynamically populated via syncAdminTable() -->
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-6 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <span id="admin-record-count">Showing 0 patient records</span>
        <div class="flex gap-1">
          <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 font-medium">Previous</button>
          <button class="px-3 py-1.5 rounded-lg bg-teal-600 text-white font-bold">1</button>
          <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 font-medium">Next</button>
        </div>
      </div>

    </div>

    </div>

  </main>
  </div>

  <!-- MODAL: REGISTER WALK-IN PATIENT -->
  <div id="walkin-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white text-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in zoom-in-95 duration-200">
      <button onclick="closeWalkinModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
        <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
          <i data-lucide="user-plus" class="w-6 h-6"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-slate-900">Direct Walk-In Intake</h3>
          <p class="text-xs text-slate-500">Generate immediate ticket and add to live department queue</p>
        </div>
      </div>

      <form onsubmit="event.preventDefault(); submitAdminWalkin();" class="space-y-4 text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Patient Full Name <span class="text-rose-500">*</span></label>
          <input type="text" id="admin-walkin-name" required placeholder="Enter full name" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Mobile Phone <span class="text-rose-500">*</span></label>
            <input type="tel" id="admin-walkin-phone" required placeholder="09XX-XXX-XXXX" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Service Department <span class="text-rose-500">*</span></label>
            <select id="admin-walkin-dept" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
              <option value="General Medicine">General Medicine (Room 03)</option>
              <option value="Pediatrics">Pediatrics (Room 02)</option>
              <option value="Dental Clinic">Dental Clinic (Room 04)</option>
              <option value="Triage & Vitals">Triage & Lab (Room 01)</option>
            </select>
          </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-center gap-2.5">
          <input type="checkbox" id="admin-walkin-priority" class="rounded text-teal-600 w-4 h-4">
          <label for="admin-walkin-priority" class="font-bold cursor-pointer">Priority Flag: Senior Citizen (60+), PWD, or Expecting Mother</label>
        </div>

        <div class="pt-3 flex justify-end gap-2">
          <button type="button" onclick="closeWalkinModal()" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold">Cancel</button>
          <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold flex items-center gap-1.5 shadow-md shadow-teal-600/20">
            <i data-lucide="ticket" class="w-4 h-4"></i> Issue Ticket
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: BROADCAST ANNOUNCEMENT -->
  <div id="broadcast-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white text-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative animate-in zoom-in-95 duration-200">
      <button onclick="closeBroadcastModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <div class="flex items-center gap-3 pb-3 mb-4 border-b border-slate-100">
        <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
          <i data-lucide="megaphone" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-900">Broadcast to TV Monitors</h3>
          <p class="text-xs text-slate-500">Send an urgent banner notice to clinic waiting areas</p>
        </div>
      </div>

      <div class="space-y-4 text-xs">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Announcement Message</label>
          <textarea id="broadcast-text" rows="3" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">Attention: Dr. Raymond Cruz (Room 01) is currently attending an emergency. Consultations will resume shortly.</textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeBroadcastModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold">Cancel</button>
          <button type="button" onclick="submitBroadcast()" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold flex items-center gap-1.5">
            <i data-lucide="send" class="w-4 h-4"></i> Broadcast Now
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: ADD DOCTOR ACCOUNT -->
  <div id="doctor-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white text-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in zoom-in-95 duration-200">
      <button onclick="closeDoctorModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
        <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
          <i data-lucide="stethoscope" class="w-6 h-6"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-slate-900">Register Doctor Account</h3>
          <p class="text-xs text-slate-500">Create login credentials and assign consultation room</p>
        </div>
      </div>

      <form onsubmit="event.preventDefault(); submitAddDoctor();" class="space-y-4 text-xs">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Account Username <span class="text-rose-500">*</span></label>
            <input type="text" id="doc-username" required placeholder="e.g. dr.santos" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Account Password <span class="text-rose-500">*</span></label>
            <input type="password" id="doc-password" required placeholder="Min 6 chars" value="123456" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Doctor Full Name & Title <span class="text-rose-500">*</span></label>
          <input type="text" id="doc-fullname" required placeholder="e.g. Dr. Sarah Santos, MD" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Clinical Department <span class="text-rose-500">*</span></label>
            <select id="doc-dept" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
              <option value="General Medicine">General Medicine</option>
              <option value="Pediatrics">Pediatrics</option>
              <option value="Dental Clinic">Dental Clinic</option>
              <option value="Triage & Vitals">Triage & Vitals</option>
              <option value="Pharmacy & Cashier">Pharmacy & Cashier</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Assigned Consultation Room</label>
            <select id="doc-room" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
              <option value="03">Room 03 (General Medicine)</option>
              <option value="02">Room 02 (Pediatrics)</option>
              <option value="04">Room 04 (Dental Clinic)</option>
              <option value="01">Room 01 (Triage & Vitals)</option>
              <option value="05">Room 05 (Pharmacy)</option>
            </select>
          </div>
        </div>

        <div class="pt-3 flex justify-end gap-2">
          <button type="button" onclick="closeDoctorModal()" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold">Cancel</button>
          <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold flex items-center gap-1.5 shadow-md shadow-teal-600/20">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Create Doctor Account
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: ADD / EDIT CONSULTATION ROOM -->
  <div id="room-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white text-slate-900 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in zoom-in-95 duration-200">
      <button onclick="closeRoomModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
        <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
          <i data-lucide="door-open" class="w-6 h-6"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-slate-900" id="room-modal-title">Add Consultation Room</h3>
          <p class="text-xs text-slate-500">Manage room number, specialty, and assigned doctor</p>
        </div>
      </div>

      <form onsubmit="event.preventDefault(); submitRoomForm();" class="space-y-4 text-xs">
        <input type="hidden" id="room-id" value="">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Room Number (e.g., 06)</label>
          <input type="text" id="room-number" required placeholder="06" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Room Name / Station Title</label>
          <input type="text" id="room-name" required placeholder="e.g. Cardiology Clinic" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Department</label>
          <select id="room-dept" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            <!-- Dynamically filled -->
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Assigned Doctor Name</label>
          <input type="text" id="room-doc-name" placeholder="Dr. John Doe, MD (or Unassigned Doctor)" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Color Theme</label>
            <select id="room-color" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
              <option value="teal">Teal</option>
              <option value="blue">Blue</option>
              <option value="purple">Purple</option>
              <option value="amber">Amber</option>
              <option value="emerald">Emerald</option>
              <option value="rose">Rose</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Status</label>
            <select id="room-status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
              <option value="AVAILABLE">AVAILABLE</option>
              <option value="BUSY">BUSY</option>
              <option value="OFFLINE">OFFLINE</option>
            </select>
          </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
          <button type="button" onclick="closeRoomModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 transition">Cancel</button>
          <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-md shadow-indigo-600/20">Save Room</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: ADD / EDIT CLINICAL DEPARTMENT -->
  <div id="dept-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white text-slate-900 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-in zoom-in-95 duration-200">
      <button onclick="closeDeptModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
        <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
          <i data-lucide="building-2" class="w-6 h-6"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-slate-900" id="dept-modal-title">Add Department</h3>
          <p class="text-xs text-slate-500">Create new clinical specialty or department</p>
        </div>
      </div>

      <form onsubmit="event.preventDefault(); submitDeptForm();" class="space-y-4 text-xs">
        <input type="hidden" id="dept-id" value="">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Department Code (e.g. CARDIO)</label>
          <input type="text" id="dept-code" required placeholder="CARDIO" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Department Name (e.g. Cardiology)</label>
          <input type="text" id="dept-name" required placeholder="Cardiology" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Description (Optional)</label>
          <textarea id="dept-desc" rows="3" placeholder="Cardiovascular care & heart diagnostics" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
          <button type="button" onclick="closeDeptModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 transition">Cancel</button>
          <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition shadow-md shadow-purple-600/20">Save Department</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/js/queue-api.js"></script>
  <script>
    lucide.createIcons();

    function openWalkinModal() {
      document.getElementById('walkin-modal').classList.remove('hidden');
    }
    function closeWalkinModal() {
      document.getElementById('walkin-modal').classList.add('hidden');
    }

    function openBroadcastModal() {
      document.getElementById('broadcast-modal').classList.remove('hidden');
    }
    function closeBroadcastModal() {
      document.getElementById('broadcast-modal').classList.add('hidden');
    }

    function openAddDoctorModal() {
      document.getElementById('doctor-modal').classList.remove('hidden');
    }
    function closeDoctorModal() {
      document.getElementById('doctor-modal').classList.add('hidden');
    }

    async function submitAddDoctor() {
      const username = document.getElementById('doc-username').value.trim();
      const password = document.getElementById('doc-password').value;
      const fullName = document.getElementById('doc-fullname').value.trim();
      const dept = document.getElementById('doc-dept').value;
      const room = document.getElementById('doc-room').value;

      if (!username || !fullName) {
        showToast('Username and Doctor Full Name are required', 'Input Warning');
        return;
      }

      const formData = new FormData();
      formData.append('username', username);
      formData.append('password', password);
      formData.append('full_name', fullName);
      formData.append('department', dept);
      formData.append('room_number', room);

      try {
        const res = await fetch('modules/admin/actions.php?action=add_doctor', {
          method: 'POST',
          body: formData
        });
        const json = await res.json();
        if (json.success) {
          closeDoctorModal();
          document.getElementById('doc-username').value = '';
          document.getElementById('doc-fullname').value = '';
          document.getElementById('doc-password').value = '123456';
          showToast(json.message || 'Doctor account created', 'Account Created');
          syncDoctorTable();
        } else {
          showToast(json.message || 'Failed to create doctor account', 'Registration Error');
        }
      } catch (e) {
        showToast('Server error creating doctor account', 'Error');
      }
    }

    async function deleteDoctorAccount(id, name) {
      if (typeof Swal !== 'undefined') {
        const res = await Swal.fire({
          title: 'Delete Doctor Account?',
          text: `Are you sure you want to delete the doctor account for ${name}?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#e11d48',
          cancelButtonColor: '#64748b',
          confirmButtonText: 'Yes, Delete Account',
          cancelButtonText: 'Cancel',
          customClass: {
            popup: 'rounded-3xl font-sans'
          }
        });
        if (!res.isConfirmed) return;
      } else {
        if (!confirm(`Are you sure you want to delete the doctor account for ${name}?`)) return;
      }
      try {
        const res = await fetch(`modules/admin/actions.php?action=delete_doctor&id=${id}`, { method: 'POST' });
        const json = await res.json();
        if (json.success) {
          showToast(`Doctor account deleted for ${name}`, 'Doctor Deleted');
          syncDoctorTable();
        } else {
          showToast(json.message || 'Failed to delete doctor', 'Error');
        }
      } catch (e) {
        showToast('Server error deleting doctor account', 'Error');
      }
    }

    async function syncDoctorTable() {
      try {
        const res = await fetch('modules/admin/actions.php?action=list_doctors');
        const json = await res.json();
        if (json.success && json.data) {
          const tbody = document.getElementById('doctor-table-body');
          if (tbody) {
            if (json.data.length === 0) {
              tbody.innerHTML = `
                <tr>
                  <td colspan="6" class="py-10 text-center text-slate-400">
                    <i data-lucide="stethoscope" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                    <p class="font-bold text-sm text-slate-600">No Doctor Accounts Registered</p>
                    <p class="text-xs text-slate-400 mt-1">Click 'Add New Doctor' above to register a physician account and assign consultation rooms.</p>
                  </td>
                </tr>
              `;
            } else {
              tbody.innerHTML = json.data.map(d => `
                <tr class="hover:bg-slate-50/80 transition">
                  <td class="py-4 px-6 font-mono font-bold text-teal-800">${d.username}</td>
                  <td class="py-4 px-6 font-bold text-slate-900">${d.full_name}</td>
                  <td class="py-4 px-6">${d.department}</td>
                  <td class="py-4 px-6"><span class="px-2.5 py-1 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 font-mono font-bold">Room ${d.room_number || 'Unassigned'}</span></td>
                  <td class="py-4 px-6"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">${d.role}</span></td>
                  <td class="py-4 px-6 text-right">
                    <button onclick="deleteDoctorAccount(${d.id}, '${d.full_name}')" class="p-2 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition" title="Delete Doctor Account"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                  </td>
                </tr>
              `).join('');
            }
            if (window.lucide) lucide.createIcons();
          }
        }
      } catch (e) {
        console.log('Sync doctor error:', e);
      }
    }

    function openAddRoomModal() {
      document.getElementById('room-id').value = '';
      document.getElementById('room-number').value = '';
      document.getElementById('room-name').value = '';
      document.getElementById('room-doc-name').value = 'Unassigned Doctor';
      document.getElementById('room-color').value = 'teal';
      document.getElementById('room-status').value = 'AVAILABLE';
      document.getElementById('room-modal-title').textContent = 'Add Consultation Room';
      document.getElementById('room-modal').classList.remove('hidden');
    }

    function closeRoomModal() {
      document.getElementById('room-modal').classList.add('hidden');
    }

    function openEditRoomModal(roomJson) {
      const room = JSON.parse(decodeURIComponent(roomJson));
      document.getElementById('room-id').value = room.id;
      document.getElementById('room-number').value = room.room_number;
      document.getElementById('room-name').value = room.room_name;
      document.getElementById('room-dept').value = room.department;
      document.getElementById('room-doc-name').value = room.doctor_name || 'Unassigned Doctor';
      document.getElementById('room-color').value = room.color_theme || 'teal';
      document.getElementById('room-status').value = room.status || 'AVAILABLE';
      document.getElementById('room-modal-title').textContent = `Edit Consultation Room ${room.room_number}`;
      document.getElementById('room-modal').classList.remove('hidden');
    }

    async function submitRoomForm() {
      const id = document.getElementById('room-id').value;
      const num = document.getElementById('room-number').value.trim();
      const name = document.getElementById('room-name').value.trim();
      const dept = document.getElementById('room-dept').value;
      const docName = document.getElementById('room-doc-name').value.trim();
      const color = document.getElementById('room-color').value;
      const status = document.getElementById('room-status').value;

      if (!num || !name) {
        showToast('Room number and room name are required', 'Warning');
        return;
      }

      const formData = new FormData();
      if (id) formData.append('id', id);
      formData.append('room_number', num);
      formData.append('room_name', name);
      formData.append('department', dept);
      formData.append('doctor_name', docName || 'Unassigned Doctor');
      formData.append('color_theme', color);
      formData.append('status', status);

      const action = id ? 'update_room' : 'add_room';
      try {
        const res = await fetch(getAdminApiUrl(action), { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          closeRoomModal();
          showToast(json.message || 'Room saved successfully', 'Success');
          syncRoomTable();
        } else {
          showToast(json.message || 'Error saving room', 'Error');
        }
      } catch (e) {
        showToast('Server error saving room', 'Error');
      }
    }

    async function deleteRoom(id, num) {
      if (!confirm(`Delete Consultation Room ${num}?`)) return;
      try {
        const res = await fetch(`${getAdminApiUrl('delete_room')}&id=${id}`, { method: 'POST' });
        const json = await res.json();
        if (json.success) {
          showToast(`Room ${num} deleted`, 'Deleted');
          syncRoomTable();
        } else {
          showToast(json.message || 'Error deleting room', 'Error');
        }
      } catch (e) {
        showToast('Server error deleting room', 'Error');
      }
    }

    async function syncRoomTable() {
      try {
        const res = await fetch(getAdminApiUrl('list_rooms'));
        const json = await res.json();
        if (json.success && json.data) {
          const tbody = document.getElementById('room-table-body');
          const docRoomSelect = document.getElementById('doc-room');

          if (tbody) {
            tbody.innerHTML = json.data.map(r => `
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-6 font-mono font-bold text-indigo-900">Room ${r.room_number}</td>
                <td class="py-4 px-6 font-bold text-slate-900">${r.room_name}</td>
                <td class="py-4 px-6"><span class="px-2.5 py-1 rounded-lg bg-slate-100 font-medium text-slate-700">${r.department}</span></td>
                <td class="py-4 px-6 font-semibold text-teal-700">${r.doctor_name || 'Unassigned Doctor'}</td>
                <td class="py-4 px-6"><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-${r.color_theme}-50 text-${r.color_theme}-700 border border-${r.color_theme}-200">${r.color_theme}</span></td>
                <td class="py-4 px-6"><span class="px-2 py-0.5 rounded text-[10px] font-bold ${r.status === 'AVAILABLE' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (r.status === 'BUSY' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200')}">${r.status}</span></td>
                <td class="py-4 px-6 text-right flex items-center justify-end gap-1">
                  <button onclick="openEditRoomModal('${encodeURIComponent(JSON.stringify(r))}')" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-indigo-600 transition" title="Edit Room"><i data-lucide="edit-3" class="w-4 h-4"></i></button>
                  <button onclick="deleteRoom(${r.id}, '${r.room_number}')" class="p-2 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition" title="Delete Room"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </td>
              </tr>
            `).join('');
          }

          if (docRoomSelect) {
            docRoomSelect.innerHTML = json.data.map(r => `<option value="${r.room_number}">Room ${r.room_number} (${r.room_name})</option>`).join('');
          }

          if (window.lucide) lucide.createIcons();
        }
      } catch (e) {
        console.log('Sync room error:', e);
      }
    }

    function openAddDeptModal() {
      document.getElementById('dept-id').value = '';
      document.getElementById('dept-code').value = '';
      document.getElementById('dept-name').value = '';
      document.getElementById('dept-desc').value = '';
      document.getElementById('dept-modal-title').textContent = 'Add Department';
      document.getElementById('dept-modal').classList.remove('hidden');
    }

    function closeDeptModal() {
      document.getElementById('dept-modal').classList.add('hidden');
    }

    function openEditDeptModal(deptJson) {
      const d = JSON.parse(decodeURIComponent(deptJson));
      document.getElementById('dept-id').value = d.id;
      document.getElementById('dept-code').value = d.code;
      document.getElementById('dept-name').value = d.name;
      document.getElementById('dept-desc').value = d.description || '';
      document.getElementById('dept-modal-title').textContent = `Edit Department ${d.name}`;
      document.getElementById('dept-modal').classList.remove('hidden');
    }

    async function submitDeptForm() {
      const id = document.getElementById('dept-id').value;
      const code = document.getElementById('dept-code').value.trim();
      const name = document.getElementById('dept-name').value.trim();
      const desc = document.getElementById('dept-desc').value.trim();

      if (!code || !name) {
        showToast('Department code and name are required', 'Warning');
        return;
      }

      const formData = new FormData();
      if (id) formData.append('id', id);
      formData.append('code', code);
      formData.append('name', name);
      formData.append('description', desc);

      const action = id ? 'update_department' : 'add_department';
      try {
        const res = await fetch(getAdminApiUrl(action), { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          closeDeptModal();
          showToast(json.message || 'Department saved successfully', 'Success');
          syncDeptTable();
        } else {
          showToast(json.message || 'Error saving department', 'Error');
        }
      } catch (e) {
        showToast('Server error saving department', 'Error');
      }
    }

    async function deleteDept(id, name) {
      if (!confirm(`Delete Department '${name}'?`)) return;
      try {
        const res = await fetch(`${getAdminApiUrl('delete_department')}&id=${id}`, { method: 'POST' });
        const json = await res.json();
        if (json.success) {
          showToast(`Department '${name}' deleted`, 'Deleted');
          syncDeptTable();
        } else {
          showToast(json.message || 'Error deleting department', 'Error');
        }
      } catch (e) {
        showToast('Server error deleting department', 'Error');
      }
    }

    async function syncDeptTable() {
      try {
        const res = await fetch(getAdminApiUrl('list_departments'));
        const json = await res.json();
        if (json.success && json.data) {
          const tbody = document.getElementById('dept-table-body');
          const docDeptSelect = document.getElementById('doc-dept');
          const roomDeptSelect = document.getElementById('room-dept');
          const filterDeptSelect = document.getElementById('filter-dept');

          if (tbody) {
            tbody.innerHTML = json.data.map(d => `
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-6 font-mono font-bold text-purple-900">${d.code}</td>
                <td class="py-4 px-6 font-bold text-slate-900">${d.name}</td>
                <td class="py-4 px-6 text-slate-500">${d.description || '---'}</td>
                <td class="py-4 px-6"><span class="px-2.5 py-1 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 font-bold">${d.doctor_count} Doctor(s)</span></td>
                <td class="py-4 px-6">${(d.rooms && d.rooms.length) ? d.rooms.map(r => `<span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 font-mono">Room ${r.room_number}</span>`).join(' ') : '<span class="text-slate-400">None</span>'}</td>
                <td class="py-4 px-6 text-right flex items-center justify-end gap-1">
                  <button onclick="openEditDeptModal('${encodeURIComponent(JSON.stringify(d))}')" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-purple-600 transition" title="Edit Department"><i data-lucide="edit-3" class="w-4 h-4"></i></button>
                  <button onclick="deleteDept(${d.id}, '${d.name}')" class="p-2 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition" title="Delete Department"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </td>
              </tr>
            `).join('');
          }

          if (docDeptSelect) {
            docDeptSelect.innerHTML = json.data.map(d => `<option value="${d.name}">${d.name}</option>`).join('');
          }
          if (roomDeptSelect) {
            roomDeptSelect.innerHTML = json.data.map(d => `<option value="${d.name}">${d.name}</option>`).join('');
          }
          if (filterDeptSelect) {
            const curVal = filterDeptSelect.value;
            filterDeptSelect.innerHTML = '<option value="">All Departments</option>' + json.data.map(d => `<option value="${d.name}">${d.name}</option>`).join('');
            filterDeptSelect.value = curVal;
          }

          if (window.lucide) lucide.createIcons();
        }
      } catch (e) {
        console.log('Sync dept error:', e);
      }
    }

    async function submitAdminWalkin() {
      const name = document.getElementById('admin-walkin-name').value;
      const phone = document.getElementById('admin-walkin-phone').value;
      const dept = document.getElementById('admin-walkin-dept').value;
      const isPriority = document.getElementById('admin-walkin-priority').checked;

      if (!name) {
        showToast('Patient name is required.', 'Input Warning');
        return;
      }

      const res = await QueueManager.addPatient({
        name: name,
        phone: phone,
        department: dept,
        reason: `${dept} Walk-in Intake`,
        isPriority: isPriority
      });

      closeWalkinModal();
      showToast(`Walk-in ticket registered for ${name}. Added to live queue.`, 'Patient Registered');
      syncAdminTable();
    }

    async function dispatchPatient(ticket, name) {
      await QueueManager.callPatient(ticket);
      showToast(`Dispatched ${ticket} to Room 03`, 'Queue Dispatched');
      syncAdminTable();
    }

    function submitBroadcast() {
      const text = document.getElementById('broadcast-text').value;
      closeBroadcastModal();
      showToast('Announcement broadcasted to Waiting Room TV displays.', 'Broadcast Sent');
      QueueManager.playChime();
    }

    function filterTable() {
      const search = document.getElementById('admin-search').value.toLowerCase();
      const dept = document.getElementById('filter-dept').value;
      const status = document.getElementById('filter-status').value;

      document.querySelectorAll('.table-row-item').forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowDept = row.getAttribute('data-dept') || '';
        const rowStatus = row.getAttribute('data-status') || '';

        const matchesSearch = text.includes(search);
        const matchesDept = !dept || rowDept.includes(dept);
        const matchesStatus = !status || rowStatus.includes(status);

        if (matchesSearch && matchesDept && matchesStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    async function syncAdminTable() {
      try {
        const res = await fetch('modules/admin/actions.php?action=get_dashboard');
        const json = await res.json();
        if (json.success && json.data) {
          const stats = json.data.stats;
          const patients = json.data.patients || [];

          document.getElementById('admin-total-stat').textContent = stats.total_today || 0;
          document.getElementById('admin-waiting-stat').textContent = stats.waiting_count || 0;
          document.getElementById('admin-completed-stat').textContent = stats.completed_count || 0;
          document.getElementById('admin-avg-stat').innerHTML = `${stats.avg_wait_min || 0} <span class="text-sm font-normal text-slate-500">mins</span>`;
          document.getElementById('admin-record-count').textContent = `Showing ${patients.length} patient records`;

          const tbody = document.getElementById('admin-table-body');
          if (tbody) {
            if (patients.length === 0) {
              tbody.innerHTML = `
                <tr>
                  <td colspan="7" class="py-12 text-center text-slate-400">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                    <p class="font-bold text-sm text-slate-600">No Queue Records Today</p>
                    <p class="text-xs text-slate-400 mt-1">The patient queue is empty. Checked-in patients will appear here automatically.</p>
                  </td>
                </tr>
              `;
            } else {
              tbody.innerHTML = patients.map(p => {
                const ticket = p.ticket_number;
                const name = p.patient_name;
                const phone = p.phone || 'N/A';
                const genderAge = `${p.gender || 'Patient'}, ${p.age || 35}`;
                const type = p.type || 'Walk-in';
                const dept = p.department || 'General Medicine';
                const doctor = p.doctor_name || 'Assigned Staff';
                const room = p.room_assigned || 'Room 03';
                const time = p.time_slot || p.created_at || 'Today';
                const status = p.status;
                const isPriority = Boolean(p.is_priority);

                let statusBadge = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Waiting</span>`;
                if (status === 'CALLING') {
                  statusBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800"><span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-ping"></span> In Consultation</span>`;
                } else if (status === 'COMPLETED') {
                  statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">Completed</span>`;
                }

                return `
                  <tr class="hover:bg-slate-50/80 transition table-row-item" data-status="${status}">
                    <td class="py-4 px-6">
                      <span class="font-mono font-black text-sm px-2.5 py-1 rounded-xl ${isPriority ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-teal-50 text-teal-800 border border-teal-200'}">${ticket}</span>
                    </td>
                    <td class="py-4 px-6">
                      <div class="flex items-center gap-1.5">
                        <p class="font-bold text-slate-900">${name}</p>
                        ${isPriority ? '<span class="text-[9px] font-black bg-amber-200 text-amber-900 px-1 py-0.2 rounded">PRIORITY</span>' : ''}
                      </div>
                      <p class="text-[10px] text-slate-400">${phone} • ${genderAge}</p>
                    </td>
                    <td class="py-4 px-6">
                      <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">${type}</span>
                    </td>
                    <td class="py-4 px-6">
                      <p class="font-medium text-slate-800">${p.complaint || 'General Consultation'}</p>
                    </td>
                    <td class="py-4 px-6 font-mono text-slate-500">${time}</td>
                    <td class="py-4 px-6">${statusBadge}</td>
                    <td class="py-4 px-6 text-right space-x-1">
                      ${status === 'WAITING' ? `<button onclick="dispatchPatient('${ticket}', '${name}')" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-teal-600" title="Call Patient to Consultation"><i data-lucide="phone-forwarded" class="w-4 h-4"></i></button>` : ''}
                      <button onclick="QueueManager.playChime(); showToast('Chime sounded for ${ticket}', 'Recall');" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-teal-600" title="Sound Chime"><i data-lucide="volume-2" class="w-4 h-4"></i></button>
                    </td>
                  </tr>
                `;
              }).join('');
            }
            if (window.lucide) lucide.createIcons();
          }
        }
      } catch (e) {
        console.log('Admin sync error:', e);
      }
    }

    function exportCSV() {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Exporting Clinic Queue Data',
          text: 'Generating CSV download for today\'s queue records (Format: Ticket, Name, Phone, Dept, Time, Status)...',
          icon: 'info',
          confirmButtonColor: '#0d9488',
          customClass: { popup: 'rounded-3xl font-sans' }
        });
      } else {
        alert('Exporting clinic daily queue log to CSV (Format: Ticket, Name, Phone, Dept, Time, Status)...');
      }
    }

    let isAdminLoggedIn = false;

    function getAdminApiUrl(action) {
      const isSubdir = window.location.pathname.includes('/modules/admin/');
      return (isSubdir ? 'actions.php' : 'modules/admin/actions.php') + '?action=' + action;
    }

    async function checkAdminSession() {
      try {
        const res = await fetch(getAdminApiUrl('get_session'));
        const json = await res.json();
        if (json.success && json.data) {
          const active = json.data.activeAdmin;
          if (active) {
            isAdminLoggedIn = true;
            document.getElementById('admin-login-view').classList.add('hidden');
            document.getElementById('admin-dashboard-view').classList.remove('hidden');

            syncAdminTable();
            syncDoctorTable();
            syncRoomTable();
            syncDeptTable();
          } else {
            isAdminLoggedIn = false;
            document.getElementById('admin-login-view').classList.remove('hidden');
            document.getElementById('admin-dashboard-view').classList.add('hidden');
          }
        }
      } catch (e) {
        console.error('Check admin session error:', e);
      }
    }

    async function quickAdminDemoLogin() {
      const formData = new FormData();
      formData.append('quick_demo', '1');

      try {
        const res = await fetch(getAdminApiUrl('login'), { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || 'Logged in as Admin', 'Access Granted');
          checkAdminSession();
        } else {
          showToast(json.message || 'Demo login failed', 'Error');
        }
      } catch (e) {
        showToast('Server connection error', 'Error');
      }
    }

    async function submitAdminLogin() {
      const u = document.getElementById('admin-login-username').value.trim();
      const p = document.getElementById('admin-login-password').value;

      const formData = new FormData();
      formData.append('username', u);
      formData.append('password', p);

      try {
        const res = await fetch(getAdminApiUrl('login'), { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || 'Welcome to Admin Dashboard', 'Authenticated');
          checkAdminSession();
        } else {
          showToast(json.message || 'Invalid admin credentials', 'Login Failed');
        }
      } catch (e) {
        showToast('Server connection error', 'Error');
      }
    }

    async function handleAdminLogout() {
      try {
        await fetch(getAdminApiUrl('logout'), { method: 'POST' });
        showToast('Admin session logged out', 'Session Ended');
        checkAdminSession();
      } catch (e) {
        console.error('Logout error:', e);
      }
    }

    checkAdminSession();

    window.addEventListener('queue_state_updated', () => {
      if (isAdminLoggedIn) {
        syncAdminTable();
        syncDoctorTable();
        syncRoomTable();
        syncDeptTable();
      }
    });
  </script>
</body>
</html>
