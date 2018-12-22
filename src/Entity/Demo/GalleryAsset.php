<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 10/12/2018
 * Time: 6:11 PM
 */

namespace App\Entity\Demo;

use App\Entity\Base\Asset;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class GalleryAsset
 * @package App\Entity\Demo
 * @ORM\Entity()
 * @ORM\Table()
 */
class GalleryAsset extends Asset{
    /**
     * @var GalleryItem
     * @ORM\ManyToOne(targetEntity="GalleryItem", inversedBy="assets")
     */
    private $galleryItem;

    /**
     * @return GalleryItem
     */
    public function getGalleryItem(): GalleryItem {
        return $this->galleryItem;
    }

    /**
     * @param GalleryItem $galleryItem
     * @return GalleryAsset
     */
    public function setGalleryItem(GalleryItem $galleryItem): GalleryAsset {
        $this->galleryItem = $galleryItem;
        return $this;
    }


}