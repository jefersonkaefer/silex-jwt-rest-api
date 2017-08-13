<?php

namespace Validator;

use Symfony\Component\Validator\Constraint;

class UniqueCategoryName extends Constraint
{
    public $message = 'Category name should be unique.';

    public function validatedBy()
    {
        return 'validator.unique_category_name';
    }
}