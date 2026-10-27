# Greg ICT Hub LMS — User & Operations Manual

**Organization**: Greg & Co. ICT Ltd, Makurdi, Benue State, Nigeria  
**Platform**: Technical & Vocational Education Learning Management System  
**Audience**: TVET Teachers (IDEAS Project), Facilitators, and System Administrators  

---

## 1. Introduction & Operating Modes

Greg ICT Hub LMS is engineered specifically for Technical and Vocational Education & Training (TVET). It bridges technical pedagogical theory with hands-on workshop practices through two distinct modes:

1. **Mode A — Teacher Training & Sandbox Mode**:
   - Every TVET teacher operates within a private sandbox.
   - Teachers generate isolated synthetic practice learners to simulate real classroom submissions.
   - Teachers complete the **Day 8 10-Step Practical Training Wizard** to prove competency in LMS instructional design.
2. **Mode B — Institutional Technical Course Delivery**:
   - Used for delivering accredited vocational programs to genuine students across Nigerian technical colleges.
   - Incorporates strict assignment cut-offs, late penalties, and rubric grading with exportable audit logs.

---

## 2. Teacher Guide: The Day 8 10-Step Practical Workflow

The Day 8 Practical Wizard verifies 10 core competencies through automated database evidence tracking:

### Step 1: Create an Independent Course
1. Click **Create New Course** from your Teacher Dashboard.
2. Select your vocational trade category (e.g., *Solar Photovoltaic (PV) Installation & Maintenance*, *Electrical Installation*, *Welding & Fabrication*).
3. Provide a course code (e.g., `TVET-SPV-01`), title, and duration.
4. Set mode to **Training / Sandbox**.
5. Save to enter the **Visual Course Builder**.

### Step 2: Label a Topic / Section
1. In the Visual Course Builder, click **+ Add Section**.
2. Give the topic a descriptive technical title (e.g., *"Solar PV Fundamentals and Safety Protocols"*).
3. Provide an orientation overview.

### Step 3: Write a Measurable Learning Objective (ABCD Model)
1. Under your section, click **+ Add Objective**.
2. Complete the 4-part ABCD structure:
   - **Audience**: "Given standard electrical safety gear, trainees will..."
   - **Behavior**: "...inspect and isolate DC disconnect switches using a multimeter..."
   - **Condition**: "...on a 48V active solar bench simulator..."
   - **Degree**: "...with 100% adherence to IEC 62548 safety protocols."
3. Select the Bloom's taxonomy level (*Apply* or *Analyze*).

### Step 4: Attach a Technical PDF or Educational URL
1. Click **+ Add Resource**.
2. Choose **PDF Document** to upload workshop schematics, or **External URL** to link regulatory standards (e.g., NAPTIN or NBTE guidelines).
3. Resources are stored in private storage with randomized file hashes to prevent unauthorized direct scraping.

### Step 5: Configure an Assignment with Submission Rules
1. Click **+ Add Assignment**.
2. Provide practical instructions and select submission type (*File Upload & Online Text*).
3. Set maximum allowed file count and allowed extensions (`.pdf`, `.docx`, `.xlsx`).

### Step 6: Configure Due Dates and Cut-Off Rules
1. In the assignment settings, define:
   - **Allow Submissions From**: The start time of the practical session.
   - **Due Date**: Expected completion deadline.
   - **Cut-Off Date**: Absolute deadline after which no submissions are accepted.
   - **Late Policy**: Choose *Allowed* (flagged as late) or *Disallowed*.

### Step 7: Define and Activate a Rubric
1. Click **+ Attach / Create Rubric**.
2. Define performance criteria with weights and points totaling **100 points**:
   - *Safety & PPE Compliance* (20 pts)
   - *Component Orientation & Mounting* (20 pts)
   - *Wiring & DC Protection* (20 pts)
   - *Cable Sizing & Crimping* (20 pts)
   - *System Commissioning Test* (20 pts)
3. For each criterion, specify 4 achievement levels (*Exemplary*, *Proficient*, *Satisfactory*, *Unsatisfactory*).

### Step 8: Preview as Student
1. Click **Preview as Learner** in the top-right header.
2. The LMS renders the student view, showing exactly what trainees see when enrolled.

### Step 9: Grade a Practice Submission (The 78/100 Demonstration)
1. Go to **Day 8 Practical Wizard** and click **Generate Synthetic Practice Learner**.
2. The LMS creates an isolated student profile (`is_synthetic = true`, owned by your account) who immediately submits a practice inspection report.
3. Open the submission and click **Grade with Rubric**.
4. Select the levels corresponding to the demonstration score:
   - Safety: Level 2 (16/20)
   - Orientation: Level 2 (16/20)
   - Wiring: Level 3 (15/20)
   - Crimping: Level 2 (16/20)
   - Commissioning: Level 3 (15/20)
   - **Total Calculated Score**: **78 / 100**.
