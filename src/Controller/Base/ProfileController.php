<?php

namespace App\Controller\Base;

use App\Entity\Base\Directory\DirectoryGroup;
use App\Entity\Base\Directory\User;
use App\FormType\Base\UserForm;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends Controller {
    /**
     * @Route("/profile", name="profile_edit")
     */
    public function edit(Request $request, UserPasswordEncoderInterface $encoder) {
        $self = $this->getUser();
        $form = $this->createForm(UserForm::class, $self, [
            "allow_username" => false,
            "validation_groups" => [
                "Default"
            ]
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $self = $form->getData();
            $password = $self->getPlainPassword();
            if ($password) {
                $password = $encoder->encodePassword($self, $self->getPlainPassword());
                $self->setPassword($password);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($self);
            $em->flush();
        }
        return $this->render('render/simple_form.html.twig', [
            "title" => "Profile Management",
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/register", name="profile_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $form = $this->createForm(UserForm::class, $user, [
            "attr" => [
                "novalidate" => true
            ]
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* @var User $user */
            $repo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
            $userGroup = $repo->findOneBy([
                "shortStr" => "user_group"
            ]);
            $user = $form->getData();
            $pw = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($pw);
            $userGroup->addChild($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userGroup);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("security_login");
        }
        return $this->render('render/simple_form.html.twig', [
            "title" => "Register Account",
            "form" => $form->createView()
        ]);
    }
}
