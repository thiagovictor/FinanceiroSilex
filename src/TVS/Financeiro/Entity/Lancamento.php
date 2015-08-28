<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\Mapping as ORM;
use TVS\Login\Entity\User;

/**
 * Lancamento
 *
 * @ORM\Table(name="lancamento")
 * @ORM\Entity(repositoryClass="TVS\Financeiro\Entity\LancamentoRepository")
 */
class Lancamento
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
     * @ORM\Column(name="pagamento", type="date", nullable=false)
     */
    private $pagamento;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vencimento", type="date", nullable=false)
     */
    private $vencimento;

    /**
     * @var integer
     *
     * @ORM\Column(name="parcelas", type="integer", nullable=true)
     */
    private $parcelas;

    /**
     * @var string
     *
     * @ORM\Column(name="arquivo_boleto", type="string", length=255, nullable=true)
     */
    private $arquivoBoleto;

    /**
     * @var string
     *
     * @ORM\Column(name="arquivo_comprovante", type="string", length=255, nullable=true)
     */
    private $arquivoComprovante;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="idparcela", type="string", length=45, nullable=true)
     */
    private $idparcela;

    /**
     * @var string
     *
     * @ORM\Column(name="idrecorrente", type="string", length=45, nullable=true)
     */
    private $idrecorrente;

    /**
     * @var string
     *
     * @ORM\Column(name="transf", type="string", length=45, nullable=true)
     */
    private $transf;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=45, nullable=true)
     */
    private $documento;

    /**
     * @ORM\ManyToOne(targetEntity="TVS\Login\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
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
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="competencia", type="date", nullable=true)
     */
    private $competencia;
        
    public function getId() {
        return $this->id;
    }

    public function getValor() {
        return $this->valor;
    }

    public function getDescricao() {
        return $this->descricao;
    }
        
    public function getPagamento() {
        return $this->pagamento;
    }
    
    public function getVencimento() {
        return $this->vencimento;
    }

    public function getParcelas() {
        return $this->parcelas;
    }

    public function getArquivoBoleto() {
        return $this->arquivoBoleto;
    }

    public function getArquivoComprovante() {
        return $this->arquivoComprovante;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getIdparcela() {
        return $this->idparcela;
    }

    public function getIdrecorrente() {
        return $this->idrecorrente;
    }

    public function getTransf() {
        return $this->transf;
    }

    public function getDocumento() {
        return $this->documento;
    }

    public function getUser() {
        return $this->user;
    }

    public function getPeriodo() {
        return $this->periodo;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function getConta() {
        return $this->conta;
    }

    public function getFavorecido() {
        return $this->favorecido;
    }

    public function getCentrocusto() {
        return $this->centrocusto;
    }

    public function getCartao() {
        return $this->cartao;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setValor($valor) {
        $this->valor = $valor;
        return $this;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
        return $this;
    }

    public function setPagamento($pagamento) {
        $this->pagamento = $pagamento;
        return $this;
    }

        public function setVencimento($vencimento) {
        $this->vencimento = $vencimento;
        return $this;
    }

    public function setParcelas($parcelas) {
        $this->parcelas = $parcelas;
        return $this;
    }

    public function setArquivoBoleto($arquivoBoleto) {
        $this->arquivoBoleto = $arquivoBoleto;
        return $this;
    }

    public function setArquivoComprovante($arquivoComprovante) {
        $this->arquivoComprovante = $arquivoComprovante;
        return $this;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function setIdparcela($idparcela) {
        $this->idparcela = $idparcela;
        return $this;
    }

    public function setIdrecorrente($idrecorrente) {
        $this->idrecorrente = $idrecorrente;
        return $this;
    }

    public function setTransf($transf) {
        $this->transf = $transf;
        return $this;
    }

    public function setDocumento($documento) {
        $this->documento = $documento;
        return $this;
    }

    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }

    public function setPeriodo(Periodo $periodo) {
        $this->periodo = $periodo;
        return $this;
    }

    public function setCategoria(Categoria $categoria) {
        $this->categoria = $categoria;
        return $this;
    }

    public function setConta(Conta $conta) {
        $this->conta = $conta;
        return $this;
    }

    public function setFavorecido(Favorecido $favorecido) {
        $this->favorecido = $favorecido;
        return $this;
    }

    public function setCentrocusto(Centrocusto $centrocusto) {
        $this->centrocusto = $centrocusto;
        return $this;
    }

    public function setCartao(Cartao $cartao) {
        $this->cartao = $cartao;
        return $this;
    }
    
    public function getCompetencia() {
        return $this->competencia;
    }

    public function setCompetencia(\DateTime $competencia) {
        $this->competencia = $competencia;
        return $this;
    }
    
    public function toArray() {
        return [
            'id' => $this->getId(),
            'valor'=>$this->getValor(),
            'vencimento'=>$this->getVencimento(),
            'arquivoBoleto'=>$this->getArquivoBoleto(),
            'arquivoComprovante'=>$this->getArquivoComprovante(),
            'competencia'=>$this->getCompetencia(),
            'descricao'=>$this->getDescricao(),
            'documento'=>$this->getDocumento(),
            'idparcela'=>$this->getIdparcela(),
            'idrecorrente'=>$this->getIdrecorrente(),
            'pagamento'=>$this->getPagamento(),
            'parcelas'=>$this->getParcelas(),
            'transf'=>$this->getTransf(),
            'status'=>$this->getStatus(),
            'conta'=>$this->getConta()->getId(),
            'periodo'=>$this->getPeriodo()->getId(),
            'favorecido'=>$this->getFavorecido()->getId(),
            'cartao'=>$this->getCartao()->getId(),
            'categoria'=>$this->getCategoria()->getId(),
            'centrocusto'=>$this->getCentrocusto()->getId(),
            'user'=>$this->getUser()->getId(),
        ];
    }


}
