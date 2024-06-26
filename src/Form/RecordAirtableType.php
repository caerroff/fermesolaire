<?php

namespace App\Form;

use App\Entity\RecordAirtable;
use App\Entity\Relais;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecordAirtableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TYPUrba')
            ->add('RPG', ChoiceType::class, [
                'multiple' => true,
            ])
            ->add('TYPDisRacc')
            ->add('TYPCapRacc')
            ->add('TYPNomRacc')
            ->add('TYPVilleRacc')
            ->add('Relais', EntityType::class, [
                'class' => Relais::class
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-success text-center'],
                'form_attr' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecordAirtable::class,
        ]);
    }
}
