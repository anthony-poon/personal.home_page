<?php

namespace App\Controller\Base;

use App\Entity\Base\Asset;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AssetController extends AbstractController {

    /**
     * @Route("/api/assets/{id}", name="api_asset_get_item", methods={"GET"})
     * @param int $id
     * @param Request $request
     * @return Response|NotFoundHttpException
     */
    public function getItem(int $id, ParameterBagInterface $bag, Request $request) {
        $repo = $this->getDoctrine()->getRepository(Asset::class);
        $asset = $repo->find($id);
        $this->denyAccessUnlessGranted(ASSET::READ_ACCESS, $asset);
        $folder = $bag->get("assets_path").DIRECTORY_SEPARATOR.$asset->getFolder();
        if ($asset) {
            $path = realpath($folder."/".$asset->getAssetPath());
            $rsp = new BinaryFileResponse($path);
            return $rsp;
        } else {
            return new NotFoundHttpException("Unable to locate entity");
        }
    }
}
