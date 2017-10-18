<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                "label" => "Prénom",
                "attr" => ["placeholder" => "Prénom"],
                "required" => true
            ])
            ->add('name', TextType::class, [
                "label" => "Nom",
                "attr" => ["placeholder" => "Nom"],
                "required" => true
            ])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => ["placeholder" => "Email"],
                "required" => true
            ])
            ->add('plainPassword', RepeatedType::class, [
                "type" => PasswordType::class,
                "first_options" => ["label" => "Votre mot de passe", "attr" => ["placeholder" => "Mot De Passe"], "required" => true],
                "second_options" => ["label" => "Confirmez votre mot de passe", "attr" => ["placeholder" => "Mot De Passe"]]
            ])
            ->add("submit", SubmitType::class, [
                "label" => "Valider",
                "attr" => ["class" => "btn btn-primary"]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Author'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_author';
    }


}
