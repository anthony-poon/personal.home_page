<?php

namespace App\Controller\Gallery;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController {
    /**
     * @Route("/gallery", name="gallery_index")
     * @Route("/gallery/{id}", requirements={"id"="\d+"})
     */
    public function index() {
        return $this->render('render/gallery/index.html.twig');
    }
}
