<?php
namespace App\Service;

use App\Entity\Base\Directory\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BaseTemplateHelper {
    private $sideMenu = [];
    private $sideMenuStyle;
    private $navMenu = [];
    private $layout;
    private $title = "Web Application";
	private $user = null;
	private $role = [];
    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage, ParameterBagInterface $params) {
        $this->sideMenuStyle = $params->get("side_menu_style");
        $this->layout = $params->get("layout");
		$token = $tokenStorage->getToken();
		$this->role = [];
		if ($token) {
			$this->user = $token->getUser();
			if ($this->user && $this->user instanceof User) {
				$this->role = $this->user->getRoles();
			}
		}
    }

    /**
     * @return array
     */
    public function getNavMenu(): array {
        return $this->navMenu;
    }

    public function addNavMenu(array $item) {
        $this->navMenu[] = $item;
        return $this;
    }

    public function setNavMenu(array $menu) {
        $this->navMenu = $menu;
    }

    /**
     * @return array
     */
    public function getSideMenu(): array {
        return $this->sideMenu;
    }

    public function addSideMenu(array $item) {
        $this->sideMenu[] = $item;
        return $this;
    }

    public function setSideMenu(array $menu) {
        $this->sideMenu = $menu;
    }

    /**
     * @return string
     */
    public function getSideMenuStyle(): string
    {
        return $this->sideMenuStyle;
    }

    /**
     * @param mixed $sideMenuStyle
     */
    public function setSideMenuStyle($sideMenuStyle) {
        $this->sideMenuStyle = $sideMenuStyle;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     * @return BaseTemplateHelper
     */
    public function setTitle(string $title): BaseTemplateHelper {
        $this->title = $title;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param mixed $layout
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    }


}