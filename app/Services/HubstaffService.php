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
            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->withToken($this->getAccessToken())
                ->post("{$this->baseUrl}/projects/{$data['project_id']}/tasks", [
                    'task' => [
                        'summary' => $data['title'],
                        'description' => $data['description'] ?? '',
                        'assignee_id' => $data['assignee_id'] ?? null,
                        'due_date' => $data['due_date'] ?? null,
                        'status' => $data['status'] ?? 'open'
                    ]
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Error al crear la tarea en Hubstaff: ' . $response->body());
        } catch (Exception $e) {
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
}
