# Figma Design & Recreation Guide: Clinic Appointment and Patient Queue (CAPQ)

A step-by-step design manual for creating the **CarePoint Clinic Appointment and Patient Queue** system in Figma. Whether you are building it from scratch using Figma Auto-Layout or importing the HTML files directly via plugins, this guide provides all the exact measurements, colors, typography, components, and layout structures.

---

## 🚀 Quick Option: 1-Click Import into Figma

If you want to instantly convert the HTML pages into fully editable native Figma layers and vector frames:

1. In Figma, go to **Plugins** → Search for **"HTML to Design"** (or **"Builder.io HTML to Figma"**).
2. Open the plugin.
3. You can either:
   - **Paste local URLs** (if XAMPP is running):
     - `http://localhost/CAPQ/index.html`
     - `http://localhost/CAPQ/kiosk.html`
     - `http://localhost/CAPQ/display.html`
     - `http://localhost/CAPQ/doctor.html`
     - `http://localhost/CAPQ/admin.html`
   - **Or drag & drop** the individual `.html` files directly into the plugin window!
4. The plugin will create pixel-perfect Figma artboards with text layers, auto-layouts, and SVG icons.

---

## 🎨 Design Tokens & Styles Setup

Before drawing frames, set up these styles in your Figma file (**Design** panel → **Local Styles**):

### 1. Color Styles

| Style Name | Hex Code | Purpose / Usage |
| :--- | :--- | :--- |
| **`Brand/Teal-600`** | `#0D9488` | Primary brand, active tabs, main buttons |
| **`Brand/Teal-500`** | `#14B8A6` | Accents, logos, glowing ticket callouts |
| **`Brand/Teal-50`** | `#F0FDFA` | Light tint badges, active card backgrounds |
| **`Neutral/Slate-950`** | `#020617` | TV Display background, Kiosk background |
| **`Neutral/Slate-900`** | `#0F172A` | Top navigation bars, dark cards, footer |
| **`Neutral/Slate-100`** | `#F1F5F9` | App page background (Doctor & Admin) |
| **`Neutral/Slate-50`** | `#F8FAFC` | Form inputs, light surface cards |
| **`Clinical/Blue-600`** | `#0284C7` | Appointment badges, walk-in card accent |
| **`Status/Success-600`**| `#059669` | Completed visit badges, online status |
| **`Status/Amber-500`**  | `#F59E0B` | Priority badges, waiting alerts, Triage |
| **`Status/Rose-600`**   | `#E11D48` | Emergency hotline, skip/cancel buttons |

### 2. Typography Styles

Use **Inter** for all UI elements and **JetBrains Mono** for numbers/tickets:

| Text Style | Font Family | Weight | Size | Line Height | Tracking |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Display / Ticket Hero** | `JetBrains Mono` | Black (900) | `96px` | `100%` | `-0.04em` |
| **Display / Ticket Card** | `JetBrains Mono` | Black (900) | `48px` | `100%` | `-0.02em` |
| **Heading 1 (Hero)** | `Inter` | ExtraBold (800)| `36px` | `44px` | `-0.02em` |
| **Heading 2 (Section)**| `Inter` | Bold (700) | `24px` | `32px` | `-0.01em` |
| **Heading 3 (Card)**   | `Inter` | SemiBold (600) | `18px` | `24px` | `0` |
| **Body (Default)**     | `Inter` | Regular (400) | `14px` | `20px` | `0` |
| **Body Semibold**      | `Inter` | SemiBold (600) | `14px` | `20px` | `0` |
| **Caption / Meta**     | `Inter` | Medium (500) | `12px` | `16px` | `0.01em` |
| **Overline (Caps)**    | `Inter` | Bold (700) | `11px` | `14px` | `0.08em` |

### 3. Corner Radius (Border Radius)
- **Buttons & Badges**: `8px` to `12px`
- **Cards & Inputs**: `16px` to `20px`
- **Top-level Frames / Large Cards**: `24px` to `32px`

---

## 🧩 Master Reusable Components to Build First

Create a Figma page named **`❖ Components`**:

### Component 1: `❖ Badge / Queue Status`
Create an Auto-Layout Frame (`Shift + A`), Padding `H: 10px, V: 4px`, Radius `999px` (Pill shape):
- **Variant `Waiting`**: Fill `#FEF3C7` (Amber-100), Text `#92400E` (Amber-800), "Waiting (Next)"
- **Variant `In Consultation`**: Fill `#CCFBF1` (Teal-100), Text `#115E59` (Teal-800), Dot `#14B8A6`, "In Consultation"
- **Variant `Completed`**: Fill `#F1F5F9` (Slate-100), Text `#475569` (Slate-600), "Completed"
- **Variant `Priority`**: Fill `#FDE68A` (Amber-200), Text `#78350F` (Amber-950), Star Icon, "PRIORITY"

