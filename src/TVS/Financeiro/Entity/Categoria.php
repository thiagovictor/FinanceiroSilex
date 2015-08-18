<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\Mapping as ORM;
use TVS\Financeiro\Entity\Centrocusto;
use TVS\Login\Entity\User;

/**
 * Categoria
 * 
 * @ORM\Entity
 * @ORM\Table(name="cartegoria")
 * @ORM\Entity(repositoryClass="TVS\Financeiro\Entity\CategoriaRepository")
 */
class Categoria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="string", length=255, nullable=false)
     */
    private $descricao;

    /**
     * @ORM\ManyToOne(targetEntity="TVS\Financeiro\Entity\Centrocusto")
     * @ORM\JoinColumn(name="centrocusto_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $centrocusto;

    /**
     * @ORM\ManyToOne(targetEntity="TVS\Login\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    public function getId() {
        return $this->id;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getCentrocusto() {
        return $this->centrocusto;
    }

    public function getUser() {
        return $this->user;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
        return $this;
    }

    public function setCentrocusto(Centrocusto $centrocusto) {
        $this->centrocusto = $centrocusto;
        return $this;
    }

    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }
    
    public function toArray() {
        return [
            'id' => $this->getId(),
            'descricao' => $this->getDescricao(),
            'centrocusto' => $this->getCentrocusto()->getId(),
            'user' => $this->getUser()->getId()
        ];
    }
}
