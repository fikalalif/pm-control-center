<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\Task;
use App\Models\Milestone;
use App\Models\Risk;
use App\Models\Issue;
use App\Models\ChangeRequest;
use App\Models\Meeting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Permissions dulu
        $permissions = [
            // Project & Task (yang udah lu buat)
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'delete_tasks',

            // Tambahan Baru: Milestones, Risks, Issues, Change Requests
            'view_milestones',
            'create_milestones',
            'edit_milestones',
            'delete_milestones',
            'view_risks',
            'create_risks',
            'edit_risks',
            'delete_risks',
            'view_issues',
            'create_issues',
            'edit_issues',
            'delete_issues',
            'view_change_requests',
            'create_change_requests',
            'edit_change_requests',
            'delete_change_requests',

            // Tambahan Baru: Stakeholders (Clients, Vendors)
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            'view_vendors',
            'create_vendors',
            'edit_vendors',
            'delete_vendors',

            // Tambahan Baru: Activity & Team
            'view_meetings',
            'create_meetings',
            'edit_meetings',
            'delete_meetings',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles' // Khusus Role & Access biasanya dibikin 1 aja biar simpel
        ];

        // ... (sisa kode looping Permission::firstOrCreate) ...

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Buat Roles berdasarkan spesifikasi PRD
        $roles = [
            'Admin',
            'Project Manager',
            'Project Member',
            'Management'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Buat User dummy dan langsung tembak Role pakai cara Spatie
        $admin = User::firstOrCreate(
            ['email' => 'admin@hetra.com'],
            [
                'name' => 'Admin System',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $admin->update(['phone' => '081234567890']);
        $admin->assignRole('Admin');

        $pm = User::firstOrCreate(
            ['email' => 'pm@hetra.com'],
            [
                'name' => 'Rett', // Product Owner / PM
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $pm->update(['phone' => '081234567891']);
        $pm->assignRole('Project Manager');

        $member = User::firstOrCreate(
            ['email' => 'fikal@hetra.com'],
            [
                'name' => 'Fikal Alif',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $member->update(['phone' => '081234567892']);
        $member->assignRole('Project Member');

        $management = User::firstOrCreate(
            ['email' => 'management@hetra.com'],
            [
                'name' => 'Executive Management',
                'phone' => '081234567893',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $management->update(['phone' => '081234567893']);
        $management->assignRole('Management');

        // Update any users without phone
        User::whereNull('phone')->each(function ($u, $idx) {
            $u->update(['phone' => '0812345678' . str_pad((string)$idx, 2, '0', STR_PAD_LEFT)]);
        });

        // Generate 10 additional users dan assign role secara random
        User::factory(10)->create([
            'is_active' => true,
        ])->each(function ($user) use ($roles) {
            // Assign random role dari array $roles
            $user->assignRole(collect($roles)->random());
        });

        // Generate Clients & Vendors[cite: 3]
        Client::factory(10)->create();
        Vendor::factory(10)->create();

        // Seed test notifications for admin user[cite: 3]
        if ($admin) {
            $admin->notify(new \App\Notifications\SystemNotification(
                'Welcome to HETRA PM!',
                'Your project management control center is ready to use.'
            ));

            $admin->notify(new \App\Notifications\SystemNotification(
                'New Feature: Export to PDF',
                'You can now export complete project reports directly from the Projects page.'
            ));
        }

        // Generate Projects and related models[cite: 3]
        Project::factory(10)->create()->each(function ($project) {
            Task::factory(rand(5, 10))->create(['project_id' => $project->id]);
            Milestone::factory(rand(2, 5))->create(['project_id' => $project->id]);
            Risk::factory(rand(1, 3))->create(['project_id' => $project->id]);
            Issue::factory(rand(1, 3))->create(['project_id' => $project->id]);
            ChangeRequest::factory(rand(0, 2))->create(['project_id' => $project->id]);
            Meeting::factory(rand(1, 3))->create(['project_id' => $project->id]);
        });
    }
}
