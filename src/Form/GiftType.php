<?php

namespace App\Form;

use App\Entity\Gift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Gift $gift */
        $gift = $options['data'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du prix'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du prix'
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo du prix',
                'data_class' => null,
                'required' => $gift->getPhoto() ? false : true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gift::class,
        ]);
    }
}
