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
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
    #[IsGranted("ROLE_USER")]
    public function initRPG(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->add('rpg', FileType::class, ['label' => 'RPG File'])
            ->add('submit', SubmitType::class, ['label' => 'Submit', 'attr' => ['class' => 'btn btn-success']])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('rpg')->getData();
            // read csv file
            $values = array_map('str_getcsv', file($file));
            foreach ($values as $value) {
                $line = str_getcsv($value[0], ';', '');
                if($line[0] == "CODE_CULTURE"){
                    continue;
                }
                $em->persist((new RPG())->setValue($line[0])->setDescription($line[1])->setIsEnable(false));
            }
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('rpg/initRPG.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
