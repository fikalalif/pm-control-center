<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'type' => $this->faker->randomElement(['Meeting', 'Action']),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'owner_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'meeting_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'due_date' => $this->faker->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['Open', 'Closed']),
        ];
    }
}
