<?php

namespace Database\Factories;

use App\Models\Risk;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RiskFactory extends Factory
{
    protected $model = Risk::class;

    public function definition(): array
    {
        return [
            'risk_code' => $this->faker->unique()->numerify('RSK-####'),
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'probability' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'impact' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'risk_level' => $this->faker->randomElement(['Low', 'Medium', 'High', 'Critical']),
            'mitigation' => $this->faker->paragraph(),
            'owner_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'status' => $this->faker->randomElement(['Open', 'Closed']),
            'due_date' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
        ];
    }
}
