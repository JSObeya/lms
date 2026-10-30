<?php

namespace Database\Factories;

use App\Models\TeacherDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeacherDocument>
 */
class TeacherDocumentFactory extends Factory
{
    protected $model = TeacherDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Curriculum & Standards',
            'Lesson Planning',
            'Policies & Guidelines',
            'Teaching Aids & Worksheets',
            'Assessment & Rubrics',
            'General',
        ];

        return [
            'uploaded_by' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(2),
            'category' => fake()->randomElement($categories),
            'file_path' => 'documents/sample_document.pdf',
            'file_name' => 'tvet_guide_'.fake()->slug().'.pdf',
            'file_size' => fake()->numberBetween(150000, 5000000),
            'mime_type' => 'application/pdf',
            'file_extension' => 'pdf',
            'target_audience' => 'teachers',
            'download_count' => fake()->numberBetween(0, 50),
            'is_published' => true,
        ];
    }
}
