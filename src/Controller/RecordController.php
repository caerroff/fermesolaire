<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\RecordAirtable;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecordController extends AbstractController
{
    #[Route('/record/{id}', name: 'internal_record_get', options: ['expose' => true])]
    public function get(RecordAirtable $record): Response
    {
        $data = json_encode($record->getRecord());
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
