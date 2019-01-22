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
use Intervention\Image\Constraint;
use Intervention\Image\ImageManagerStatic as Image;
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
     * @var array
     * @ORM\Column(type="json", nullable=true)
     */
    private $exif;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $thumbnailPath;

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

    /**
     * @return array
     */
    public function getExif(): ?array {
        return $this->exif;
    }

    /**
     * @param array $exif
     */
    public function setExif(?array $exif): void {
        $this->exif = $exif;
    }

    /**
     * @return string
     */
    public function getThumbnailPath(): ?string {
        return $this->thumbnailPath;
    }

    /**
     * @param string $thumbnailPath
     * @return GalleryAsset
     */
    public function setThumbnailPath(?string $thumbnailPath): GalleryAsset {
        $this->thumbnailPath = $thumbnailPath;
        return $this;
    }

    static public function getFolder() {
        return parent::getFolder().DIRECTORY_SEPARATOR."gallery";
    }

    static public function createFromImage($src, string $dstFolder): GalleryAsset {
        $dstFolder = realpath($dstFolder.DIRECTORY_SEPARATOR.GalleryAsset::getFolder());
        if (!$dstFolder || !is_dir($dstFolder)) {
            throw new \Exception("Invalid destination folder.");
        }
        $gAsset = new GalleryAsset();
        $img = Image::make($src);
        $exif = $img->exif();
        $name = md5(uniqid()).".png";
        $img->save("$dstFolder/$name");
        $gAsset->setAssetPath($name);
        $gAsset->setExif($exif);
        $gAsset->setMimeType($img->mime());
        $img->resize(null, 250, function(Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $thumbName = md5(uniqid()).".png";
        $img->save("$dstFolder/$thumbName");
        $gAsset->setThumbnailPath($thumbName);
        return $gAsset;
    }
}