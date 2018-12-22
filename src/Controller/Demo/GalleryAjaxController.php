<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 29/10/2018
 * Time: 1:30 PM
 */

namespace App\Controller\Demo;

use App\Entity\Base\Asset;
use App\Entity\Demo\GalleryAsset;
use App\Entity\Demo\GalleryItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GalleryAjaxController extends AbstractController{
    /**
     * @Route("/api/gallery", name="gallery_ajax_index")
     */
    public function index(Request $request, ValidatorInterface $validator) {
        $json = json_decode($request->getContent(), true);
        $constrain = new Assert\Collection([
            'header' => new Assert\NotBlank(),
            'content' => new Assert\NotBlank(),
            'base64_file' => new Assert\NotBlank()
        ]);
        $errors = $validator->validate($json, $constrain);
        if (count($errors) == 0) {
            $galleryItem = new GalleryItem();
            $galleryItem->setHeader($json["header"]);
            $galleryItem->setContent($json["content"]);
            preg_match("/^(data:(.+);.+,)?(.+)$/", $json["base64_file"], $match);
            $mime = $match[2] ?? null;
            $data = $match[3];
            $asset = new GalleryAsset();
            $asset->setBase64($data);
            $asset->setMimeType($mime);
            $asset->setGalleryItem($galleryItem);
            $em = $this->getDoctrine()->getManager();
            $em->persist($galleryItem);
            $em->persist($asset);
            $em->flush();
            return new JsonResponse([
                "status" => "success"
            ]);
        } else {
            $rsp = [
                "status" => "error",
                "errors" => []
            ];
            foreach ($errors as $error) {
                $rsp["errors"][] = [
                    "field" => $error->getPropertyPath(),
                    "error" =>$error->getMessage()
                ];
            }
            return new JsonResponse($rsp, 500);
        }
    }
}