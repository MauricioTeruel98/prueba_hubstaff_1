<?php

namespace App\Http\Controllers;

use App\Models\HubstaffTask;
use App\Services\HubstaffService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class HubstaffTaskController extends Controller
{
    protected $hubstaffService;

    public function __construct(HubstaffService $hubstaffService)
    {
        $this->hubstaffService = $hubstaffService;
    }

    public function create()
    {
        try {
            $projects = $this->hubstaffService->getProjects();
            $users = $this->hubstaffService->getOrganizationUsers();
            $token = Cache::get('hubstaff_access_token');

            return Inertia::render('Tasks/Create', [
                'projects' => $projects,
                'users' => $users,
                'hubstaffToken' => $token
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al cargar datos: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|string',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'assignee_id' => 'nullable|string',
            ]);

            // Crear la tarea en Hubstaff
            $hubstaffResponse = $this->hubstaffService->createTask($validated);

            // Guardar en nuestra base de datos
            HubstaffTask::create([
                'hubstaff_id' => $hubstaffResponse['task']['id'],
                'project_id' => $validated['project_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'due_date' => $validated['due_date'],
                'assignee_id' => $validated['assignee_id'],
            ]);

            return redirect()->route('tasks.index')
                ->with('success', 'Tarea creada exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al crear la tarea: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function index()
    {
        return Inertia::render('Tasks/Index', [
            'tasks' => []
        ]);
    }
} 