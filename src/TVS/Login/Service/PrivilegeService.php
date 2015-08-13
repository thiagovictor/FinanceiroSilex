<?php

namespace TVS\Login\Service;

use Doctrine\ORM\EntityManager;
use TVS\Login\Entity\Privilege;
use TVS\Base\Service\AbstractService;
use TVS\Application;

class PrivilegeService extends AbstractService {

    public function __construct(EntityManager $em, Privilege $privilege, Application $app) {
        parent::__construct($em, $app);
        $this->object = $privilege;
        $this->entity = "TVS\Login\Entity\Privilege";
    }

    public function ajustaData(array $data = array()) {
        $privilegios = [
            'display', 'new', 'edit', 'delete'
        ];
        if (!empty($data["user"])) {
            $repo = $this->em->getRepository("TVS\Login\Entity\User");
            $data["user"] = $repo->findById($data["user"]);
        }
        if (!empty($data["route"])) {
            $repo = $this->em->getRepository("TVS\Login\Entity\Route");
            $data["route"] = $repo->findById($data["route"]);
        }
        foreach ($privilegios as $key => $value) {
            if ($data[$value]) {
                $data[$value] = 1;
                continue;
            }
            $data[$value] = 0;
        }
        return $data;
    }

    public function privilegeMap($id = null) {
        $rotas = $this->app["RouteService"]->findAll();
        $user = $this->app["LoginService"]->find($id);
        $map = array();
        if (!$user) {
            return $map;
        }
        foreach ($rotas as $rota) {
            $map[$rota->getRoute()] = $this->getPrivilege($rota, $user);
        }
        return $map;
    }

    public function getPrivilege(\TVS\Login\Entity\Route $rota, $user) {
        $privilege = $this->findOneBy(array('user' => $user, 'route' => $rota));
        if (!$privilege) {
            return [
                'id' => $rota->getId(),
                'display' => false,
                'new' => false,
                'edit' => false,
                'delete' => false
            ];
        }
        return $privilege->getPermission();
    }
    
    public function removePrivileges($notRemove, $user) {
        $privileges = $this->app['PrivilegeService']->findBy(array('user' => $user));
        if(!$privileges){
            return false;
        }
        
        foreach ($privileges as $privilege) {
            if(array_search($privilege->getRoute()->getId(), $notRemove)=== false){
                $this->delete($privilege->getId());
            }
        }
        $this->em->flush();
    }
    
    public function UpdateAll(array $array) {
        $app = $this->app;
        $user = $app['LoginService']->find($array['id']);
        unset($array['id']);
        $id_temp = 0;
        $chaves = array();
        $privilege = null;
        foreach ($array as $key => $value) {
            $arg = explode('_', $key);
            if ($id_temp != $arg[0]) {
                $chaves[] = $arg[0]; 
                $route = $app['RouteService']->find($arg[0]);
                $privilege[$arg[0]] = $app['PrivilegeService']->findOneBy(array('user' => $user, 'route' => $route));
                if (!$privilege[$arg[0]]) {
                    $privilege[$arg[0]] = new Privilege();
                    $privilege[$arg[0]]->setUser($user);
                    $privilege[$arg[0]]->setRoute($route);
                }
                $privilege[$arg[0]]->setDisplay(false);
                $privilege[$arg[0]]->setNew(false);
                $privilege[$arg[0]]->setEdit(false);
                $privilege[$arg[0]]->setDelete(false);
            }
            $id_temp = $arg[0];
            $action = 'set' . ucfirst($arg[1]);
            $privilege[$arg[0]]->$action(true);
        }
        
        $this->removePrivileges($chaves,$user);
        foreach ($privilege as $object) {
            $this->em->persist($object);
        }
        $this->em->flush();
        $this->setMessage("Registro atualizado com sucesso!");
    }

}