### Component 2: `❖ Ticket Box / Number`
Auto-Layout Frame (`Shift + A`), Radius `14px`:
- **Size Small (Table)**: Width `56px`, Height `36px`, Fill `#F0FDFA`, Stroke `#99F6E4`, Text `JetBrains Mono 14px Black`
- **Size Large (Doctor)**: Width `72px`, Height `72px`, Fill `#0D9488`, Text `JetBrains Mono 28px Black White`

### Component 3: `❖ Button / Primary`
Auto-Layout Frame, Radius `12px`, Padding `H: 20px, V: 12px`, Fill `#0D9488`, Text `Inter 14px Bold White`, Gap `8px` (Icon + Label).

### Component 4: `❖ Thermal Ticket Slip`
Width `340px`, Fill `#FFFFFF`, Radius `24px`, Stroke `#E2E8F0`:
- Dashed separator line: Stroke `2px #E2E8F0`, Dash pattern `4, 4`.
- Ticket number centered (`JetBrains Mono 56px #0D9488`).
- Meta data list (`Patients ahead`, `Wait time`, `Issued at`).
- Simulated QR Code: Square `96 x 96px`, Fill `#0F172A`.

---

## 📐 Step-by-Step Screen Layout Guides

Create a Figma page named **`🖥️ Screens`**:

### Screen 1: Patient Appointment Booking Portal (`index.html`)
- **Frame Preset**: Desktop `1440 x 1024` (or `1200 x Auto`), Fill `#F8FAFC`.
- **Structure**:
  1. **Top Announcement Bar**: Height `36px`, Fill `#0F172A`, text links.
  2. **Header Bar**: Height `80px`, Fill `#FFFFFF`, Border Bottom `1px #E2E8F0`. Left: Logo icon + "CarePoint Medical". Right: Operating hours + Emergency hotline + "Book Appointment" button.
  3. **Hero Section**: Height `400px`, Fill Linear Gradient (`#134E4A` to `#020617`).
     - Left: Big headline ("Skip the Waiting Room..."), description, stats chips (15m wait, 5 clinics).
     - Right: Floating tracker card (Width `380px`, Glassmorphic fill `#FFFFFF 10%`, border `#FFFFFF 20%`, input + "Track" button).
  4. **Booking Card**: Width `1100px`, Auto-Layout Vertical, Fill `#FFFFFF`, Radius `28px`, Shadow `0 20px 25px -5px rgba(0,0,0,0.08)`:
     - 3-Step Stepper bar at top (`#F8FAFC`).
     - Form Section 1: Demographics (3-column grid of text inputs).
     - Form Section 2: Department & Doctor dropdowns.
     - Form Section 3: Date picker + 8 Time slot pill buttons.
     - Form Section 4: Complaint textarea + Priority checkbox banner.
     - Bottom CTA bar: "Confirm & Generate Ticket" (`#0D9488`).

---

### Screen 2: Self-Service Patient Check-In Kiosk (`kiosk.html`)
- **Frame Preset**: Tablet Landscape `1024 x 768`, Fill `#020617` (Deep Dark).
- **Structure**:
  1. **Kiosk Header**: Logo, "CarePoint Kiosk Station 01", Big Clock (`JetBrains Mono 28px`).
  2. **Headline**: Centered, "Welcome! How can we assist you today?"
  3. **Two Primary Touch Cards** (Auto-Layout Horizontal, Gap `24px`, each Card Width `450px`, Height `380px`):
     - **Card Left (Appointment)**: Border `2px #334155`, Fill `#0F172A`. Calendar icon, "I Have an Appointment", input box with large text + "Check-in" button.
     - **Card Right (Walk-In)**: Border `2px #334155`, Fill `#0F172A`. Users icon, "Walk-In Patient", 2x2 grid of department buttons (General Care, Pediatrics, Dental, Triage).
  4. **Bottom Priority Assistance Banner**: Fill `#F59E0B 10%`, Border `#F59E0B 30%`, Senior/PWD icon + "Request Priority Pass" button.

---

