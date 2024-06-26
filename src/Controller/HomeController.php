<?php

namespace App\Controller;

use App\Entity\RecordAirtable;
use App\Form\RecordAirtableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $recherche = $request->query->get('recherche') ? $request->query->get('recherche') : 'receI98tyqDwdJVyf';
        $reponseApi = AirtableController::getRecord($recherche);
        $record = new RecordAirtable();
        $record->setRecord($reponseApi);
        $form = $this->createForm(RecordAirtableType::class, $record);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $record = $form->getData();
            dd($record);
            $em->persist($record);
            $em->flush();
        }
        return $this->render('home/index.html.twig', [
            'recherche' => $recherche,
            'form' => $form->createView(),
        ]);
    }
}
