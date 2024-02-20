<?php

namespace App\Form;

use App\Entity\Deposer;
use App\Entity\Persona;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NomDuGroupe')
            ->add('Origine')
            ->add('Ville')
            ->add('AnneeDebut')
            ->add('AnneeSeparation')
            ->add('Fodateurs')
            ->add('Members')
            ->add('CourantMusical')
            ->add('Presentation')
            ->add('deposer', EntityType::class, [
                'class' => Deposer::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Persona::class,
        ]);
    }
}
