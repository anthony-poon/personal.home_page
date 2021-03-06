<?php

namespace App\Controller;

use App\Service\BaseTemplateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {
    /**
     * @Route("/", name="home")
     */
    public function index(BaseTemplateHelper $template) {
        $template->setTitle("Anthony Poon");
        return $this->render('render/index.html.twig');
    }

    /**
     * @Route("/member", name="member_home")
     */
    public function member(BaseTemplateHelper $template) {
        return $this->render('render/index.html.twig');
    }
}


