<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory {
    public function definition(): array
    {
        $dateDebut = $this->faker->dateTimeBetween('-1 year', 'now');
        // S'assurer que la date de fin est bien postérieure
        $dateFin = Carbon::instance($dateDebut)->addDays($this->faker->numberBetween(30, 365));
        
        return [
            'client_id' => Client::factory(),
            'nom' => 'Projet ' . $this->faker->words(2, true),
            'budget' => $this->faker->randomFloat(2, 5000, 100000),
            'date_fin' => $dateFin,
            'is_urgent' => $this->faker->boolean(20),
        ];
    }
}

