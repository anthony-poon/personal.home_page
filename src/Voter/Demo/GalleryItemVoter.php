<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 28/10/2018
 * Time: 12:28 PM
 */

namespace App\Voter\Demo;


use App\Entity\Base\Asset;
use App\Entity\Base\User;
use App\Entity\Demo\GalleryItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GalleryItemVoter extends Voter {
    const VIEW = 'view';
    const EDIT = 'edit';
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    protected function supports($attribute, $subject) {
        if ($subject && $attribute) {
            switch (get_class($subject)) {
                case GalleryItem::class:
                    return in_array($attribute, [
                        self::VIEW,
                        self::EDIT,
                    ]);
                case Asset::class:
                    /* @var $subject \App\Entity\Base\Asset */
                    return $subject->getNamespace() === "gallery_image" && in_array($attribute, [
                            self::VIEW,
                            self::EDIT,
                        ]);
                default:
                    return false;
            }
        }
        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();
        switch (get_class($subject)) {
            case GalleryItem::class:
                switch ($attribute) {
                    case self::VIEW:
                        return $user instanceof User;
                    case self::EDIT:
                        return true;
                    default:
                        return false;
                }
                break;
            case Asset::class:
                switch ($attribute) {
                    case self::VIEW:
                        return true;
                    case self::EDIT:
                        $repo = $this->em->getRepository(GalleryItem::class);
                        $galleryItems = $repo->findBy([
                            "owner" => $user->getId()
                        ]);

                        foreach ($galleryItems as $galleryItem) {
                            if ($galleryItem->getAssets()->contains($subject)) {
                                return true;
                            }
                        }
                        return false;
                    default:
                        return false;
                }
            default:
                return false;
        }

    }

}