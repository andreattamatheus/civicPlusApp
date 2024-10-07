<?php

namespace App\Controllers;

use App\Requests\EventFormRequest;
use App\Services\IntegrationCivicService;

class EventController
{
    private IntegrationCivicService $integrationService;

    private EventFormRequest $eventFormRequest;

    public function __construct()
    {
        $this->integrationService = new IntegrationCivicService();
        $this->eventFormRequest = new EventFormRequest();
    }

    public function index(): void
    {
        try {
            $result = $this->integrationService->getEvents();
            $this->integrationService->jsonResponse($result, 201);  // 201 Created
        } catch (\Exception $e) {
            $this->integrationService->errorREsponse( $e->getMessage(), 400);
        }
    }

    public function store(): void
    {
        try {
            $data = $this->eventFormRequest->getInputData();
            $this->eventFormRequest->validateInputData($data);

            $title = $data['title'];
            $description = $data['description'];
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];

            $result = $this->integrationService->addEvent($title, $description, $startDate, $endDate);
            $this->integrationService->jsonResponse($result, 201);  // 201 Created
        } catch (\InvalidArgumentException $e) {
            $this->integrationService->errorREsponse( $e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->integrationService->errorREsponse( $e->getMessage(), 500);
        }
    }

    // Display event details
    public function show($id): void
    {
        try {
            echo $this->integrationService->getEventDetails($id);
        } catch (\Exception $e) {
            $this->integrationService->errorREsponse( $e->getMessage(), 500);
        }
    }
}
