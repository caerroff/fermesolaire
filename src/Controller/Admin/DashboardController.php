<?php

namespace App\Controller\Admin;

use App\Entity\Loi\Littoral;
use App\Entity\Loi\Montagne;
use App\Entity\RecordAirtable;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\RPG;
use App\Entity\Relais;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted("ROLE_USER")]
    public function index(): Response
    {

        return $this->render('admin/dashboard.html.twig');

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Fermesolaire')
            ->setLocales(['fr_FR']);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('CRUD');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Enregistrement Airtable', 'fa fa-database', RecordAirtable::class);
        yield MenuItem::linkToCrud('RPG', 'fa fa-database', RPG::class);
        yield MenuItem::linkToCrud('Loi Littoral', 'fa fa-water', Littoral::class);
        yield MenuItem::linkToCrud('Loi Montagne', 'fa fa-mountain', Montagne::class);

        yield MenuItem::section('Retour');
        yield MenuItem::linkToUrl('Retour au site', 'fa fa-map', '/');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
