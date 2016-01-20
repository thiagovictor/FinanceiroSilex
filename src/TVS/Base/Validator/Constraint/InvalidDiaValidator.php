<?php

namespace TVS\Base\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InvalidDiaValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
        if (!$this->checkDateByFormat($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
    
    public function checkDateByFormat($date, $format = 'd') {
        $t = \DateTime::createFromFormat($format, $date);

        if ($t === false) {
            return false;
        }

        return $t->format($format) === $date;
    }
}
