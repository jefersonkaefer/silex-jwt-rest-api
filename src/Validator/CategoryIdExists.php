<?php

namespace Validator;

use Symfony\Component\Validator\Constraint;

class CategoryIdExists extends Constraint
{
    public $message = 'Category ID not exists.';

    public function validatedBy()
    {
        return 'validator.category_id_exists';
    }
}