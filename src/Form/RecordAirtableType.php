<?php

namespace App\Form;

use App\Entity\RecordAirtable;
use App\Entity\Relais;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class RecordAirtableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TYPUrba')
            ->add('RPG')
            ->add('TYPDisRacc')
            ->add('TYPCapRacc')
            ->add('TYPNomRacc')
            ->add('TYPVilleRacc')
            ->add('Relais', Select2EntityType::class, [
                'class' => Relais::class,
                'remote_route' => 'app_relais_select2',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecordAirtable::class,
        ]);
    }
}
