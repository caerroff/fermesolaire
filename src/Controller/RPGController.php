<?php

namespace App\Controller;

use App\Repository\RPGRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\RPG;

class RPGController extends AbstractController
{
    #[Route('/api/rpg', name: 'app_rpg', options: ['expose' => true])]
    public function api(RPGRepository $repository): JsonResponse
    {
        $rpg = $repository->findBy(array(), array('value' => 'ASC'));
        $data = [];
        foreach ($rpg as $rpg) {
            $data[] = [$rpg->getValue(), $rpg->getDescription()];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('initRPG', name: 'app_init_rpg')]
    public function initRPG(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->add('rpg', FileType::class, ['label' => 'RPG File'])
            ->add('submit', SubmitType::class, ['label' => 'Submit', 'attr' => ['class' => 'btn btn-success']])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('rpg')->getData();
            // read json file
            $json = file_get_contents($file);
            $json = json_decode($json, true);
            foreach ($json as $value => $description) {
                $em->persist((new RPG())->setValue($value)->setDescription($description));
            }
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('rpg/initRPG.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
