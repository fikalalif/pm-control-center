<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneFactory extends Factory
{
    protected $model = Milestone::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['Pending', 'Completed']);
        $isCompleted = $status === 'Completed';

        return [
            'milestone_code' => $this->faker->unique()->numerify('MLS-####'),
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'status' => $status,
            'progress_percentage' => $isCompleted ? 100 : $this->faker->numberBetween(0, 99),
            'assigned_user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'dependency_milestone_id' => null,
            'completed_at' => $isCompleted ? $this->faker->dateTimeThisMonth() : null,
        ];
    }
}
