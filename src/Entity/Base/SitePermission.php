<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 17/1/2019
 * Time: 6:00 PM
 */

namespace App\Entity\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AccessToken
 * @package App\Entity\Base
 * @ORM\Table()
 * @ORM\Entity()
 */
class SitePermission extends AbstractPermission {
    /**
     * @var string
     * @ORM\Column(type="string", length=256)
     */
    private $role;

    /**
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }

    /**
     * @param string $role
     * @return SitePermission
     */
    public function setRole(string $role): SitePermission {
        $this->role = $role;
        return $this;
    }


}