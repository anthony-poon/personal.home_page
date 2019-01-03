<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 24/12/2018
 * Time: 2:06 PM
 */

namespace App\Controller\Demo\Api;

use App\Entity\Demo\GalleryItem;
use App\Service\Normalizer\GalleryItemNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class GalleryApiController extends AbstractController {
    /**
     * @Route("/api/gallery/gallery-item", name="api_gallery_get_gallery_items", methods={"GET"})
     * @param GalleryItemNormalizer $normalizer
     * @return Response
     */
    public function getGalleryItems(GalleryItemNormalizer $normalizer) {
        $repo = $this->getDoctrine()->getRepository(GalleryItem::class);
        $galleryItems = $repo->findAll();
        $serializer = new Serializer(
            [ $normalizer ],
            [ new JsonEncoder() ]
        );
        return new Response($serializer->serialize($galleryItems, "json"));
    }

    /**
     * @Route("/api/gallery/gallery-item/{id}", name="api_gallery_get_gallery_item", methods={"GET"})
     * @param GalleryItemNormalizer $normalizer
     * @param int $id
     * @return Response
     */
    public function getGalleryItem(GalleryItemNormalizer $normalizer, int $id) {
        $repo = $this->getDoctrine()->getRepository(GalleryItem::class);
        $galleryItem = $repo->find($id);
        $serializer = new Serializer(
            [ $normalizer ],
            [ new JsonEncoder() ]
        );
        return new Response($serializer->serialize($galleryItem, "json"));
    }

    /**
     * @Route("/api/gallery/gallery-item", name="api_gallery_create_gallery_item", methods={"POST"})
     */
    public function createGalleryItem(Request $request) {
        var_dump($request->files->all());
        exit();
    }
}