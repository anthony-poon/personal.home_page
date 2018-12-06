<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 4/12/2018
 * Time: 7:43 PM
 */

namespace App\Entity\Base\Directory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class DirectoryMember
 * @package App\Entity\Base
 * @ORM\Table()
 * @ORM\Entity()
 */
class DirectoryMember extends DirectoryObject {
    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="DirectoryGroup", mappedBy="children", indexBy="shortStr")
     */
    private $parents;

    public function __construct() {
        parent::__construct();
        $this->parents = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getParents(): Collection {
        return $this->parents;
    }

    public function getEffectiveAccessTokens(): Collection {
        $rtn = $this->getAccessTokens();
        foreach ($this->getParents() as $parent) {
            /* @var DirectoryGroup $parent */
            foreach ($parent->getAccessTokens() as $token) {

                if (!$rtn->contains($token)) {
                    var_dump($token);
                    $rtn->add($token);
                }
            }
        }
        return $rtn;
    }
}