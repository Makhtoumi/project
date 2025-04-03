<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Project;

class StoreTaskRequest extends FormRequest {
    public function authorize() {
        return true;
    }

    public function rules() {
        $project = Project::find($this->project_id);
        $isUrgent = $project ? $project->is_urgent : false;
        $client = $project ? $project->client : null;
        $isHealthSector = $client && $client->secteur === 'Santé';

        return [
            'project_id' => 'required|exists:projects,id',
            'description' => [
                'required',
                'string',
                Rule::when($isHealthSector, ['min:100'], [])
            ],
            'coût' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($project) {
                    if ($project && $value > $project->budgetRestant()) {
                        $fail("Le coût de la tâche dépasse le budget disponible du projet.");
                    }
                }
            ],
            'deadline' => [
                'required',
                'date',
                Rule::when($isUrgent, function ($query) {
                    $maxDeadline = Carbon::now()->addWeekdays(3)->format('Y-m-d');
                    return $query->beforeOrEqual($maxDeadline);
                })
            ]
        ];
    }

    public function messages() {
        return [
            'description.min' => "La description doit contenir au moins 100 caractères pour les clients du secteur Santé.",
            'coût.min' => "Le coût doit être un montant positif.",
            'deadline.before_or_equal' => "La deadline doit être dans un délai de 3 jours ouvrables pour les projets urgents."
        ];
    }
}

