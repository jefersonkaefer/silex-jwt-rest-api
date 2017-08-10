<?php

namespace Validator;

use Symfony\Component\Validator\Constraint;

class UniqueUsername extends Constraint
{
    public $message = 'Username should be unique.';

    public function validatedBy()
    {
        return 'validator.unique_username';
    }
}