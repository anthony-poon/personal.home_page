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
     * @ORM\ManyToMany(targetEntity="AccessToken", inversedBy="bearers", indexBy="token")
     * @ORM\JoinTable(name="access_token_mapping")
     */
    private $accessTokens;

    public function __construct() {
        $this->accessTokens = new ArrayCollection();
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
    public function getAccessTokens(): Collection {
        return $this->accessTokens;
    }

    /**
     * @param AccessToken $accessTokens
     */
    public function addAccessTokens(AccessToken $accessTokens): void {
        $this->accessTokens = $this->getAccessTokens()->add($accessTokens);
    }
}