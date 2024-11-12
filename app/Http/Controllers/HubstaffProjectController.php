<?php

namespace App\Http\Controllers;

use App\Services\HubstaffService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HubstaffProjectController extends Controller
{
    protected $hubstaffService;

    public function __construct(HubstaffService $hubstaffService)
    {
        $this->hubstaffService = $hubstaffService;
    }

    public function index()
    {
        try {
            $projects = $this->hubstaffService->getProjects();
            
            return Inertia::render('Projects/Index', [
                'projects' => $projects
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al cargar proyectos: ' . $e->getMessage()
            ]);
        }
    }

    public function show($projectId, Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = 10;
            
            $tasks = $this->hubstaffService->getProjectTasks($projectId, $page, $perPage);
            $project = $this->hubstaffService->getProject($projectId);
            
            return Inertia::render('Projects/Show', [
                'project' => $project,
                'tasks' => [
                    'data' => $tasks['data'],
                    'meta' => [
                        'current_page' => (int)$page,
                        'per_page' => (int)$perPage,
                        'total' => $tasks['meta']['total']
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al cargar tareas: ' . $e->getMessage()
            ]);
        }
    }
} 