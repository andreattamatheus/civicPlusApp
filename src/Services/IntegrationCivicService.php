<?php

namespace App\Services;

use App\Exceptions\ApiIntegrationBadResponse;
use Exception;

class IntegrationCivicService
{
    private string $clientId;
    private string $clientSecret;
    private string $apiUrl;
    private string $bearerTokenFile;

    public function __construct()
    {
        $this->clientId = $_ENV['CLIENT_ID'];
        $this->clientSecret = $_ENV['CLIENT_SECRET'];
        $this->apiUrl = $_ENV['AUTH_URL'];
        $this->bearerTokenFile = dirname(__DIR__, 2) . '/cache/bearer_token.json';
    }

    private function getAccessToken()
    {
        if (file_exists($this->bearerTokenFile)) {
            $accessToken = json_decode(file_get_contents($this->bearerTokenFile), true, 512, JSON_THROW_ON_ERROR);
            if (isset($accessToken)) {
                return $accessToken;
            }
        }

        return $this->getBearerToken();
    }

    public function getBearerToken() : string
    {
        $postData = json_encode([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret
        ], JSON_THROW_ON_ERROR);

        $ch = curl_init($this->apiUrl . '/Auth');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postData)
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Error in cURL: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if (!isset($responseData['access_token'])) {
            throw new Exception('Unable to retrieve access token.');
        }

        file_put_contents($this->bearerTokenFile, json_encode($responseData['access_token']));

        return $responseData['access_token'];
    }

    private function makeAuthenticatedRequest($endpoint, $method = 'GET', $data = []) : string
    {
        $accessToken = $this->getAccessToken();

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            http_response_code(400);
            throw new Exception('Error in cURL: ' . curl_error($ch));
        }

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!in_array($httpStatusCode, [200 , 201], true)) {
            echo $this->badResponse($response);
            throw new ApiIntegrationBadResponse('Error: ' . $httpStatusCode);
        }

        $jsonData =  json_encode($response, JSON_THROW_ON_ERROR);

        return json_decode($jsonData, true);
    }

    private function badResponse(string $response) : string|false
    {
        preg_match('/\{.*\}/s', $response, $matches);

        $responseData = [
            'status' => false,
            'details' => [],
        ];

        if (isset($matches[0])) {
            $jsonString = stripslashes($matches[0]);

            $jsonArray = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

            if (json_last_error() === JSON_ERROR_NONE) {
                $responseData['message'] = $jsonArray['message'] ?? 'An error occurred.';
                $responseData['details'] = $jsonArray['details'] ?? [];
            } else {
                $responseData['message'] = 'Invalid response format.';
                $responseData['details'] = ['error' => json_last_error_msg()];
            }
        } else {
            $responseData['message'] = 'No JSON found in the string.';
        }
        http_response_code(400);

        return json_encode($responseData, JSON_THROW_ON_ERROR);
    }

    /**
     * Send a JSON response.
     *
     * @param mixed $data
     * @param int $statusCode
     */
    public function jsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo $data;
        exit;
    }

    /**
     * Send a JSON response.
     *
     * @param mixed $data
     * @param int $statusCode
     */
    public function errorREsponse($errorMessage, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode(['error' => $errorMessage], JSON_THROW_ON_ERROR);
        exit;
    }

    public function getEvents() : string
    {
        $url = $this->apiUrl . '/Events';

        return $this->makeAuthenticatedRequest($url);
    }

    public function getEventDetails(string $id) : string
    {
        $url = $this->apiUrl . '/Events/' . $id;

        return $this->makeAuthenticatedRequest($url);
    }

    public function addEvent(string $title, string $description, string $startDate, string $endDate) : string
    {
        $url = $this->apiUrl . '/Events';
        $data = [
            'title' => $title,
            'description' => $description,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return $this->makeAuthenticatedRequest($url, 'POST', $data);
    }
}