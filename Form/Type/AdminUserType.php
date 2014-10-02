<?php
namespace Mesalab\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('username', 'text')
			->add('password', 'password')
			->add('email', 'text')
			->add('save', 'submit', array('label' => 'Create',
				'attr' => array('class'=>'btn btn-primary btn-lg')
			));
    }

    public function getName()
    {
        return 'adminuser';
    }
}