<?php

namespace App\Controller\Admin;

use App\Entity\RecordAirtable;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RecordAirtableCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RecordAirtable::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new', 'edit', 'delete')
            ->add('index', 'detail');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('recordId'),
            TextField::new('TYPUrba'),
            TextField::new('TYPDisRacc'),
            TextField::new('TYPCapRacc'),
            TextField::new('TYPNomRacc'),
            TextField::new('TYPVilleRacc'),
            ArrayField::new('TYPEnviro'),
            TextField::new('ZNIEFF1')->hideOnIndex(),
            TextField::new('ZNIEFF2')->hideOnIndex(),
            TextField::new('N2000Habitats')->hideOnIndex(),
            TextField::new('N2000DOiseaux')->hideOnIndex(),
            TextField::new('PNR'),
            TextField::new('TYPPpri'),
            TextField::new('TYPZonePpri'),
            TextareaField::new('TYPInfoComp')->hideOnIndex(),
            TextField::new('Relais')->hideOnIndex(),
            TextField::new('TYPGhi'),
            CodeEditorField::new('MH')->setLanguage('js')->hideOnIndex(),
            CodeEditorField::new('RPG')->setLanguage('js')->hideOnIndex(),
            CodeEditorField::new('ZoneHumide')->setLanguage('js')->hideOnIndex(),
        ];
    }
}
