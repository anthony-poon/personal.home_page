<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 24/12/2018
 * Time: 3:23 PM
 */

namespace App\Service\Normalizer;

use App\Entity\Demo\GalleryAsset;
use App\Entity\Demo\GalleryItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class GalleryItemNormalizer implements NormalizerInterface {
    private $router;
    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    public function normalize($object, $format = null, array $context = array()) {
        /* @var \App\Entity\Demo\GalleryItem $object */
        return [
            "id" => $object->getId(),
            "header" => $object->getHeader(),
            "content" => $object->getContent(),
            "assets" => $object->getAssets()->map(function(GalleryAsset $asset) {
                return $this->router->generate("api_asset_get_item", [
                    "id" => $asset->getId()
                ]);
            })->toArray(),
        ];
    }

    public function supportsNormalization($data, $format = null) {
        return $data instanceof GalleryItem;
    }
}