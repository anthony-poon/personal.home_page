<?php
namespace App\Entity\Base\Directory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="directory_group")
 * @ORM\Entity
 */
class DirectoryGroup extends DirectoryObject {

	/**
	 * @var string
	 * @ORM\Column(type="string", length=256)
	 */
	private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=256)
     */
    private $shortStr;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="DirectoryMember", inversedBy="parents")
     */
    private $children;

    public function __construct() {
        parent::__construct();
        $this->children = new ArrayCollection();
    }

    /**
	 * @return string
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return DirectoryGroup
	 */
	public function setName(string $name): DirectoryGroup {
		$this->name = $name;
		return $this;
	}

    /**
     * @return string
     */
    public function getShortStr(): string {
        return $this->shortStr;
    }

    /**
     * @param string $shortStr
     */
    public function setShortStr(string $shortStr): void {
        $this->shortStr = $shortStr;
    }

    /**
     * @return Collection
     */
    public function getChildren() {
        return $this->children;
    }

    public function addChild(DirectoryMember $child): DirectoryGroup {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
        return $this;
    }

    public function setChildren(array $children): DirectoryGroup {
        $this->children = new ArrayCollection($children);
        return $this;
    }
}