<?php

namespace App\Controller\Gallery;

use App\Service\BaseTemplateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController {
    /**
     * @Route("/gallery", name="gallery_index")
     * @Route("/gallery/{id}", requirements={"id"="\d+"})
     */
    public function index(BaseTemplateHelper $template) {
        $template->setTitle("Gallery");
        return $this->render('render/gallery/index.html.twig');
    }
}
