<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Initialize Faker
        $faker = FakerFactory::create();
        
        // Disable foreign key checks for better performance
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear existing data
        Client::truncate();
        Project::truncate();
        Task::truncate();
        
        // Création de 10 clients
        Client::factory()
            ->count(10)
            ->create()
            ->each(function ($client) use ($faker) {
                // Pour chaque client, créer entre 1 et 5 projets
                $projects = Project::factory()
                    ->count($faker->numberBetween(1, 5))
                    ->create(['client_id' => $client->id]);
                
                // Pour chaque projet, créer entre 3 et 10 tâches
                $projects->each(function ($project) use ($faker) {
                    // S'assurer que date_fin est après created_at
                    if ($project->created_at >= $project->date_fin) {
                        $project->date_fin = $project->created_at->addDays(30);
                        $project->save();
                    }
                    
                    Task::factory()
                        ->count($faker->numberBetween(3, 10))
                        ->create(['project_id' => $project->id]);
                });
            });
        
        // Projet spécifique pour tester la validation
        $clientTest = Client::factory()->create(['nom' => 'Client Test', 'secteur' => 'Santé']);
        $projetTest = Project::factory()->create([
            'client_id' => $clientTest->id,
            'nom' => 'Projet Urgent',
            'is_urgent' => true,
            'budget' => 50000,
            'date_fin' => now()->addDays(30), // Explicit end date
        ]);
        
        Task::factory()->create([
            'project_id' => $projetTest->id,
            'description' => str_repeat('Description très détaillée ', 10),
            'coût' => 10000,
            'deadline' => now()->addDays(2),
        ]);
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}