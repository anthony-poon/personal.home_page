<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 27/10/2018
 * Time: 11:24 PM
 */

namespace App\Controller\Base;

use App\Entity\Base\Directory\DirectoryGroup;
use App\FormType\Base\DirectoryGroupsForm;
use App\Service\BaseTemplateHelper;
use App\Service\EntityTableHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserGroupController extends AbstractController {
    /**
     * @Route("/admin/user-groups", name="user_group_list")
     */
    public function list(EntityTableHelper $helper, RouterInterface $router) {
        $grpRepo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
        $grps = $grpRepo->findAll();
        $helper->setHeader([
            "#",
            "Group Name",
            "Member count"
        ]);
        foreach ($grps as $g) {
            /* @var DirectoryGroup $g */
            $helper->addRow($g->getId(), [
                $g->getId(),
                $g->getName(),
                $g->getChildren()->count()
            ]);
        }
        $helper->addButton("Create", "user_group_create");
        $helper->addButton("Edit", "user_group_edit");
        $helper->addButton("Delete", "user_group_delete");
        $helper->setTitle("User Groups");
        return $this->render("render/entity_table.html.twig", $helper->compile());
    }

    /**
     * @Route("/admin/user-groups/create", name="user_group_create")
     */
    public function create(BaseTemplateHelper $helper, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(DirectoryGroupsForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $group = $form->getData();
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute("user_group_list");
        }
        return $this->render("render/simple_form.html.twig", [
            "title" => "Create Group",
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user-groups/{id}", name="user_group_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, int $id, PropertyAccessorInterface $accessor) {
        $em = $this->getDoctrine()->getManager();
        $groupRepo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
        $group = $groupRepo->find($id);
        if (empty($group)) {
            throw new NotFoundHttpException("Unable to locate entity.");
        }
        $form = $this->createForm(DirectoryGroupsForm::class, $group);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $group = $form->getData();
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute("user_group_list");
        }
        return $this->render("render/simple_form.html.twig", [
            "title" => "Edit Group",
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user-groups/delete/{id}", name="user_group_delete")
     */
    public function delete(int $id) {
        $repo = $this->getDoctrine()->getRepository(DirectoryGroup::class);
        $grp = $repo->find($id);
        if (!$grp) {
            throw new NotFoundHttpException("Unable to locate entity.");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($grp);
        $em->flush();
        return $this->redirectToRoute("user_group_list");
    }
}