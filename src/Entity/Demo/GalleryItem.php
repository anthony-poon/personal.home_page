<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 28/10/2018
 * Time: 12:20 PM
 */

namespace App\Entity\Demo;

use App\Entity\Base\Directory\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class GalleryItem
 * @package App\Entity\Demo
 * @ORM\Table(name="gallery_item")
 * @ORM\Entity()
 */
class GalleryItem {
    public const CREATE_ACCESS = "create";
    public const READ_ACCESS = "read";
    public const UPDATE_ACCESS = "update";
    public const DELETE_ACCESS = "delete";
    public const LIKE_ACCESS = "like";
    /**
     * @var int
     * @ORM\Column(type="integer", length=11)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\Base\Directory\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(
     *     min=1,
     *     max=64,
     *     minMessage="Cannot be empty",
     *     maxMessage="Cannot be longer than 64 characters"
     * )
     */
    private $header;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @var GalleryAsset
     * @ORM\OneToOne(targetEntity="GalleryAsset", inversedBy="galleryItem", cascade={"remove"})
     */
    private $asset;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Base\Directory\User")
     * @ORM\JoinTable(name="user_like_mapping",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="gallery_item_id", referencedColumnName="id")}
     * )
     */
    private $likes;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isApproved = false;

    public function __construct() {
        $this->likes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getOwner(): ?User {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return GalleryItem
     */
    public function setOwner(?User $owner): GalleryItem {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeader(): ?string {
        return $this->header;
    }

    /**
     * @param string $header
     * @return GalleryItem
     */
    public function setHeader(?string $header): GalleryItem {
        $this->header = $header;
        return $this;
    }

    /**
     * @return User
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string $content
     * @return GalleryItem
     */
    public function setContent(?string $content): GalleryItem {
        $this->content = $content;
        return $this;
    }

    /**
     * @return GalleryAsset
     */
    public function getAsset(): GalleryAsset {
        return $this->asset;
    }

    public function setAsset(GalleryAsset $asset): GalleryItem {
        $this->asset = $asset;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getLikes(): Collection {
        return $this->likes;
    }

    /**
     * @param User $user
     * @return GalleryItem
     */
    public function addLikes(User $user): GalleryItem {
        $this->likes->add($user);
        return $this;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool {
        return $this->isApproved;
    }

    /**
     * @param bool $isApproved
     * @return GalleryItem
     */
    public function setIsApproved(bool $isApproved): GalleryItem {
        $this->isApproved = $isApproved;
        return $this;
    }


}