<?php

namespace TVS\Base\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class InvalidCompetencia extends Constraint {
    public $message = 'O valor "%string%" n&atilde;o est&aacute; em um formato v&aacute;lido. Adote mm/aaaa.';
}
