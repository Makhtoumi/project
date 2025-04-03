<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory {
    public function definition(): array
    {
        $project = Project::factory()->create();
        $dateDebutProjet = $project->created_at;
        $dateFinProjet = $project->date_fin;
        
        // Vérifier que la date de fin est bien postérieure à la date de début
        if ($dateDebutProjet >= $dateFinProjet) {
            $dateFinProjet = $dateDebutProjet->copy()->addDays(30); // Ajouter 30 jours si problème
        }
        
        return [
            'project_id' => $project->id,
            'description' => $this->faker->sentence(10),
            'coût' => $this->faker->randomFloat(2, 100, 5000), // Match your DB column
            'deadline' => $this->faker->dateTimeBetween($dateDebutProjet, $dateFinProjet),
        ];
    }
}

