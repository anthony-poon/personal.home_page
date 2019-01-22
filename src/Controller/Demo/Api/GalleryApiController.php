<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 24/12/2018
 * Time: 2:06 PM
 */

namespace App\Controller\Demo\Api;

use App\Entity\Base\User;
use App\Entity\Demo\GalleryAsset;
use App\Entity\Demo\GalleryItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
class GalleryApiController extends AbstractController {
    /**
     * @Route("/api/gallery/gallery-items", name="api_gallery_get_gallery_items", methods={"GET"})
     * @return Response
     */
    public function getGalleryItems() {
        $repo = $this->getDoctrine()->getRepository(GalleryItem::class);
        $gItems = $repo->findAll();
        $rtn = [];
        foreach ($gItems as $gItem) {
            /* @var GalleryItem $gItem */
            $rtn[] = $this->normalizeGalleryItem($gItem);
        }
        return new JsonResponse($rtn);
    }

    /**
     * @Route("/api/gallery/gallery-items/{id}", name="api_gallery_get_gallery_item", methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function getGalleryItem(int $id) {
        $repo = $this->getDoctrine()->getRepository(GalleryItem::class);
        $galleryItem = $repo->find($id);
        return new JsonResponse($this->normalizeGalleryItem($galleryItem));
    }

    /**
     * @Route("/api/gallery/gallery-assets/{id}", name="api_gallery_get_gallery_asset", methods={"GET"})
     * @param int $id
     * @param ParameterBagInterface $bag
     * @param Request $request
     * @return Response|NotFoundHttpException
     */
    public function getGalleryAsset(int $id, ParameterBagInterface $bag, Request $request) {
        $repo = $this->getDoctrine()->getRepository(GalleryAsset::class);
        $asset = $repo->find($id);
        $this->denyAccessUnlessGranted(GalleryAsset::READ_ACCESS, $asset);
        $folder = $bag->get("assets_path").DIRECTORY_SEPARATOR.$asset->getFolder();
        $isThumbnail = (bool) $request->get("thumbnail");
        if ($asset) {
            if ($isThumbnail) {
                $path = $asset->getThumbnailPath();
            } else {
                $path = $asset->getAssetPath();
            }
            $path = realpath($folder."/".$path);
            $rsp = new BinaryFileResponse($path);
            return $rsp;
        } else {
            return new NotFoundHttpException("Unable to locate entity");
        }
    }

    private function normalizeGalleryItem(GalleryItem $galleryItem): array {
        $assets = $galleryItem->getAssets()->map(function (GalleryAsset $asset) {
            return $this->generateUrl("api_gallery_get_gallery_asset", [
                "id" => $asset->getId()
            ]);
        })->toArray();
        return [
            "id" => $galleryItem->getId(),
            "header" => $galleryItem->getHeader(),
            "content" => $galleryItem->getContent(),
            "owner" => $galleryItem->getOwner() ? $galleryItem->getOwner()->getFullName() : null,
            "assets" => $assets,
            "thumbnail" => $this->generateUrl("api_gallery_get_gallery_asset", [
                "id" => $galleryItem->getAssets()->first()->getId(),
                "thumbnail" => true
            ]),
            "likes" => $galleryItem->getLikes()->count(),
            "isLiked" => $galleryItem->getLikes()->contains($this->getUser()),
            "canDelete" => $this->isGranted(GalleryItem::DELETE_ACCESS, $galleryItem),
            "canEdit" => $this->isGranted(GalleryItem::UPDATE_ACCESS, $galleryItem)
        ];
    }

    /**
     * @Route("/api/gallery/gallery-items", name="api_gallery_create_gallery_item", methods={"POST"})
     * @throws \Exception
     */
    public function createGalleryItem(Request $request, ParameterBagInterface $bag) {
        $photoName = $request->request->get("name"); // Can be null
        $files = $request->files->all();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $gItem = new GalleryItem();
        $gItem->setHeader($photoName);
        if ($user instanceof User) {
            $gItem->setOwner($user);
        }
        if (empty($files)) {
            // Need to report why cannot upload
            throw new \Exception("Unable to read uploaded files");
        }
        foreach ($files as $file) {
            /* @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $gAsset = GalleryAsset::createFromImage($file->getRealPath(), $bag->get("assets_path"));
            $gAsset->setGalleryItem($gItem);
            $em->persist($gAsset);
        }
        $em->persist($gItem);
        $em->flush();
        return new JsonResponse([
            "status" => "success"
        ]);
    }


    /**
     * @Route("/api/gallery/gallery-items/{id}", name="api_gallery_delete_gallery_item", methods={"DELETE"})
     */
    public function deleteGalleryItem(int $id, ParameterBagInterface $bag) {
        $repo = $this->getDoctrine()->getRepository(GalleryItem::class);
        /* @var GalleryItem %gItem */
        $gItem = $repo->find($id);
        $this->isGranted(GalleryItem::DELETE_ACCESS, $gItem);
        /* @var \App\Entity\Demo\GalleryAsset $gAsset */
        $gAsset = $gItem->getAsset();
        $em = $this->getDoctrine()->getManager();
        $em->remove($gItem);
        $em->remove($gAsset);
        $path = $bag->get("assets_path").GalleryAsset::getFolder().DIRECTORY_SEPARATOR.$gAsset->getAssetPath();
        $em->flush();
        unlink($path);
        return new JsonResponse([
            "status" => "success"
        ]);
    }

    /**
     * @Route("/api/gallery/gallery-items/{id}/likes", name="api_gallery_like_gallery_item", methods={"POST"})
     */
    public function likeGalleryItem(int $id) {
        $repo = $this->getDoctrine()->getRepository(GalleryItem::class);
        /* @var \App\Entity\Demo\GalleryItem $gItem */
        $gItem = $repo->find($id);
        $this->denyAccessUnlessGranted(GalleryItem::LIKE_ACCESS, $gItem);
        if (!$gItem->getLikes()->contains($this->getUser())) {
            $gItem->addLikes($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($gItem);
            $em->flush();
        }
        return new JsonResponse([
            "status" => "success"
        ]);
    }

    /**
     * @Route("/api/gallery/gallery-items/{id}/likes", name="api_gallery_unlike_gallery_item", methods={"DELETE"})
     */
    public function unlikeGalleryItem(int $id) {
        $repo = $this->getDoctrine()->getRepository(GalleryItem::class);
        /* @var \App\Entity\Demo\GalleryItem $gItem */
        $gItem = $repo->find($id);
        $this->denyAccessUnlessGranted(GalleryItem::LIKE_ACCESS, $gItem);
        if ($gItem->getLikes()->contains($this->getUser())) {
            $gItem->getLikes()->removeElement($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($gItem);
            $em->flush();
        }
        return new JsonResponse([
            "status" => "success"
        ]);
    }
}