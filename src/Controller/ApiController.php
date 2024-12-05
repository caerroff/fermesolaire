<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private $mapbox_token;

    public function __construct()
    {
        $this->mapbox_token = $_ENV['MAPBOX_TOKEN'];
    }

    #[Route('/api/fetchParcelle/{lon}/{lat}', name: 'app_api_fetch_parcelle', options: ['expose' => true])]
    public function fetchParcelle(string $lon, string $lat): Response
    {
        dd($lon, $lat);
        return $this->json([
            'lon' => $lon,
            'lat' => $lat,
        ]);
    }

    #[Route('api/mapboxtoken', name: 'api_mapbox_token', options:['expose' => true])]
    public function apiMapboxToken(): Response
    {
        return new JsonResponse($this->mapbox_token);
    }
}
