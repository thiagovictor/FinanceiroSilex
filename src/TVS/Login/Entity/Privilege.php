<?php

namespace TVS\Login\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TVS\Login\Entity\PrivilegeRepository")
 * @ORM\Table(name="privilege")
 */
class Privilege {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue 
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Route")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $route;

    /**
     * @ORM\Column(name="display", type="boolean", nullable=true) 
     */
    private $display;

    /**
     * @ORM\Column(name="new", type="boolean", nullable=true) 
     */
    private $new;

    /**
     * @ORM\Column(name="edit", type="boolean", nullable=true) 
     */
    private $edit;

    /**
     * @ORM\Column(name="remove", type="boolean", nullable=true) 
     */
    private $delete;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $user;

    function getRoute() {
        return $this->route;
    }

    function getUser() {
        return $this->user;
    }

    function setRoute($route) {
        $this->route = $route;
        return $this;
    }

    function setUser($user) {
        $this->user = $user;
        return $this;
    }

    function getId() {
        return $this->id;
    }

    function getDisplay() {
        return $this->display;
    }

    function getNew() {
        return $this->new;
    }

    function getEdit() {
        return $this->edit;
    }

    function getDelete() {
        return $this->delete;
    }

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setDisplay($display) {
        $this->display = $display;
        return $this;
    }

    function setNew($new) {
        $this->new = $new;
        return $this;
    }

    function setEdit($edit) {
        $this->edit = $edit;
        return $this;
    }

    function setDelete($delete) {
        $this->delete = $delete;
        return $this;
    }

    public function toArray() {
        return [
            'id' => $this->getId(),
            'user' => $this->getUser()->getId(),
            'route' => $this->getRoute()->getId(),
            'display' => $this->getDisplay(),
            'new' => $this->getNew(),
            'edit' => $this->getEdit(),
            'delete' => $this->getDelete()
        ];
    }
    
    public function getPermission() {
        return [
            'id'=>  $this->getRoute()->getId(),
            'display' => $this->getDisplay(),
            'new' => $this->getNew(),
            'edit' => $this->getEdit(),
            'delete' => $this->getDelete()
        ];
    }

}
