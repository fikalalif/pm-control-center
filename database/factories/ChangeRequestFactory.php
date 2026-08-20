<?php

namespace Database\Factories;

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChangeRequestFactory extends Factory
{
    protected $model = ChangeRequest::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['Pending', 'Approved', 'Rejected']);

        return [
            'cr_code' => $this->faker->unique()->numerify('CR-####'),
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'description' => $this->faker->paragraph(),
            'requested_by' => $this->faker->name(),
            'requested_at' => $this->faker->dateTimeThisMonth(),
            'impact' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'status' => $status,
            'decision_date' => $status !== 'Pending' ? $this->faker->dateTimeThisMonth() : null,
            'decision_notes' => $status !== 'Pending' ? $this->faker->sentence() : null,
            'approved_by' => $status === 'Approved' ? (User::inRandomOrder()->first()->id ?? User::factory()) : null,
        ];
    }
}
