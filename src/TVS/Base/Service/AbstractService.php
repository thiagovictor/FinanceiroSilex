<?php

namespace TVS\Base\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use TVS\Application;

abstract class AbstractService {

    protected $em;
    protected $message = array();
    protected $object;
    protected $entity;
    protected $app;

    public function __construct(EntityManager $em, Application $app) {
        $this->em = $em;
        $this->app = $app;
    }

    public function ajustaData(array $data = array()) {
        return $data;
    }

    private function popular(array $data = array()) {
        $data_checked = $this->ajustaData($data);
        foreach ($data_checked as $metodo => $valor) {
            $metodo = 'set' . ucfirst($metodo);
            if (!method_exists($this->object, $metodo)) {
                $this->setMessage("N&atilde;o foi poss&iacute;vel converter, verifique os atributos enviados:{$metodo}");
                return false;
            }
            $this->object->$metodo($valor);
        }
        return $this->object;
    }

    public function insert(array $data = array()) {
        if ($this->popular($data)) {
            $this->em->persist($this->object);
            $this->em->flush();
            $this->setMessage("Registro adicionado com sucesso!");
            return true;
        }
        return false;
    }

    public function update(array $data = array()) {
        if (!isset($data["id"])) {
            $this->setMessage("Par&acirc;metro :id nao encontrado");
            return false;
        }
        $this->object = $this->em->getReference($this->entity, $data["id"]);
        if (!$this->popular($data)) {
            return false;
        }
        $this->em->persist($this->object);
        $this->em->flush();
         $this->setMessage("Registro atualizado com sucesso!");
        return true;
    }

    public function delete($id) {
        $this->object = $this->em->getReference($this->entity, $id);
        $this->em->remove($this->object);
        try {
            $this->em->flush();
             $this->setMessage("Registro removido com sucesso!");
            return true;
        } catch (ForeignKeyConstraintViolationException $ex) {
            $this->setMessage($ex->getMessage());
            return false;
        }
    }

    public function findAll() {
        $repo = $this->em->getRepository($this->entity);
        return $repo->findAll();
    }

    protected function toArray($objects) {
        if (!isset($objects[0])) {
            return array();
        }
        $methods = get_class_methods($objects[0]);
        $prop = array();
        for ($i = 0; $i < sizeof($methods); $i++) {
            if (substr($methods[$i], 0, 3) == 'get') {
                $prop[$methods[$i]] = str_replace('get', '', $methods[$i]);
                continue;
            }
        }

        foreach ($objects as $tag) {
            $data = array();
            foreach ($prop as $key => $value) {
                $object = $tag->$key();
                if (is_object($object)) {
                    $data[$value] = $this->toArray([0 => $object]);
                    continue;
                }
                if (is_array($object)) {
                    $data[$value] = $this->toArray($object);
                    continue;
                }
                $data[$value] = $tag->$key();
            }
            $return[] = $data;
        }
        return $return;
    }

    public function fatchPairs() {
        $repo = $this->em->getRepository($this->entity);
        return $repo->fatchPairs();
    }

    public function findAllToArray() {
        $repo = $this->em->getRepository($this->entity);
        $objects = $repo->findAll();
        return $this->toArray($objects);
    }

    public function findToArray($id) {
        $repo = $this->em->getRepository($this->entity);
        $object = $repo->find($id);
        return $this->toArray([0 => $object]);
    }

    public function findPagination($firstResult, $maxResults) {
        $repo = $this->em->getRepository($this->entity);
        return $repo->findPagination($firstResult, $maxResults);
    }
    
    public function findSearch($firstResult, $maxResults,$search,$field) {
        $repo = $this->em->getRepository($this->entity);
        return $repo->findSearch($firstResult, $maxResults,$search,$field);
    }

    public function getRows($search = false, $field = false) {
        $repo = $this->em->getRepository($this->entity);
        if($search and $field){
            return $repo->getRowsSearch($search, $field);
        }
        return $repo->getRows();
    }

    public function find($id) {
        $repo = $this->em->getRepository($this->entity);
        return $repo->find($id);
    }
    
    public function findOneBy(array $param) {
        $repo = $this->em->getRepository($this->entity);
        $object = $repo->findOneBy($param);
        if ($object) {
            return $object;
        }
        return false;
    }
    
    public function findBy(array $param) {
        $repo = $this->em->getRepository($this->entity);
        $object = $repo->findBy($param);
        if ($object) {
            return $object;
        }
        return false;
    }
    
    function getMessage() {
        return $this->message;
    }

    function setMessage($message) {
        $this->message[] = $message;
        return $this;
    }

    public function mountArrayRoute() {
        $request = $this->app['request'];
        $rotas = ['controller', 'action', 'param'];
        $route = [];
        $params = explode("/", substr($request->getRequestUri(), 1, strlen($request->getRequestUri())));
        foreach ($rotas as $key => $value) {
            if (isset($params[$key])) {
                $route[$value] = $params[$key];
                continue;
            }
            $route[$value] = '';
        }
        if ('' == $route['action'] or $route["action"] == "page") {
            $route['action'] = 'display';
        }
        return $route;
    }

