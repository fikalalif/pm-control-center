<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'project_code' => $this->faker->unique()->numerify('PRJ-####'),
            'name' => $this->faker->words(3, true),
            'client_id' => Client::inRandomOrder()->first()->id ?? Client::factory(),
            'project_type_id' => null, // Assuming nullable, or we could create them
            'project_manager_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'current_phase_id' => null,
            'start_date' => $this->faker->date(),
            'target_completion' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'progress_percentage' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['Pending', 'In Progress', 'Completed', 'On Hold']),
            'health_override' => $this->faker->randomElement([null, 'Green', 'Yellow', 'Red']),
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'description' => $this->faker->paragraph(),
            'last_update_at' => $this->faker->dateTimeThisMonth(),
            'next_action' => $this->faker->sentence(),
        ];
    }
}
