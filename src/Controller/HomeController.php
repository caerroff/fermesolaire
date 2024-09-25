<?php

namespace App\Controller;

use App\Entity\RecordAirtable;
use App\Entity\RPG;
use App\Form\RecordAirtableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $recherche = 'receI98tyqDwdJVyf';
        // get content for https://www.georisques.gouv.fr/cartes-interactives#/
        $rechercheForm = $this->createFormBuilder()
            ->add('recherche', null, ['data' => $recherche])
            ->add('envoyer', SubmitType::class, ['label' => 'Rechercher'])
            ->setAction($this->generateUrl('app_home'))
            ->setMethod('POST')
            ->getForm();
        $rechercheForm->handleRequest($request);
        if ($rechercheForm->isSubmitted() && $rechercheForm->isValid()) {
            $recherche = $rechercheForm->getData()['recherche'];
        }
        $reponseApi = AirtableController::getRecord($recherche);
        if ($reponseApi instanceof \Error) {
            $form = $this->createForm(RecordAirtableType::class);
            return $this->render('home/index.html.twig', [
                'recherche' => $recherche,
                'rechercheForm' => $rechercheForm->createView(),
                'form' => $form->createView(),
                'error' => $reponseApi->getMessage(),
            ]);
        }
        $latitude = $reponseApi['fields']['Latitude'];
        $longitude = $reponseApi['fields']['Longitude'];
        $record = new RecordAirtable();
        $record->setRecord($reponseApi);
        $form = $this->createForm(RecordAirtableType::class, $record);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $record = $form->getData();
            $rpgs = $record->getRPG();
            $record->setRPG([]);
            foreach ($rpgs as $rpg) {
                $rpg = $em->getRepository(RPG::class)->find($rpg);
                $record->addRPG($rpg->getDescription());
            }
            $em->persist($record);
            $em->flush();
            return $this->redirectToRoute('update_record', ['id' => $record->getId()]);
        }
        return $this->render('home/index.html.twig', [
            'recherche' => $recherche,
            'form' => $form->createView(),
            'rechercheForm' => $rechercheForm->createView(),
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    #[Route('/update/{id}', name: 'update_record', requirements: ['id' => '\d+'])]
    public function updateRecord(RecordAirtable $record, Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('home/updateRecord.html.twig', [
            'record' => $record
        ]);
    }
}
