<?php

namespace App\Controller;

use App\Entity\Loi\Littoral;
use App\Entity\Loi\Montagne;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

#[Route('/loi', name: 'loi_')]
class LoiController extends AbstractController
{
    #[Route('/littoral', name: 'littoral')]
    public function littoral(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, ['label' => 'Fichier'])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $em->getRepository(Littoral::class)->truncate();

            // Read CSV file
            $csv = array_map('str_getcsv', file($file));
            foreach ($csv as $line) {
                if ($line[0] == 'ANNEE_COG') {
                    continue;
                }
                $littoral = new Littoral();
                $littoral->setAnneeCog($line[0]);
                $littoral->setInseeReg2016($line[1]);
                $littoral->setNomReg2016($line[2]);
                $littoral->setInseeDep($line[3]);
                $littoral->setNomDept($line[4]);
                $littoral->setInseeCom($line[5]);
                $littoral->setNomCom($line[6]);
                $littoral->setClassement($line[7]);
                $em->persist($littoral);
            }
            $em->flush();
            $this->addFlash('success', 'Fichier importé avec succès');
            return $this->redirectToRoute('loi_littoral');
        }


        return $this->render('loi/littoral.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/littoral/{code_insee}', name: 'api_littoral', options: ['expose' => true])]
    public function apiLittoral($code_insee, EntityManagerInterface $em): Response
    {
        $littoral = $em->getRepository(Littoral::class)->findOneBy(['insee_com' => $code_insee]);
        if ($littoral) {
            return $this->json($littoral, 200, [], ['groups' => 'json']);
        }
        return $this->json('Non présent', 200);
    }

    #[Route('/montagne', name: 'montagne')]
    public function montagne(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, ['label' => 'Fichier'])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-success']])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $em->getRepository(Montagne::class)->truncate();

            // Read CSV file
            $csv = array_map('str_getcsv', file($file));
            foreach ($csv as $line) {
                if ($line[0] == 'ANNEE_COG') {
                    continue;
                }
                $montagne = new Montagne();
                $montagne->setAnneeCog($line[0]);
                $montagne->setInseeReg2016($line[1]);
                $montagne->setNomReg2016($line[2]);
                $montagne->setInseeDep($line[3]);
                $montagne->setNomDept($line[4]);
                $montagne->setInseeCom($line[5]);
                $montagne->setNomCom($line[6]);
                $montagne->setReglementation($line[7]);
                $em->persist($montagne);
            }
            $em->flush();
            $this->addFlash('success', 'Fichier importé avec succès');
            return $this->redirectToRoute('loi_montagne');
        }


        return $this->render('loi/montagne.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/montagne/{code_insee}', name: 'api_montagne', options: ['expose' => true])]
    public function apiMontagne($code_insee, EntityManagerInterface $em): Response
    {
        $montagne = $em->getRepository(Montagne::class)->findOneBy(['insee_com' => $code_insee]);
        if ($montagne) {
            return $this->json($montagne, 200, [], ['groups' => 'json']);
        }
        return $this->json('Non présent', 200);
    }
}
