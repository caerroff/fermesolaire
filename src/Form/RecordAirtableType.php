<?php

namespace App\Form;

use App\Entity\RecordAirtable;
use App\Entity\Relais;
use App\Repository\RPGRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\RPG;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RecordAirtableType extends AbstractType
{
    private $entitymanager;

    public function __construct(EntityManagerInterface $entitymanager)
    {
        $this->entitymanager = $entitymanager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recordId', TextType::class, [
                "required" => true,
                "attr" => ["class" => "d-none"]
            ])
            ->add('TYPUrba')
            ->add('latitude', TextType::class, [
                "required" => false,
                "attr" => ["class" => "d-none"]
            ])
            ->add('longitude', TextType::class, [
                "required" => false,
                "attr" => ["class" => "d-none"]
            ])            
            ->add(
                'RPG',
                ChoiceType::class,
                [
                    'multiple' => true,
                    'choices' => [$this->entitymanager->getRepository(RPG::class)->findBy(["isEnable" => "true"], ['value' => 'ASC'])][0],
                    'choice_label' => 'label',
                    'choice_value' => 'id',
                    'validation_groups' => false,
                    'allow_extra_fields' => true,
                    'attr' => ["class" => "h-75"]
                ]
            )
            ->add('TYPDisRacc')
            ->add('TYPCapRacc')
            ->add('TYPNomRacc')
            ->add('TYPVilleRacc')
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
                    'Biotope' => 'Biotope',
                    'Parcs Nationaux' => 'Parcs Nationaux',
                    'Aucun enjeu environnemental' => 'Aucun enjeu environnemental',
                ],
                'expanded' => true,
            ])
            ->add('ZNIEFF1')
            ->add('ZNIEFF2')
            ->add('N2000Habitats')
            ->add('N2000DOiseaux')
            ->add('Biotope')
            ->add('ParcNationaux')
            ->add('PNR')
            ->add('TYPPpri', ChoiceType::class, [
                'choices' => [
                    'OUI' => 'OUI',
                    'NON' => 'NON',
                ]
            ])
            ->add('TYPZonePpri', ChoiceType::class, [
                'choices' => [
                    'Hors interdiction / prescription' => 'Hors interdiction / prescription',
                    'Interdiction' => 'Interdiction',
                    'Prescription' => 'Prescription',
                    'NON' => 'NON',
                ]
            ])
            ->add('TYPGhi')
            ->add('MH', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'Monument historique' => 'Monument historique',
                    'Site classé' => 'Site classé',
                    'Site inscrit' => 'Site inscrit',
                    'Non concerné' => 'Non concerné',
                    'Perimetre de protection aux abords d\'un monument historique' => 'Perimetre de protection aux abords d\'un monument historique',
                    'Site classé en partie' => 'Site classé en partie',
                    'zone de présomption de prescription archéologique' => 'zone de présomption de prescription archéologique',
                    'Patrimoine mondial de l\'UNESCO' => 'Patrimoine mondial de l\'UNESCO',
                ],
                'attr' => [
                    'style' => 'height: 15.5rem;',
                ],
                'expanded' => true,
            ])
            ->add('ZoneHumide', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'Zone humide effective' => 'Zone humide effective',
                    'Zone humide probable assez forte' => 'Zone humide probable assez forte',
                    'Zone humide probable forte' => 'Zone humide probable forte',
                    'Zone humide probable très forte' => 'Zone humide probable très forte',
                    'Proche d\'une zone humide probable' => 'Proche d\'une zone humide probable',
                    'Hors zone' => 'Hors zone',
                ],
                'expanded' => true,
            ])
            ->add('TYPInfoComp', null, [
                'attr' => [
                    'style' => 'height: 15.5rem;',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-lg btn-success text-center'],
                'form_attr' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecordAirtable::class,
            'validation_groups' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
