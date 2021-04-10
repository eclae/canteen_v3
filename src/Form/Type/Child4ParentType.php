<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Meal;
use App\Entity\Child;

class Child4ParentType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('firstName', null, ['label' => 'cantine.form.child.firstname'])
            ->add('lastName', null, ['label' => 'cantine.form.child.lastname'])
            ->add('comment', null, ['label' => 'cantine.form.child.comment', 'attr' => ['rel' => 'tooltip', 'data-placement' => 'right', 'data-title' => 'cantine.form.child.comment']])
            ->add('meal', EntityType::class, ['class' => Meal::class, 'label' => 'cantine.form.child.meal'])
    ;
  }

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults(array(
        'data_class' => Child::class,
    ));
  }

  public function getName() {
    return 'app_child4parenttype';
  }

}
