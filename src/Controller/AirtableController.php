<?php

namespace App\Controller;

use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class AirtableController extends AbstractController
{
    private $airtable_api_key;
    private $airtable_api;

    public function __construct()
    {
        $this->airtable_api_key = $_ENV['API_KEY'];
        $this->airtable_api = $_ENV['API_URL'];
    }

    #[Route('/airtable/{record}', name: 'app_airtable', options: ['expose' => true])]
    public function index(string $record): JsonResponse
    {
        try {
            $response = file_get_contents($this->airtable_api . $record, false, stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer '
                        . $this->airtable_api_key
                ]
            ]));
            return new JsonResponse(json_decode($response, true));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    static public function getRecord(string $record): array|Error
    {
        try {
            $response = file_get_contents($_ENV['API_URL'] . $record, false, stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer '
                        . $_ENV['API_KEY']
                ]
            ]));
            return json_decode($response, true);
        } catch (\Exception $e) {
            return new Error($e->getMessage());
        }
    }
}
