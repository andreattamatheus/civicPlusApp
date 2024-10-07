<?php

namespace App\Requests;

class EventFormRequest
{
    /**
     * Get and decode the input data.
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getInputData(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true, 512, JSON_THROW_ON_ERROR);

        if ($data === null) {
            throw new \InvalidArgumentException('Invalid JSON payload');
        }

        return $data;
    }

    /**
     * Validate input data.
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function validateInputData(array $data): void
    {
        $requiredFields = ['title', 'description', 'startDate', 'endDate'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("The field '$field' is required.");
            }
        }
    }

}