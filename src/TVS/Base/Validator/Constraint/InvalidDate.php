<?php

namespace TVS\Base\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class InvalidDate extends Constraint {
    public $message = 'A data "%string%" n&atilde;o est&aacute; em um formato v&aacute;lido. Adote dd/mm/aaaa.';
}
