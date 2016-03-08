<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use TVS\Base\Validator\Constraint\InvalidDate;
use TVS\Base\Validator\Constraint\InvalidDateWithEmpty;
use TVS\Application;

class RelatorioCustomizadoType extends AbstractType {

    protected $centrocusto;
    protected $favorecido;
    protected $conta;
    protected $cartao;
    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->centrocusto = $this->app['CentrocustoService']->selecao();
        $this->favorecido = $this->app['FavorecidoService']->fatchPairs();
        $this->conta = $this->app['ContaService']->fatchPairs();
        $this->cartao = $this->app['CartaoService']->fatchPairs();

        return $builder->add('valor', "money", array(
                    'currency' => 'BRL',
                    'grouping' => '1', //NAO SETAR TRUE CONFORME DOCUMENTAÇÃO SYMFONY
                    'label' => 'Valor',
                    'required'    => false,
                        )
                )->add('descricao', "text", array(
                    'label' => 'Descri&ccedil;&atilde;o',
                    'required'    => false,
                        )
                )->add('documento', "text", array(
                    'required' => false,
                    'label' => 'Documento',
                        )
                )->add('vencInicio', "text", array(
                    'constraints' => array(new InvalidDateWithEmpty()),
                    'required'    => false,
                    'label' => 'Vencimento Inicial',
                        )
                )->add('vencFim', "text", array(
                    'constraints' => array(new InvalidDateWithEmpty()),
                    'required'    => false,
                    'label' => 'Vencimento Final',
                        )
                )->add('pagInicio', "text", array(
                    'constraints' => array(new InvalidDateWithEmpty()),
                    'required'    => false,
                    'label' => 'Pagamento Inicial',
                        )
                )->add('pagFim', "text", array(
                    'constraints' => array(new InvalidDateWithEmpty()),
                    'required'    => false,
                    'label' => 'Pagamento Final',
                        )
                )->add('tipo', 'choice', array(
                    'choices' => array('DESPESA' => 'DESPESA', 'RECEITA' => 'RECEITA'),
                    'label' => 'Lan&ccedil;ar como',
                    'required'    => false,
                    'placeholder' => 'Todos',
                        )
                )->add('centrocusto', 'choice', array(
                    'choices' => $this->centrocusto,
                    'label' => 'C.custo',
                    'required'    => false,
                    'placeholder' => 'Todos',
                        )
                )->add('favorecido', 'choice', array(
                    'choices' => $this->favorecido,
                    'label' => 'Favorecido/Pagador',
                    'required'    => false,
                    'placeholder' => 'Todos',
                        )
                )->add('conta', 'choice', array(
                    'choices' => $this->conta,
                    'label' => 'Debitar/Creditar',
                    'required'    => false,
                    'placeholder' => 'Todos',
                        )
                )->add('cartao', 'choice', array(
                    'choices' => $this->cartao,
                    'label' => 'Lan&ccedil;ar no Cart&atilde;o de credito?',
                    'required'    => false,
                    'placeholder' => 'Todos',
                        )
                )->add('compInicio', "text", array(
                    'label' => 'Inicio M&ecirc;s Compet&ecirc;ncia',
                    'required'    => false,
                        )
                )->add('compFim', "text", array(
                    'label' => 'Fim M&ecirc;s Compet&ecirc;ncia',
                    'required'    => false,
                        )
                )->add('status', 'checkbox', array(
                    'label' => 'Registro pago?',
                    'required'    => false,
                        )
                );
    }

    public function getName() {
        return 'CustomRelatorioForm';
    }

}
