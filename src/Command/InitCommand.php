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
    private const GALLERY_ASSET_PER_ITEM_COUNT = 3;
    private const GALLERY_ASSET_COUNT = 50;
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
        if (!realpath($this->folder."/placeholder")) {
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

        $images = $this->initImage();
        $galleryItems = [];
        for ($i = 1; $i <= self::GALLERY_ITEM_COUNT; $i++) {
            $output->writeln("Creating Gallery Item $i");
            $galleryItem = $this->initGalleryItem($images);
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
    private function initImage(): array {
        $images = [];
        $folder = $this->folder."/placeholder";
        foreach (scandir($folder) as $name) {
            if ($name != "." && $name != "..") {
                $path = "$folder/$name";
                if (is_file($path) && preg_match("/^image/", mime_content_type($path))) {
                    $this->output->writeln("Found $path.");
                    $images[] = "placeholder/".$name;
                }
            }
        }
        // Download some image only if not enough
        $count = self::GALLERY_ASSET_COUNT - count($images);
        for ($i = 0; $i < $count; $i++) {
            $client = new Client();
            $width = random_int(self::GALLERY_ASSET_MIN_WIDTH, self::GALLERY_ASSET_MAX_WIDTH);
            $height = random_int(self::GALLERY_ASSET_MIN_HEIGHT, self::GALLERY_ASSET_MAX_HEIGHT);
            $url = "https://picsum.photos/$width/$height?random";
            $response = $client->request("GET", $url);
            $name = uniqid().".png";
            file_put_contents("$folder/$name", $response->getBody());
            $this->output->writeln("Generated $folder/$name.");
            $images[] = "placeholder/".$name;
        }
        return $images;
    }


    private function initGalleryItem(array $images): GalleryItem {
        $galleryItem = new GalleryItem();
        $galleryItem->setIsApproved(true);
        if (!$this->lorem) {
            $this->lorem = new LoremIpsum();
        }
        $galleryItem->setHeader(ucwords($this->lorem->words(2)));
        $galleryItem->setContent($this->lorem ->paragraphs(2));
        foreach (array_rand($images, self::GALLERY_ASSET_PER_ITEM_COUNT) as $key) {
            $path = $images[$key];
            $asset = new GalleryAsset();
            $asset->setAssetPath($path);
            $asset->setMimeType(mime_content_type($this->folder."/".$path));
            $asset->setGalleryItem($galleryItem);
            $this->em->persist($asset);
        }
        return $galleryItem;
    }
}