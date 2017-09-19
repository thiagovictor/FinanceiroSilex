<?php

namespace TVS\Login\Service;

use Doctrine\ORM\EntityManager;
use TVS\Login\Entity\Menu;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class MenuService extends AbstractService {

    public function __construct(EntityManager $em, Menu $menu, Application $app) {
        parent::__construct($em,$app);
        $this->object = $menu;
        $this->entity = "TVS\Login\Entity\Menu";
    }

    public function ajustaData(array $data = array()) {
        return $data;
    }

    public function getMenu() {
        $user = $this->app['session']->get('user');
        if ($this->app['session']->get('Puser')) {
            $user = $this->app['session']->get('Puser');
        }
        $PrivilegeRepositoty = $this->em->getRepository('TVS\Login\Entity\Privilege');
        $menuRepositoty = $this->em->getRepository('TVS\Login\Entity\Menu');
        $routeRepository = $this->em->getRepository('TVS\Login\Entity\Route');
        $barramenu = array();     
        $objectRouteGeneric = $routeRepository->findOneByRoute('*');
        $objectPrivilege = $PrivilegeRepositoty->findOneBy(array('user' => $user, 'route' => $objectRouteGeneric));

        if ($objectPrivilege) {
            $objectsMenu = $menuRepositoty->findBy([], ['label' => 'ASC']);
            foreach ($objectsMenu as $menu) {
                $itensArray = $menu->getRoutes();
                if(!empty($itensArray)){
                    $barramenu[$menu->getLabel()] = $itensArray;
                }
            }
            return $barramenu;
        }
        
        $objectsMenu = $menuRepositoty->findBy([], ['label' => 'ASC']);
        if ($objectsMenu) {
            foreach ($objectsMenu as $menu) {
                $itens = $menu->getRoutes();
                $itensArray = array();
                foreach ($itens as $item) {
                    if ($this->isAllowedRoute($item->getRoute())) {
                        $itensArray[] = $item;
                    }
                }
                if(!empty($itensArray)){
                    $barramenu[$menu->getLabel()] = $itensArray;
                }
                
            }
            return $barramenu;
        }
        return false;
    }

}
