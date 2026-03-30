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
                'choices' => $options['agences'] ?? [], // tableau ['Nom agence' => ID]
                'placeholder' => 'Sélectionnez votre agence',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('statutDemande', TextType::class, [
                'label' => 'Statut de la demande',
                'attr' => ['class' => 'form-control', 'readonly' => true, 'value' => 'En attente'],
                'mapped' => false // pas lié directement aux données envoyées, tu peux le fixer dans le controller
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'agences' => [], // option pour passer les agences depuis le controller
        ]);
    }
}
