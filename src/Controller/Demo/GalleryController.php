<?php

namespace App\Controller\Demo;

use App\Entity\Base\Asset;
use App\Entity\Demo\GalleryAsset;
use App\Entity\Demo\GalleryItem;
use App\FormType\Form\Demo\GalleryItemForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
