<?php

namespace App\Command;

use App\Entity\Base\Directory\DirectoryGroup;
use App\Entity\Base\SitePermission;
use App\Entity\Base\User;
use App\Entity\Base\UserGroup;
use App\Entity\Demo\GalleryAsset;
use App\Entity\Demo\GalleryItem;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\Exception\NotReadableException;
use joshtronic\LoremIpsum;
use Proxies\__CG__\App\Entity\Base\Directory\AccessToken;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitCommand extends Command {
    private $em;
    private $passwordEncoder;
    private const GALLERY_ITEM_COUNT = 15;
    private const GALLERY_PIC_PER_ITEM_COUNT = 5;
    private const GALLERY_ASSET_MAX_WIDTH = 2000;
    private const GALLERY_ASSET_MIN_WIDTH = 600;
//    private const GALLERY_ASSET_MAX_HEIGHT = 2250;
//    private const GALLERY_ASSET_MIN_HEIGHT = 350;
    /**
     * @var OutputInterface
     */
    private $output;
    private $lorem;
    private $folder;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $bag, UserPasswordEncoderInterface $passwordEncoder, $name = null) {
        $this->em = $em;
        $this->folder = realpath($bag->get("assets_path"));
        if (!$this->folder) {
            throw new \Exception("Invalid var folder.");
        }
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($name);
    }

    protected function configure() {
        $this->setName("app:init")
            ->setDescription("Create root user and role");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $output->writeln("Creating Admin Group");
        $adminGroup = $this->initGroup("Admin Group");
        $output->writeln("Creating User Group");
        $userGroup = $this->initGroup("User Group");

        $adminGroup->joinGroups($userGroup);

        $output->writeln("Creating Admin Permission");
        $adminPermission = $this->initSitePermission("ROLE_ADMIN");
        $userPermission = $this->initSitePermission("ROLE_USER");

        $adminPermission->setBearer($adminGroup);
        $userPermission->setBearer($userGroup);

        $output->writeln("Creating root users");
        $root = $this->initUser("root", "password");
        $root->joinGroups($adminGroup);

        $this->em->persist($root);
        $this->em->persist($adminGroup);
        $this->em->persist($userGroup);
        $this->em->persist($adminPermission);
        $this->em->persist($userPermission);
        $this->em->flush();

        $output->writeln("Username: root");
        $output->writeln("Password: ".$root->getPlainPassword());
        $output->writeln("Role:");
        foreach ($root->getRoles() as $role) {
            $output->writeln($role);
        }

        $galleryItems = [];
        for ($i = 1; $i <= self::GALLERY_ITEM_COUNT; $i++) {
            $output->writeln("Creating Gallery Item $i");
            $galleryItem = $this->initGalleryItem();
            $galleryItems[] = $galleryItem;
            $this->em->persist($galleryItem);
        }
        $this->em->persist($root);
        $this->em->flush();
    }

    private function initSitePermission(string $roleName) {
        $repo = $this->em->getRepository(SitePermission::class);
        $token = $repo->findOneBy([
            "role" => $roleName
        ]);
        if (!$token) {
            $token = new SitePermission();
            $token->setRole($roleName);
        }
        return $token;
    }

    private function initGroup(string $groupName): DirectoryGroup {
        $repo = $this->em->getRepository(UserGroup::class);
        $group = $repo->findOneBy([
            "groupName" => $groupName
        ]);
        if (!$group) {
            $group = new UserGroup();
            $group->setGroupName($groupName);
        }
        return $group;
    }

    private function initUser(string $username, string $password = "password"): User {
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy([
            "username" => $username
        ]);
        if (!$user) {
            $user = new User();
            $user->setUsername($username);
            $user->setFullName($username);
        }
        $user->setPlainPassword($password);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        return $user;
    }

    /**
     * @throws \Exception
     */
    private function initAsset(): GalleryAsset {
        $width = random_int(self::GALLERY_ASSET_MIN_WIDTH, self::GALLERY_ASSET_MAX_WIDTH);
        $retry = 0;
        $isSuccess = false;
        $gAsset = null;
        while (!$isSuccess && $retry < 5) {
            try {
                $landscaped = rand(0, 1);
                $is4To3 = rand(0, 1); // Either 16 : 9 or 4 : 3
                if ($landscaped) {
                    // Landscaped
                    if ($is4To3) {
                        $height = $width / 4 * 3;
                    } else {
                        $height = $width / 16 * 9;
                    }
                } else {
                    // Portrait
                    if ($is4To3) {
                        $height = $width / 3 * 4;
                    } else {
                        $height = $width / 9 * 16;
                    }
                }
                $url = "https://picsum.photos/$width/$height?random";
                $gAsset = GalleryAsset::createFromImage($url, $this->folder);
                $this->output->writeln("Generated ".$gAsset->getAssetPath());
                $isSuccess = true;
            } catch (NotReadableException $ex) {
                $retry += 1;
                $this->output->writeln("Retry $retry... ");
            }
        }
        if ($gAsset === null) {
            throw new \Exception("Unable to download image after $retry retry");
        }
        return $gAsset;
    }

    /**
     * @throws \Exception
     */
    private function initGalleryItem(): GalleryItem {
        $galleryItem = new GalleryItem();
        $galleryItem->setIsApproved(true);
        if (!$this->lorem) {
            $this->lorem = new LoremIpsum();
        }

        $galleryItem->setHeader(ucwords($this->lorem->words(2)));
        $galleryItem->setContent("Placeholder generated from https://picsum.photos.");
        for ($i = 0; $i < self::GALLERY_PIC_PER_ITEM_COUNT; $i ++) {
            $asset = $this->initAsset();
            $asset->setGalleryItem($galleryItem);
            $this->em->persist($asset);
        }
        $this->em->persist($galleryItem);
        return $galleryItem;
    }
}