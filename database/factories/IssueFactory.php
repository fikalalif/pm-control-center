<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    protected $model = Issue::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['Open', 'Closed']);
        $isClosed = $status === 'Closed';

        return [
            'issue_code' => $this->faker->unique()->numerify('ISS-####'),
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'impact' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'owner_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'action' => $this->faker->paragraph(),
            'deadline' => $this->faker->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
            'status' => $status,
            'resolved_at' => $isClosed ? $this->faker->dateTimeThisMonth() : null,
        ];
    }
}
