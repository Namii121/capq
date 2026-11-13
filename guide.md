CAPQ – Clinic Appointment and Patient Queue Management System
Project Guide & Deliverables Documentation
1. Project Overview

The Clinic Appointment and Patient Queue Management System (CAPQ) is a web-based application designed to streamline clinic appointment scheduling, patient queue management, physician consultations, and administrative monitoring.

The system provides five integrated portals that support different clinic operations:

Patient Portal (index.php) – Allows patients to book appointments online and track their appointment status.
Kiosk Portal (kiosk.php) – Allows walk-in patients to generate queue tickets, including regular and priority tickets.
TV Display Portal (display.php) – Displays real-time queue information and patient calling announcements.
Doctor Portal (doctor.php) – Enables physicians to call patients, manage consultations, and record patient vital signs.
Admin Portal (admin.php) – Provides administrative monitoring and clinic statistics.

All project requirements specified in the project rubric have been implemented, tested, and verified.

2. Project Deliverables

The following files and resources are included in the project submission.

Rubric Requirement	File / Link	Description
Code Repository (50%)	GitHub Repository	Contains the working codebase, Native PHP REST API, MySQL database schema, and five integrated frontend portals.
API Documentation	CAPQ_Postman_Collection.json	Exported Postman Collection v2.1 containing all 10 configured API endpoints, request bodies, parameters, and test scripts.
Interactive API Documentation	docs.html / api/docs.html	Browser-based API testing interface for sending HTTP requests and inspecting JSON responses.
Software Testing and QA	Software_Testing_and_QA_Suite.pdf	Complete printable QA report containing test plans, test cases, UAT scripts, and bug tracking records.
QA Report Source	Software_Testing_and_QA_Suite.md	Markdown source of the Software Testing and QA report.
Printable QA Source	qa_report_printable.html	HTML version of the QA report used for PDF generation.
3. Technology Stack

The system uses the following technologies:

Backend: Native PHP
Database: MySQL
Frontend: HTML, CSS, and JavaScript
API Architecture: REST API
API Testing: Postman Collection v2.1
API Documentation: Interactive HTML API Explorer
Testing and QA: Functional Testing, Integration Testing, and User Acceptance Testing
4. REST API Backend

The backend is located inside the /api/ directory.

4.1 API Modules
File	Functionality
appointments.php	Handles online appointment creation, required-field validation, and appointment tracking through reference codes.
queues.php	Handles walk-in ticket generation, queue management, patient calling, consultation completion, and recording of patient vital signs.
doctors.php	Retrieves the list of practicing physicians, assigned consultation rooms, and active queue lengths.
stats.php	Provides clinic KPI statistics, including registered patients, queue counts, consultations, completed appointments, and average waiting time.
database.php	Manages MySQL database connections with automatic port detection for ports 3306 and 3307, including an in-memory demo fallback.
4.2 Key API Features

The REST API supports the following operations:

Online appointment booking.
Required-field validation.
Appointment tracking using reference codes (CP-XXXX).
Regular walk-in ticket generation.
Priority walk-in ticket generation.
Real-time queue monitoring.
Doctor patient calling.
Consultation completion.
Electronic Health Record (EHR) vital signs recording.
Clinic KPI and analytics retrieval.
4.3 Patient Vital Signs

The consultation module supports recording the following patient information:

Blood Pressure (BP)
Temperature
Pulse Rate
Oxygen Saturation (SpO2)
5. Frontend Portals

The system consists of five integrated portals.

5.1 Patient Portal – index.php

Provides patients with an online appointment booking interface.

Features:

Online appointment registration.
Required-field validation.
Appointment reference code generation.
Appointment status tracking.
5.2 Kiosk Portal – kiosk.php

Designed for patients visiting the clinic without an online appointment.

Features:

Walk-in patient registration.
Regular queue ticket generation.
Priority queue ticket generation.
Queue number assignment.
5.3 TV Display Portal – display.php

Displays the current clinic queue information.

Features:

Real-time queue status.
Current patient number.
Doctor calling announcements.
Queue updates.
Chime or sound notification when a patient is called.
5.4 Doctor Portal – doctor.php

Provides physicians with tools for managing patient consultations.

Features:

Viewing assigned patients.
Calling the next patient.
Managing active consultations.
Recording patient vital signs.
Completing consultations.
5.5 Admin Portal – admin.php

Provides clinic administrators with an overview of clinic operations.

Features:

Total registered patients for the day.
Patients currently in queue.
Patients currently in consultation.
Completed consultations.
Average patient waiting time.
6. Postman Collection and API Documentation
6.1 Postman Collection

File: CAPQ_Postman_Collection.json

The exported Postman Collection follows the Postman Collection v2.1 format.

It contains:

10 configured API endpoints.
Request methods and URLs.
Request bodies.
Query parameters.
Automated test scripts.
Mock response fixtures.
6.2 Response Fixtures

The collection includes representative HTTP response scenarios:

HTTP Status	Description
200 OK	Successful API request.
201 Created	Successfully created resource.
400 Bad Request	Invalid or incomplete request data.
404 Not Found	Requested resource does not exist.
6.3 Interactive API Explorer

Files: docs.html / api/docs.html

The project also includes a browser-based API testing interface.

To use it:

Start the PHP development server or your local web server.
Ensure the backend and database are configured.
Open the API documentation page in your browser.
Select an API endpoint.
Provide the required parameters or request body.
Click Send Request.
Inspect the returned JSON response.

Local development URL:

http://localhost/CAPQ/docs.html

7. Software Testing and Quality Assurance

Official Report: Software_Testing_and_QA_Suite.pdf

