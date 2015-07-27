<?php

namespace Brdeju\Bundle\CurrenciesChartsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('username', 'text', array(
                'label' => 'common.label.username'
            ))
            ->add('email', null, array(
                'label' => 'common.label.email'
            ))
            ->add('firstname', null, array(
                'label' => 'common.label.firstname'
            ))
            ->add('lastname', null, array(
                'label' => 'common.label.lastname'
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'security.register.form.password.not_match',
                'required' => true,
                'first_options' => array('label' => 'common.label.password'),
                'second_options' => array('label' => 'common.label.repeat_password'),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Brdeju\Bundle\CurrenciesChartsBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'brdeju_bundle_currencieschartsbundle_user';
    }

}
