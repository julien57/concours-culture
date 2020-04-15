<?php

namespace App\Form;

use App\Entity\Gift;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Quiz $quiz */
        $quiz = $options['data'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du quiz'
            ])
            ->add('theme', TextType::class, [
                'label' => 'Thème du quiz'
            ])
            ->add('atDate',DateTimeType::class, [
                'label' => 'Date début concours',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy HH:mm',
                'view_timezone' => 'Europe/Paris'
            ])
            ->add('atFinish',DateTimeType::class, [
                'label' => 'Date fin concours',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy HH:mm',
                'view_timezone' => 'Europe/Paris'
            ])
            ->add('document', FileType::class, [
                'label' => 'Ajoutez votre document',
                'attr' => [
                    'class' => 'custom-file-input'
                ],
                'label_attr' => [
                    'class' => 'custom-file-label'
                ],
                'data_class' => null,
                'required' => $quiz->getDocument() ? false : true
            ])
            ->add('questions', EntityType::class, [
                'class' => Question::class,
                'choice_label' => 'question',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false
            ])
            ->add('gift', EntityType::class, [
                'class' => Gift::class,
                'placeholder' => 'Prix',
                'required' => false,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
