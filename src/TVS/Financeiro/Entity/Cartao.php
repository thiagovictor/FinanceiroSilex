<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\Mapping as ORM;
use TVS\Login\Entity\User;

/**
 * Cartao
 * @ORM\Entity
 * @ORM\Table(name="cartao")
 * @ORM\Entity(repositoryClass="TVS\Financeiro\Entity\CartaoRepository")
 */
class Cartao
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
     * @var string
     *
     * @ORM\Column(name="vencimento", type="string",length=2, nullable=false)
     */
    private $vencimento;

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

    public function getVencimento() {
        return $this->vencimento;
    }

    public function getUser() {
        return $this->user;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
        return $this;
    }

    public function setVencimento($vencimento) {
        $this->vencimento = $vencimento;
        return $this;
    }

    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }
    
    public function toArray() {
        return [
            'id' => $this->getId(),
            'descricao'=> $this->getDescricao(),
            'vencimento'=> $this->getVencimento(),
            'user'=>  $this->getUser()->getId()
        ];
    }

}
