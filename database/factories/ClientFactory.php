<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory {
    public function definition(): array {

        $secteurs = ['Santé', 'Banque', 'Assurance', 'Technologie', 'Éducation', 'Commerce'];

        return [
            'nom' => $this->faker->company . ' ' . $this->faker->randomElement(['SA', 'SARL', 'SAS', 'Groupe']),
            'secteur' => $this->faker->randomElement($secteurs),
        ];
    }
}

