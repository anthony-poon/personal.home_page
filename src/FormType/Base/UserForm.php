<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 5/12/2018
 * Time: 4:24 PM
 */

namespace App\FormType\Base;

use App\Entity\Base\Directory\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        if ($options["allow_username"]) {
            $builder->add("username", TextType::class);
        }
        $builder
            ->add("fullName", TextType::class)
            ->add("plainPassword", RepeatedType::class, array(
                "type" => PasswordType::class,
                "invalid_message" => "Repeat password did not match.",
                "required" => !$options["optional_password"],
                "first_options"  => [
                    "label" => "Password"
                ],
                "second_options" => [
                    "label" => "Repeat Password"
                ],
            ))
            ->add('email', EmailType::class, [
                "required" => false
            ])
            ->add("submit", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => function(FormInterface $form) {
                if ($form->getConfig()->getOption("optional_password")) {
                    return [
                        "Default"
                    ];
                } else {
                    return [
                        "Default",
                        "Registration"
                    ];
                }
            },
            'allow_username' => true,
            "optional_password" => false
        ]);
    }
}