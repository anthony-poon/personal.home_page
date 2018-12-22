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
    private const PLACEHOLDER_COUNT = 15;
    private const IMG_W_UP_BOUND = 600;
    private const IMG_W_LOW_BOUND = 300;
    private const IMG_H_UP_BOUND = 600;
    private const IMG_H_LOW_BOUND = 300;
    /**
     * @Route("/gallery", name="gallery_index")
     */
    public function index() {
        $repo = $this->getDoctrine()->getRepository(GalleryItem::class);
        foreach ($repo->findAll() as $galleryItem) {
            /* @var GalleryItem $galleryItem */
            $asset = $galleryItem->getAssets()->first();
            $data[] = [
                "header" => $galleryItem->getHeader(),
                "content" => $galleryItem->getContent(),
                "url" => $this->generateUrl("api_asset_get_item", [
                    "id" => $asset->getId()
                ])
            ];
        }
        $form = $this->createForm(GalleryItemForm::class);
        return $this->render('render/demo/gallery/index.html.twig', [
            'data' => $data,
            'form' => $form->createView()
        ]);
    }
}
