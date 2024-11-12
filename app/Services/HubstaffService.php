<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class HubstaffService
{
    protected $baseUrl = 'https://api.hubstaff.com/v2';

    protected function getAccessToken()
    {
        $token = Cache::get('hubstaff_access_token');

        if (!$token) {
            throw new Exception('No hay token de acceso disponible. Por favor, conecte su cuenta de Hubstaff.');
        }

        return $token;
    }

    public function createTask(array $data)
    {
        try {
            $payload = [
                'status' => 'active',
                'summary' => $data['title'],
                'details' => $data['description'] ?? '',
                'assignee_id' => $data['assignee_id'] ? (int)$data['assignee_id'] : null,
                'metadata' => []
            ];

            \Log::info('Intentando crear tarea en Hubstaff:', [
                'project_id' => $data['project_id'],
                'payload' => $payload
            ]);

            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->withToken($this->getAccessToken())
                ->post("{$this->baseUrl}/projects/{$data['project_id']}/tasks", $payload);

            \Log::info('Respuesta de Hubstaff:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $taskData = $response->json();
                
                // Formatear la respuesta para que coincida con el formato deseado
                return [
                    'task' => [
                        'id' => $taskData['task']['id'] ?? null,
                        'integration_id' => $taskData['task']['integration_id'] ?? null,
                        'status' => 'active',
                        'project_id' => (int)$data['project_id'],
                        'project_type' => 'project',
                        'summary' => $data['title'],
                        'details' => $data['description'] ?? '',
                        'assignee_ids' => $data['assignee_id'] ? [(int)$data['assignee_id']] : [],
                        'metadata' => [],
                        'created_at' => $taskData['task']['created_at'] ?? now()->toIso8601String(),
                        'updated_at' => $taskData['task']['updated_at'] ?? now()->toIso8601String()
                    ]
                ];
            }

            throw new Exception('Error al crear la tarea en Hubstaff: ' . $response->body());
        } catch (Exception $e) {
            \Log::error('Error en createTask:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Error en el servicio de Hubstaff: ' . $e->getMessage());
        }
    }

    public function getUserInfo()
    {
        try {
            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->withToken($this->getAccessToken())
                ->get("{$this->baseUrl}/users/me");

            \Log::info('Hubstaff User Response:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['user'] ?? null;
            }

            throw new Exception('Error al obtener información del usuario: ' . $response->body());
        } catch (Exception $e) {
            \Log::error('Error en getUserInfo:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Error en el servicio de Hubstaff: ' . $e->getMessage());
        }
    }

    public function getOrganizationInfo()
    {
        try {
            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->withToken($this->getAccessToken())
                ->get("{$this->baseUrl}/organizations");

            \Log::info('Hubstaff Organization Response:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // La API devuelve un array de organizaciones, tomamos la primera
                return $data['organizations'][0] ?? null;
            }

            throw new Exception('Error al obtener información de la organización: ' . $response->body());
        } catch (Exception $e) {
            \Log::error('Error en getOrganizationInfo:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Error en el servicio de Hubstaff: ' . $e->getMessage());
        }
    }

    public function getProjects()
    {
        try {
            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->withToken($this->getAccessToken())
                ->get("{$this->baseUrl}/organizations/{$this->getOrganizationId()}/projects");

            if ($response->successful()) {
                $projects = $response->json()['projects'] ?? [];
                // Filtrar solo los proyectos que permiten crear tareas
                return array_filter($projects, function($project) {
                    return empty($project['task_integration']);
                });
            }

            throw new Exception('Error al obtener los proyectos: ' . $response->body());
        } catch (Exception $e) {
            throw new Exception('Error en el servicio de Hubstaff: ' . $e->getMessage());
        }
    }

    public function getOrganizationUsers()
    {
        try {
            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->withToken($this->getAccessToken())
                ->get("{$this->baseUrl}/organizations/{$this->getOrganizationId()}/members");

            if ($response->successful()) {
                $members = $response->json()['members'] ?? [];
                
                return array_map(function($member) {
                    $user = $member['user'] ?? [];
                    return [
                        'id' => $user['id'] ?? $member['user_id'],
                        'name' => $user['name'] ?? 'Sin nombre',
                        'email' => $user['email'] ?? 'Sin email',
                        'membership_role' => $member['membership_role'] ?? 'Sin rol',
                        'effective_role' => $member['effective_role'] ?? 'Sin rol efectivo',
                        'trackable' => $member['trackable'] ?? false,
                        'status' => $member['membership_status'] ?? 'unknown'
                    ];
                }, $members);
            }

            throw new Exception('Error al obtener los usuarios: ' . $response->body());
        } catch (Exception $e) {
            throw new Exception('Error en el servicio de Hubstaff: ' . $e->getMessage());
        }
    }

    private function getOrganizationId()
    {
        $organization = $this->getOrganizationInfo();
        return $organization['id'] ?? null;
    }

    public function getProject($projectId)
    {
        try {
            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->withToken($this->getAccessToken())
                ->get("{$this->baseUrl}/projects/{$projectId}");

            if ($response->successful()) {
                return $response->json()['project'] ?? null;
            }

            throw new Exception('Error al obtener el proyecto: ' . $response->body());
        } catch (Exception $e) {
            throw new Exception('Error en el servicio de Hubstaff: ' . $e->getMessage());
        }
    }

    public function getProjectTasks($projectId, $page = 1, $perPage = 10)
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->withToken($this->getAccessToken())
                ->get("{$this->baseUrl}/projects/{$projectId}/tasks", [
                    'offset' => $offset,
                    'limit' => $perPage,
                    'status' => 'active'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'data' => $data['tasks'] ?? [],
                    'meta' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $data['pagination']['total_items'] ?? 0
                    ]
                ];
            }

            throw new Exception('Error al obtener las tareas: ' . $response->body());
        } catch (Exception $e) {
            throw new Exception('Error en el servicio de Hubstaff: ' . $e->getMessage());
        }
    }
}
