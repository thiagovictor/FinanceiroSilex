<?php

namespace TVS\Login\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TVS\Login\Entity\RouteRepository")
 * @ORM\Table(name="route")
 */
class Route {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue 
     */
    private $id;

    /**
     * @ORM\Column(name="route", type="string", nullable=false) 
     */
    private $route;

    /**
     * @ORM\Column(name="label", type="string", nullable=false) 
     */
    private $label;
    
    /**
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="route")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     **/
    private $menu;
    
    function getMenu() {
        return $this->menu;
    }

    function setMenu($menu) {
        $this->menu = $menu;
        return $this;
    }
    
    function getLabel() {
        return $this->label;
    }

    function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    function getId() {
        return $this->id;
    }

    function getRoute() {
        return $this->route;
    }

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setRoute($route) {
        $this->route = $route;
        return $this;
    }

    function toArray() {
        $menu_id = '';
        if(null !== $this->getMenu()){
            $menu_id = $this->getMenu()->getId();
        }
        return [
            'id' => $this->getId(),
            'route' => $this->getRoute(),
            'label' => $this->getLabel(),
            'menu' => $menu_id
        ];
    }

}
