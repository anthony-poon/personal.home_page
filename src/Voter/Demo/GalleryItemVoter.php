<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 28/10/2018
 * Time: 12:28 PM
 */

namespace App\Voter\Demo;

use App\Entity\Base\Directory\User;
use App\Entity\Demo\GalleryAsset;
use App\Entity\Demo\GalleryItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GalleryItemVoter extends Voter {
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    protected function supports($attribute, $subject) {
        if ($subject && $attribute) {
            switch (get_class($subject)) {
                case GalleryItem::class:
                    return in_array($attribute, [
                        GalleryItem::CREATE_ACCESS,
                        GalleryItem::READ_ACCESS,
                        GalleryItem::UPDATE_ACCESS,
                        GalleryItem::DELETE_ACCESS,
                        GalleryItem::LIKE_ACCESS
                    ]);
                case GalleryAsset::class:
                    /* @var $subject \App\Entity\Demo\GalleryAsset */
                    return in_array($attribute, [
                        GalleryAsset::CREATE_ACCESS,
                        GalleryAsset::READ_ACCESS,
                        GalleryAsset::UPDATE_ACCESS,
                        GalleryAsset::DELETE_ACCESS,

                    ]);
                default:
                    return false;
            }
        }
        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();
        $isLoggedIn = $user instanceof User;
        /* @var User $user */
        $isAdmin = $isLoggedIn && in_array("ROLE_ADMIN", $user->getRoles());
        switch (get_class($subject)) {
            case GalleryItem::class:
                switch ($attribute) {
                    case GalleryItem::CREATE_ACCESS:
                    case GalleryItem::READ_ACCESS:
                        return true;
                    case GalleryItem::UPDATE_ACCESS:
                    case GalleryItem::DELETE_ACCESS:
                        /* @var \App\Entity\Demo\GalleryItem $subject */
                        return $isAdmin || ($isLoggedIn && $subject->getOwner() === $user);
                    case GalleryItem::LIKE_ACCESS:
                        return $user instanceof User && $subject->getOwner() !== $user;
                    default:
                        return false;
                }
                break;
            case GalleryAsset::class:
                switch ($attribute) {
                    case GalleryAsset::CREATE_ACCESS:
                    case GalleryAsset::READ_ACCESS:
                        return true;
                    case GalleryAsset::UPDATE_ACCESS:
                    case GalleryAsset::DELETE_ACCESS:
                    /* @var \App\Entity\Demo\GalleryAsset $subject */
                        return $isAdmin || ($isLoggedIn && $subject->getGalleryItem()->getOwner() === $user);
                    default:
                        return false;
                }
            default:
                return false;
        }

    }

}