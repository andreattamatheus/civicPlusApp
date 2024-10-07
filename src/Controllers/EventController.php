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
            echo false;
        }
    }

    public function store(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $title = $data['title'];
            $description = $data['description'];
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];

            echo $this->integrationService->addEvent($title, $description, $startDate, $endDate);
        } catch (\Exception $e) {
            echo false;
        }
    }

    // Display event details
    public function show($id): void
    {
        try {
            echo $this->integrationService->getEventDetails($id);
        } catch (\Exception $e) {
            echo false;
        }
    }
}
