<?php
// src/AppBundle/Form/RegistrationFormType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('invitation', 'AppBundle\Form\InvitationFormType');

        // Or for Symfony < 2.8
        // $builder->add('invitation', 'app_invitation_type');
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // Not necessary on Symfony 3+
    public function getName()
    {
        return 'app_user_registration';
    }
}
