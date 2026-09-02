<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase; // Mereset database otomatis setiap test selesai

    public function test_admin_can_delete_user_without_projects()
    {
        $admin = User::factory()->create();
        $userToDelete = User::factory()->create();

        // Simulasi login sebagai admin dan mengirim request DELETE
        $response = $this->actingAs($admin)->delete("/users/{$userToDelete->id}");

        // Pastikan proses berhasil dan data benar-benar hilang dari database
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_cannot_delete_user_assigned_as_project_manager()
    {
        $admin = User::factory()->create();
        $manager = User::factory()->create();

        // Sengaja buat bentrok dengan mengikat manager ke sebuah project
        Project::factory()->create(['project_manager_id' => $manager->id]);

        // Simulasi request DELETE
        $response = $this->actingAs($admin)->delete("/users/{$manager->id}");

        // Pastikan sistem mengembalikan error 500 (atau kode error custom lu)
        $response->assertStatus(500);

        // Pastikan manager tersebut batal dihapus dan masih ada di database
        $this->assertDatabaseHas('users', ['id' => $manager->id]);
    }
}
