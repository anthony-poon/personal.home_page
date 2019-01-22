<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 18/1/2019
 * Time: 3:07 PM
 */

namespace App\Entity\Base;

use App\Entity\Base\Directory\DirectoryGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserGroup
 * @package App\Entity\Base
 * @ORM\Entity()
 * @ORM\Table()
 */
class UserGroup extends DirectoryGroup {

    /**
     * @var string
     * @ORM\Column(type="string", length=256)
     */
    private $groupName;



    /**
     * @return string
     */
    public function getGroupName(): string {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     * @return UserGroup
     */
    public function setGroupName(string $groupName): UserGroup {
        $this->groupName = $groupName;
        return $this;
    }

    function getDOName(): string {
        return $this->getGroupName();
    }
}