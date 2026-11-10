<?php
//L'auteur et le salon seront automatiquement définis par notre contrôleur
//  L'utilisateur ne doit pas pouvoir choisir un autre auteur ou salon dans le formulaire
namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('contenu', TextareaType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le message ne peut pas être vide.'),
                    new Length(
                        max: 2000,
                        maxMessage: 'Le message ne peut pas dépasser 2000 caractères.'
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}