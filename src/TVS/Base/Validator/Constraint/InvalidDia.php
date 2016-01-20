<?php

namespace TVS\Base\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */

class InvalidDia extends Constraint {
    public $message = 'O valor "%string%" n&atilde;o est&aacute; em um formato v&aacute;lido. Este valor deve estar entre 1 - 31.';
}
