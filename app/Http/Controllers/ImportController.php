<?php 
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function importCSV(Request $request)
    {
        // Valider le fichier CSV
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Fichier invalide'], 400);
        }

        // Traiter le fichier CSV
        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path)); // Lire le fichier CSV

        // Démarrer une transaction pour garantir l'intégrité des données
        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                // Extrait les données du CSV
                [$client_nom, $client_secteur, $projet_nom, $projet_budget, $tache_description, $tache_cout, $tache_deadline] = $row;

                // Vérification de l'intégrité des données
                $tache_cout = (float)$tache_cout;
                $projet_budget = (float)$projet_budget;

                // Vérification de la validité des données
                if (!$client_nom || !$projet_nom || !$tache_description || !$tache_deadline || $tache_cout <= 0 || $projet_budget <= 0) {
                    throw new \Exception('Données invalides dans le CSV');
                }

                // Trouver ou créer le client
                $client = Client::firstOrCreate(['nom' => $client_nom], ['secteur' => $client_secteur]);

                // Trouver ou créer le projet pour ce client
                $projet = Project::firstOrCreate(
                    ['client_id' => $client->id, 'nom' => $projet_nom],
                    ['budget' => $projet_budget]
                );

                // Ajouter la tâche au projet, vérifier les doublons
                $existingTask = Task::where('project_id', $projet->id)
                    ->where('description', $tache_description)
                    ->where('deadline', $tache_deadline)
                    ->first();

                if (!$existingTask) {
                    Task::create([
                        'project_id' => $projet->id,
                        'description' => $tache_description,
                        'cout' => $tache_cout,
                        'deadline' => $tache_deadline
                    ]);
                } else {
                    Log::info("Doublon de tâche détecté: " . $tache_description);
                }
            }

            // Commit de la transaction si tout est OK
            DB::commit();
            return response()->json(['success' => 'Données importées avec succès'], 200);

        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            Log::error("Erreur d'importation CSV: " . $e->getMessage());
            return response()->json(['error' => 'Erreur d\'importation'], 500);
        }
    }
}

