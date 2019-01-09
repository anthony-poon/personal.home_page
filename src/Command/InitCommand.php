<?php

namespace App\Command;

use App\Entity\Base\Directory\AccessToken;
use App\Entity\Base\Directory\DirectoryGroup;
use App\Entity\Base\Directory\User;
use App\Entity\Demo\GalleryAsset;
use App\Entity\Demo\GalleryItem;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use joshtronic\LoremIpsum;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitCommand extends Command {
    private $em;
    private $passwordEncoder;
    private const GALLERY_ITEM_COUNT = 15;
    private const GALLERY_ASSET_MAX_WIDTH = 1200;
    private const GALLERY_ASSET_MIN_WIDTH = 200;
    private const GALLERY_ASSET_MAX_HEIGHT = 900;
    private const GALLERY_ASSET_MIN_HEIGHT = 200;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $output->writeln("Creating Admin Group");
        $adminGroup = $this->initGroup("Admin Group", "admin_group");
        $output->writeln("Creating User Group");
        $userGroup = $this->initGroup("User Group", "user_group");

        $output->writeln("Creating Admin Token");
        $adminToken = $this->initAccessToken("ROLE_ADMIN");
        $userToken = $this->initAccessToken("ROLE_USER");
        if (!$adminGroup->getAccessTokens()->contains($adminToken)) {
            $adminGroup->getAccessTokens()->add($adminToken);
        }
        if (!$userGroup->getAccessTokens()->contains($userToken)) {
            $userGroup->getAccessTokens()->add($userToken);
        }

        $output->writeln("Creating root users");
        $root = $this->initUser("root", md5(random_bytes(32)));
        $output->writeln("Username: root");
        $output->writeln("Password: ".$root->getPlainPassword());
        $adminGroup->addChild($root);

        $galleryItems = [];
        for ($i = 1; $i <= self::GALLERY_ITEM_COUNT; $i++) {
            $output->writeln("Creating Gallery Item $i");
            $galleryItem = $this->initGalleryItem();
            $galleryItems[] = $galleryItem;
            $this->em->persist($galleryItem);
        }

        $this->em->persist($adminGroup);
        $this->em->persist($userGroup);
        $this->em->persist($adminToken);
        $this->em->persist($userToken);
        $this->em->persist($root);
        $this->em->flush();
    }

    private function initAccessToken(string $tokenStr) {
        $repo = $this->em->getRepository(AccessToken::class);
        $token = $repo->findOneBy([
            "token" => $tokenStr
        ]);
        if (!$token) {
            $token = new AccessToken();
            $token->setToken($tokenStr);
        }
        return $token;
    }

    private function initGroup(string $groupName, string $shortStr): DirectoryGroup {
        $repo = $this->em->getRepository(DirectoryGroup::class);
        $group = $repo->findOneBy([
            "shortStr" => $shortStr
        ]);
        if (!$group) {
            $group = new DirectoryGroup();
            $group->setName($groupName);
            $group->setShortStr($shortStr);
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function initAsset(): GalleryAsset {
        $width = random_int(self::GALLERY_ASSET_MIN_WIDTH, self::GALLERY_ASSET_MAX_WIDTH);
        $height = random_int(self::GALLERY_ASSET_MIN_HEIGHT, self::GALLERY_ASSET_MAX_HEIGHT);
        $url = "https://picsum.photos/$width/$height?random";
        $gAsset = GalleryAsset::createFromImage($url, $this->folder);
        $this->output->writeln("Generated ".$gAsset->getAssetPath());
        return $gAsset;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function initGalleryItem(): GalleryItem {
        $galleryItem = new GalleryItem();
        $galleryItem->setIsApproved(true);
        if (!$this->lorem) {
            $this->lorem = new LoremIpsum();
        }
        $galleryItem->setHeader(ucwords($this->lorem->words(2)));
        $galleryItem->setContent($this->lorem ->paragraphs(1));

        $asset = $this->initAsset();
        $galleryItem->setAsset($asset);
        $this->em->persist($galleryItem);
        $this->em->persist($asset);
        return $galleryItem;
    }
}