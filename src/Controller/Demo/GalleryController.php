<?php

namespace App\Controller\Demo;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController {
    /**
     * @Route("/gallery", name="gallery_index")
     * @Route("/gallery/{id}", requirements={"id"="\d+"})
     */
    public function index() {
        return $this->render('render/demo/gallery/index.html.twig');
    }
}
