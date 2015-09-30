<?php

namespace TVS\Financeiro\Entity;

use Doctrine\ORM\Mapping as ORM;
use TVS\Login\Entity\User;
use TVS\Financeiro\Service\LancamentoService;

/**
 * Lancamento
 * @ORM\Entity(repositoryClass="TVS\Financeiro\Entity\LancamentoRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="lancamento")
 */
class Lancamento {

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
     * @ORM\Column(name="pagamento", type="date", nullable=true)
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
    private $tipo;

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
        $this->setTipo($this->getTipo());
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

    public function getCompetencia() {
        return $this->competencia;
    }

    public function setCompetencia(\DateTime $competencia) {
        $this->competencia = $competencia;
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

    /**
     * @ORM\PrePersist
     * @ORM\preUpdate
     */
    public function uploadDocs() {
//        $completePath = LancamentoService::checkDir($this->user->getUsername());
//        $this->arquivoBoleto->move($completePath."/profile/{$this->user->getUsername()}/docs/", "boleto_" . time() . "." . $this->arquivoBoleto->getClientOriginalExtension());
//        $this->arquivoComprovante->move($completePath."/profile/{$this->user->getUsername()}/docs/", "comprovante" . time() . "." . $this->arquivoComprovante->getClientOriginalExtension());
//       var_dump($this->arquivoBoleto);
////        var_dump($comprov_temp);
//        exit();

        $boleto_temp = $this->arquivoBoleto;
        $comprov_temp = $this->arquivoComprovante;
        $forms = ["LancamentoForm"];
        $file = null;
        foreach ($forms as $value) {
            if (!isset($_FILES[$value])) {
                continue;
            }
            $file = $_FILES[$value];
        }

        if (!$file) {
            return false;
        }
        $result = LancamentoService::uploadDocs($this->user->getUsername(), $file);

        if ($result) {
            if (isset($result["boleto"])) {
                $this->arquivoBoleto = $result["boleto"];
            }
            if (isset($result["comprovante"])) {
                $this->arquivoComprovante = $result["comprovante"];
            }


            if (!empty($boleto_temp)) {
                LancamentoService::removeDocs($boleto_temp);
            }
            if (!empty($comprov_temp)) {
                LancamentoService::removeDocs($comprov_temp);
            }
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function removeArquivos() {
        if (!empty($this->arquivoBoleto)) {
            LancamentoService::removeDocs($this->arquivoBoleto);
        }
        if (!empty($this->arquivoComprovante)) {
            LancamentoService::removeDocs($this->arquivoComprovante);
        }
    }

    public function toArray() {

        return [
            'id' => $this->getId(),
            'valor' => str_replace("-", "", $this->getValor()),
            'vencimento' => $this->getVencimento()->format('d/m/Y'),
            //'arquivoBoleto' => new \Symfony\Component\HttpFoundation\File\File("../data".$this->getArquivoBoleto()),
            //'arquivoComprovante' => new \Symfony\Component\HttpFoundation\File\File("../data".$this->getArquivoComprovante()),
            'competencia' => $this->getCompetencia()->format('m/Y'),
            'descricao' => $this->getDescricao(),
            'documento' => $this->getDocumento(),
            'idparcela' => $this->getIdparcela(),
            'idrecorrente' => $this->getIdrecorrente(),
            'pagamento' => $this->returnValue($this->getPagamento()),
            'parcelas' => $this->getParcelas(),
            'transf' => $this->getTransf(),
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
