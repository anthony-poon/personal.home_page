<?php

namespace App\Command;

use App\Entity\Base\AccessToken;
use App\Entity\Base\DirectoryGroup;
use App\Entity\Base\DirectoryObject;
use App\Entity\Base\SecurityGroup;
use App\Entity\Base\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitCommand extends Command {
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, $name = null) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($name);
    }

    protected function configure() {
        $this->setName("app:init")
            ->setDescription("Create root user and role");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
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

        $this->entityManager->persist($adminGroup);
        $this->entityManager->persist($userGroup);
        $this->entityManager->persist($adminToken);
        $this->entityManager->persist($userToken);
        $this->entityManager->persist($root);
        $this->entityManager->flush();
    }

    private function initAccessToken(string $tokenStr) {
        $repo = $this->entityManager->getRepository(AccessToken::class);
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
        $repo = $this->entityManager->getRepository(DirectoryGroup::class);
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
        $userRepo = $this->entityManager->getRepository(User::class);
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
}