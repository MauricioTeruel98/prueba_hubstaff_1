<?php

namespace App\Http\Controllers;

use App\Services\HubstaffService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HubstaffActivityController extends Controller
{
    protected $hubstaffService;

    public function __construct(HubstaffService $hubstaffService)
    {
        $this->hubstaffService = $hubstaffService;
    }

    public function index(Request $request)
    {
        try {
            $startDate = $request->input('start_date') 
                ? now()->parse($request->input('start_date'))->startOfDay() 
                : now()->subDays(7)->startOfDay();
            
            $endDate = $request->input('end_date') 
                ? now()->parse($request->input('end_date'))->endOfDay() 
                : now()->endOfDay();
            
            $projectId = $request->input('project_id');
            $userId = $request->input('user_id');

            $activities = $this->hubstaffService->getActivities(
                $startDate,
                $endDate,
                $projectId,
                $userId
            );

            $projects = $this->hubstaffService->getProjects();
            $users = $this->hubstaffService->getOrganizationUsers();

            return Inertia::render('Activities/Index', [
                'activities' => $activities['daily_activities'] ?? [],
                'projects' => $projects,
                'users' => $users,
                'filters' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'project_id' => $projectId,
                    'user_id' => $userId,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en ActivityController:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors([
                'error' => 'Error al cargar actividades: ' . $e->getMessage()
            ]);
        }
    }
} 