import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    """Set background color of a table cell."""
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tc_pr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    """Set internal padding for a table cell."""
    tc_pr = cell._tc.get_or_add_tcPr()
    tc_mar = parse_xml(
        f'<w:tcMar {nsdecls("w")}>'
        f'<w:top w:w="{top}" w:type="dxa"/>'
        f'<w:bottom w:w="{bottom}" w:type="dxa"/>'
        f'<w:left w:w="{left}" w:type="dxa"/>'
        f'<w:right w:w="{right}" w:type="dxa"/>'
        f'</w:tcMar>'
    )
    tc_pr.append(tc_mar)

def add_callout(doc, text, title="NOTE / BEST PRACTICE", border_color="E11D48", bg_color="FFF1F2"):
    """Adds a stylish callout box with a colored left border and light background."""
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    table.columns[0].width = Inches(6.5)

    cell = table.cell(0, 0)
    set_cell_background(cell, bg_color)
    set_cell_margins(cell, top=140, bottom=140, left=200, right=200)

    # Set left border thick, clear others
    tc_pr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:top w:val="none"/>'
        f'<w:left w:val="single" w:sz="24" w:space="0" w:color="{border_color}"/>'
        f'<w:bottom w:val="none"/>'
        f'<w:right w:val="none"/>'
        f'</w:tcBorders>'
    )
    tc_pr.append(borders)

    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.line_spacing = 1.15

    r_title = p.add_run(f"[{title}] ")
    r_title.bold = True
    r_title.font.size = Pt(10)
    r_title.font.name = 'Segoe UI'
    if border_color == "E11D48":
        r_title.font.color.rgb = RGBColor(190, 18, 60)
    elif border_color == "0D9488":
        r_title.font.color.rgb = RGBColor(15, 118, 110)
    else:
        r_title.font.color.rgb = RGBColor(30, 41, 59)

    r_body = p.add_run(text)
    r_body.font.size = Pt(9.5)
    r_body.font.name = 'Segoe UI'
    r_body.font.color.rgb = RGBColor(51, 65, 85)

    # Empty paragraph after table for spacing
    p_after = doc.add_paragraph()
    p_after.paragraph_format.space_before = Pt(0)
    p_after.paragraph_format.space_after = Pt(6)

