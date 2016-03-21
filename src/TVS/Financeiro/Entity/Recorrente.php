<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\Mapping as ORM;
use TVS\Login\Entity\User;

/**
 * Recorrente
 * @ORM\Entity(repositoryClass="TVS\Financeiro\Entity\RecorrenteRepository")
 * @ORM\Table(name="recorrente")
 */
class Recorrente {

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
     * @ORM\Column(name="valor", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="string", length=255, nullable=false)
     */
    private $descricao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vencimento", type="date", nullable=false)
     */
    private $vencimento;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status = '0';

    /**
     * @ORM\ManyToOne(targetEntity="TVS\Login\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $user;

    /**
     * @var Periodo
     *
     * @ORM\ManyToOne(targetEntity="TVS\Financeiro\Entity\Periodo")
     * @ORM\JoinColumn(name="periodo_id", referencedColumnName="id")
     */
    private $periodo;

    /**
     * @var Categoria
     *
     * @ORM\ManyToOne(targetEntity="TVS\Financeiro\Entity\Categoria")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id", nullable=true)
     */
    private $categoria;

    /**
     * @var Conta
     *
     * @ORM\ManyToOne(targetEntity="TVS\Financeiro\Entity\Conta")
     * @ORM\JoinColumn(name="conta_id", referencedColumnName="id")
     */
    private $conta;

    /**
     * @var Favorecido
     *
     * @ORM\ManyToOne(targetEntity="TVS\Financeiro\Entity\Favorecido")
     * @ORM\JoinColumn(name="favorecido_id", referencedColumnName="id")
     */
    private $favorecido;

    /**
     * @var Centrocusto
     *
     * @ORM\ManyToOne(targetEntity="TVS\Financeiro\Entity\Centrocusto")
     * @ORM\JoinColumn(name="centrocusto_id", referencedColumnName="id")
     */
    private $centrocusto;

    /**
     * @var Cartao
     *
     * @ORM\ManyToOne(targetEntity="TVS\Financeiro\Entity\Cartao")
     * @ORM\JoinColumn(name="cartao_id", referencedColumnName="id")
     */
    private $cartao;

    
    private $tipo;

    function getId() {
        return $this->id;
    }

    function getValor() {
        return $this->valor;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getVencimento() {
        return $this->vencimento;
    }

    function getStatus() {
        return $this->status;
    }

    function getUser() {
        return $this->user;
    }

    function getPeriodo() {
        return $this->periodo;
    }

    function getCategoria() {
        return $this->categoria;
    }

    function getConta() {
        return $this->conta;
    }

    function getFavorecido() {
        return $this->favorecido;
    }

    function getCentrocusto() {
        return $this->centrocusto;
    }

    function getCartao() {
        return $this->cartao;
    }

    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setValor($valor) {
        $this->valor = $valor;
        $this->setTipo($this->getTipo());
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

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }

    public function setPeriodo($periodo) {
        $this->periodo = $periodo;
        return $this;
    }

    public function setCategoria($categoria) {
        $this->categoria = $categoria;
        return $this;
    }

    public function setConta(Conta $conta) {
        $this->conta = $conta;
        return $this;
    }

    public function setFavorecido($favorecido) {
        $this->favorecido = $favorecido;
        return $this;
    }

    public function setCentrocusto($centrocusto) {
        $this->centrocusto = $centrocusto;
        return $this;
    }

    public function setCartao($cartao) {
        $this->cartao = $cartao;
        return $this;
    }

    public function returnId($object) {
        if (is_object($object)) {
            return $object->getId();
        }
        return null;
    }

    public function returnValue($object) {
        if (is_object($object)) {
            return $object->format('d/m/Y');
        }
        return null;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function getTipo() {
        $this->setTipo(($this->getValor() <= 0) ? 'DESPESA' : "RECEITA");
        return $this->tipo;
    }

    public function concatCentroCustoCategoria() {
        return ($this->returnId($this->getCategoria())) ? $this->returnId($this->getCentrocusto()) . "_" . $this->returnId($this->getCategoria()) : $this->returnId($this->getCentrocusto());
    }

    
    public function toArray() {

        return [
            'id' => $this->getId(),
            'valor' => str_replace("-", "", $this->getValor()),
            'vencimento' => $this->getVencimento()->format('d/m/Y'),
            'descricao' => $this->getDescricao(),
            'status' => $this->getStatus(),
            'tipo' => $this->getTipo(),
            'conta' => $this->returnId($this->getConta()),
            'periodo' => $this->returnId($this->getPeriodo()),
            'favorecido' => $this->returnId($this->getFavorecido()),
            'cartao' => $this->returnId($this->getCartao()),
            'categoria' => $this->returnId($this->getCategoria()),
            'centrocusto' => $this->concatCentroCustoCategoria(),
            'user' => $this->returnId($this->getUser()),
        ];
    }

}
