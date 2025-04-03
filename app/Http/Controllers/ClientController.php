<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\Response;

class ClientController extends Controller {

    public function index(Request $request)
    {
        // Query clients with pagination
        $clients = Client::paginate(10);

        

        // Return the response in JSON or XML format based on Accept header
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $clients->items(),
                'meta' => [
                    'total' => $clients->total(),
                    'sectors' => Client::selectRaw('secteur, count(*) as count')
                        ->groupBy('secteur')
                        ->get()
                ]
            ]);
        }

        if ($request->header('Accept') === 'application/xml') {
            
            return Response::xml([
                'data' => $clients->items(),
                'meta' => [
                    'total' => $clients->total(),
                    'sectors' => Client::selectRaw('secteur, count(*) as count')
                        ->groupBy('secteur')
                        ->get()
                ]
            ]);
        }

        // Default to JSON
        return response()->json($clients);
    }


        // Récupérer tous les clients avec le total de leur budget (somme des budgets de projets)
        // $clients = Client::all()->map(function ($client) {
        //     return [
        //         'client' => $client->nom,
        //         'secteur' => $client->secteur,
        //         'total_budget' => $client->totalBudget(),
        //     ];
        // });
    
    // Obtenir la répartition des coûts par projet pour un client donné

    public function getCostsDistribution($id) {
        $client = Client::with(['projects' => function($query) {
            $query->withSum('tasks', 'coût');
        }])->findOrFail($id);

        return response()->json($client->projects);
    }

    public function store(Request $request) {
        $request->validate([
            'nom' => 'required|string',
            'secteur' => 'required|string',
        ]);

        $client = Client::create($request->all());
        return response()->json($client, 201);
    }
}
