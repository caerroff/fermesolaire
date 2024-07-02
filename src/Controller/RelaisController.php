<?php

namespace App\Controller;

use App\Entity\Relais;
use App\Form\RelaisType;
use App\Repository\RelaisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/relais')]
class RelaisController extends AbstractController
{
    #[Route('/', name: 'app_relais_index', methods: ['GET'])]
    public function index(RelaisRepository $relaisRepository): Response
    {
        return $this->render('relais/index.html.twig', [
            'relais' => $relaisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_relais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $relai = new Relais();
        $form = $this->createForm(RelaisType::class, $relai);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($relai);
            $entityManager->flush();

            return $this->redirectToRoute('app_relais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('relais/new.html.twig', [
            'relai' => $relai,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_relais_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Relais $relai): Response
    {
        return $this->render('relais/show.html.twig', [
            'relai' => $relai,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_relais_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Relais $relai, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RelaisType::class, $relai);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_relais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('relais/edit.html.twig', [
            'relai' => $relai,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_relais_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Relais $relai, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $relai->getId(), $request->request->get('_token'))) {
            $entityManager->remove($relai);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_relais_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/select2', name: 'app_relais_select2', options: ['expose' => true])]
    public function select2(RelaisRepository $relaisRepository): Response
    {
        $relais = $relaisRepository->findAll();
        return $this->json($relais, 200, [], ['groups' => 'select2']);
    }
}
