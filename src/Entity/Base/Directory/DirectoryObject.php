<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 16/7/2018
 * Time: 10:34 AM
 */

namespace App\Entity\Base\Directory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * Class DirectoryObject
 * @package App\Entity\Base
 * @ORM\Table(name="directory_object")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="object_type", type="string")
 */
abstract class DirectoryObject {
    /**
     * @var int
     * @ORM\Column(type="integer", length=11)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AbstractPermission", mappedBy="bearer")
     */
    private $permissions;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="DirectoryGroup", inversedBy="members")
     */
    private $groups;

    public function __construct() {
        $this->permissions = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    /**
	 * @return int
	 */
	public function getId(): ?int {
		return $this->id;
	}

    /**
     * @return Collection
     */
    public function getPermissions(): Collection {
        return $this->permissions;
    }

    /**
     * @return Collection
     */
    public function getGroups(): Collection {
        return $this->groups;
    }

    public function joinGroups(DirectoryGroup $group): DirectoryObject {
        // Need to detect circular reference
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->getMember()->add($this);
        }
        return $this;
    }

    public function getEffectiveGroups($rtn = []): Collection {
        foreach ($this->getGroups() as $parent) {
            /* @var DirectoryObject $parent */
            if (!in_array($parent, $rtn)) {
                $rtn[] = $parent;
                if (!$parent->getGroups()->isEmpty()) {
                    $rtn = $parent->getEffectiveGroups($rtn)->toArray();
                }
            }
        }
        return new ArrayCollection($rtn);
    }

    public function getEffectivePermissions() : Collection {
        $rtn = $this->getPermissions()->toArray();
        foreach ($this->getEffectiveGroups() as $group) {
            /* @var \App\Entity\Base\Directory\DirectoryObject $group */
            $rtn = array_merge($rtn, $group->getPermissions()->toArray());
        }
        return new ArrayCollection($rtn);
    }

    abstract function getDOName(): string;
}