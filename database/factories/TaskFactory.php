<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['Pending', 'In Progress', 'Completed']);
        $isCompleted = $status === 'Completed';

        return [
            'task_code' => $this->faker->unique()->numerify('TSK-####'),
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'assigned_user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'vendor_id' => $this->faker->boolean(30) ? (Vendor::inRandomOrder()->first()->id ?? Vendor::factory()) : null,
            'dependency_task_id' => null,
            'start_date' => $this->faker->date(),
            'deadline' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'status' => $status,
            'progress_percentage' => $isCompleted ? 100 : $this->faker->numberBetween(0, 99),
            'completed_at' => $isCompleted ? $this->faker->dateTimeThisMonth() : null,
        ];
    }
}
