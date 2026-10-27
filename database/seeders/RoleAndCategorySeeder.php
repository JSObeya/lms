<?php

namespace Database\Seeders;

use App\Models\TradeCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndCategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            'super_admin' => 'Full system access, settings, backups, audits',
            'lms_admin' => 'Manage courses, teachers, categories, enrollments, reports',
            'facilitator' => 'Monitor assigned teachers, review assessments, moderate scores',
            'teacher' => 'Create/manage owned courses, rubrics, assignments, grade submissions',
            'student' => 'Access enrolled courses, submit assignments, view released grades',
            'observer' => 'Read-only access to courses and assessments without editing',
        ];

        foreach ($roles as $roleName => $desc) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Base Permissions
        $permissions = [
            'view admin dashboard',
            'manage users',
            'manage categories',
            'manage all courses',
            'create course',
            'manage own course',
            'manage rubrics',
            'grade submissions',
            'export grades',
            'submit assignment',
            'view grades',
            'view facilitator dashboard',
            'verify practice wizard',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $superAdmin = Role::findByName('super_admin');
        $superAdmin->givePermissionTo(Permission::all());

        $lmsAdmin = Role::findByName('lms_admin');
        $lmsAdmin->givePermissionTo([
            'view admin dashboard',
            'manage users',
            'manage categories',
            'manage all courses',
            'export grades',
            'verify practice wizard',
        ]);

        $facilitator = Role::findByName('facilitator');
        $facilitator->givePermissionTo([
            'view facilitator dashboard',
            'export grades',
            'verify practice wizard',
        ]);

        $teacher = Role::findByName('teacher');
        $teacher->givePermissionTo([
            'create course',
            'manage own course',
            'manage rubrics',
            'grade submissions',
            'export grades',
        ]);

        $student = Role::findByName('student');
        $student->givePermissionTo([
            'submit assignment',
            'view grades',
        ]);

        // 3. Trade Categories (8 IDEAS-TVET + 6 Professional/Digital)
        $categories = [
            // 8 TVET Trades
            ['name' => 'Solar PV Installation and Maintenance', 'is_tvet_trade' => true, 'icon' => 'Sun', 'order_index' => 1, 'description' => 'Solar panel mounting, inverter wiring, charge controllers, battery banks and troubleshooting.'],
            ['name' => 'Electrical Works', 'is_tvet_trade' => true, 'icon' => 'Zap', 'order_index' => 2, 'description' => 'Domestic and industrial electrical installations, conduits, switchgear and safety protocols.'],
            ['name' => 'Welding and Fabrication', 'is_tvet_trade' => true, 'icon' => 'Flame', 'order_index' => 3, 'description' => 'Arc welding, MIG/TIG welding, metal joint preparation, cutting and workshop safety.'],
            ['name' => 'Agriculture', 'is_tvet_trade' => true, 'icon' => 'Sprout', 'order_index' => 4, 'description' => 'Crop production, modern irrigation, poultry, aquaculture, and agribusiness value chains.'],
            ['name' => 'Catering Services', 'is_tvet_trade' => true, 'icon' => 'Utensils', 'order_index' => 5, 'description' => 'Food preparation, culinary arts, pastry making, hygiene standards and banquet management.'],
            ['name' => 'Carpentry and Joinery', 'is_tvet_trade' => true, 'icon' => 'Hammer', 'order_index' => 6, 'description' => 'Roofing trusses, timber joinery, furniture making, sanding and structural woodwork.'],
            ['name' => 'Automobile Repairs', 'is_tvet_trade' => true, 'icon' => 'Wrench', 'order_index' => 7, 'description' => 'Internal combustion engines, automotive diagnostics, braking systems and mechatronics.'],
            ['name' => 'Garment Making', 'is_tvet_trade' => true, 'icon' => 'Scissors', 'order_index' => 8, 'description' => 'Pattern drafting, tailoring, fashion design, industrial sewing machine operation.'],

            // Digital & Professional Categories
            ['name' => 'ICT and Digital Literacy', 'is_tvet_trade' => false, 'icon' => 'Laptop', 'order_index' => 9, 'description' => 'Foundational computing, office productivity, Internet navigation and digital safety.'],
            ['name' => 'Cybersecurity', 'is_tvet_trade' => false, 'icon' => 'ShieldCheck', 'order_index' => 10, 'description' => 'Network defense, threat analysis, ethical hacking and information security standards.'],
            ['name' => 'Generative AI', 'is_tvet_trade' => false, 'icon' => 'Cpu', 'order_index' => 11, 'description' => 'AI prompting, large language models, automation workflows and ethical AI applications.'],
            ['name' => 'Web Development', 'is_tvet_trade' => false, 'icon' => 'Code', 'order_index' => 12, 'description' => 'Modern frontend and backend development with Laravel, Vue, JavaScript and MySQL.'],
            ['name' => 'General Technical Education', 'is_tvet_trade' => false, 'icon' => 'BookOpen', 'order_index' => 13, 'description' => 'Technical drawing, workshop mathematics, applied science and occupational safety.'],
            ['name' => 'Instructor Professional Development', 'is_tvet_trade' => false, 'icon' => 'GraduationCap', 'order_index' => 14, 'description' => 'Pedagogy, TVET assessment methodologies, digital LMS management and curriculum design.'],
        ];

        foreach ($categories as $cat) {
            TradeCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'is_tvet_trade' => $cat['is_tvet_trade'],
                    'icon' => $cat['icon'],
                    'order_index' => $cat['order_index'],
                    'description' => $cat['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
