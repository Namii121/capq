<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarePoint Medical - Online Appointment & Patient Queue Portal</title>
  
  <!-- Google Fonts: Inter & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
  
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
</head>
<body class="min-h-screen flex flex-col font-sans text-slate-800 antialiased bg-slate-50 selection:bg-teal-500 selection:text-white">

  <!-- ========================================================================= -->
  <!-- UNIFIED DEMO SUITE SWITCHER BAR                                           -->
  <!-- ========================================================================= -->
  <div class="bg-slate-900 text-slate-300 px-4 py-2 text-xs border-b border-slate-800 flex-shrink-0">
    <div class="max-w-[1920px] mx-auto flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
        <span class="font-bold text-white uppercase text-[11px]">CarePoint CAPQ:</span>
        <span class="text-slate-400 hidden sm:inline">Clinic Appointment & Patient Queue System</span>
      </div>
      <nav class="flex items-center gap-1.5 flex-wrap">
        <a href="index.php" class="px-2.5 py-1 rounded bg-teal-600 text-white font-bold flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> Booking Portal</a>
        <a href="kiosk.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="tablet" class="w-3 h-3"></i> Self Kiosk</a>
        <a href="display.php" target="_blank" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="tv" class="w-3 h-3"></i> Live TV Queue <i data-lucide="external-link" class="w-2.5 h-2.5 ml-0.5"></i></a>
        <a href="doctor.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="stethoscope" class="w-3 h-3"></i> Doctor Console</a>
        <a href="admin.php" class="px-2.5 py-1 rounded hover:bg-slate-800 text-slate-300 font-medium flex items-center gap-1"><i data-lucide="shield" class="w-3 h-3"></i> Admin Dashboard</a>
      </nav>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- MAIN CLINIC HEADER                                                        -->
  <!-- ========================================================================= -->
  <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm flex-shrink-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      
      <!-- Clinic Brand -->
      <a href="index.php" class="flex items-center gap-3 group">
        <div class="w-11 h-11 rounded-2xl bg-teal-600 text-white flex items-center justify-center shadow-md shadow-teal-600/20 group-hover:scale-105 transition">
          <i data-lucide="activity" class="w-6 h-6"></i>
        </div>
        <div>
          <h1 class="text-xl font-black tracking-tight text-slate-900 leading-tight">CarePoint Medical</h1>
          <p class="text-[11px] font-bold text-teal-700 tracking-wider uppercase">Outpatient Clinic & Queue System</p>
        </div>
      </a>

      <!-- Quick Info & Action -->
      <div class="hidden md:flex items-center gap-8 text-xs text-slate-600">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center">
            <i data-lucide="clock" class="w-4 h-4"></i>
          </div>
          <div>
            <p class="font-bold text-slate-800">Clinic Hours</p>
            <p class="text-slate-500">Mon - Sat: 8:00 AM - 6:00 PM</p>
          </div>
        </div>

        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
            <i data-lucide="phone-call" class="w-4 h-4"></i>
          </div>
          <div>
            <p class="font-bold text-slate-800">Emergency Line</p>
            <p class="text-slate-500">(02) 8900-CARE</p>
          </div>
        </div>

        <a href="#booking-section" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-md shadow-teal-600/20 transition flex items-center gap-1.5">
          <i data-lucide="calendar-plus" class="w-4 h-4"></i>
          <span>Book Appointment</span>
        </a>
      </div>

    </div>
  </header>

  <!-- ========================================================================= -->
  <!-- HERO BANNER SECTION                                                       -->
  <!-- ========================================================================= -->
  <section class="bg-gradient-to-b from-teal-900 via-teal-950 to-slate-950 text-white py-14 px-4 sm:px-6 relative overflow-hidden flex-shrink-0">
    <div class="absolute -right-24 -top-24 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-24 bottom-0 w-80 h-80 bg-teal-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 items-center relative z-10">
      
      <div class="lg:col-span-7 space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/20 border border-teal-500/30 text-teal-300 text-xs font-semibold">
          <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
          Live Queue & Advance Booking System Active
        </div>

        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
          Skip the Waiting Room. <br>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-emerald-300">Book Doctor Visits Online.</span>
        </h2>

        <p class="text-sm sm:text-base text-slate-300 max-w-xl leading-relaxed">
          Schedule your consultation ahead of time, check live doctor queues from your smartphone, and arrive just in time for your turn.
        </p>

        <!-- Quick Stats Banner -->
        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-slate-800/80 max-w-lg">
          <div>
            <p class="text-2xl font-black text-teal-300 font-mono">15m</p>
            <p class="text-xs text-slate-400">Avg. Wait Time</p>
          </div>
          <div>
            <p class="text-2xl font-black text-white font-mono">5</p>
            <p class="text-xs text-slate-400">Specialty Clinics</p>
          </div>
          <div>
            <p class="text-2xl font-black text-emerald-400 font-mono">100%</p>
            <p class="text-xs text-slate-400">Contactless Queue</p>
          </div>
        </div>
      </div>

      <!-- Quick Appointment Tracker Lookup Card -->
      <div class="lg:col-span-5 bg-white/10 backdrop-blur-xl border border-white/20 p-6 sm:p-7 rounded-3xl shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 text-teal-300 flex items-center justify-center">
            <i data-lucide="ticket" class="w-5 h-5"></i>
          </div>
          <div>
            <h3 class="text-base font-bold text-white">Have a Booking Reference?</h3>
            <p class="text-xs text-slate-300">Check queue status or digital ticket</p>
          </div>
        </div>

        <form onsubmit="event.preventDefault(); trackAppointment();" class="space-y-3">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Appointment Code / Ticket #</label>
            <div class="flex gap-2">
              <input type="text" id="lookup-code" placeholder="e.g. CP-8921 or A-101" value="" class="flex-1 bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-3 text-sm uppercase font-mono tracking-wider text-teal-300 focus:outline-none focus:ring-2 focus:ring-teal-500">
              <button type="submit" class="px-5 py-3 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs transition flex items-center gap-1 shadow-md shadow-teal-500/20">
                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                <span>Track</span>
              </button>
            </div>
          </div>
          <p class="text-[11px] text-slate-400 flex items-center gap-1">
            <i data-lucide="info" class="w-3.5 h-3.5 text-teal-400"></i>
            Sent via SMS confirmation upon booking.
          </p>
        </form>

        <!-- Live Wait Preview -->
        <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between text-xs">
          <span class="text-slate-300">Currently Serving in Gen Med:</span>
          <span class="font-mono font-bold text-teal-300 bg-teal-950/60 px-2.5 py-1 rounded-lg border border-teal-500/30" id="hero-now-serving">---</span>
        </div>
      </div>

    </div>
  </section>

  <!-- ========================================================================= -->
  <!-- MAIN APPOINTMENT BOOKING WORKFLOW                                         -->
  <!-- ========================================================================= -->
  <main id="booking-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-1 w-full">
    
    <div class="mb-8 text-center max-w-2xl mx-auto">
      <span class="text-xs font-bold uppercase tracking-wider text-teal-700 bg-teal-50 border border-teal-200 px-3 py-1 rounded-full">
        Step-by-Step Patient Registration
      </span>
      <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">Book Your Doctor Consultation</h3>
      <p class="text-sm text-slate-600 mt-1">Fill out the clinical intake details below to secure your consultation slot and obtain your priority ticket.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
      
      <!-- Stepper Header -->
      <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-bold">
        <div class="flex items-center gap-3 text-teal-800">
          <div class="w-7 h-7 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs">1</div>
          <span>Patient Demographics</span>
        </div>
        <div class="flex items-center gap-3 text-slate-700">
          <div class="w-7 h-7 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs">2</div>
          <span>Department & Doctor</span>
        </div>
        <div class="flex items-center gap-3 text-slate-700">
          <div class="w-7 h-7 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs">3</div>
          <span>Time Slot & Ticket Issue</span>
        </div>
      </div>

      <!-- Booking Form -->
      <form id="appointment-form" onsubmit="event.preventDefault(); submitBooking();" class="p-6 sm:p-10 space-y-8">
        
        <div>
          <h4 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="user" class="w-4 h-4 text-teal-600"></i>
            1. Patient Demographics
          </h4>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Legal Name <span class="text-rose-500">*</span></label>
              <input type="text" name="patient_name" id="field-name" required placeholder="e.g. Juan C. Dela Cruz" value="" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Contact Mobile Number <span class="text-rose-500">*</span></label>
              <input type="tel" name="phone_number" id="field-phone" required placeholder="0917-XXX-XXXX" value="" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Address (Optional)</label>
              <input type="email" name="email" id="field-email" placeholder="patient@example.com" value="" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Date of Birth <span class="text-rose-500">*</span></label>
              <input type="date" name="birth_date" id="field-dob" value="" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Biological Gender <span class="text-rose-500">*</span></label>
              <select name="gender" id="field-gender" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                <option value="Female" selected>Female</option>
                <option value="Male">Male</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Patient Category</label>
              <select name="patient_type" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                <option value="New Patient">New Patient (First Visit)</option>
                <option value="Returning" selected>Returning Patient / Follow-Up</option>
              </select>
            </div>
          </div>

          <!-- Priority Lane Option -->
          <div class="mt-4 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-between text-xs text-amber-900">
            <div class="flex items-center gap-2.5">
              <i data-lucide="shield-alert" class="w-4 h-4 text-amber-600 flex-shrink-0"></i>
              <span><strong>Express Priority Lane:</strong> Senior Citizens (60+), Persons with Disabilities (PWD), and Pregnant Mothers receive automated priority queue tickets.</span>
            </div>
            <label class="inline-flex items-center gap-2 cursor-pointer font-bold flex-shrink-0">
              <input type="checkbox" name="is_priority" id="field-priority" class="rounded text-teal-600 focus:ring-teal-500 w-4 h-4">
              <span>Apply Priority</span>
            </label>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="stethoscope" class="w-4 h-4 text-teal-600"></i>
            2. Medical Specialty & Assigned Doctor
          </h4>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Clinical Department <span class="text-rose-500">*</span></label>
              <select name="department_id" id="field-dept" onchange="updateDoctors()" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                <option value="General Medicine" selected>General Medicine / Family Medicine</option>
                <option value="Pediatrics">Pediatrics (Children's Clinic)</option>
                <option value="Dental Clinic">Dental & Oral Care</option>
                <option value="Cardiology">Cardiology Specialization</option>
                <option value="Dermatology">Dermatology & Skin Care</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Attending Doctor / Specialist <span class="text-rose-500">*</span></label>
              <select name="doctor_id" id="field-doctor" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                <option value="General Medicine Physician (Room 03)" selected>General Medicine Physician (Room 03)</option>
              </select>
            </div>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="calendar" class="w-4 h-4 text-teal-600"></i>
            3. Consultation Date, Preferred Time & Chief Complaint
          </h4>

          <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
            <div class="md:col-span-4">
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Appointment Date <span class="text-rose-500">*</span></label>
              <input type="date" name="appointment_date" id="field-date" value="" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
            </div>

            <div class="md:col-span-8">
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Available Time Slot <span class="text-rose-500">*</span></label>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="slot-container">
                <button type="button" onclick="selectSlot(this, '08:30 AM')" class="slot-btn py-2 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-teal-500 hover:bg-teal-50 transition">08:30 AM</button>
                <button type="button" onclick="selectSlot(this, '09:15 AM')" class="slot-btn py-2 px-3 text-xs font-semibold rounded-xl border-2 border-teal-600 bg-teal-50 text-teal-800 transition">09:15 AM</button>
                <button type="button" onclick="selectSlot(this, '10:00 AM')" class="slot-btn py-2 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-teal-500 hover:bg-teal-50 transition">10:00 AM</button>
                <button type="button" onclick="selectSlot(this, '11:15 AM')" class="slot-btn py-2 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-teal-500 hover:bg-teal-50 transition">11:15 AM</button>
                <button type="button" onclick="selectSlot(this, '01:30 PM')" class="slot-btn py-2 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-teal-500 hover:bg-teal-50 transition">01:30 PM</button>
                <button type="button" onclick="selectSlot(this, '02:45 PM')" class="slot-btn py-2 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-teal-500 hover:bg-teal-50 transition">02:45 PM</button>
                <button type="button" onclick="selectSlot(this, '03:30 PM')" class="slot-btn py-2 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-teal-500 hover:bg-teal-50 transition">03:30 PM</button>
                <button type="button" class="py-2 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed line-through" disabled>04:15 PM</button>
              </div>
              <input type="hidden" name="slot_time" id="field-slot" value="09:15 AM">
            </div>
          </div>

          <div class="mt-5">
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Chief Complaint / Primary Medical Concern</label>
            <textarea name="chief_complaint" id="field-complaint" rows="3" placeholder="Briefly describe symptoms (e.g. Mild headache, follow-up lab test review, routine physical)" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition"></textarea>
          </div>
        </div>

        <!-- Submit & Actions -->
        <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
          <p class="text-xs text-slate-500 flex items-center gap-1.5">
            <i data-lucide="lock" class="w-4 h-4 text-teal-600"></i>
            HIPAA & Data Privacy compliant
          </p>

          <div class="flex items-center gap-3">
            <button type="reset" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition">
              Reset Form
            </button>
            <button type="submit" class="px-7 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/25 transition flex items-center gap-2">
              <i data-lucide="check-circle" class="w-4 h-4"></i>
              <span>Confirm Appointment & Generate Ticket</span>
            </button>
          </div>
        </div>

      </form>
    </div>

  </main>

  <!-- Digital Ticket Modal -->
  <div id="ticket-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white text-slate-900 rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in duration-200">
      <button onclick="closeTicketModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>

      <div class="text-center border-b-2 border-dashed border-slate-200 pb-5">
        <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-700 flex items-center justify-center mx-auto mb-2 font-bold">
          <i data-lucide="activity" class="w-5 h-5"></i>
        </div>
        <h3 class="text-base font-black tracking-tight text-slate-900 uppercase">CarePoint Medical Center</h3>
        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Appointment Queue Pass</p>
      </div>

      <div class="py-6 text-center">
        <p class="text-xs font-bold uppercase text-slate-400 tracking-wider">YOUR QUEUE TICKET</p>
        <h2 id="modal-ticket-num" class="text-5xl font-black font-mono tracking-tight text-teal-600 my-2">A-107</h2>
        <p id="modal-ticket-dept" class="text-sm font-bold text-slate-800">General Medicine • Room 03</p>
        <p id="modal-ticket-doc" class="text-xs text-slate-500">Dr. Sarah Santos, MD</p>
      </div>

      <div class="bg-slate-50 rounded-2xl p-4 text-xs space-y-2 border border-slate-100">
        <div class="flex justify-between">
          <span class="text-slate-500">Patient:</span>
          <span class="font-bold text-slate-800" id="modal-patient-name">Maria Clara Santos</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Scheduled:</span>
          <span class="font-bold text-slate-800" id="modal-slot-time">Sep 18, 2026 (09:15 AM)</span>
        </div>
        <div class="flex justify-between">
          <span class="text-slate-500">Reference:</span>
          <span class="font-mono font-bold text-teal-700">CP-8921</span>
        </div>
      </div>

      <div class="mt-6 text-center">
        <div class="w-24 h-24 bg-slate-900 text-white mx-auto rounded-xl flex items-center justify-center p-2 shadow-inner">
          <i data-lucide="qr-code" class="w-16 h-16 text-teal-300"></i>
        </div>
        <p class="text-[10px] text-slate-400 mt-2">Present this QR upon arrival at the kiosk for fast-track entry.</p>
      </div>

      <div class="mt-6 pt-4 border-t border-slate-200 flex gap-2">
        <button onclick="window.print()" class="flex-1 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs flex items-center justify-center gap-1.5 transition">
          <i data-lucide="printer" class="w-4 h-4"></i> Print Pass
        </button>
        <button onclick="closeTicketModal()" class="py-2.5 px-4 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-slate-900 text-slate-400 py-8 px-4 text-xs border-t border-slate-800 mt-auto flex-shrink-0">
    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
      <div>
        <p class="text-white font-bold text-sm">CarePoint Medical Center</p>
        <p class="text-slate-500">Clinic Appointment & Patient Queue (CAPQ) System</p>
      </div>
      <div class="flex items-center gap-6">
        <a href="kiosk.php" class="hover:text-white transition">Self Kiosk</a>
        <a href="display.php" class="hover:text-white transition">Waiting Room TV</a>
        <a href="doctor.php" class="hover:text-white transition">Doctor Console</a>
        <a href="admin.php" class="hover:text-white transition">Admin Portal</a>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="assets/js/queue-api.js"></script>
  <script>
    lucide.createIcons();

    function selectSlot(btn, time) {
      document.querySelectorAll('.slot-btn').forEach(b => {
        b.className = 'slot-btn py-2 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:border-teal-500 hover:bg-teal-50 transition';
      });
      btn.className = 'slot-btn py-2 px-3 text-xs font-semibold rounded-xl border-2 border-teal-600 bg-teal-50 text-teal-800 transition';
      document.getElementById('field-slot').value = time;
    }

    async function updateDoctors() {
      const dept = document.getElementById('field-dept').value;
      const docSelect = document.getElementById('field-doctor');
      docSelect.innerHTML = '';

      try {
        const res = await fetch('modules/booking/actions.php?action=get_doctors');
        const json = await res.json();
        if (json.success && json.data) {
          const list = json.data.list || [];
          const type = json.data.type;
          
          if (type === 'users' && list.length > 0) {
            const matching = list.filter(d => !dept || d.department.toLowerCase().includes(dept.toLowerCase()) || dept.toLowerCase().includes(d.department.toLowerCase()));
            const targetList = matching.length > 0 ? matching : list;
            targetList.forEach(d => {
              const roomStr = d.room_number ? ` (Room ${d.room_number})` : '';
              docSelect.add(new Option(`${d.full_name}${roomStr} - ${d.department}`, d.full_name));
            });
            return;
          }
        }
      } catch (e) {
        console.log('Fetch doctors fallback:', e);
      }

      // Dynamic department room specialist mapping
      if (dept === 'Pediatrics') {
        docSelect.add(new Option('Pediatric Specialist (Room 02)', 'Pediatric Specialist (Room 02)'));
      } else if (dept === 'Dental Clinic') {
        docSelect.add(new Option('Dental & Oral Specialist (Room 04)', 'Dental Specialist (Room 04)'));
      } else if (dept === 'Cardiology') {
        docSelect.add(new Option('Cardiology Specialist (Room 03)', 'Cardiology Specialist (Room 03)'));
      } else if (dept === 'Dermatology') {
        docSelect.add(new Option('Dermatology Specialist (Room 03)', 'Dermatology Specialist (Room 03)'));
      } else {
        docSelect.add(new Option('General Medicine Physician (Room 03)', 'General Medicine Physician (Room 03)'));
        docSelect.add(new Option('Triage & Vitals Practitioner (Room 01)', 'Triage Practitioner (Room 01)'));
      }
    }

    async function submitBooking() {
      const name = document.getElementById('field-name').value;
      const phone = document.getElementById('field-phone').value;
      const dept = document.getElementById('field-dept').value;
      const doc = document.getElementById('field-doctor').value;
      const slot = document.getElementById('field-slot').value;
      const complaint = document.getElementById('field-complaint').value;
      const isPriority = document.getElementById('field-priority').checked ? '1' : '0';

      if (!name || !phone) {
        showToast('Please provide your full name and contact phone number.', 'Booking Warning');
        return;
      }

      const formData = new FormData();
      formData.append('name', name);
      formData.append('phone', phone);
      formData.append('department', dept);
      formData.append('time_slot', slot);
      formData.append('complaint', complaint);
      formData.append('is_priority', isPriority);

      try {
        const res = await fetch('modules/booking/actions.php?action=book_appointment', {
          method: 'POST',
          body: formData
        });
        const json = await res.json();
        if (json.success && json.data) {
          const t = json.data;
          document.getElementById('modal-ticket-num').textContent = t.ticket_number;
          document.getElementById('modal-ticket-dept').textContent = t.department;
          document.getElementById('modal-ticket-doc').textContent = t.doctor_name;
          document.getElementById('modal-patient-name').textContent = t.patient_name;
          document.getElementById('modal-slot-time').textContent = t.time_slot;

          document.getElementById('ticket-modal').classList.remove('hidden');
          QueueManager.playChime();
          QueueManager.syncFromBackend();
        } else {
          showToast(json.message || 'Booking failed.', 'Booking Error');
        }
      } catch (e) {
        showToast('Server connection error. Please try again.', 'Error');
      }
    }

    function closeTicketModal() {
      document.getElementById('ticket-modal').classList.add('hidden');
    }

    async function trackAppointment() {
      const code = document.getElementById('lookup-code').value.trim();
      if (!code) {
        showToast('Please enter your ticket number (e.g., A-102)', 'Input Required');
        return;
      }
      try {
        const res = await fetch(`modules/booking/actions.php?action=track_appointment&code=${encodeURIComponent(code)}`);
        const json = await res.json();
        if (json.success && json.data) {
          showToast(json.data.display_text, `Ticket Status: ${json.data.ticket.ticket_number}`);
        } else {
          showToast(json.message || 'Ticket code not found', 'Lookup Failed');
        }
      } catch (e) {
        showToast('Could not fetch status.', 'Lookup Error');
      }
    }

    // Sync live serving ticket
    function syncHeroServing() {
      const state = QueueManager.getState();
      if (state && state.nowCalling && (state.nowCalling.ticket_number || state.nowCalling.ticket)) {
        const t = state.nowCalling.ticket_number || state.nowCalling.ticket;
        const r = state.nowCalling.room_assigned || state.nowCalling.room || 'Room 03';
        document.getElementById('hero-now-serving').textContent = `${t} (${r})`;
      } else {
        document.getElementById('hero-now-serving').textContent = '--- (Queue Empty)';
      }
    }
    syncHeroServing();
    window.addEventListener('queue_state_updated', syncHeroServing);
  </script>
</body>
</html>
