<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_projects_list()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/projects');

        // Pastikan halaman berhasil dimuat tanpa error 500
        $response->assertStatus(200);
    }

    public function test_user_can_create_a_new_project()
    {
        $user = User::factory()->create();

        // Simulasi input data project baru
        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'AI Deteksi Sesar dan Breksi Geologi',
            'project_code' => 'PRJ-GEO-01',
            'status' => 'planning',
            'project_manager_id' => $user->id,
        ]);

        $response->assertRedirect();
        // Verifikasi data benar-benar tersimpan di SQLite memory
        $this->assertDatabaseHas('projects', [
            'project_code' => 'PRJ-GEO-01',
        ]);
    }

    public function test_project_requires_a_name_and_code()
    {
        $user = User::factory()->create();

        // Sengaja dikosongkan untuk memastikan validasi berjalan
        $response = $this->actingAs($user)->post('/projects', [
            'name' => '',
            'project_code' => '',
        ]);

        // Harus mengembalikan pesan error validasi
        $response->assertSessionHasErrors(['name', 'project_code']);
    }
}
