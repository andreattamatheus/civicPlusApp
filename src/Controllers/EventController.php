<?php

namespace App\Controllers;

use App\Services\IntegrationCivicService;

class EventController
{
    private IntegrationCivicService $integrationService;

    public function __construct()
    {
        $this->integrationService = new IntegrationCivicService();
    }

    public function index(): void
    {
        try {
            echo $this->integrationService->getEvents();
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    public function store(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['title']) || empty($data['description']) || empty($data['startDate']) || empty($data['endDate'])) {
                http_response_code(400);
                echo json_encode(['error' => 'All fields are required'], JSON_THROW_ON_ERROR);
                exit;
            }

            $title = $data['title'];
            $description = $data['description'];
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];

            echo $this->integrationService->addEvent($title, $description, $startDate, $endDate);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    // Display event details
    public function show($id): void
    {
        try {
            echo $this->integrationService->getEventDetails($id);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }
}
