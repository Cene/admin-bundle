<?php
namespace |namespace|\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class |entity|Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('name', 'text')
			->add('save', 'submit', array('label' => 'Create',
				'attr' => array('class'=>'btn btn-primary btn-lg')
			));
    }

    public function getName()
    {
        return '|entity|';
    }
}