<?php

namespace Database\Seeders;

use App\Models\Role;
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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles berdasarkan spesifikasi PRD
        $roles = [
            'Admin',
            'Project Manager',
            'Project Member',
            'Management'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Buat User dummy untuk masing-masing role
        User::firstOrCreate(
            ['email' => 'admin@hetra.com'],
            [
                'name' => 'Admin System',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'Admin')->first()->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'pm@hetra.com'],
            [
                'name' => 'Rett', // Product Owner / PM
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'Project Manager')->first()->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'fikal@hetra.com'],
            [
                'name' => 'Fikal Alif',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'Project Member')->first()->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'management@hetra.com'],
            [
                'name' => 'Executive Management',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'Management')->first()->id,
                'is_active' => true,
            ]
        );

        // Generate 10 additional users
        $roles_ids = Role::pluck('id')->toArray();
        User::factory(10)->create([
            'role_id' => fn() => collect($roles_ids)->random(),
            'is_active' => true,
        ]);

        // Generate Clients & Vendors
        Client::factory(10)->create();
        Vendor::factory(10)->create();

        // Seed test notifications for admin user
        $adminUser = User::first();
        if ($adminUser) {
            $adminUser->notify(new \App\Notifications\SystemNotification(
                'Welcome to HETRA PM!',
                'Your project management control center is ready to use.'
            ));
            
            $adminUser->notify(new \App\Notifications\SystemNotification(
                'New Feature: Export to PDF',
                'You can now export complete project reports directly from the Projects page.'
            ));
        }

        // Generate Projects and related models
        // Create 10 projects
        Project::factory(10)->create()->each(function ($project) {
            // For each project, generate related records
            Task::factory(rand(5, 10))->create(['project_id' => $project->id]);
            Milestone::factory(rand(2, 5))->create(['project_id' => $project->id]);
            Risk::factory(rand(1, 3))->create(['project_id' => $project->id]);
            Issue::factory(rand(1, 3))->create(['project_id' => $project->id]);
            ChangeRequest::factory(rand(0, 2))->create(['project_id' => $project->id]);
            Meeting::factory(rand(1, 3))->create(['project_id' => $project->id]);
        });
    }
}