The QA documentation provides a comprehensive overview of the testing activities performed during the development and verification of the CAPQ system.

7.1 Test Plan

The test plan identifies the objectives, scope, procedures, and expected results of the system testing process.

7.2 Functional Test Cases

Total: 12 Functional Test Cases

Test Case	Description
FTC-01	Online appointment booking.
FTC-02	Required-field validation.
FTC-03	Appointment status tracking.
FTC-04	Walk-in ticket generation.
FTC-05	Priority queue rules.
FTC-06	Doctor patient calling.
FTC-07	Queue status updates.
FTC-08	Chime or sound notification emission.
FTC-09	Patient vital signs recording.
FTC-10	Consultation completion.
FTC-11	Doctor directory and assigned room retrieval.
FTC-12	Clinic KPI and statistics retrieval.
7.3 Integration Test Cases

Total: 6 Integration Test Cases

The integration tests validate the communication and data flow between the system modules.

The primary workflow covers:

Online Booking → Kiosk → TV Display → Doctor Consultation → Analytics

These tests verify that patient information and queue updates are properly reflected throughout the system.

7.4 User Acceptance Testing (UAT)

Total: 4 UAT Persona Scripts

UAT ID	User Persona	Workflow
UAT-01	Patient	Books an appointment and tracks appointment status.
UAT-02	Senior Walk-in	Registers through the kiosk and receives a priority queue ticket.
UAT-03	Physician	Calls patients, manages consultations, and records vital signs.
UAT-04	Head Nurse	Monitors clinic queues and reviews operational statistics.
7.5 Initial Bug Tracking Logs

Total: 6 Bug Tracking Records

The QA report documents six initial bugs encountered during development.

Each bug record includes:

Bug identification number.
Description of the issue.
Root cause analysis.
Corrective action or fix applied.
Verification status.

These records demonstrate the defect identification, resolution, and verification process followed during development.

7.6 Testing Results

The completed QA report documents a 100% pass rate for the reported test cases.

The test documentation includes:

12 Functional Test Cases.
6 Integration Test Cases.
4 User Acceptance Testing Scripts.
6 Initial Bug Tracking Logs.
8. Project Setup and Execution

Follow these instructions to run the CAPQ system locally.

Step 1: Install Required Software

Ensure that the following applications are installed:

XAMPP or another PHP-compatible local development environment.
MySQL or MariaDB.
A modern web browser.
Postman (optional, for API testing).
Step 2: Place the Project in the Web Server Directory

Copy the CAPQ project folder into your web server directory.

For XAMPP:

C:\xampp\htdocs\CAPQ

Step 3: Configure the Database
Start Apache and MySQL through the XAMPP Control Panel.
Open phpMyAdmin.
Create or select the required database.
Import the provided database.sql file.
Verify that the required database tables have been created.
Step 4: Configure the Backend

Check the database configuration in:

api/database.php

Ensure that the database credentials and connection settings match your local MySQL configuration.

The connection logic supports automatic detection of MySQL ports 3306 and 3307.

Step 5: Launch the Application

Open the following URL in your browser:

http://localhost/CAPQ/

You can access the different portals using their respective PHP files:

Portal	Local URL
Patient Portal	http://localhost/CAPQ/index.php
Kiosk Portal	http://localhost/CAPQ/kiosk.php
TV Display	http://localhost/CAPQ/display.php
Doctor Portal	http://localhost/CAPQ/doctor.php
Admin Portal	http://localhost/CAPQ/admin.php
API Documentation	http://localhost/CAPQ/docs.html
Step 6: Test the REST API

To test the API:

Open Postman.
Import CAPQ_Postman_Collection.json.
Configure the local API base URL if necessary.
Select an endpoint.
Send the request.
Verify the HTTP status code and JSON response.

Alternatively, use the built-in interactive API Explorer.

9. Project Directory Structure

The primary project files are organized as follows:

text
CAPQ/
│
├── api/
│   ├── appointments.php
│   ├── queues.php
│   ├── doctors.php
│   ├── stats.php
│   ├── database.php
│   └── docs.html
│
├── index.php
├── kiosk.php
├── display.php
├── doctor.php
├── admin.php
│
├── database.sql
│
├── CAPQ_Postman_Collection.json
│
├── docs.html
│
├── Software_Testing_and_QA_Suite.pdf
├── Software_Testing_and_QA_Suite.md
├── qa_report_printable.html
│
└── GUIDE.md


Note: This directory structure highlights the primary project files and may omit additional assets, scripts, and supporting directories.

10. Submission Checklist

The following deliverables are prepared for submission through Google Classroom.

GitHub repository containing the project codebase.
Native PHP REST API implementation.
MySQL database schema.
Five integrated frontend portals.
Postman Collection v2.1 JSON export.
Interactive API documentation.
12 Functional Test Cases.
6 Integration Test Cases.
4 User Acceptance Testing Scripts.
6 Initial Bug Tracking Logs.
Printable Software Testing and QA PDF report.
Markdown source documentation.
11. Conclusion

The Clinic Appointment and Patient Queue Management System (CAPQ) was developed to provide an integrated solution for clinic appointment scheduling, walk-in patient registration, queue monitoring, physician consultations, and administrative reporting.

The project includes a Native PHP REST API backend, MySQL database integration, five functional frontend portals, Postman API documentation, and a comprehensive Software Testing and Quality Assurance report.

All requirements specified in the project rubric have been implemented, tested, and verified according to the documented testing results.

The completed deliverables are prepared for submission and evaluation through Google Classroom.

Project: Clinic Appointment and Patient Queue Management System (CAPQ)
Documentation: Project Guide and Deliverables
Status: Implementation and testing completed
Primary Documentation File: GUIDE.md
