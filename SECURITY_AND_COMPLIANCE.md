# Greg ICT Hub LMS — Security & Compliance Architecture

**Organization**: Greg & Co. ICT Ltd, Makurdi, Benue State, Nigeria  
**Target Domain**: [https://lms.gregicthub.com](https://lms.gregicthub.com/)  
**Standard**: Nigeria Data Protection Act (NDPA 2023) & OWASP Top 10  

---

## 1. Compliance with Nigeria Data Protection Act (NDPA 2023)

Greg ICT Hub LMS handles educator and student personal data under the regulatory mandate of the Nigeria Data Protection Commission (NDPC):

1. **Lawful Basis & Purpose Limitation**:
   - Educator and student accounts are provisioned solely for technical vocational training, skills assessment, and certification.
   - Profile data is restricted to essential educational parameters: full name, institutional technical college, trade specialization, and institutional email.
2. **Data Minimization & Encryption**:
   - User passwords are encrypted using Bcrypt (work factor 12).
   - Session identifiers and CSRF tokens are encrypted in transit and at rest.
3. **Data Subject Rights**:
   - Every educator and student can inspect and update their profile through `GET /profile`.
   - Data export logs (`export_logs` table) maintain an immutable record of whenever a teacher or administrator exports gradebook data.

---

## 2. CSV Formula Injection (CSV Injection) Prevention

Exporting student gradebooks to CSV creates an attack vector where malicious student names or qualitative comments starting with spreadsheet formula triggers (`=`, `+`, `-`, `@`, tab, or carriage return) can execute arbitrary code on an administrator's machine when opened in Microsoft Excel or Google Sheets.

**Remediation Implemented in `GradingController.php`**:
```php
$sanitize = function ($value) {
    $value = (string) $value;
    if (in_array(substr($value, 0, 1), ['=', '+', '-', '@', "\t", "\r"])) {
        return "'" . $value;
    }
    return $value;
};
```
Every text column in the streamed CSV gradebook is sanitized through this closure, prepending a single quote `'` that forces spreadsheet engines to interpret the input strictly as literal text.

---

## 3. Strict Teacher Course & Submission Isolation

In a multi-teacher institutional LMS, privacy between instructors is paramount:
1. **Course Authorization (`CoursePolicy.php`)**:
   - Only the course creator or a Super/LMS Admin can edit course settings, reorder sections, add learning objectives, or delete content.
   - Teachers cannot view or edit courses authored by other teachers unless explicitly designated as co-instructors.
2. **Private Resource Streaming (`ResourceController.php`)**:
   - Direct web access to `storage/app/private/resources/` is barred. Files are assigned 40-character cryptographically random hashes upon upload.
   - The streaming download endpoint enforces that only the course instructor, enrolled students, or system administrators can retrieve files.
3. **Student Submission Protection (`AssignmentPolicy.php` & `SubmissionController.php`)**:
   - Students can only view and submit their own work.
   - Teachers can only evaluate submissions belonging to assignments within their own courses.

---

## 4. Audit Logging & Non-Repudiation

The LMS maintains an immutable `audit_logs` table tracking critical platform events:
- Course creation and updates (`course_created`, `course_updated`)
- Resource uploads and secure downloads (`resource_uploaded`, `resource_downloaded`)
- Assignment creation and student submissions (`assignment_created`, `assignment_submitted`)
- Rubric grading evaluations (`submission_graded`)
- Teacher account status modifications (`user_status_changed`)
- Bulk teacher CSV import executions (`teachers_bulk_imported`)

All audit records log the authenticated user ID, target model class, target primary key, client IP address, and JSON payload of modified state.
