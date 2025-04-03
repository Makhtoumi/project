<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Client extends Model {
    use HasFactory;
    
    protected $table = 'clients';

    protected $fillable = ['nom', 'secteur'];

    public function projects() {
        return $this->hasMany(Project::class);
    }

    public function totalBudget()
    {
        return $this->projets()->sum('budget');
    }

    public function projetCostDistribution()
    {
        return $this->projets()->with('taches')->get()->map(function ($projet) {
            return [
                'projet' => $projet->nom,
                'total_cost' => $projet->taches->sum('cout'),
            ];
        });
    }
}
    