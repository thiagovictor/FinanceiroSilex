<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Periodo
 * @ORM\Entity
 * @ORM\Table(name="periodo")
 * @ORM\Entity(repositoryClass="TVS\Financeiro\Entity\PeriodoRepository")
 */
class Periodo {

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
     * @ORM\Column(name="descricao", type="string", length=45, nullable=false)
     */
    private $descricao;

    public function getId() {
        return $this->id;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
        return $this;
    }
    
    public function toArray() {
        return [
            'id'=>  $this->getId(),
            'descricao'=> $this->getDescricao()
        ];
    }
}
