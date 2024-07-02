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
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non'
                ]
            ])
            ->add('TYPDisRacc')
            ->add('TYPCapRacc')
            ->add('TYPNomRacc')
            ->add('TYPVilleRacc')
            ->add('Relais', EntityType::class, [
                'class' => Relais::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un relais',
                'required' => false
            ])
            ->add('TYPEnviro', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'ZNIEFF 1' => 'ZNIEFF 1',
                    'ZNIEFF 2' => 'ZNIEFF 2',
                    'Natura 2000 - Habitats' => 'Natura 2000 - Habitats',
                    'Natura 2000 - Oiseaux' => 'Natura 2000 - Oiseaux',
                    'Parc Naturel Régional' => 'Parc Naturel Régional',
                    'Loi Littoral' => 'Loi Littoral',
                    'Loi Montagne' => 'Loi Montagne',
                    'Aucun enjeu environnemental' => 'Aucun enjeu environnemental',
                ]
            ])
            ->add('ZNIEFF1')
            ->add('ZNIEFF2')
            ->add('N2000Habitats')
            ->add('N2000DOiseaux')
            ->add('PNR')
            ->add('TYPPpri')
            ->add('TYPZonePpri')
            ->add('TYPGhi')
            ->add('MH', ChoiceType::class, [
                'multiple' => true
            ])
            ->add('ZoneHumide')
            ->add('TYPInfoComp')
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
