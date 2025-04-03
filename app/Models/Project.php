<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    use HasFactory;
    protected $table = 'projects';
    protected $fillable = ['client_id', 'nom', 'budget', 'date_fin', 'is_urgent' , 'version'];

    protected $casts = [
        'is_urgent' => 'boolean',
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }   

    public function tasks() {
        return $this->hasMany(Task::class);
    }



    public function budgetRestant() {
        return $this->budget - $this->tasks()->sum('coût');
    }
}
