<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Support\Facades\Response;


class ProjectController extends Controller {

    public function index(Request $request, Client $client)
    {
        $query = $client->projets();
    
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
    
        if ($request->has('sort')) {
            $sortField = $request->input('sort');
            if (in_array($sortField, ['budget', 'date_fin'])) {
                $query->orderBy($sortField);
            }
        }
    
        $projects = $query->paginate(10);
    
        if ($request->wantsJson()) {
            return response()->json($projects);
        }
    
        if ($request->header('Accept') === 'application/xml') {
            return Response::xml($projects);
        }
    
        return response()->json($projects);
    }
    



    public function show($id) {
        return response()->json(Project::findOrFail($id));
    }

    public function store(StoreProjectRequest $request)
    {
        // Retrieve validated data
        $validated = $request->validated();

        // Create the project
        $projet = Project::create([
            'client_id' => $validated['client_id'],
            'nom' => $validated['nom'],
            'budget' => $validated['budget'],
            'date_fin' => $validated['date_fin'],
            'is_urgent' => $validated['is_urgent'],
        ]);

        // Add tasks to the project
        foreach ($validated['taches'] as $taskData) {
            $projet->taches()->create($taskData);
        }

        return response()->json([
            'message' => 'Projet créé avec succès!',
            'data' => $projet
        ], 201);
    }

    public function destroy($id) {
        Project::findOrFail($id)->delete();
        return response()->json(['message' => 'Projet supprimé'], 200);
    }
}

