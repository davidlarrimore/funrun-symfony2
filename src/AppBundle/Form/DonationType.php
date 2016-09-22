<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use AppBundle\Entity\Student;
use AppBundle\Entity\Teacher;
use Doctrine\ORM\EntityRepository;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DonationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('student', EntityType::class, array(
              'class' => 'AppBundle:Student',
              'query_builder' => function (EntityRepository $er) {
                  return $er->createQueryBuilder('u')->orderBy('u.teacher', 'ASC');
              },
              'choice_label' => 'getStudentAndTeacher',
              ))
            ->add('amount', MoneyType::class, array('required' => true, 'currency' => 'USD'))
            ->add('donated_at', DateType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Donation',
        ));
    }
}