<?php

namespace Validator;

use Doctrine\DBAL\Connection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCategoryNameValidator extends ConstraintValidator
{
    private $db;

    public function validate($value, Constraint $constraint)
    {
        $categoryNameIsNotValid = $this->db->fetchColumn('SELECT COUNT(*) FROM categories WHERE name = ?', [$value]);

        if ($categoryNameIsNotValid) {
            $this->context->addViolation($constraint->message);
        }
    }

    public function setDependency(Connection $db)
    {
        $this->db = $db;
    }
}