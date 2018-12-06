<?php

namespace App\Controller\Base;

use App\Entity\Base\Directory\User;
use App\FormType\Base\UserForm;
use App\Service\BaseTemplateHelper;
use App\Service\EntityTableHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller {
    public function __construct(BaseTemplateHelper $helper, RouterInterface $router) {
        $helper->setTitle("Administration");
    }

    /**
     * @Route("/admin/users", name="user_list")
     */
    public function list(EntityTableHelper $helper, RouterInterface $router) {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        $helper->addButton("Create", "user_create");
        $helper->addButton("Edit", "user_edit");
        $helper->addButton("Delete", "user_delete");
        $helper->setHeader([
            "#",
            "Username",
            "Full Name",
            "Email"
        ]);
        $helper->setTitle("Users");
        foreach ($users as $u) {
            /* @var User $u */
            $helper->addRow($u->getId(), [
                $u->getId(),
                $u->getUsername(),
                $u->getFullName(),
                $u->getEmail()
            ]);
        }
        return $this->render("render/entity_table.html.twig", $helper->compile());
    }

    /**
     * @Route("/admin/users/create", name="user_create")
     */
    public function create(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* @var User $user */
            $user = $form->getData();
            $pw = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($pw);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("user_list");
        }
        return $this->render("render/simple_form.html.twig", [
            "title" => "Create User",
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="user_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, UserPasswordEncoderInterface $encoder, int $id) {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);
        if (!$user) {
            throw new NotFoundHttpException("Unable to locate entity.");
        }
        $form = $this->createForm(UserForm::class, $user, [
            "allow_username" => false,
            "validation_groups" => "Default"
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* @var User $user */
            $user = $form->getData();
            $pw = $user->getPlainPassword();
            if ($pw) {
                $pw = $encoder->encodePassword($user, $pw);
                $user->setPassword($pw);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("user_list");
        }
        return $this->render("render/simple_form.html.twig", [
            "title" => "Edit User",
            "form" => $form->createView()
        ]);
    }

	/**
	 * @Route("/admin/users/delete/{id}", name="user_delete")
	 */
	public function deleteUser(int $id) {
		$repo = $this->getDoctrine()->getRepository(User::class);
		$user = $repo->find($id);
		if (!$user) {
			throw new NotFoundHttpException("Unable to locate entity.");
		}
		$em = $this->getDoctrine()->getManager();
		$em->remove($user);
		$em->flush();
		return $this->redirectToRoute("user_list");
	}


}
