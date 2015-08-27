<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use TVS\Application;

class LancamentoType extends AbstractType {

    protected $centrocusto;
    protected $favorecido;
    protected $conta;
    protected $cartao;
    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->centrocusto = $this->app['CentrocustoService']->fatchPairs();
        $this->favorecido = $this->app['FavorecidoService']->fatchPairs();
        $this->conta = $this->app['ContaService']->fatchPairs();
        $this->conta = $this->app['CartaoService']->fatchPairs();

        return $builder->add('valor', "money", array(
                    'currency' => 'BRL',
                    'grouping' => '1', //NAO SETAR TRUE CONFORME DOCUMENTA��O SYMFONY
                    'constraints' => array(new NotBlank()),
                    'label' => 'Saldo',
                        )
                )->add('descricao', "text", array(
                    'constraints' => array(new NotBlank(), new Length(array('max' => 100))),
                    'label' => 'Descri&ccedil;&atilde;o',
                        )
                )->add('documento', "text", array(
                    'label' => 'Documento',
                        )
                )->add('vencimento', "text", array(
                    'constraints' => array(new NotBlank(), new Length(array('max' => 10))),
                    'label' => 'Vencimento',
                        )
                )->add('tipo', 'choice', array(
                    'choices' => array('DESPESA' => 'DESPESA', 'RECEITA' => 'RECEITA'),
                    'required' => true,
                    'label' => 'Lan&ccedil;ar como'
                        )
                )->add('centrocusto', 'choice', array(
                    'choices' => $this->centrocusto,
                    'required' => true,
                    'label' => 'C.custo'
                        )
                )->add('favorecido', 'choice', array(
                    'choices' => $this->favorecido,
                    'required' => true,
                    'label' => 'Favorecido/Pagador'
                        )
                )->add('conta', 'choice', array(
                    'choices' => $this->conta,
                    'required' => true,
                    'label' => 'Debitar/Creditar'
                        )
                )->add('cartao', 'choice', array(
                    'choices' => $this->cartao,
                    'required' => false,
                    'label' => 'Lan&ccedil;ar no Cart&atilde;o de credito?'
                        )
                )->add('competencia', "text", array(
                    'constraints' => array(new NotBlank(), new Length(array('max' => 7))),
                    'label' => 'M&ecirc;s Compet&ecirc;ncia',
                        )
                )->add('status', 'checkbox', array(
                    'label' => 'Registro pago?',
                    'required' => false,
                        )
        );
    }

    public function getName() {
        return 'LancamentoForm';
    }

}

/*
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
            'tipo'=>$this->getTipo()->getId(),
            'user'=>$this->getUser()->getId(),
 *  */