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

def set_cell_margins(cell, top=120, bottom=120, left=160, right=160):
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

def add_callout(doc, text, title="NOTE / BEST PRACTICE", border_color="0D9488", bg_color="F0FDFA"):
    """Adds an executive callout box with a colored left border and light background."""
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    table.columns[0].width = Inches(6.8)

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
    r_title.font.size = Pt(9.5)
    r_title.font.name = 'Segoe UI'

    if border_color == "E11D48":
        r_title.font.color.rgb = RGBColor(190, 18, 60)
    elif border_color == "0D9488":
        r_title.font.color.rgb = RGBColor(15, 118, 110)
    elif border_color == "D97706":
        r_title.font.color.rgb = RGBColor(180, 83, 9)
    else:
        r_title.font.color.rgb = RGBColor(30, 41, 59)

    r_body = p.add_run(text)
    r_body.font.size = Pt(9.5)
    r_body.font.name = 'Segoe UI'
    r_body.font.color.rgb = RGBColor(51, 65, 85)

    p_after = doc.add_paragraph()
    p_after.paragraph_format.space_before = Pt(0)
    p_after.paragraph_format.space_after = Pt(6)

def add_styled_table(doc, headers, rows, col_widths=None):
    """Creates a styled table with dark header and alternating row colors."""
    table = doc.add_table(rows=len(rows) + 1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False

    # Header row
    for i, title in enumerate(headers):
        cell = table.cell(0, i)
        set_cell_background(cell, "0F172A") # Navy slate
        set_cell_margins(cell, top=120, bottom=120, left=140, right=140)
        p = cell.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        p.paragraph_format.space_before = Pt(2)
        p.paragraph_format.space_after = Pt(2)
        r = p.add_run(title)
        r.bold = True
        r.font.size = Pt(9)
        r.font.name = 'Segoe UI'
        r.font.color.rgb = RGBColor(255, 255, 255)

    # Data rows
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
            r.font.size = Pt(8.5)
            r.font.name = 'Segoe UI'
            r.font.color.rgb = RGBColor(30, 41, 59)

    if col_widths:
        for row in table.rows:
            for idx, width in enumerate(col_widths):
                row.cells[idx].width = Inches(width)

    p_after = doc.add_paragraph()
    p_after.paragraph_format.space_before = Pt(0)
    p_after.paragraph_format.space_after = Pt(6)

def build_day8_guide(output_path):
    doc = docx.Document()

    # Margins (0.75 in)
    for section in doc.sections:
        section.top_margin = Inches(0.75)
        section.bottom_margin = Inches(0.75)
        section.left_margin = Inches(0.75)
        section.right_margin = Inches(0.75)

        # Header
        header = section.header
        hp = header.paragraphs[0]
        hp.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        hrun = hp.add_run("IDEAS-TVET Day 8 Topic Guide: AI-Assisted Lesson Prep, Simulations & Assessment")
        hrun.font.name = 'Segoe UI'
        hrun.font.size = Pt(8.5)
        hrun.font.color.rgb = RGBColor(148, 163, 184)

        # Footer
        footer = section.footer
        fp = footer.paragraphs[0]
        fp.alignment = WD_ALIGN_PARAGRAPH.CENTER
        frun = fp.add_run("© 2026 Greg & Co. ICT Ltd • Benue State IDEAS-TVET Project • https://lms.gregicthub.com")
        frun.font.name = 'Segoe UI'
        frun.font.size = Pt(8)
        frun.font.color.rgb = RGBColor(148, 163, 184)

    # Document Styles
    normal_style = doc.styles['Normal']
    normal_style.font.name = 'Segoe UI'
    normal_style.font.size = Pt(10)
    normal_style.font.color.rgb = RGBColor(30, 41, 59)
    normal_style.paragraph_format.line_spacing = 1.15
    normal_style.paragraph_format.space_after = Pt(6)

    # Cover / Header Box
    head_table = doc.add_table(rows=1, cols=1)
    head_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    head_table.autofit = False
    head_table.columns[0].width = Inches(6.8)

    hcell = head_table.cell(0, 0)
    set_cell_background(hcell, "0F172A") # Dark Navy
    set_cell_margins(hcell, top=260, bottom=260, left=300, right=300)

    hp1 = hcell.paragraphs[0]
    hp1.alignment = WD_ALIGN_PARAGRAPH.LEFT
    hr1 = hp1.add_run("BENUE STATE IDEAS-TVET CAPACITY BUILDING INITIATIVE\nGREG & CO. ICT HUB LEARNING MANAGEMENT SYSTEM (LMS)")
    hr1.font.name = 'Segoe UI'
    hr1.font.size = Pt(9.5)
    hr1.font.bold = True
    hr1.font.color.rgb = RGBColor(45, 212, 191) # Teal

    hp2 = hcell.add_paragraph()
    hp2.paragraph_format.space_before = Pt(8)
    hp2.paragraph_format.space_after = Pt(4)
    hr2 = hp2.add_run("DAY 8 INSTRUCTIONAL RESOURCE GUIDE")
    hr2.font.name = 'Segoe UI'
    hr2.font.size = Pt(18)
    hr2.font.bold = True
    hr2.font.color.rgb = RGBColor(255, 255, 255)

    hp3 = hcell.add_paragraph()
    hp3.paragraph_format.space_before = Pt(2)
    hp3.paragraph_format.space_after = Pt(8)
    hr3 = hp3.add_run("AI-Assisted Lesson Preparation, Simulations, Digital Assessment and Responsible AI")
    hr3.font.name = 'Segoe UI'
    hr3.font.size = Pt(13)
    hr3.font.bold = True
    hr3.font.color.rgb = RGBColor(251, 191, 36) # Amber 400

    hp4 = hcell.add_paragraph()
    hr4 = hp4.add_run(
        "A Competency-Based Practical Guide for TVET Instructors: Integrating Artificial Intelligence, "
        "Workshop Digital Twin Simulations, Criterion-Referenced Rubrics, and Ethical Safety Guardrails.\n"
        "Official Course Resource for Upload to Greg ICT Hub LMS (lms.gregicthub.com)"
    )
    hr4.font.name = 'Segoe UI'
    hr4.font.size = Pt(9.5)
    hr4.font.color.rgb = RGBColor(226, 232, 240)

    doc.add_paragraph() # Spacing

    # Executive Metadata Table
    meta_headers = ["Metadata Field", "Specification / Value"]
    meta_rows = [
        ["Target Module / Topic", "Day 8: AI-Assisted Lesson Preparation, Simulations, Digital Assessment and Responsible AI"],
        ["Target Audience", "200 TVET Technical Teachers, Vocational Instructors, Master Trainers & Workshop Facilitators"],
        ["Target Trade Sectors", "Electrical & Solar PV, Automotive/Mechanical, Building Construction/Welding, Computer Hardware & Networking, Garment & Fashion, Agricultural Mechanization"],
        ["Curriculum Alignment", "NBTE Modular Curriculum, National Occupational Standards (NOS), World Bank IDEAS Project TVET Competency Matrix"],
        ["LMS Implementation", "Fulfills Step 2 (Topic Labeling) & Step 4 (Resource Upload) of the Day 8 TVET Practical Wizard"],
        ["Authoring Body", "Greg & Co. ICT Hub Technical Pedagogy Division in collaboration with Benue State IDEAS-TVET Unit"],
        ["Date / Version", "October 2026 | Version 2.4 (Executive Release)"],
    ]
    add_styled_table(doc, meta_headers, meta_rows, col_widths=[2.2, 4.6])

    # Section 1
    doc.add_heading("1. Executive Summary & Instructional Purpose", level=1)
    p = doc.add_paragraph(
        "In the contemporary Technical and Vocational Education and Training (TVET) landscape, digital transformation "
        "is no longer an elective enhancement but an operational necessity. Under the Benue State IDEAS-TVET project, "
        "technical instructors are empowered to leverage artificial intelligence, interactive simulation engines, "
        "and digital assessment rubrics to elevate practical vocational training across 10 technical colleges and vocational centers."
    )
    doc.add_paragraph(
        "This resource guide provides the instructional framework, prompt templates, simulation methodologies, "
        "and safety guardrails required to prepare and teach the Day 8 topic: 'AI-Assisted Lesson Preparation, "
        "Simulations, Digital Assessment and Responsible AI'. Once reviewed, this document serves as the official "
        "technical reference to be uploaded directly into your course section on Greg ICT Hub LMS (https://lms.gregicthub.com), "
        "satisfying the verified requirements of Step 2 and Step 4 of the Day 8 TVET Practical Wizard."
    )

    add_callout(
        doc,
        "Day 8 represents the foundational bridge between digital curriculum authoring on the LMS and advanced "
        "competency-based instructional design (Days 9 & 10). Trainees must master using AI as an accelerator, "
        "not a replacement for technical craftsmanship.",
        title="CORE TVET PEDAGOGICAL PILLAR",
        border_color="0D9488",
        bg_color="F0FDFA"
    )

    # Section 2
    doc.add_heading("2. Measurable Learning Objectives (The ABCD Model)", level=1)
    doc.add_paragraph(
        "Competency-based TVET requires that every lesson objective is strictly measurable, observable, and directly "
        "linked to occupational standards. Vague terms such as 'understand', 'know', or 'appreciate' are prohibited. "
        "Instructors must structure learning objectives using the rigorous ABCD Model:"
    )

    abcd_headers = ["Component", "Definition", "Technical TVET Application"]
    abcd_rows = [
        ["A — Audience", "Who is executing the competency?", "The TVET trainee, apprentice, or technical student."],
        ["B — Behavior", "What measurable action verb will be observed?", "Assemble, calibrate, simulate, troubleshoot, solder, wire, diagnose, fabricate, test, or calculate (Bloom's Psychomotor & Cognitive verbs)."],
        ["C — Condition", "Under what tools, equipment, schematics, or constraints?", "Using a digital circuit simulator, multimeter, wiring diagram, PPE gear, or workshop test bench without instructor intervention."],
        ["D — Degree", "What is the acceptable standard of performance?", "Within 30 minutes, achieving 100% safety compliance, with zero short-circuits, or meeting manufacturer torque specifications ±5%."],
    ]
    add_styled_table(doc, abcd_headers, abcd_rows, col_widths=[1.4, 2.4, 3.0])

    doc.add_heading("2.1 Exemplar Learning Objectives for the Day 8 Topic", level=2)
    doc.add_paragraph("By the conclusion of this instructional unit, trainee instructors will be able to:")

    doc.add_paragraph(
        "1. Prompt an AI language model using the RTCC (Role, Task, Context, Constraints) framework to generate a 4-step "
        "practical TVET demonstration plan aligned with NBTE standards within 15 minutes.",
        style='List Bullet'
    )
    doc.add_paragraph(
        "2. Configure an interactive virtual workshop simulation (e.g., Tinkercad, PhET, Falstad, or Electude) that demonstrates "
        "a critical trade principle with zero material waste and complete hazard mitigation.",
        style='List Bullet'
    )
    doc.add_paragraph(
        "3. Design a 4-tier, 100-point criterion-referenced digital assessment rubric incorporating safety compliance, "
        "tool handling, craftsmanship, and diagnostic testing.",
        style='List Bullet'
    )
    doc.add_paragraph(
        "4. Apply the Human-in-the-Loop (HITL) protocol to audit AI outputs for hallucinations, factual inaccuracies, "
        "and Nigerian electrical/industrial safety non-negotiables before student dissemination.",
        style='List Bullet'
    )

    # Section 3
    doc.add_heading("3. AI-Assisted Lesson Preparation & TVET Prompt Engineering", level=1)
    doc.add_paragraph(
        "Artificial Intelligence drastically reduces the administrative burden of TVET lesson planning—allowing instructors "
        "to dedicate maximum time to hands-on workshop coaching. However, generic prompts produce generic, ungrounded responses. "
        "TVET educators must employ the RTCC Prompt Engineering Framework:"
    )

    rtcc_headers = ["Framework Element", "Purpose in TVET", "Example Prompt Formulation"]
    rtcc_rows = [
        ["Role (R)", "Assigns domain authority and pedagogical role to the AI.", "\"Act as a Senior NBTE-certified TVET Master Instructor and Vocational Curriculum Specialist in Electrical & Solar Installation...\""],
        ["Task (T)", "Specifies the exact deliverable to produce.", "\"Draft a structured 2-hour practical workshop lesson plan and Bill of Materials (BOM)...\""],
        ["Context (C)", "Sets the operational setting, student level, and local environment.", "\"For 2nd-year National Technical Certificate (NTC) trainees in Makurdi, Benue State, learning off-grid solar inverter installation with limited workshop bench equipment...\""],
        ["Constraints (C)", "Enforces safety codes, pedagogical models, and formatting boundaries.", "\"Use the ABCD objective model, specify Nigerian Electrical Code (NEC) safety standards, include a 15-minute equipment rotation matrix, and highlight mandatory PPE...\""],
    ]
    add_styled_table(doc, rtcc_headers, rtcc_rows, col_widths=[1.5, 2.3, 3.0])

    doc.add_heading("3.1 Master Prompt Template for TVET Lesson Plans", level=2)
    doc.add_paragraph(
        "Copy and adapt the following structured prompt into your AI model (Claude, ChatGPT, or DeepSeek) to generate "
        "comprehensive, workshop-ready lesson outlines:"
    )

    add_callout(
        doc,
        "\"You are a certified TVET master instructor specializing in [Insert Trade: e.g., Solar Photovoltaic Installation / Automotive Diagnostics]. "
        "Create a competency-based practical lesson plan for the topic: '[Insert Topic: e.g., Off-Grid Inverter Sizing and Safe Terminal Connections]'.\n\n"
        "Requirements:\n"
        "1. Write 3 measurable ABCD learning objectives using Bloom's psychomotor verbs.\n"
        "2. Provide a 4-Step Demonstration Guide (Instructor Shows, Instructor Explains, Trainee Repeats, Trainee Explains).\n"
        "3. Provide a Workshop Bill of Materials (BOM) including exact tool specifications and personal protective equipment (PPE).\n"
        "4. Include 2 common student fault/hazard scenarios and how the instructor should safely guide troubleshooting.\n"
        "5. Output a 4-tier grading rubric (Safety, Tool Handling, Workmanship, Testing) totaling 100 points.\n"
        "Format the output using clear markdown headings and structured tables.\"",
        title="COPY-PASTE TVET LESSON PLAN PROMPT",
        border_color="D97706",
        bg_color="FFFBEB"
    )

    # Section 4
    doc.add_heading("4. Workshop Virtual Simulations & Digital Twin Tools", level=1)
    doc.add_paragraph(
        "A major challenge in TVET colleges across sub-Saharan Africa is the high cost of consumable raw materials "
        "(copper cables, welding electrodes, refrigerant gases) and equipment wear. Virtual simulations serve as "
        "'digital twins' of workshop benches, allowing students to experiment, make catastrophic mistakes safely, "
        "and build muscle memory prior to touching energized or expensive physical hardware."
    )

    sim_headers = ["Occupational Trade", "Recommended Simulation Tool", "Pedagogical Advantage & Workshop Application"]
    sim_rows = [
        ["Electrical & Solar Installation", "Tinkercad Circuits, PhET (Univ. of Colorado), Falstad Circuit Simulator", "Interactive breadboarding, multimeter probe testing, solar PV series/parallel shading simulations, zero risk of electrical shock or blown fuses."],
        ["Computer Hardware & Networking", "Cisco Packet Tracer, VirtualBox / Proxmox, GNS3", "Complete LAN/WAN topology configuration, virtual router/switch CLI practice, DHCP/VLAN troubleshooting before physical patch cabling."],
        ["Automotive & Mechanical", "Electude Virtual Engine Trainer, SimScale, FreeCAD", "Virtual disassembly of four-stroke internal combustion engines, electronic fuel injection (EFI) sensor diagnostics, fluid mechanics simulations."],
        ["Welding & Fabrication", "AugmentedArc, Soldamatic Virtual Welding (or 3D CAD)", "Real-time feedback on travel speed, torch angle, and arc length without burning electrodes or releasing toxic fumes."],
        ["Building Construction", "SketchUp for Schools, Sweet Home 3D, FreeCAD BIM", "3D architectural floor plans, timber roof truss calculation, brick bonding layout visualization."],
    ]
    add_styled_table(doc, sim_headers, sim_rows, col_widths=[1.8, 2.3, 2.7])

    doc.add_heading("4.1 The 3-Stage Simulation Instructional Flow", level=2)
    doc.add_paragraph("To maximize transfer from screen to workshop bench, instructors must mandate this three-phase workflow:")
    doc.add_paragraph(
        "• Phase 1: Virtual Pre-Lab Simulation: Every trainee must independently construct the circuit or mechanical model "
        "in the simulator, verify voltage/current readings, and save a screenshot of success before entering the workshop.",
        style='List Bullet'
    )
    doc.add_paragraph(
        "• Phase 2: Supervised Physical Workshop Execution: Trainees replicate their verified simulation on the physical bench "
        "using real tools, wires, and components under full instructor observation and PPE compliance.",
        style='List Bullet'
    )
    doc.add_paragraph(
        "• Phase 3: Diagnostic Debrief & Reconciliation: Trainees compare real-world multimeter measurements against simulation values, "
        "accounting for component tolerances, wire resistance, and physical losses.",
        style='List Bullet'
    )

    # Section 5
    doc.add_heading("5. Digital Assessment & Criterion-Referenced Rubrics", level=1)
    doc.add_paragraph(
        "In traditional technical training, grading is often subjective ('looks neat: 7/10'). In competency-based TVET, "
        "assessment is strictly criterion-referenced. Greg ICT Hub LMS features a native 100-point rubric engine "
        "that breaks performance into transparent, weighted criteria across four performance levels:"
    )

    rubric_headers = ["Criterion (Weight)", "Beginning (0-49%)", "Developing (50-74%)", "Competent (75-89%)", "Exemplary (90-100%)"]
    rubric_rows = [
        [
            "1. Workshop Safety & PPE Compliance (20 pts)",
            "Disregards eye protection, improper footwear, poses safety hazard to peers.",
            "Wears PPE only when prompted; occasional minor safety infraction.",
            "Wears complete PPE; observes all workshop safety boundaries consistently.",
            "Proactively inspects work area, identifies peer hazards, demonstrates zero-tolerance safety mindset."
        ],
        [
            "2. Tool Selection & Handling (20 pts)",
            "Uses wrong tool for job (e.g. pliers for bolt); risks tool damage.",
            "Selects correct tool with prompting; awkward handling technique.",
            "Independently selects proper tools; uses tools safely and accurately.",
            "Selects optimal tools; calibrates and inspects tools prior to use; cleans and stores properly."
        ],
        [
            "3. Practical Workmanship & Accuracy (40 pts)",
            "Incomplete assembly, reversed polarity, severe structural flaws.",
            "Assembled with guidance; functional but neatness and tolerances marginal.",
            "Fully assembled to schematic; meets all dimensional and electrical tolerances.",
            "Flawless craftmanship, optimal routing, industrial-grade terminations, zero defects."
        ],
        [
            "4. Testing, Troubleshooting & Housekeeping (20 pts)",
            "Fails to test before energizing; leaves tools scattered; messy workspace.",
            "Tests only with instructor prompting; struggles to locate basic faults; partial cleanup.",
            "Systematically tests circuit/assembly; correctly diagnoses injected fault; cleans bench.",
            "Expert diagnostic methodology; documents test readings accurately; leaves pristine 5S workshop."
        ],
    ]
    add_styled_table(doc, rubric_headers, rubric_rows, col_widths=[1.5, 1.3, 1.3, 1.3, 1.4])

    add_callout(
        doc,
        "Criterion 1 (Safety & PPE) is a mandatory 'gatekeeper'. If a student commits a severe safety violation "
        "(such as short-circuiting live mains or removing safety goggles at a grinder), their practical assessment "
        "is immediately halted until safety remediation is completed.",
        title="CRITICAL SAFETY GATEKEEPER RULE",
        border_color="E11D48",
        bg_color="FFF1F2"
    )

    # Section 6
    doc.add_heading("6. Responsible AI, Ethics, Academic Integrity & Workshop Safety Guardrails", level=1)
    doc.add_paragraph(
        "While Artificial Intelligence offers unprecedented acceleration, deploying it within technical trade education "
        "introduces severe ethical and physical risks that instructors must actively manage:"
    )

    doc.add_heading("6.1 The Human-in-the-Loop (HITL) Imperative", level=2)
    doc.add_paragraph(
        "AI models have zero physical awareness. They can generate syntactically convincing wiring diagrams that are "
        "catastrophically dangerous in physical reality (e.g., undersized battery cables that would catch fire, incorrect "
        "circuit breaker amperage, or inverted diode polarities). The TVET instructor bears full legal and professional "
        "responsibility for verifying all AI-generated technical materials against official manufacturer service manuals "
        "and the Nigerian Electrical Code (NEC) prior to sharing with trainees."
    )

    doc.add_heading("6.2 Data Privacy & Nigeria Data Protection Regulation (NDPR)", level=2)
    doc.add_paragraph(
        "Never input sensitive student data—such as real names, national identification numbers, phone numbers, or "
        "medical conditions—into public, consumer AI tools. When asking AI to generate personalized feedback or analyze "
        "student performance, always anonymize records (e.g., refer to 'Trainee A' or 'Sample Student 1')."
    )

    doc.add_heading("6.3 Academic Integrity vs. Technical Skill Acquisition", level=2)
    doc.add_paragraph(
        "In TVET, theoretical writing is subordinate to psychomotor competence. Students may utilize AI to assist in "
        "drafting technical lab reports, understanding complex schematics, or writing code. However, practical hands-on "
        "assessments must always be evaluated through direct, synchronous physical observation and real-time fault "
        "diagnosis checks where AI cannot fabricate manual dexterity."
    )

    # Section 7
    doc.add_heading("7. Step-by-Step LMS Upload & Day 8 Practical Wizard Guide", level=1)
    doc.add_paragraph(
        "To fulfill your Day 8 certification on the Greg ICT Hub LMS (https://lms.gregicthub.com), follow this exact "
        "operational procedure. All labels, button texts, and modal titles below match the exact interface of the LMS:"
    )

    doc.add_heading("7.1 Day 8 Practical Wizard Competency Mapping (10 Steps)", level=2)
    doc.add_paragraph("The table below maps the 10 steps in the Day 8 Wizard to their exact in-app actions:")

    steps_headers = ["Wizard Step # & Title", "Action Button in Wizard", "Expected Outcome & Verification"]
    steps_rows = [
        ["Step 1: Create a Practice Course", "\"Create Course Now\" or \"View Course Builder\"", "Course record exists with title, code, and trade category."],
        ["Step 2: Label a Topic / Section", "\"Manage Sections in Builder\"", "At least one section created in the course structure."],
        ["Step 3: Write a Measurable Learning Objective", "\"Add Objective in Builder\"", "Measurable ABCD objective saved with Bloom action verb."],
        ["Step 4: Add a PDF or Educational URL", "\"Attach Resource in Builder\" or \"Download Topic Guide (.docx)\"", "Technical reference guide/file attached to a course section."],
        ["Step 5: Create an Assignment", "\"Configure Assignment\"", "Practical assessment task created with instructions and marks."],
        ["Step 6: Configure Deadlines and Timezone", "\"Verify Deadlines\"", "Submission due date, cut-off date, and WAT timezone configured."],
        ["Step 7: Define a Grading Rubric", "\"Design Rubric\"", "100-point multi-level rubric linked to practical criteria."],
        ["Step 8: Preview the Learner View", "\"Preview as Student\"", "Student preview opened to inspect trainee perspective."],
        ["Step 9: Grade a Sample Submission", "\"Grade in Gradebook\"", "Synthetic practice learner evaluated with rubric criterion scores."],
        ["Step 10: Export the Grade Report", "\"Export CSV Grade Report\"", "Official CSV course grade report exported and downloaded."],
    ]
    add_styled_table(doc, steps_headers, steps_rows, col_widths=[2.1, 2.2, 2.5])

    doc.add_heading("7.2 Detailed Step-by-Step Action Walk-Through (Exact UI Labels)", level=2)

    doc.add_paragraph(
        "• Part A: Open the Course Builder:\n"
        "  1. Go to https://lms.gregicthub.com and log in with your teacher credentials.\n"
        "  2. Click \"Day 8 Wizard\" in the top navigation bar.\n"
        "  3. On \"Step 1: Create a Practice Course\", click \"View Course Builder\" (or click \"Courses\" in the top bar, then click your course title).",
        style='Normal'
    )

    doc.add_paragraph(
        "• Part B: Label Course Topic (Verifies Wizard Step 2):\n"
        "  1. In the top-right header of the Visual Course Builder, click the button labeled \"Add Topic / Section\" (or click \"Add First Section\" if no sections exist yet).\n"
        "  2. In the modal titled \"Add Course Section / Topic\", fill in:\n"
        "     - Topic Title *: Enter \"AI-Assisted Lesson Preparation, Simulations, Digital Assessment and Responsible AI\"\n"
        "     - Description / Overview: Enter \"Instructional unit covering generative AI lesson planning, workshop simulations, 100-point rubrics, and ethical safety guardrails.\"\n"
        "  3. Click \"Save Section\".\n"
        "  ➜ Outcome: Wizard Step 2 is now marked Completed with a green checkmark!",
        style='Normal'
    )

    doc.add_paragraph(
        "• Part C: Add a Measurable Learning Objective (Verifies Wizard Step 3):\n"
        "  1. Inside the newly created section card, click the button labeled \"Add Objective\" (with the Target icon).\n"
        "  2. In the modal titled \"Write Measurable Learning Objective\", enter:\n"
        "     - Condition *: e.g., \"Given an NBTE trade module descriptor and an AI language model\"\n"
        "     - Action Verb *: e.g., \"Formulate\"\n"
        "     - Bloom Level: Select \"Apply (Practical)\"\n"
        "     - Performance Standard *: e.g., \"generate a 4-step demonstration plan within 15 minutes with zero safety violations\"\n"
        "  3. Click \"Save Objective\".\n"
        "  ➜ Outcome: Wizard Step 3 is now marked Completed!",
        style='Normal'
    )

    doc.add_paragraph(
        "• Part D: Attach This Guide Document (Verifies Wizard Step 4):\n"
        "  1. Download this document using the \"Download Topic Guide (.docx)\" button in the Day 8 Wizard or from /day8-guide/download-word.\n"
        "  2. Inside your section card in the Course Builder, click the button labeled \"Add Resource\" (with the Paperclip icon).\n"
        "  3. In the modal titled \"Attach Learning Resource\", configure:\n"
        "     - Resource Type: Select \"Word Document / Guide (.docx)\" (or \"PDF Document (.pdf)\")\n"
        "     - Resource Title *: Enter \"Day 8 Instructional Guide: AI-Assisted Lesson Prep, Simulations & Assessment\"\n"
        "     - File Upload (PDF, DOCX, Images max 20MB) *: Click and choose your downloaded \"Day8_AI_Assisted_Lesson_Preparation_Guide.docx\" file.\n"
        "  4. Click the button labeled \"Upload & Attach\".\n"
        "  ➜ Outcome: Wizard Step 4 is now marked Completed with a green checkmark!",
        style='Normal'
    )

    doc.add_paragraph(
        "• Part E: Create Practical Assignment & Deadlines (Verifies Wizard Steps 5 & 6):\n"
        "  1. Inside your section card, click the button labeled \"Add Assignment\" (with the ClipboardList icon).\n"
        "  2. On the Create Assignment page, enter:\n"
        "     - Assignment Title *: \"Practical Simulation & Rubric Assessment Exercise\"\n"
        "     - Instructions & Practical Requirements *: Detail the workshop simulation task and submission guidelines.\n"
        "     - Maximum Score: 100 marks.\n"
        "     - Submission Due Date & Cut-Off Date: Set realistic future dates.\n"
        "     - Timezone: Africa/Lagos (West Africa Time).\n"
        "  3. Click \"Publish Assignment\".\n"
        "  ➜ Outcome: Wizard Steps 5 & 6 are now marked Completed!",
        style='Normal'
    )

    add_callout(
        doc,
        "Once Steps 1 through 6 are completed, proceed to Step 7 (\"Design Rubric\"), Step 8 (\"Preview as Student\"), "
        "Step 9 (\"Grade in Gradebook\" after generating a sample learner), and Step 10 (\"Export CSV Grade Report\"). "
        "Your Day 8 Practical Wizard progress will reach 100%, enabling your Lead Facilitator to review and approve your submission.",
        title="WIZARD 100% COMPLETION CHECKLIST",
        border_color="0D9488",
        bg_color="F0FDFA"
    )

    # Save document
    doc.save(output_path)
    print(f"Successfully generated Day 8 guide at: {output_path}")

if __name__ == '__main__':
    downloads_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'public', 'downloads')
    os.makedirs(downloads_dir, exist_ok=True)
    out_file = os.path.join(downloads_dir, 'Day8_AI_Assisted_Lesson_Preparation_Guide.docx')
    build_day8_guide(out_file)
