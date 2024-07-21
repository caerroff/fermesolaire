<?php

namespace App\Controller\Admin;

use App\Entity\RPG;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RPGCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RPG::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('value'),
            TextField::new('description'),
        ];
    }
}