5. Provide actionable qualitative feedback: *"Commendable craftsmanship on PV string wiring and DC crimping. Pay closer attention to cable labeling in the combiner box."*
6. Choose whether to release immediately or hold in draft.

### Step 10: Export Grade Report to CSV
1. In the Course Gradebook, click **Export CSV Report**.
2. The system streams a formula-injection-safe CSV report with all student names, IDs, scores, and assessor remarks.

---

## 3. Facilitator Guide: Verification & Mentorship

1. **Assigned Teachers Roster**:
   - Access **Facilitator Dashboard** to view your assigned cohort of TVET teachers.
2. **Reviewing Practical Progress**:
   - Click **Inspect Practice** on any teacher's row.
   - Inspect their course sections, ABCD objectives, uploaded files, and sample rubric grading.
3. **Formal Verification**:
   - In the verification modal, select **Approve Practice**.
   - Input constructive facilitator remarks and submit.
   - The teacher's Day 8 wizard badge changes to **Verified by Facilitator**.

---

## 4. Administrator Guide: 200 TVET Teachers Bulk Onboarding

### Step 4.1 — Bulk CSV Format
Prepare a UTF-8 CSV file named `tvet_teachers.csv` with the following header:
```csv
First Name,Last Name,Email,Phone,Institution,State,Trade Specialization
Terver,Paul,terver.paul@gstcmakurdi.edu.ng,08012345678,GSTC Makurdi,Benue,Solar PV Installation
Dooshima,Akaa,dooshima.akaa@bstcgboko.edu.ng,08087654321,BSTC Gboko,Benue,Electrical Installation
```

### Step 4.2 — Upload Process
1. Log in as an Administrator (`admin@gregicthub.com`).
2. Navigate to **Administration** &rarr; **User Management**.
3. Under **Bulk TVET Teacher Import**, select the CSV file and click **Process Bulk Import**.
4. The system validates entries, skips existing duplicates, sets default secure passwords, assigns the `teacher` role, and logs the action in the audit trail.

---

## 5. Administrator Guide: User Management, Soft Deletes & Audit Trail

### 5.1 User Management & Inline Editing (`/admin/users`)
- **Status & Role Filtering**: Filter users by role (`Super Admin`, `Admin`, `Facilitator`, `Teacher`, `Student`) and trashed state (`Active Only`, `Trashed Only`, `All Users`).
- **Inline Editing**: Click on any user's name in the table to open the Edit User modal. Update their name, email, role, phone, institution, and active status.
- **Bulk Role Conversions**: Select multiple checkboxes (or "Select All") and click "Convert Role" to instantly convert batches of students to teachers or facilitators.
- **Soft Delete & Restore**: Soft-deleted users are disabled from signing in while their course submissions and grades remain completely preserved. Restore them at any time with a single click.

### 5.2 System Audit Trail (`/admin/audit-logs`)
- Every administrative event (user created, edited, soft deleted, restored, bulk role converted) is logged with the actor's identity, IP address, user-agent, and before/after diffs.

---

## 6. TVET Pedagogy & Inclusive Design Studio (Days 9 & 10)

- **Studio Hub** (`/pedagogy/studio`): Access the NBTE Occupational Standards database and track completion for Day 9 and Day 10.
- **Competency Lesson Builder** (`/pedagogy/lessons/builder`): Design competency lessons with 4-step demonstrations (Show & Tell), guided practice, independent practice, project-based learning (PBL), fault diagnosis scenarios, and work-based learning simulations.
- **Inclusive Lesson Designer** (`/pedagogy/inclusive-designer`): Universal Design for Learning (UDL), 3-tier differentiated tasks, non-negotiable safety preservation check, gender equity, and workshop equipment rotation schedule.
- **Facilitator Pedagogy Reviews** (`/facilitator/pedagogy-reviews`): Facilitators evaluate submitted plans across 5 criteria (20 points each, 100 points total) with qualitative feedback and approval workflows.
- **Export / Print Lesson** (`/pedagogy/lessons/{lesson}/export`): Clean official A4 document ready for printing or PDF export.

---

## 7. UI Experience: Password Eye Toggle, Themes & Page Load Spinners

- **Password Reveal / Hide Eye Toggle**: Tap the eye icon inside password fields on the Login and Register forms to confirm character entry and prevent authentication errors.
- **Light & Dark Theme Switcher**: Toggle between daytime workshop light mode and low-glare dark mode (`#020617`) from the top navigation bar.
- **Page Load & Activity Spinners**:
  - *Initial Preloader*: An animated dual-ring spinner with the Greg ICT Hub badge displays instantly during full page loads and hard refreshes before Vue mounts.
  - *Inertia Activity Indicators*: A top glowing progress line and a floating rolling spinner badge in the top-right corner indicate ongoing page transitions and background requests.

---

*For complete, comprehensive documentation, refer to `COMPREHENSIVE_USER_AND_ADMIN_GUIDE.md` or access the in-app interactive guide at `https://lms.gregicthub.com/user-guide`.*

