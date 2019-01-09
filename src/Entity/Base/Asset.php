<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 28/10/2018
 * Time: 1:15 PM
 */

namespace App\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraint as Assert;

/**
 * Class Asset
 * @package App\Entity\Base
 * @ORM\Table(name="asset")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="asset_type", type="string")
 */

class Asset {
    public const CREATE_ACCESS = "create";
    public const READ_ACCESS = "read";
    public const UPDATE_ACCESS = "update";
    public const DELETE_ACCESS = "delete";
    /**
     * @var int
     * @ORM\Column(type="integer", length=11)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(type="string", length=1024)
     */
    private $assetPath;

    /**
     * @var string
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $mimeType;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAssetPath(): string {
        return $this->assetPath;
    }

    /**
     * @param string $assetPath
     * @return Asset
     */
    public function setAssetPath(string $assetPath): Asset {
        $this->assetPath = $assetPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return Asset
     */
    public function setMimeType(string $mimeType): Asset
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    static public function getFolder() {
        return "";
    }

}