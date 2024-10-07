<?php

namespace App\Controllers;

use App\Services\IntegrationCivicService;

class AuthController
{
    private IntegrationCivicService $integrationService;

    public function __construct()
    {
        $this->integrationService = new IntegrationCivicService();
    }

    public function login()
    {
        $this->integrationService->getBearerToken();
    }
}