def add_styled_table(doc, headers, rows, col_widths=None):
    """Creates a beautifully styled table with dark header and alternating row colors."""
    table = doc.add_table(rows=len(rows) + 1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False

    # Style header
    for i, title in enumerate(headers):
        cell = table.cell(0, i)
        set_cell_background(cell, "0F172A") # Dark Navy Slate
        set_cell_margins(cell, top=120, bottom=120, left=140, right=140)
        p = cell.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        p.paragraph_format.space_before = Pt(2)
        p.paragraph_format.space_after = Pt(2)
        r = p.add_run(title)
        r.bold = True
        r.font.size = Pt(9.5)
        r.font.name = 'Segoe UI'
        r.font.color.rgb = RGBColor(255, 255, 255)

    # Style rows
    for r_idx, row_data in enumerate(rows):
        bg = "F8FAFC" if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, val in enumerate(row_data):
            cell = table.cell(r_idx + 1, c_idx)
            set_cell_background(cell, bg)
            set_cell_margins(cell, top=100, bottom=100, left=140, right=140)
            p = cell.paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            r = p.add_run(str(val))
            r.font.size = Pt(9)
            r.font.name = 'Segoe UI'
            r.font.color.rgb = RGBColor(30, 41, 59)

    # Set column widths if provided
    if col_widths:
        for row in table.rows:
            for idx, width in enumerate(col_widths):
                row.cells[idx].width = Inches(width)

    # Empty paragraph after table
    p_after = doc.add_paragraph()
    p_after.paragraph_format.space_before = Pt(0)
    p_after.paragraph_format.space_after = Pt(6)

def build_word_document(output_path):
    doc = docx.Document()

    # Set page margins (0.75 in)
    sections = doc.sections
    for section in sections:
        section.top_margin = Inches(0.75)
        section.bottom_margin = Inches(0.75)
        section.left_margin = Inches(0.75)
        section.right_margin = Inches(0.75)

        # Header & Footer setup
        header = section.header
        hp = header.paragraphs[0]
        hp.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        hrun = hp.add_run("Greg ICT Hub LMS — Comprehensive User & Operations Manual")
        hrun.font.name = 'Segoe UI'
        hrun.font.size = Pt(8.5)
        hrun.font.color.rgb = RGBColor(148, 163, 184)

        footer = section.footer
        fp = footer.paragraphs[0]
        fp.alignment = WD_ALIGN_PARAGRAPH.CENTER
        frun = fp.add_run("© 2026 Greg & Co. ICT Ltd, Makurdi, Benue State, Nigeria • IDEAS-TVET Project • https://lms.gregicthub.com")
        frun.font.name = 'Segoe UI'
        frun.font.size = Pt(8)
        frun.font.color.rgb = RGBColor(148, 163, 184)

    # Base styling
    normal_style = doc.styles['Normal']
    normal_style.font.name = 'Segoe UI'
    normal_style.font.size = Pt(10)
    normal_style.font.color.rgb = RGBColor(30, 41, 59)
    normal_style.paragraph_format.line_spacing = 1.15
    normal_style.paragraph_format.space_after = Pt(6)

    # ==========================================
    # COVER PAGE / TITLE SECTION
    # ==========================================
    title_p = doc.add_paragraph()
    title_p.paragraph_format.space_before = Pt(20)
    title_p.paragraph_format.space_after = Pt(4)
    run_org = title_p.add_run("GREG & CO. ICT LIMITED • MAKURDI, BENUE STATE")
    run_org.font.size = Pt(11)
    run_org.bold = True
    run_org.font.color.rgb = RGBColor(225, 29, 72) # Rose Red Accent

    h1 = doc.add_paragraph()
    h1.paragraph_format.space_before = Pt(4)
    h1.paragraph_format.space_after = Pt(8)
    run_main = h1.add_run("Greg ICT Hub LMS\nComprehensive User & Administrator Reference Guide")
    run_main.font.size = Pt(24)
    run_main.bold = True
    run_main.font.color.rgb = RGBColor(15, 23, 42) # Dark Slate Navy

    sub_p = doc.add_paragraph()
    sub_p.paragraph_format.space_before = Pt(0)
    sub_p.paragraph_format.space_after = Pt(16)
    run_sub = sub_p.add_run(
        "Standard Operating Procedures, Instructional Design Workflows, and Technical Manual "
        "for System Administrators, TVET Instructors, Facilitators, and Technical Trainees."
    )
    run_sub.font.size = Pt(11)
    run_sub.italic = True
    run_sub.font.color.rgb = RGBColor(71, 85, 105)

    # Metadata Box
    meta_headers = ["Parameter", "System Specification / Details"]
    meta_rows = [
        ["Target Portal URL", "https://lms.gregicthub.com"],
        ["Institutional Sponsoring Body", "Greg & Co. ICT Ltd in partnership with Benue State IDEAS-TVET Project"],
        ["Audience Scope", "System Administrators, Technical Instructors, Facilitators, Trainees"],
        ["Supported Trades (8 Key Trades)", "Solar PV, Electrical Installation, Welding, Automobile, Carpentry, Catering, Garment Making, Agricultural Technology"],
        ["Document Release Version", "Version 2.4 — Complete Production Release (October 2026)"],
        ["Technical Foundations", "Laravel 11, Vue 3, Inertia.js, Vite, Tailwind CSS, MySQL, Hostinger Dedicated Cloud Architecture"]
    ]
    add_styled_table(doc, meta_headers, meta_rows, [2.0, 4.5])

    add_callout(
        doc,
        "This official operations manual provides end-to-end guidance for the Greg ICT Hub TVET Learning Management System. "
        "It includes instructions for User Management & System Audit Trails, Visual Course Building, Competency-Based TVET Pedagogy (Days 9 & 10), "
        "Inclusive Workshop & UDL Instructional Design, and Trainee Assignment Submissions.",
        title="DOCUMENT INTENT & USAGE POLICY",
        border_color="E11D48",
        bg_color="FFF1F2"
    )

    doc.add_page_break()

    # ==========================================
    # TABLE OF CONTENTS / SUMMARY
    # ==========================================
    h_toc = doc.add_heading("Table of Contents", level=1)
    h_toc.paragraph_format.space_before = Pt(12)
    h_toc.paragraph_format.space_after = Pt(8)

    toc_items = [
        ("Section 1: Executive Architecture & Supported TVET Trades", "Overview of TVET educational mission and 8 key vocational trades"),
        ("Section 2: User Interface, Themes (Light & Dark) and Page Load Spinners", "Theme switching, anti-glare dark mode, and multi-tier activity indicators"),
        ("Section 3: Module 1 — System Administrator Operations Guide", "User management, inline editing, role conversions, soft deletes, and audit logs"),
        ("Section 4: Module 2 — TVET Teachers & Instructional Design Guide", "Course builder, ABCD objectives, 100-pt rubrics, gradebook CSV export, Day 8 wizard"),
        ("Section 5: Module 3 — TVET Pedagogy & Inclusive Design Studio (Days 9 & 10)", "NBTE NOS standards, 4-step demos, PBL, fault diagnosis, UDL, rotation matrix"),
        ("Section 6: Module 4 — Facilitators & Assessors Mentorship Guide", "Review queue, 5-dimension 100-point rubric evaluation, and approval workflow"),
        ("Section 7: Module 5 — Trainees & Technical Students Guide", "Self-registration, sign-in, password eye toggle, assignments, and rubric feedback"),
        ("Section 8: Module 6 — Technical Maintenance, Security Protocols & FAQ", "Maintenance mode bypass, SSL headers, session security, and troubleshooting")
    ]
    add_styled_table(doc, ["Module / Section", "Core Topic Focus"], toc_items, [2.5, 4.0])

    # ==========================================
    # SECTION 1: EXECUTIVE ARCHITECTURE & TRADES
    # ==========================================
    h_s1 = doc.add_heading("Section 1: Executive Architecture & Supported TVET Trades", level=1)
    h_s1.paragraph_format.space_before = Pt(18)
    h_s1.paragraph_format.space_after = Pt(6)

    doc.add_paragraph(
        "The Greg ICT Hub Learning Management System is custom-engineered for Technical and Vocational Education & Training (TVET). "
        "Unlike generic academic LMS platforms that focus primarily on text lectures and multiple-choice quizzes, Greg ICT Hub LMS is "
        "fundamentally centered around practical workshop skills, measurable performance criteria, occupational safety standards, "
        "and competency-based progression."
    )

    doc.add_heading("1.1 The 8 Priority IDEAS-TVET Vocational Trades", level=2)
    trades_data = [
        ["Solar Photovoltaic (PV) Installation & Maintenance", "NBTE-NOS-SOL-01", "DC/AC isolation, PV array sizing, charge controllers, inverters, deep-cycle battery banks, IEC 62548."],
        ["Electrical Installation & Maintenance Works", "NBTE-NOS-ELE-02", "Conduit wiring, distribution boards, earthing tests, circuit breakers, load balancing, IEE 18th edition."],
        ["Welding & Fabrication Technology", "NBTE-NOS-WLD-03", "SMAW/MMA arc welding, joint preparation, beveling, welding positions (1G-4G), tensile inspection, PPE."],
        ["Automobile Mechanics & Diagnostics", "NBTE-NOS-AUT-04", "OBD-II scanner diagnosis, DTC code clearance, brake hydraulic bleeding, powertrain servicing, sensor testing."],
        ["Carpentry & Furniture Craftsmanship", "NBTE-NOS-CRP-05", "Mortise & tenon joints, dovetail joinery, power circular sawing, sanding, moisture testing, wood finishing."],
        ["Catering & Hospitality Craft Practice", "NBTE-NOS-CAT-06", "HACCP hygiene, cross-contamination prevention, knife craftsmanship, 3-course plating, oven temperature control."],
        ["Garment Making & Fashion Design", "NBTE-NOS-GAR-07", "Body dimension taking, block drafting, industrial sewing machine threading, French seams, zipper insertions."],
        ["Agricultural Technology & Mechanization", "NBTE-NOS-AGR-08", "Tractor 3-point hitch implement coupling, boom sprayer calibration, drip irrigation scheduling, seed rate metering."]
    ]
    add_styled_table(doc, ["Vocational Trade", "Standard Code", "Key Practical Competencies & Workshop Focus"], trades_data, [2.0, 1.3, 3.2])

    # ==========================================
    # SECTION 2: UI, THEMES & LOADING SPINNERS
    # ==========================================
    h_s2 = doc.add_heading("Section 2: User Interface, Themes & Loading Spinners", level=1)
    h_s2.paragraph_format.space_before = Pt(18)
    h_s2.paragraph_format.space_after = Pt(6)

    doc.add_paragraph(
        "The Greg ICT Hub LMS features an interface specifically adapted for diverse physical environments—from bright outdoor workshops "
        "in Makurdi, Gboko, and Otukpo, to computer training labs and evening instructional planning."
    )

    doc.add_heading("2.1 Day/Night Theme Switching", level=2)
    doc.add_paragraph(
        "Users can switch between Light Mode and Dark Mode with a single click on the Sun/Moon toggle in the navigation bar. "
        "Light mode offers high-contrast text and crisp borders ideal for brightly lit classrooms. Dark mode (#020617) offers a sleek, "
        "energy-efficient dark palette that significantly reduces visual fatigue during extended lesson planning. Preferences are saved "
        "in local storage and applied instantly on subsequent visits."
    )

    doc.add_heading("2.2 Multi-Tier Page Load Spinner System", level=2)
    doc.add_paragraph(
        "To ensure users always have immediate feedback when navigating or submitting data on varied network connections, the LMS employs three distinct loading mechanisms:"
    )
    spinner_steps = [
        ["Initial Page Preloader (app.blade.php)", "A pure CSS animated dual-ring spinner with the Greg ICT Hub LMS badge displays instantaneously when a URL is accessed or hard-refreshed (Ctrl+F5). It requires zero JavaScript to run and smoothly fades out once the Vue application mounts."],
        ["Top Screen Glowing Progress Line (PageActivityIndicator.vue)", "A full-width animated gradient line (rose-600 via brand-500 to amber-400) pulses along the top viewport edge during all Inertia page transitions, filtering, and form submissions."],
        ["Floating Rolling Spinner Badge", "A floating dark glassmorphic badge in the top-right corner displays an animated spinning dual ring, active status dot, and 'Loading...' text. It features a minimum 250ms display duration to eliminate confusing visual flickers."],
        ["Centered Translucent Overlay Spinner", "For requests exceeding 220ms (e.g. database seeding, bulk imports, report generation), a centered translucent backdrop with an enlarged rotating ring confirms background processing."]
    ]
    add_styled_table(doc, ["Loading Mechanism", "Visual Behavior & Technical Role"], spinner_steps, [2.2, 4.3])

    # ==========================================
    # SECTION 3: SYSTEM ADMINISTRATOR GUIDE
    # ==========================================
    h_s3 = doc.add_heading("Section 3: Module 1 — System Administrator Operations Guide", level=1)
    h_s3.paragraph_format.space_before = Pt(18)
    h_s3.paragraph_format.space_after = Pt(6)

    doc.add_paragraph(
        "Administrators maintain total control over user roles, account lifecycles, and institutional oversight via the User Administration "
        "and System Audit Trail modules."
    )

    doc.add_heading("3.1 User Administration & Role Management (/admin/users)", level=2)
    doc.add_paragraph("The User Administration dashboard provides real-time counts and precise filters for all system accounts:")
    admin_steps = [
        ["1. Role Filtering", "Select between Super Admin, Admin, Facilitator, Teacher, and Student quick filter pills to isolate specific user groups."],
        ["2. Trashed State Filtering", "Toggle between 'Active Only' (default operational accounts), 'Trashed Only' (soft-deleted accounts awaiting restore or purge), and 'All Users'."],
        ["3. Instant Search", "Search by user name, email address, phone number, or technical college name (e.g., 'GSTC Makurdi')."],
        ["4. Inline Name Editing", "Click directly on any user's name in the table to open the Edit User modal. Update their name, email, role, phone, institution, trade specialization, and active status."],
        ["5. Bulk Role Conversions", "Select multiple checkboxes or click 'Select All'. In the sliding Bulk Actions bar, select 'Convert Role' and choose 'teacher' to instantly convert batches of student accounts into certified teachers without losing their data."],
        ["6. Soft Delete & Restore", "Soft delete non-compliant accounts to immediately revoke portal access while preserving all submitted coursework and grades. Restore them with one click at any time."]
    ]
    add_styled_table(doc, ["Operation Step", "Action & Administrative Safeguards"], admin_steps, [1.8, 4.7])

    add_callout(
        doc,
        "Soft Deletion vs Force Deletion: When an account is soft-deleted, the LMS sets deleted_at in the database. "
        "The user is barred from signing in, but their historical grades, assignment submissions, and audit footprints remain 100% intact. "
        "Force Delete (permanent purge) is strictly restricted to Super Administrators with an explicit confirmation safeguard.",
        title="CRITICAL DATA INTEGRITY SAFEGUARD",
        border_color="0D9488",
        bg_color="F0FDFA"
    )

    doc.add_heading("3.2 System Audit Trail (/admin/audit-logs)", level=2)
    doc.add_paragraph(
        "Every administrative action is permanently recorded in the immutable audit trail. Each log entry records: "
        "Actor Name and ID, Event Type (user_created, user_updated, user_soft_deleted, user_restored, bulk_role_converted), "
        "IP Address, Browser User-Agent, and a detailed JSON Before/After Diff allowing administrators to inspect exactly what parameters changed."
    )

    # ==========================================
    # SECTION 4: TVET TEACHER & COURSE BUILDER
    # ==========================================
    h_s4 = doc.add_heading("Section 4: Module 2 — TVET Teachers & Instructional Design Guide", level=1)
    h_s4.paragraph_format.space_before = Pt(18)
    h_s4.paragraph_format.space_after = Pt(6)

    doc.add_paragraph(
        "TVET Instructors can design modular courses, specify rigorous ABCD learning outcomes, configure strict submission rules, "
        "and establish 100-point competency rubrics."
    )

    doc.add_heading("4.1 The 4-Part ABCD Learning Objective Framework", level=2)
    doc.add_paragraph("Instructors must formulate technical outcomes using the ABCD model:")
    abcd_data = [
        ["Audience (A)", "The specific learner cohort", "'Given standard personal protective equipment, TVET trainees will...'"],
        ["Behavior (B)", "Action verb denoting physical or cognitive task", "'...measure open-circuit voltage (Voc) and short-circuit current (Isc)...'"],
        ["Condition (C)", "Tools, workshop setting, or equipment constraints", "'...using a calibrated digital multimeter on a 4-module string...'"],
        ["Degree (D)", "The acceptable standard of craftsmanship or tolerance", "'...within ±2% of manufacturer STC specifications.'"]
    ]
    add_styled_table(doc, ["ABCD Element", "Pedagogical Definition", "TVET Practical Example"], abcd_data, [1.4, 2.0, 3.1])

    doc.add_heading("4.2 100-Point Competency Rubric Design", level=2)
    doc.add_paragraph(
        "Instructors create transparent rubrics totaling 100 points distributed across 4-5 weighted performance criteria (e.g. Workshop Safety [20 pts], "
        "Component Mounting [20 pts], Wiring Integrity [20 pts], Cable Crimping [20 pts], System Commissioning [20 pts]). "
        "Each criterion includes 4 achievement level descriptors: Exemplary (100%), Proficient (80%), Satisfactory (60%), and Unsatisfactory (0-40%). "
        "When grading submissions, instructors simply click the corresponding rubric cells, and total marks calculate automatically."
    )

    doc.add_heading("4.3 Day 8 Practical Wizard & Synthetic Practice Grading", level=2)
    doc.add_paragraph(
        "To verify instructional competency, teachers complete the Day 8 10-Step Practical Wizard. Teachers click 'Generate Synthetic Practice Learner', "
        "which spawns an isolated sandbox student profile that immediately submits a practice report. The teacher grades the report according to the "
        "standard 78/100 benchmark, adds qualitative remarks, and submits the milestone for facilitator verification."
    )

    # ==========================================
    # SECTION 5: TVET PEDAGOGY & INCLUSIVE STUDIO
    # ==========================================
    h_s5 = doc.add_heading("Section 5: Module 3 — TVET Pedagogy & Inclusive Design Studio (Days 9 & 10)", level=1)
    h_s5.paragraph_format.space_before = Pt(18)
    h_s5.paragraph_format.space_after = Pt(6)

    doc.add_paragraph(
        "The TVET Pedagogy Studio is the centerpiece of the Days 9 and 10 enhancements, bridging occupational standard alignment "
        "with Universal Design for Learning (UDL) and workshop safety management."
    )

    doc.add_heading("5.1 Studio Hub (/pedagogy/studio)", level=2)
    doc.add_paragraph(
        "The Studio Hub provides comprehensive milestone tracking for Day 9 (Competency-Based TVET Pedagogy) and Day 10 (Inclusive Workshop Design). "
        "It features quick access cards to the 6 studio modules and hosts the Official NBTE Occupational Standards database table."
    )

    doc.add_heading("5.2 Competency-Based Lesson Builder (/pedagogy/lessons/builder)", level=2)
    doc.add_paragraph("The 7-section Lesson Builder guides teachers through standard-aligned lesson planning:")
    builder_steps = [
        ["1. Trade & Standards Link", "Select vocational trade, link official NBTE standard, state the occupational task, duration, and target skill level."],
        ["2. ABCD Outcomes", "Formulate measurable cognitive, psychomotor, and safety outcomes."],
        ["3. 4-Step Demonstration Plan", "Structure workshop demonstration: Step 1 (Show & Tell), Step 2 (Show & Elicit), Step 3 (Perform with Prompting), Step 4 (Independent Practice)."],
        ["4. Guided & Independent Practice", "Establish workstation layouts, tool checklists, PPE protocols, and quality tolerances."],
        ["5. Project-Based Learning (PBL)", "Formulate driving questions, milestones, and deliverables."],
        ["6. Fault Diagnosis Scenarios", "Define realistic faults, symptom identification, diagnostic testing procedures, corrective actions, and critical safety rules."],
        ["7. Work-Based Learning (WBL)", "Structure industry simulations, employer logbook expectations, and multi-method assessment criteria."]
    ]
    add_styled_table(doc, ["Builder Section", "Core Pedagogical Requirements"], builder_steps, [2.0, 4.5])

    doc.add_heading("5.3 Inclusive Lesson Designer (/pedagogy/inclusive-designer)", level=2)
    doc.add_paragraph("Adapts technical lessons for diverse learners without compromising occupational safety standards:")
    inclusive_steps = [
        ["1. 3-Tier Differentiated Activities", "Tier 1: Foundation/Beginner (pictorial schematics, color-coded terminals, step cue cards). Tier 2: Standard Core Competency. Tier 3: Advanced/Enrichment (complex fault injection, system sizing challenges)."],
        ["2. Universal Design for Learning (UDL)", "Multiple Means of Engagement (choice of projects, real-world relevance), Multiple Means of Representation (multi-sensory manuals, tactile samples), Multiple Means of Action & Expression (physical builds, verbal briefs, digital drawings)."],
        ["3. Non-Negotiable Safety Preservation Check", "Mandatory verification ensuring safety protocols, PPE, and hazard mitigation are never diluted during accommodations."],
        ["4. Gender-Responsive Workshop Strategies", "Active elimination of gender bias, supportive female peer-pairing, and equal rotation into technical leadership roles."],
        ["5. Workshop Rotation Matrix", "Structured shift schedule ensuring high-cost, limited tools (inverter testers, hydraulic crimpers) are shared equitably across trainee pods."]
    ]
    add_styled_table(doc, ["Inclusive Element", "Workshop Adaptation & Equity Focus"], inclusive_steps, [2.2, 4.3])

    add_callout(
        doc,
        "Non-Negotiable Safety Policy: Under no circumstances may an instructional accommodation bypass or lower workshop safety rules. "
        "For example, a trainee requiring physical accommodations must still operate with full PPE, circuit isolation, and safety interlocks intact. "
        "Accommodations adjust the instructional method, not the safety standard.",
        title="ZERO-TOLERANCE SAFETY STANDARD",
        border_color="E11D48",
        bg_color="FFF1F2"
    )

    doc.add_heading("5.4 Printable Lesson Plan Export (/pedagogy/lessons/{lesson}/export)", level=2)
    doc.add_paragraph(
        "Teachers and facilitators can export any completed lesson plan with its inclusive adaptations into a clean, official A4 printable format. "
        "The export includes all ABCD outcomes, 4-step demo breakdown, fault scenarios, UDL matrices, and equipment rotation schedules, ready for physical workshop binders or PDF archiving."
    )

    # ==========================================
    # SECTION 6: FACILITATOR & ASSESSOR GUIDE
    # ==========================================
    h_s6 = doc.add_heading("Section 6: Module 4 — Facilitators & Assessors Mentorship Guide", level=1)
    h_s6.paragraph_format.space_before = Pt(18)
    h_s6.paragraph_format.space_after = Pt(6)

    doc.add_paragraph(
        "Facilitators evaluate submitted teacher lesson plans and inclusive adaptations using the standardized 5-dimension TVET rubric "
        "in the Pedagogy Review Queue (/facilitator/pedagogy-reviews)."
    )

    doc.add_heading("6.1 The 5-Dimension Rubric Evaluation (100 Points Total)", level=2)
    rubric_dims = [
        ["1. Standard Alignment & ABCD Objectives (20 pts)", "Checks alignment with official NBTE NOS criteria and measurable ABCD outcome structure."],
        ["2. 4-Step Demonstration & Practice Structure (20 pts)", "Evaluates the clarity of instructor modeling, prompted learner practice, and independent mastery benchmarks."],
        ["3. Fault Diagnosis & Industry Simulation (20 pts)", "Evaluates realism of troubleshooting scenarios, logical diagnostic workflows, and work-based learning fidelity."],
        ["4. UDL & Differentiated Inclusion (20 pts)", "Verifies 3-tier task differentiation, multi-sensory representation, and strict non-negotiable safety compliance."],
        ["5. Assessment Rigor & Workshop Equipment Matrix (20 pts)", "Evaluates direct observation checklists, product rubrics, oral questions, and the rotation schedule for tools."]
    ]
    add_styled_table(doc, ["Evaluation Dimension", "Assessor Criteria & Scoring Scope"], rubric_dims, [2.5, 4.0])

    doc.add_paragraph(
        "Facilitators enter constructive qualitative feedback, calculate scores automatically, and select either 'Approve Lesson Plan' "
        "(marking the plan as Approved in the teacher's portfolio) or 'Request Revisions' (returning the plan with detailed revision notes)."
    )

    # ==========================================
    # SECTION 7: TRAINEES & STUDENTS GUIDE
    # ==========================================
    h_s7 = doc.add_heading("Section 7: Module 5 — Trainees & Technical Students Guide", level=1)
    h_s7.paragraph_format.space_before = Pt(18)
    h_s7.paragraph_format.space_after = Pt(6)

    doc.add_paragraph(
        "Technical trainees utilize the portal to track vocational progress, download workshop schematics, and submit practical assignments."
    )

    doc.add_heading("7.1 Account Registration & Show/Hide Password Eye Toggle", level=2)
    doc.add_paragraph(
        "New trainees can self-register on the portal homepage by entering their full name, email, and password. "
        "During login and registration, trainees can tap the Eye icon inside the password field to unmask characters, ensuring complex "
        "passwords are typed accurately on mobile devices. Trainees are reminded to re-mask their password when working in shared computer labs."
    )

    doc.add_heading("7.2 Submitting Practical Workshop Assignments", level=2)
    doc.add_paragraph(
        "Trainees access assignments from their dashboard. Submissions can include workshop inspection photos (.jpg, .png), technical reports (.pdf, .docx), "
        "or online text debriefs. The system clearly displays start dates, deadlines, and absolute cut-off times. Submissions made after the cut-off date "
        "are automatically rejected by the server."
    )

    doc.add_heading("7.3 Reviewing Transparent Rubric Feedback", level=2)
    doc.add_paragraph(
        "Once an instructor grades a submission, the trainee can view their complete rubric breakdown to see exact points awarded across each performance "
        "criterion (e.g. Safety, Precision, Wiring, Testing) alongside personalized qualitative improvement tips from their instructor."
    )

    # ==========================================
    # SECTION 8: TECHNICAL MAINTENANCE & FAQ
    # ==========================================
    h_s8 = doc.add_heading("Section 8: Module 6 — Technical Maintenance, Security & FAQ", level=1)
    h_s8.paragraph_format.space_before = Pt(18)
    h_s8.paragraph_format.space_after = Pt(6)

    doc.add_heading("8.1 Maintenance Mode & Emergency Deployment Bypass", level=2)
    doc.add_paragraph("During database migrations or system updates, administrators can activate maintenance mode:")
    doc.add_paragraph("php artisan down --render=\"errors::503\" --secret=\"greg-deploy-bypass-2026\"")
    doc.add_paragraph(
        "Administrators can bypass maintenance mode and inspect the portal by visiting: https://lms.gregicthub.com/?secret=greg-deploy-bypass-2026. "
        "To bring the platform back online, execute: php artisan up."
    )

    doc.add_heading("8.2 Security Headers & SSL Protocol", level=2)
    doc.add_paragraph(
        "All HTTP traffic is permanently redirected to HTTPS via Let's Encrypt SSL. Production headers enforce Strict-Transport-Security, "
        "X-Frame-Options: SAMEORIGIN (clickjacking defense), and X-Content-Type-Options: nosniff (MIME sniffing prevention)."
    )

    doc.add_heading("8.3 Frequently Asked Questions", level=2)
    faqs = [
        ["How do I convert students to teachers in bulk?", "In /admin/users, check the boxes next to the students, click 'Convert Role' in the Bulk Actions bar, select 'teacher', and confirm. All selected accounts update immediately."],
        ["Where can I find the Word version of this manual?", "Click the 'Download Word Guide (.docx)' button on the in-app User Guide page (/user-guide) or download directly from the portal repository."],
        ["Can inclusive accommodations compromise workshop safety?", "No. The Non-Negotiable Safety Preservation Check explicitly bars any accommodation from lowering PPE requirements, high-voltage safety rules, or machine guards."],
        ["What file formats are accepted for trainee submissions?", "Images (.jpg, .png), documents (.pdf, .docx, .xlsx), and zip archives up to 25 MB are accepted."],
        ["How do I inspect who made changes to a user account?", "Navigate to /admin/audit-logs to view the timestamped audit log, actor identity, IP address, and before/after parameter diff."]
    ]
    add_styled_table(doc, ["Frequently Asked Question", "Official System Answer & Procedure"], faqs, [2.5, 4.0])

    # Save document
    doc.save(output_path)
    print(f"Document successfully created at: {output_path}")

if __name__ == "__main__":
    out_dir = os.path.join(os.getcwd(), "public", "downloads")
    os.makedirs(out_dir, exist_ok=True)
    out_file = os.path.join(out_dir, "Greg_ICT_Hub_LMS_Complete_User_and_Admin_Guide.docx")
    build_word_document(out_file)
