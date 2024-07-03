<?php

namespace App\Controller;

use App\Entity\RecordAirtable;
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
        $georisques = file_get_contents('https://www.georisques.gouv.fr/cartes-interactives#/');
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
                'georisques' => $georisques
            ]);
        }
        $record = new RecordAirtable();
        $record->setRecord($reponseApi);
        $form = $this->createForm(RecordAirtableType::class, $record);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $record = $form->getData();
            $em->persist($record);
            dd($record);
            $em->flush();
        }
        return $this->render('home/index.html.twig', [
            'recherche' => $recherche,
            'form' => $form->createView(),
            'rechercheForm' => $rechercheForm->createView(),
            'georisques' => $georisques
        ]);
    }
}
