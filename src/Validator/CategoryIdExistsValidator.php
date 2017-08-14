<?php

namespace Validator;

use Doctrine\DBAL\Connection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CategoryIdExistsValidator extends ConstraintValidator
{
    private $db;

    public function validate($value, Constraint $constraint)
    {
        if (null === $value) return;

        $categoryIdExists = $this->db->fetchColumn('SELECT COUNT(*) FROM categories WHERE id = ?', [$value]);

        if (!$categoryIdExists) {
            $this->context->addViolation($constraint->message);
        }
    }

    public function setDependency(Connection $db)
    {
        $this->db = $db;
    }
}