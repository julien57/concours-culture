<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Response;
use App\Repository\ResponseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConcoursType extends AbstractType
{
    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    public function __construct(ResponseRepository $responseRepository)
    {
        $this->responseRepository = $responseRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $question $question */
        $question = $options['data'];
        $reponses = $this->responseRepository->findBy(['question' => $question]);

        $reponsesNames = [];
        /** @var Response $response */
        foreach ($reponses as $response) {
            $reponsesNames[$response->getResponse()] = $response->getResponse();
        }

        $builder
            ->add('question', ChoiceType::class, [
                'label' => $question->getQuestion(),
                'choices' => $reponsesNames,
                'expanded' => true,
                'multiple' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class
        ]);
    }
}