### Screen 3: Waiting Area Live Queue TV Monitor (`display.html`)
- **Frame Preset**: Presentation 16:9 `1920 x 1080`, Fill `#020617` (High Contrast TV).
- **Structure**:
  1. **Top Bar**: Height `90px`, Fill `#0F172A`. Clinic brand + Big Digital Clock `10:48:32 AM` (`JetBrains Mono 36px`).
  2. **Main Screen (Split 50/50, Gap `32px`, Padding `32px`)**:
     - **Left Hero ("NOW CALLING")**:
       - Fill `#042F2E` (Dark Teal gradient), Border `3px #14B8A6`, Radius `32px`, Outer Glow effect (`Blur 40px, #14B8A6 30%`).
       - Badge: "NOW CALLING / NOW SERVING".
       - Ticket Number: `JetBrains Mono 110px Black #5EEAD4`.
       - Patient Name: `Inter 24px Bold #F8FAFC`.
       - Destination Box (`#020617`, Radius `20px`): "PLEASE PROCEED TO ROOM 03 • GENERAL MEDICINE", "Dr. Sarah Santos, MD".
     - **Right Queue Board (5 Stacked Room Cards, Auto-Layout Vertical, Gap `14px`)**:
       - Room 01: Triage (Serving `T-089` | Next `T-090`)
       - Room 02: Pediatrics (Serving `P-045` | Next `P-046`)
       - Room 03: General Medicine (Highlighted border `#14B8A6`, Serving `A-102` | Next `A-103`)
       - Room 04: Dental (Serving `D-018` | Next `D-019`)
       - Room 05: Pharmacy (Serving `B-201` | Next `B-202`)
  3. **Bottom Ticker**: Height `56px`, Fill `#0F172A`, Announcement tag (`#14B8A6`) + Scrolling ticker text.

---

### Screen 4: Doctor Workstation & Consultation Console (`doctor.html`)
- **Frame Preset**: Desktop `1440 x 900`, Fill `#F1F5F9`.
- **Structure**:
  1. **Doctor Header**: Height `84px`, Fill `#0F172A`. Doctor avatar, "Dr. Sarah Santos, MD • Room 03", Waiting counter (5), Completed counter (18).
  2. **Controls Toolbar**: Height `60px`, Fill `#FFFFFF`, Border Bottom `1px #E2E8F0`.
     - Left: `[CALL NEXT PATIENT]` (`#0D9488`), `[Recall Chime]` (`#FFFFFF`).
     - Right: `[Finish Visit]` (`#059669`), `[No Show / Skip]` (`#FFF1F2`).
  3. **Main Content (Split 7 / 5 cols)**:
     - **Left Column (Active Patient Card)**:
       - Width `760px`, Fill `#FFFFFF`, Radius `24px`.
       - Patient Ticket Box (`A-102`) + Name ("Juan Carlos Dela Cruz, 38, Male").
       - Triage Vitals Grid: 4 cards (BP `120/80`, Temp `36.8°C`, Heart Rate `76 bpm`, SpO2 `99%`).
       - Chief complaint text box.
       - Doctor's Diagnosis & Prescription inputs.
     - **Right Column (Upcoming Room 3 Queue)**:
       - Width `500px`, Fill `#FFFFFF`, Radius `24px`.
       - List of patients waiting in Room 3 with ticket badges, time, reason, and direct `Call` buttons.
       - Highlighted Senior Citizen card (`P-012`) with Amber styling.

---

### Screen 5: Clinic Admin Operations Dashboard (`admin.html`)
- **Frame Preset**: Desktop `1440 x 900`, Fill `#F1F5F9`.
- **Structure**:
  1. **Admin Header**: Fill `#0F172A`, Logo, "Clinic Operations Console", `+ Register Walk-in` button, `Export CSV` button.
  2. **4 Stat KPI Cards** (Row, Gap `16px`):
     - Total Today (`54`), In Queue (`12`), Avg Wait (`15 mins`), Completed (`38`).
  3. **Master Queue Table Panel**:
     - Fill `#FFFFFF`, Radius `24px`.
     - Filter toolbar: Search input, Department dropdown, Status dropdown.
     - Table rows with Columns: `Ticket`, `Patient Name`, `Type`, `Department & Doctor`, `Check-in Time`, `Status Badge`, `Actions`.

---

## 💡 Figma Pro-Tips for this Project

1. **Use Auto-Layout Everywhere (`Shift + A`)**:
   - Set Card containers to **Fill Container** horizontally so forms and tables resize fluidly.
   - Use fixed gap values (`8px`, `12px`, `16px`, `24px`) to adhere to the 8pt grid.
2. **Icons**:
   - Install the **"Lucide Icons"** or **"Iconify"** Figma plugin to insert the exact icons matching the HTML code (`stethoscope`, `activity`, `clock`, `phone-call`, `qr-code`, `printer`, etc.).
3. **Interactive Prototyping Links**:
   - In Figma's **Prototype** tab:
     - Link the "Book Appointment" button on Screen 1 to the Ticket Confirmation Modal.
     - Link the Kiosk cards on Screen 2 to open the Thermal Slip Modal as an **Overlay** (centered).
     - Link the "Call Next" button on Screen 4 to trigger the chime and change the active ticket.

