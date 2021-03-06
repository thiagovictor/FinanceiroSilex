<?php

namespace TVS\Financeiro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use TVS\Base\Validator\Constraint\InvalidDate;
use TVS\Application;

class ParceladoType extends AbstractType {

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

        return $builder->add('option', 'hidden', array(
                    'data' => 'normal',
                        )
                )->add('valor', "money", array(
                    'currency' => 'BRL',
                    'grouping' => '1', //NAO SETAR TRUE CONFORME DOCUMENTAÇÃO SYMFONY
                    'constraints' => array(new NotBlank()),
                    'label' => 'Valor da parcela',
                        )
                )->add('descricao', "text", array(
                    'constraints' => array(new NotBlank(), new Length(array('max' => 100))),
                    'label' => 'Descri&ccedil;&atilde;o',
                        )
                )->add('parcelas', "text", array(
                    'required' => true,
                    'label' => 'Qtd. de parcelas',
                        )
                )->add('vencimento', "text", array(
                    'constraints' => array(new NotBlank(),new InvalidDate()),
                    'label' => 'Vencimento da 1&ordf; parcela',
                        )
                )->add('tipo', 'choice', array(
                    'choices' => array('DESPESA' => 'DESPESA', 'RECEITA' => 'RECEITA'),
                    'required' => true,
                    'label' => 'Lan&ccedil;ar como'
                        )
                )->add('centrocusto', 'choice', array(
                    'choices' => $this->centrocusto,
                    'required' => false,
                    'label' => 'C.custo'
                        )
                )->add('favorecido', 'choice', array(
                    'choices' => $this->favorecido,
                    'required' => false,
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
                );
    }

    public function getName() {
        return 'ParceladoForm';
    }

}
