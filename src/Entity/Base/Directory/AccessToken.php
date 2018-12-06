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
 */
class AccessToken {
    /**
     * @var int
     * @ORM\Column(type="integer", length=11)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $token;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="DirectoryObject", mappedBy="accessTokens")
     */
    private $bearers;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @param string $token
     * @return AccessToken
     */
    public function setToken(string $token): AccessToken {
        $this->token = $token;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getBearers(): Collection {
        return $this->bearers;
    }


}