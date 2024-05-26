<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/fetchParcelle/{lon}/{lat}', name: 'app_api_fetch_parcelle', options: ['expose' => true])]
    public function fetchParcelle(string $lon, string $lat): Response
    {
        dd($lon, $lat);
        return $this->json([
            'lon' => $lon,
            'lat' => $lat,
        ]);
    }
}
