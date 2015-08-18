<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\Mapping as ORM;
use TVS\Login\Entity\User;

/**
 * Centrocusto
 * @ORM\Entity
 * @ORM\Table(name="centrocusto")
 * @ORM\Entity(repositoryClass="TVS\Financeiro\Entity\CentrocustoRepository")
 */
class Centrocusto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="string", length=255, nullable=false)
     */
    protected $descricao;

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

    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }
   
    public function toArray() {
        return [
            'id'=> $this->getId(),
            'descricao' => $this->getDescricao(),
            'user' => $this->getUser()->getId()
        ];
    }
}
