<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() {
        return [
            'client_id' => 'required|exists:clients,id',
            'nom' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0',
            'date_fin' => 'required|date',
            'is_urgent' => 'boolean'
        ];


        if ($this->input('is_urgent')) {
            // Urgent project rules:
            // - Task deadlines must be within 3 working days
            // - Budget should automatically increase by 15%
            $rules['taches.*.deadline'] = 'required|date|before_or_equal:' . Carbon::now()->addDays(3)->toDateString();
            $rules['budget'] = 'required|numeric|min:0|gt:budget * 1.15'; // 15% increase for urgent projects
        }

        // Client sector-specific rules
        if ($this->client()->secteur === 'Santé') {
            // For "Santé" sector:
            // - Task descriptions must have at least 100 characters
            $rules['taches.*.description'] = 'required|min:100';
            // Each project must include a task with the tag "documentation"
            $rules['taches.*.tags'] = 'required|array';
            $rules['taches.*.tags.*'] = 'in:documentation';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'taches.*.deadline.before_or_equal' => 'La deadline des tâches urgentes doit être dans les 3 jours ouvrables.',
            'budget.gt' => 'Le budget d\'un projet urgent doit être supérieur de 15% par rapport au budget initial.',
            'taches.*.description.min' => 'Les descriptions des tâches dans le secteur santé doivent comporter au moins 100 caractères.',
            'taches.*.tags.in' => 'Chaque projet dans le secteur santé doit inclure une tâche avec le tag "documentation".',
        ];
    }

    public function client()
    {
        // Accessing the client associated with the project
        return $this->route('client');
    }
}
    
