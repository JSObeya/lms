<?php

namespace Database\Seeders;

use App\Models\TeacherDocument;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('super_admin')->first() ?? User::role('lms_admin')->first() ?? User::first();

        if (! $admin) {
            return;
        }

        $documents = [
            [
                'title' => 'IDEAS-TVET Competency-Based Training & Lesson Delivery Framework 2026',
                'category' => 'Curriculum & Standards',
                'description' => 'Comprehensive national policy document on competency-based TVET instructional design, practical lesson breakdown, learning outcomes, and workplace safety compliance for technical trade instructors.',
                'file_name' => 'IDEAS_TVET_Competency_Training_Framework_2026.pdf',
                'target_audience' => 'teachers',
            ],
            [
                'title' => 'Day 8–12 TVET Pedagogy Studio & Practical Workshop Safety Manual',
                'category' => 'Workshop & Safety',
                'description' => 'Official technical workshop standard operational procedures (SOP), PPE requirements, electrical/mechanical safety checklists, and hazardous equipment management for technical training centers.',
                'file_name' => 'TVET_Workshop_Safety_and_SOP_Manual.pdf',
                'target_audience' => 'teachers',
            ],
            [
                'title' => 'Universal Design for Learning (UDL) & Inclusive Lesson Planning Guide',
                'category' => 'Lesson Planning',
                'description' => 'Detailed pedagogical guide outlining multiple means of representation, engagement, and expression for differentiated TVET classrooms and inclusive workshop accommodations.',
                'file_name' => 'UDL_Inclusive_Lesson_Planning_Guide.pdf',
                'target_audience' => 'teachers',
            ],
            [
                'title' => 'National TVET Assessment & Rubric Scoring Standards Manual',
                'category' => 'Assessment & Rubrics',
                'description' => 'Standardized 4-level rubric scoring matrix, psychomotor task criteria, objective performance evidence verification, and facilitator moderation rubrics.',
                'file_name' => 'National_TVET_Rubric_Scoring_Manual.pdf',
                'target_audience' => 'teachers',
            ],
            [
                'title' => 'Technical Trade Lesson Plan & Practical Project Planner Template',
                'category' => 'Teaching Aids & Worksheets',
                'description' => 'Standard printable lesson template with sections for Measurable Objectives, Equipment Checklist, Demonstration Steps, Group Activity, and Formative Assessment.',
                'file_name' => 'Technical_Trade_Lesson_Plan_Template.pdf',
                'target_audience' => 'teachers',
            ],
            [
                'title' => 'Facilitator & Lead Instructor Mentorship Directives',
                'category' => 'Policies & Guidelines',
                'description' => 'Guidelines for lead instructors and facilitators evaluating teacher lesson plans, peer review protocols, and digital portfolio verification.',
                'file_name' => 'Facilitator_Mentorship_Directives_2026.pdf',
                'target_audience' => 'all',
            ],
        ];

        // Ensure storage directory exists
        Storage::disk('local')->makeDirectory('teacher_documents');

        foreach ($documents as $doc) {
            $existing = TeacherDocument::where('file_name', $doc['file_name'])->first();
            if ($existing) {
                continue;
            }

            $storageFileName = Str::random(40).'.pdf';
            $filePath = 'teacher_documents/'.$storageFileName;

            $pdfContent = $this->generateSamplePdfContent($doc['title']);
            Storage::disk('local')->put($filePath, $pdfContent);

            TeacherDocument::create([
                'uploaded_by' => $admin->id,
                'title' => $doc['title'],
                'category' => $doc['category'],
                'description' => $doc['description'],
                'target_audience' => $doc['target_audience'],
                'file_path' => $filePath,
                'file_name' => $doc['file_name'],
                'file_size' => strlen($pdfContent),
                'mime_type' => 'application/pdf',
                'file_extension' => 'pdf',
                'download_count' => rand(5, 42),
                'is_published' => true,
            ]);
        }
    }

    private function generateSamplePdfContent(string $title): string
    {
        $escapedTitle = addcslashes($title, '()\\');
        $stream = "BT\n/F1 16 Tf\n50 720 Td\n(IDEAS-TVET Technical Learning Management System) Tj\n/F1 12 Tf\n0 -30 Td\n({$escapedTitle}) Tj\n/F1 10 Tf\n0 -25 Td\n(Official Institutional Document - Greg ICT Hub LMS) Tj\n0 -20 Td\n(Downloaded for Offline Technical Reference and Study) Tj\nET";
        $length = strlen($stream);

        return "%PDF-1.4\n"
            ."1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n"
            ."2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n"
            ."3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n"
            ."4 0 obj << /Length {$length} >>\n"
            ."stream\n"
            .$stream."\n"
            ."endstream\n"
            ."endobj\n"
            ."5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n"
            ."xref\n"
            ."0 6\n"
            ."0000000000 65535 f \n"
            ."0000000009 00000 n \n"
            ."0000000058 00000 n \n"
            ."0000000115 00000 n \n"
            ."0000000244 00000 n \n"
            ."0000000350 00000 n \n"
            ."trailer << /Size 6 /Root 1 0 R >>\n"
            ."startxref\n"
            ."450\n"
            ."%%EOF\n";
    }
}
