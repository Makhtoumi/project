<?php 


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'client' => $this->nom,
            'projets_actifs' => $this->projects->count(),
            'moyenne_couts' => $this->projects->flatMap(function ($project) {
                return $project->tasks->pluck('coût');
            })->avg(),
            'taches_cheres' => $this->projects->flatMap(function ($project) {
                return $project->tasks->filter(function ($task) use ($project) {
                    return $task->coût > 0.75 * $project->budget;
                })->map(function ($task) use ($project) {
                    return [
                        'project' => $project->nom,
                        'coût' => $task->coût,
                    ];
                });
            }),
        ];
    }
}