    public function isAllowed($objectToArray = false) {
        $user = $this->app['session']->get('user');
        $route = $this->mountArrayRoute();
        $routeRepository = $this->em->getRepository('TVS\Login\Entity\Route');
        $objectRoute = $routeRepository->findOneByRoute($route['controller']);
        $PrivilegeRepositoty = $this->em->getRepository('TVS\Login\Entity\Privilege');
        $objectPrivilege = $PrivilegeRepositoty->findOneBy(array('user' => $user, 'route' => $objectRoute));
        if ($objectToArray) {
            if($objectPrivilege){
                return $objectPrivilege->toArray();
            }
            $objectRouteGeneric = $routeRepository->findOneByRoute('*');
            $objectPrivilegeGeneric = $PrivilegeRepositoty->findOneBy(array('user' => $user, 'route' => $objectRouteGeneric));
            if($objectPrivilegeGeneric){
                return $objectPrivilegeGeneric->toArray();
            }
            return ['new'=>false,'edit'=>false,'delete'=>false];
        }
        $getAction = 'get' . ucfirst($route['action']);
        if ($objectPrivilege) {
            return $objectPrivilege->$getAction();
        }
        $objectRouteGeneric = $routeRepository->findOneByRoute('*');
        $objectPrivilegeGeneric = $PrivilegeRepositoty->findOneBy(array('user' => $user, 'route' => $objectRouteGeneric));
        if ($objectPrivilegeGeneric) {
            return $objectPrivilegeGeneric->$getAction();
        }
        return false;
    }
    
    public function ObjectValidPrivilege(\TVS\Login\Entity\Privilege $privilege) {
        $acoes = ['display','new','edit','delete'];
        $return = false;
        foreach ($acoes as $acao) {
            $action = 'get'.ucfirst($acao);
            if($privilege->$action()){
                $return = true;
            }
        }
        return $return;
    }
    
    
    public function isAllowedRoute($route, $action = null) {
        $routeRepository = $this->em->getRepository('TVS\Login\Entity\Route');
        $objectRoute = $routeRepository->findOneByRoute($route);
        $PrivilegeRepositoty = $this->em->getRepository('TVS\Login\Entity\Privilege');
        $objectPrivilege = $PrivilegeRepositoty->findOneBy(array('user' => $this->app['session']->get('user'), 'route' => $objectRoute));
        if ($objectPrivilege) {
            if(!$this->ObjectValidPrivilege($objectPrivilege)){
                return false;
            }
            if(!$action){
                return true;
            }
            $getAction = 'get' . ucfirst($action);
            return $objectPrivilege->$getAction();
        }
        return false;
    }

    public function pagination($page_atual, $registros_por_pagina, $search = false, $field = false ) {
        $numero_paginas = ceil($this->getRows() / $registros_por_pagina);
        $rota = "page";
        $busca = '';
        if($search and $field){
            $numero_paginas = ceil($this->getRows($search,$field) / $registros_por_pagina);
            $rota = 'display/search';
            $busca = "/$search";
        }
        
        $route = $this->mountArrayRoute();
        $disabled_prev = '';
        if ($page_atual == 1) {
            $disabled_prev = 'disabled';
        }
        $disabled_next = '';
        if ($page_atual >= $numero_paginas) {
            $disabled_next = 'disabled';
        }
        $link_prev = '';
        if ($page_atual > 1) {
            $page_prev = $page_atual - 1;
            $link_prev = "href='/{$route["controller"]}/{$rota}/{$page_prev}{$busca}'";
        }

        $link_next = '';
        if ($page_atual < $numero_paginas) {
            $page_next = $page_atual + 1;
            $link_next = "href='/{$route["controller"]}/{$rota}/{$page_next}{$busca}'";
        }
        $return = '<div align="center" >
                <nav>
                    <ul class="pagination">';
        for ($i = 1; $i <= $numero_paginas; $i++) {
            if ($i == 1) {
                $return .= '<li class="prev ' . $disabled_prev . '"><a ' . $link_prev . '>Anterior</a></li>';
            }
            if ($numero_paginas > 0) {
                $return .= '<li ';
                if ($page_atual == $i) {
                    $return .= 'class="active"';
                }
                $return .= "><a href='/{$route["controller"]}/{$rota}/{$i}{$busca}'>{$i}</a></li>";
            }
            if ($i + 1 > $numero_paginas) {
                $return .= '<li class="next ' . $disabled_next . '"><a ' . $link_next . '>Pr&oacute;ximo</a></li>';
            }
        }
        $return .= '</ul>
                </nav>

        </div>';

        return $return;
    }

}
