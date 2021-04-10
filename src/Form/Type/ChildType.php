<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Child;
use App\Entity\Grade;
use App\Entity\Meal;
use App\Entity\User;

class ChildType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('firstName', null, ['label' => 'cantine.form.child.firstname', 'required' => true])
            ->add('lastName', null, ['label' => 'cantine.form.child.lastname', 'required' => true])
            ->add('comment', null, ['label' => 'cantine.form.child.comment', 'required' => false, 'attr' => ['rel' => 'tooltip', 'data-placement' => 'right', 'data-title' => 'cantine.form.child.comment']])
            ->add('principalUser', EntityType::class, ['class' => User::class, 'label' => 'cantine.form.child.principalUser', 'multiple' => false, 'required' => false, 'attr' => ['style' => 'width: 200px']])
            ->add('users', EntityType::class, ['class' => User::class, 'label' => 'cantine.form.child.ger', 'multiple' => true, 'expanded' => false, 'by_reference' => false, 'attr' => ['style' => 'width: 200px'], 'required' => false])
            ->add('grade', EntityType::class, ['class' => Grade::class, 'label' => 'cantine.form.child.grade'])
            ->add('meal', EntityType::class, ['class' => Meal::class, 'label' => 'cantine.form.child.meal'])
    ;
  }

  ////public function setDefaultOptions(OptionsResolverInterface $resolver) {
   public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults(array(
       'data_class' => Child::class,
    ));
  }

  public function getName() {
    return 'app_childtype';
  }

}
