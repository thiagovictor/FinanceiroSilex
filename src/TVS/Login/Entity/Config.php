<?php

namespace TVS\Login\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TVS\Login\Entity\ConfigRepository")
 * @ORM\Table(name="config")
 */
class Config {
    
    private $temp;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue 
     */
    private $id;

    /**
     * @ORM\Column(name="nome", type="string", nullable=false) 
     */
    private $nome;
    
    /**
     * @ORM\Column(name="parametros", type="text") 
     */
    private $parametros;
    
    function getId() {
        return $this->id;
    }

    function getNome() {
        return $this->nome;
    }

    function getParametros() {
        return $this->parametros;
    }

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setNome($nome) {
        $this->nome = $nome;
        return $this;
    }

    function setParametros($parametros) {
        $this->parametros = $parametros;
        return $this;
    }
    
    public function parametrosToArray() {
        $array = array();
        $p = explode('<br />' , nl2br($this->parametros));
        
        foreach ($p as $param) {
            $valores = explode(":::", trim($param));
            if(sizeof($valores) != 2){
                return false;
            }
            $array[$valores[0]] = $valores[1];
        }
        return $array;
    }
    
    public function getParametro($parametro) {
        $parametros  = $this->parametrosToArray();
        if(isset($parametros[$parametro])){
            return $parametros[$parametro];
        }
        return false;
    }
    
    public function help() {
        var_dump($this->parametrosToArray());
        exit();
    }
    
    public function toArray() {
        return [
            'id' => $this->getId(),
            'nome'=>  $this->getNome(),
            'parametros'=>  $this->getParametros()
        ];
    }


    
}
