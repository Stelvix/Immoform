<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        // Récupération des agences passées en option depuis le controller
        $agences = $options['agences'] ?? []; 
        $builder
            ->add('objet', TextType::class, [
                'label' => 'Objet de la formation',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Formation C# et .NET Core'],
            ])
            ->add('typeFormation', TextType::class, [
                'label' => 'Type de formation',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Technique, Management...'],
            ])
            ->add('descriptionBesoins', TextareaType::class, [
                'label' => 'Description des besoins',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Décrivez vos besoins'],
            ])
            ->add('nombreParticipants', IntegerType::class, [
                'label' => 'Nombre de participants',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateSouhaitee', DateType::class, [
                'label' => 'Date souhaitée',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('agenceID', ChoiceType::class, [
                'label' => 'Agence',
                'choices' => array_combine(
                    array_column($agences, 'nom'),
                    array_column($agences, 'agenceID')
                ),
                'placeholder' => 'Sélectionnez votre agence',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('statutDemande', TextType::class, [
                'label' => 'Statut de la demande',
                'data' => 'En attente',
                'attr' => [
                'class' => 'form-control',
                'readonly' => 'readonly',
    ],
]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'agences' => [], // option pour passer les agences depuis le controller
        ]);
    }
}
