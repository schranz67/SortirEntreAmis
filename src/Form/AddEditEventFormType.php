<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AddEditEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => '--- Choisir une catégorie ---'
            ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => new \DateTime(),
                        'message' => 'La date de l’évènement doit être postérieure à la date actuelle.',
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => (new \DateTime())->modify('+1 year'),
                        'message' => 'La date de l’évènement ne peut pas dépasser un an à l’avance.',
                    ]),
                ],
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
                'placeholder' => '--- Choisir un lieu ---'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 5, 'cols' => 40],
                'required' => false])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
