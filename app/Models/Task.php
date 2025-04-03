<?php 
namespace App\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory;

    protected $fillable = ['project_id', 'description', 'coût', 'deadline'];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function validateTaskBudget(Project $project, $taskCost)
    {
        // Calculate the sum of existing tasks' costs
        $totalTaskCosts = $project->taches()->sum('cout');

        // Calculate the available budget
        $availableBudget = $project->budget - $totalTaskCosts;

        // Check if the task cost exceeds the available budget
        if ($taskCost > $availableBudget) {
            throw new \Exception("Le coût de la tâche dépasse le budget disponible du projet.");
        }
    }
}
