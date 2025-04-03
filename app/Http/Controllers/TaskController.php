<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller {
    public function index() {
        return response()->json(Task::all());
    }

    public function store($projetId, Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'cout' => 'required|numeric|min:0',
            'deadline' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the project
        $projet = Project::findOrFail($projetId);

        // Ensure the task's cost does not exceed the available budget
        $totalTachesCout = $projet->taches->sum('coût');
        if ($totalTachesCout + $request->input('coût') > $projet->budget) {
            return response()->json(['error' => 'The task cost exceeds the project budget.'], 400);
        }

        // Create the new task
        $task = new Task();
        $task->project_id = $projetId;
        $task->description = $request->input('description');
        $task->coût = $request->input('coût');
        $task->deadline = $request->input('deadline');
        $task->save();

        return response()->json($task, 201);
    }

    public function destroy($id) {
        Task::findOrFail($id)->delete();
        return response()->json(['message' => 'Tâche supprimée'], 200);
    }
}
