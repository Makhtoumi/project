<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Resources\ClientReportResource;
use Illuminate\Support\Facades\Cache;

class ClientReportController extends Controller
{
  

    public function rapport($client_id)
    {
        $cacheKey = "client_report_{$client_id}";
        $rapport = Cache::get($cacheKey);
    
        if (!$rapport) {
            $client = Client::with([
                'projects' => function ($query) {
                    $query->where('date_fin', '>', now());
                },
                'projects.tasks' => function ($query) {
                    $query->select('id', 'project_id', 'coût');
                }
            ])->findOrFail($client_id);
    
            $rapport = new ClientReportResource($client);
    
            Cache::put($cacheKey, $rapport, 15 * 60);
        }
    
        return $rapport;
    }
}
