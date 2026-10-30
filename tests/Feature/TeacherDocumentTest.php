<?php

namespace Tests\Feature;

use App\Models\TeacherDocument;
use App\Models\User;
use Database\Seeders\RoleAndCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndCategorySeeder::class);
        Storage::fake('local');
    }

    public function test_unauthenticated_user_cannot_access_documents(): void
    {
        $response = $this->get('/teacher-documents');
        $response->assertRedirect('/login');
    }

    public function test_teacher_can_view_document_repository_and_published_documents(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $doc = TeacherDocument::factory()->create([
            'title' => 'National TVET Safety Guide 2026',
            'is_published' => true,
            'target_audience' => 'teachers',
        ]);

        $unpublishedDoc = TeacherDocument::factory()->create([
            'title' => 'Secret Admin Notes',
            'is_published' => false,
            'target_audience' => 'teachers',
        ]);

        $response = $this->actingAs($teacher)->get('/teacher-documents');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Teacher/Documents/Index')
            ->has('documents.data', 1)
            ->where('documents.data.0.title', 'National TVET Safety Guide 2026')
            ->where('canManage', false)
        );
    }

    public function test_teacher_can_download_pdf_document_to_individual_system(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $fakeContent = '%PDF-1.4 sample pdf content for offline download';
        $storagePath = 'teacher_documents/curriculum_guideline.pdf';
        Storage::disk('local')->put($storagePath, $fakeContent);

        $doc = TeacherDocument::factory()->create([
            'title' => 'Curriculum Guideline',
            'file_name' => 'Curriculum_Guideline_2026.pdf',
            'file_path' => $storagePath,
            'file_size' => strlen($fakeContent),
            'mime_type' => 'application/pdf',
            'file_extension' => 'pdf',
            'is_published' => true,
            'download_count' => 0,
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher-documents.download', $doc->id));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Curriculum_Guideline_2026.pdf');

        // Check download count incremented
        $this->assertEquals(1, $doc->fresh()->download_count);

        // Check audit log recorded
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $teacher->id,
            'action' => 'teacher_document_downloaded',
            'entity_id' => $doc->id,
        ]);
    }

    public function test_teacher_can_preview_pdf_document_inline(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $fakeContent = '%PDF-1.4 preview content';
        $storagePath = 'teacher_documents/preview_test.pdf';
        Storage::disk('local')->put($storagePath, $fakeContent);

        $doc = TeacherDocument::factory()->create([
            'title' => 'Preview Test',
            'file_name' => 'Preview_Test.pdf',
            'file_path' => $storagePath,
            'file_size' => strlen($fakeContent),
            'mime_type' => 'application/pdf',
            'is_published' => true,
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher-documents.preview', $doc->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    public function test_admin_can_upload_new_document_for_teachers(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('lms_admin');

        $file = UploadedFile::fake()->create('TVET_Instructional_Design_Framework.pdf', 2500, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('teacher-documents.store'), [
            'title' => 'TVET Instructional Design Framework',
            'category' => 'Curriculum & Standards',
            'description' => 'Official instructional guide for all technical training institutions.',
            'target_audience' => 'teachers',
            'is_published' => true,
            'document_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('teacher_documents', [
            'title' => 'TVET Instructional Design Framework',
            'category' => 'Curriculum & Standards',
            'file_name' => 'TVET_Instructional_Design_Framework.pdf',
            'file_extension' => 'pdf',
            'is_published' => true,
            'uploaded_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'teacher_document_uploaded',
        ]);
    }

    public function test_teacher_cannot_upload_document(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $file = UploadedFile::fake()->create('Unauthorized.pdf', 100, 'application/pdf');

        $response = $this->actingAs($teacher)->post(route('teacher-documents.store'), [
            'title' => 'Unauthorized Document',
            'category' => 'General',
            'target_audience' => 'teachers',
            'document_file' => $file,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_document(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $doc = TeacherDocument::factory()->create([
            'title' => 'Old Title',
            'category' => 'General',
        ]);

        $response = $this->actingAs($admin)->put(route('teacher-documents.update', $doc->id), [
            'title' => 'Updated Document Title 2026',
            'category' => 'Workshop & Safety',
            'description' => 'Updated description content',
            'target_audience' => 'all',
            'is_published' => true,
        ]);

        $response->assertRedirect();
        $this->assertEquals('Updated Document Title 2026', $doc->fresh()->title);
        $this->assertEquals('Workshop & Safety', $doc->fresh()->category);
    }

    public function test_admin_can_delete_document(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $storagePath = 'teacher_documents/to_delete.pdf';
        Storage::disk('local')->put($storagePath, '%PDF content');

        $doc = TeacherDocument::factory()->create([
            'file_path' => $storagePath,
        ]);

        $response = $this->actingAs($admin)->delete(route('teacher-documents.destroy', $doc->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('teacher_documents', ['id' => $doc->id]);
    }

    public function test_admin_can_bulk_upload_multiple_documents_up_to_60mb(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('lms_admin');

        $file1 = UploadedFile::fake()->create('Curriculum_Module_1.pdf', 35000, 'application/pdf'); // 35MB
        $file2 = UploadedFile::fake()->create('Curriculum_Module_2.docx', 42000, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'); // 42MB

        $response = $this->actingAs($admin)->post(route('teacher-documents.bulk-store'), [
            'category' => 'Curriculum & Standards',
            'target_audience' => 'teachers',
            'is_published' => true,
            'documents' => [$file1, $file2],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('teacher_documents', [
            'file_name' => 'Curriculum_Module_1.pdf',
            'category' => 'Curriculum & Standards',
            'file_extension' => 'pdf',
            'is_published' => true,
            'uploaded_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('teacher_documents', [
            'file_name' => 'Curriculum_Module_2.docx',
            'category' => 'Curriculum & Standards',
            'file_extension' => 'docx',
            'is_published' => true,
            'uploaded_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'teacher_documents_bulk_uploaded',
        ]);
    }

    public function test_teacher_cannot_bulk_upload_documents(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $file = UploadedFile::fake()->create('Unauthorized_Bulk.pdf', 500, 'application/pdf');

        $response = $this->actingAs($teacher)->post(route('teacher-documents.bulk-store'), [
            'category' => 'General',
            'target_audience' => 'teachers',
            'documents' => [$file],
        ]);

        $response->assertStatus(403);
    }
}
