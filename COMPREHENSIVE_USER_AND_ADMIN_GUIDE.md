# Greg ICT Hub LMS — Comprehensive User & Administrator Reference Guide

**Organization**: Greg & Co. ICT Ltd, Makurdi, Benue State, Nigeria  
**Platform**: Technical & Vocational Education Learning Management System (IDEAS-TVET Project)  
**Target Domain**: [https://lms.gregicthub.com](https://lms.gregicthub.com/)  
**Target Audiences**: System Administrators, TVET Instructors/Teachers, Facilitators, and Technical Trainees  

---

## Table of Contents

1. [Executive Summary & System Architecture](#1-executive-summary--system-architecture)
2. [Interface Foundations: Branding, Themes & Loading Spinners](#2-interface-foundations-branding-themes--loading-spinners)
3. [Module 1: System Administrator Guide](#3-module-1-system-administrator-guide)
   - 3.1 User Administration & Role Filtering
   - 3.2 Inline Name Editing & Edit User Modal
   - 3.3 Bulk Actions: Conversion, Soft Deletes & Restoration
   - 3.4 System Audit Trail & Security Event Inspection
   - 3.5 Bulk TVET Teacher CSV Onboarding
4. [Module 2: TVET Teacher & Instructional Design Guide](#4-module-2-tvet-teacher--instructional-design-guide)
   - 4.1 Visual Course Builder & Modular Structure
   - 4.2 Formulating ABCD Measurable Learning Objectives
   - 4.3 Technical Resource Attachments (PDFs & Standards)
   - 4.4 Assignment Configuration, Deadlines & Cut-Off Policies
   - 4.5 Competency Rubric Builder (100-Point Model)
   - 4.6 Gradebook Management & Formula-Safe CSV Export
   - 4.7 Day 8 Practical Capstone Wizard & Synthetic Practice Grading
5. [Module 3: TVET Pedagogy & Inclusive Design Studio (Days 9 & 10)](#5-module-3-tvet-pedagogy--inclusive-design-studio-days-9--10)
   - 5.1 TVET Pedagogy Studio Hub & Milestone Tracking
   - 5.2 Official NBTE Occupational Standards Database
   - 5.3 Competency-Based Lesson Builder (4-Step Demo Plan, Guided & Independent Practice, PBL, Fault Diagnosis & WBL)
   - 5.4 Inclusive Lesson Designer (3-Tier Differentiation, UDL 3 Pillars, Safety Non-Negotiable Check, Gender Strategies & Rotation Matrix)
   - 5.5 Day 9 & Day 10 Guided Interactive Wizards
   - 5.6 A4 Official Lesson Plan Print & Export
6. [Module 4: Facilitator & Assessor Mentorship Guide](#6-module-4-facilitator--assessor-mentorship-guide)
   - 6.1 Accessing the Pedagogy Review Queue
   - 6.2 5-Dimension Rubric Grading (100-Point Scale)
   - 6.3 Qualitative Feedback, Approval & Revisions Workflow
7. [Module 5: Trainee & Learner Guide](#7-module-5-trainee--learner-guide)
   - 7.1 Self-Registration, Sign-In & Password Visibility Eye Toggle
   - 7.2 Enrolling & Navigating Trade Syllabi
   - 7.3 Submitting Practical Workshop Assignments
   - 7.4 Reviewing Rubric Feedback & Records of Competency
8. [Module 6: Technical Maintenance, Security & FAQ](#8-module-6-technical-maintenance-security--faq)
   - 8.1 Maintenance Mode & Secret Deployment Bypass
   - 8.2 Security Headers & SSL Enforce
   - 8.3 Frequently Asked Questions & Troubleshooting

---

## 1. Executive Summary & System Architecture

The **Greg ICT Hub LMS** is engineered specifically for Technical and Vocational Education & Training (TVET) under the Nigeria Innovation, Development and Effectiveness in the Acquisition of Skills (IDEAS) Project.

The platform supports 8 key TVET trades:
1. **Solar Photovoltaic (PV) Installation & Maintenance**
2. **Electrical Installation & Maintenance Works**
3. **Welding & Fabrication Technology**
4. **Automobile Mechanics & Diagnostics**
5. **Carpentry & Furniture Craftsmanship**
6. **Catering & Hospitality Craft Practice**
7. **Garment Making & Fashion Design**
8. **Agricultural Technology & Mechanization**

### Dual Operational Paradigm:
- **Mode A (Pedagogy Studio & Teacher Sandbox)**: Teachers create competency lessons aligned with official National Board for Technical Education (NBTE) National Occupational Standards (NOS), plan 4-step demonstrations, structure differentiated UDL adaptations, and undergo facilitator verification.
- **Mode B (Accredited Institutional Course Delivery)**: Vocational institutions deliver structured curricula to trainees with assignment cutoff policies, transparent rubric scoring, and exportable gradebooks.

---

## 2. Interface Foundations: Branding, Themes & Loading Spinners

### 2.1 Brand Theme & Typography
The LMS utilizes Greg & Co. ICT Ltd corporate branding, combining deep slate navy (`#020617` / `#0f172a`), rich rose-crimson accents (`#e11d48`), emerald success indicators (`#10b981`), and amber warning badges (`#f59e0b`).

### 2.2 Light & Dark Theme Switcher
- **Light Theme**: High-contrast, clean slate background (`#f8fafc`) optimized for high-glare workshop environments.
- **Dark Theme**: Deep charcoal slate (`#020617`) designed to minimize eye fatigue during extended instructional design sessions.
- **Persistence**: Theme choices are preserved in browser `localStorage` and enforced before HTML rendering to eliminate theme flashing.

### 2.3 Page Load & Activity Spinner Indicators
To guarantee immediate feedback and eliminate user uncertainty during page loads:
1. **Initial Full-Page Preloader (`app.blade.php`)**: When loading the URL or hard refreshing (`Ctrl + F5`), a pure CSS animated dual-ring spinner with the Greg ICT Hub badge displays instantly before JavaScript mounts.
2. **Top-Screen Glowing Progress Bar**: During Inertia page transitions, a full-width gradient progress line (`rose-600` &rarr; `amber-400`) pulses along the top edge of the browser viewport.
3. **Floating Rolling Spinner Badge**: In the top-right corner, a dark glassmorphic badge displays an animated spinning dual ring, pulsing activity dot, and "Loading..." text during background data operations.
4. **Centered Translucent Overlay Spinner**: For heavier operations taking longer than 220ms, a subtle centered backdrop with an enlarged rotating spinner confirms background processing.

---

## 3. Module 1: System Administrator Guide

### 3.1 User Administration & Filtering (`/admin/users`)
1. Log in with an administrator account (`admin@gregicthub.com` or `super_admin`).
2. Navigate to **Users** in the top navigation bar.
3. **Role Filters**: Click quick pills to isolate `Super Admin`, `Admin`, `Facilitator`, `Teacher`, or `Student`.
4. **Trashed Status Filter**:
   - `Active Only` (default): Displays operational accounts.
   - `Trashed Only`: Displays soft-deleted accounts eligible for restoration.
   - `All Users`: Comprehensive historical registry.
5. **Search & Pagination**: Search by name, email, phone, or institution name. Set page size to 15, 25, 50, or 100 rows.

### 3.2 Inline Name Editing & Edit User Modal
1. Click directly on any user's name in the table row.
2. The **Edit User Modal** opens with their current parameters:
   - Full Name
   - Email Address
   - Role (Super Admin, Admin, Facilitator, Teacher, Student)
   - Technical College / Institution
   - Phone Number
   - Trade Specialization
   - Active Account Status checkbox
3. Click **Save Changes**. The table updates immediately without page reloads, and the modification is logged in the System Audit Trail.

### 3.3 Bulk Actions: Conversions, Soft Deletes & Restoration
1. Use the checkboxes in the leftmost column to select multiple users, or click the header checkbox to select all users on the current page.
2. The **Bulk Actions Bar** slides in with batch controls:
   - **Convert Role**: Select a new role (e.g. convert students to teachers) and confirm. The system changes all selected accounts in a single atomic transaction.
   - **Bulk Soft Delete**: Soft-deletes selected users, disabling login while preserving course and submission integrity.
   - **Bulk Restore**: Restores soft-deleted users back to active status.
   - **Bulk Force Delete**: Permanently removes selected accounts from MySQL (available for super admins with confirmation dialog).

### 3.4 System Audit Trail (`/admin/audit-logs`)
Every administrative action produces an immutable audit record:
- **Timestamp & Actor**: Exact UTC/local time, actor ID, and actor name.
- **Action Type**: `user_created`, `user_updated`, `user_soft_deleted`, `user_restored`, `bulk_role_converted`, `login_attempt`.
- **IP Address & User Agent**: Network origin of the request.
- **Diff Inspector**: Click "View Details" to see the before and after JSON values.

### 3.5 Bulk TVET Teacher CSV Onboarding
To onboard up to 200 teachers across Benue State:
1. Prepare a UTF-8 CSV named `tvet_teachers.csv` with headers:
   ```csv
   First Name,Last Name,Email,Phone,Institution,State,Trade Specialization
   Terver,Paul,terver.paul@gstcmakurdi.edu.ng,08012345678,GSTC Makurdi,Benue,Solar PV Installation
   ```
2. Navigate to `/admin/users`, select the file under **Bulk Import**, and click **Upload**.

---

## 4. Module 2: TVET Teacher & Instructional Design Guide

### 4.1 Visual Course Builder
1. Click **Create New Course** from your Teacher Dashboard.
2. Select your trade category (e.g. *Solar Photovoltaic Installation*), course code (e.g. `TVET-SPV-01`), title, and level.
3. Click **+ Add Section** to create modular topics.

### 4.2 ABCD Measurable Learning Objectives
Structure practical objectives using the 4-part ABCD framework:
- **Audience (A)**: "Given personal protective equipment, TVET trainees will..."
- **Behavior (B)**: "...test and verify open-circuit voltage (Voc) on a 4-module string..."
- **Condition (C)**: "...using a calibrated digital multimeter in the workshop..."
- **Degree (D)**: "...within ±2% of manufacturer datasheet tolerances."

### 4.3 Technical Resource Attachments
- Click **+ Add Resource** to upload workshop manuals (`.pdf`, `.docx`) or link external regulatory URLs (NBTE, NAPTIN, NEMSA).

### 4.4 Assignment Configuration & Cut-Off Policies
- Set **Allow Submissions From**, **Due Date**, and **Cut-Off Date**. Submissions past the cut-off are automatically rejected by the server.

### 4.5 Competency Rubric Builder (100 Points)
1. Navigate to **Rubrics** &rarr; **+ New Rubric**.
2. Create criteria totaling 100 points across 4 achievement levels:
   - *Exemplary* (100%)
   - *Proficient* (80%)
   - *Satisfactory* (60%)
   - *Unsatisfactory* (0–40%)
3. Attach to assignments for single-click assessment.

### 4.6 Gradebook Management & CSV Export
- View class submissions, apply rubric scores, and click **Export CSV Report** for formula-safe gradebook downloads.

### 4.7 Day 8 Practical Wizard & Synthetic Practice
1. Open `/day8-wizard` and click **Generate Synthetic Practice Learner**.
2. The LMS spawns an isolated sandbox student who submits sample work.
3. Grade the work according to the standard 78/100 benchmark and submit for facilitator verification.

---

## 5. Module 3: TVET Pedagogy & Inclusive Design Studio (Days 9 & 10)

### 5.1 TVET Pedagogy Studio Hub (`/pedagogy/studio`)
The central hub for advanced instructional design:
- **Progress Trackers**: Visual completion percentage for Day 9 and Day 10.
- **Trade Coverage**: Quick links across all 8 IDEAS trades.
- **Occupational Standards Registry**: Search and inspect official NBTE standards.

### 5.2 Competency Lesson Builder (`/pedagogy/lessons/builder`)
A 7-section professional lesson design studio:
1. **Trade & Standards Mapping**: Select trade, link NBTE NOS, task, duration, and level.
2. **ABCD Learning Outcomes**: Measurable knowledge, skill, and safety outcomes.
3. **4-Step Instructor Demonstration Plan**:
   - *Step 1: Instructor Shows & Tells*
   - *Step 2: Instructor Shows, Learner Tells*
   - *Step 3: Learner Shows, Instructor Tells*
   - *Step 4: Learner Shows & Tells Independently*
4. **Guided & Independent Practice**: Workstations, tools, PPE checklists, and quality benchmarks.
5. **Project-Based Learning (PBL)**: Driving questions, milestones, and deliverables.
6. **Fault Diagnosis & Troubleshooting Scenarios**: Symptoms, diagnostic tests, rectifications, and critical safety rules.
7. **Work-Based Learning (WBL) & Assessment Criteria**: Industry simulations, logbook criteria, and multi-method assessment (Observation, Product Inspection, Oral Questions, Underpinning Knowledge).

### 5.3 Inclusive Lesson Designer (`/pedagogy/inclusive-designer`)
Adapts technical lessons for diverse learners without compromising safety:
1. **3-Tier Differentiated Practical Activities**:
   - *Tier 1 (Foundation/Beginner)*: Pictorial schematics, color-coded terminals, and step-by-step cue cards.
   - *Tier 2 (Standard)*: Core curriculum task according to standard criteria.
   - *Tier 3 (Advanced/Enrichment)*: In-depth fault injection, optimization, and system sizing challenges.
2. **Universal Design for Learning (UDL)**:
   - *Multiple Means of Engagement*: Real-world context, learner choice, collaborative pods.
   - *Multiple Means of Representation*: Multi-sensory manuals, tactile samples, video walkthroughs.
   - *Multiple Means of Action & Expression*: Physical assemblies, verbal debriefs, digital diagrams.
3. **Mandatory Safety Preservation Check**: Checkbox ensuring safety protocols, PPE, and hazard mitigation are never diluted.
4. **Gender-Responsive Strategies**: Female leadership in male-dominated trades, pair rotations, supportive culture.
5. **Workshop Rotation Matrix**: Timed rotation schedules so limited tools/equipment are shared safely and efficiently.

### 5.4 Day 9 & Day 10 Guided Wizards
Interactive step-by-step wizards (`/pedagogy/day9-wizard` and `/pedagogy/day10-wizard`) that validate required fields and record milestone evidence in the database.

### 5.5 Printable Lesson Plan Export (`/pedagogy/lessons/{lesson}/export`)
Formats the complete lesson, demonstration steps, fault scenarios, and inclusive adaptations into an official A4 document ready for printing or PDF export.

---

## 6. Module 4: Facilitator & Assessor Mentorship Guide

### 6.1 Pedagogy Review Queue (`/facilitator/pedagogy-reviews`)
Facilitators access submitted lesson plans requiring assessment.

### 6.2 5-Dimension Rubric Grading (100 Points)
Each submitted lesson is evaluated across 5 dimensions (0–20 points each):
1. **Standard Alignment & ABCD Objectives** (20 pts)
2. **4-Step Demonstration & Practical Design** (20 pts)
3. **Fault Diagnosis & Industry Simulation** (20 pts)
4. **UDL & Differentiated Inclusion** (20 pts) — *Safety compliance verified*
5. **Assessment Rigor & Workshop Equipment Matrix** (20 pts)

### 6.3 Verdict & Feedback
- Total score is calculated automatically.
- Facilitator enters constructive qualitative remarks.
- Click **Approve Lesson Plan** or **Request Revisions**.

---

## 7. Module 5: Trainee & Learner Guide

### 7.1 Self-Registration, Sign-In & Password Eye Toggle
1. Navigate to `https://lms.gregicthub.com` and click **Register** or **Sign In**.
2. Enter email and password.
3. Tap the **Eye icon** (👁️) to unmask your password and confirm accuracy before submitting.
4. Re-mask your password in public labs to protect credentials.

### 7.2 Accessing Courses & Learning Materials
1. The Trainee Dashboard displays active trade courses and upcoming deadlines.
2. Click into any topic to read objectives, download PDF schematics, and view assignments.

### 7.3 Submitting Practical Assignments
1. Review instructions, allowed file extensions (`.pdf`, `.jpg`, `.docx`), and due dates.
2. Attach evidence files or enter workshop notes.
3. Click **Submit Assignment**.

### 7.4 Reviewing Graded Rubrics
1. Once graded, open the assignment to view your breakdown across each criterion.
2. Read specific instructor remarks for technical improvement.

---

## 8. Module 6: Technical Maintenance, Security & FAQ

### 8.1 Maintenance Mode & Bypass
When running production updates:
```bash
php artisan down --render="errors::503" --secret="greg-deploy-bypass-2026"
```
Administrators can bypass maintenance mode by visiting:
`https://lms.gregicthub.com/?secret=greg-deploy-bypass-2026`

To bring the platform back online:
```bash
php artisan up
```

### 8.2 Security Headers & SSL Enforce
- All HTTP traffic is permanently redirected to HTTPS via Let's Encrypt SSL.
- Production `.htaccess` enforces `Strict-Transport-Security`, `X-Frame-Options: SAMEORIGIN`, and `X-Content-Type-Options: nosniff`.

### 8.3 Frequently Asked Questions

**Q1: How do I change a student to a teacher?**  
*Answer*: As an admin, go to `/admin/users`, check the box next to the user's name, click **Convert Role**, select `teacher`, and click **Apply Role Change**.

**Q2: What happens if an instructor soft-deletes a user?**  
*Answer*: The user cannot sign in, but their course records, grades, and submissions are preserved. Filter by `Trashed Only` to restore them at any time.

**Q3: Can inclusive accommodations lower workshop safety rules?**  
*Answer*: No. The Non-Negotiable Safety Preservation Check explicitly prevents any accommodation from compromising PPE compliance, electrical isolation protocols, or machine safety rules.

**Q4: Where do I view audit logs of who made changes?**  
*Answer*: Administrators can visit `/admin/audit-logs` to inspect timestamped logs with old/new values, actor names, and IP addresses.

---

*Greg ICT Hub LMS — Greg & Co. ICT Ltd, Makurdi, Benue State, Nigeria*  
*Developed for Technical & Vocational Education (IDEAS-TVET Project)*
