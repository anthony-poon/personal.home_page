<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 4/12/2018
 * Time: 3:34 PM
 */

namespace App\Entity\Base\Directory;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AccessToken
 * @package App\Entity\Base
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="permission_type", type="string")
 */
abstract class AbstractPermission {
    /**
     * @var int
     * @ORM\Column(type="integer", length=11)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DirectoryObject
     * @ORM\ManyToOne(targetEntity="DirectoryObject", inversedBy="permissions")
     */
    private $bearer;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return DirectoryObject
     */
    public function getBearer(): DirectoryObject {
        return $this->bearer;
    }

    /**
     * @param DirectoryObject $bearer
     * @return AbstractPermission
     */
    public function setBearer(DirectoryObject $bearer): AbstractPermission {
        $this->bearer = $bearer;
        if (!$bearer->getPermissions()->contains($this)) {
            $bearer->getPermissions()->add($this);
        }
        return $this;
    }




}