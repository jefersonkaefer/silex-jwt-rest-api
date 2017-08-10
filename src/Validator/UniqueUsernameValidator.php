<?php

namespace Validator;

use Doctrine\DBAL\Connection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUsernameValidator extends ConstraintValidator
{
    private $db;

    public function validate($value, Constraint $constraint)
    {
        $usernameIsNotValid = $this->db->fetchColumn('SELECT COUNT(*) FROM users WHERE username = ?', [$value]);

        if ($usernameIsNotValid) {
            $this->context->addViolation($constraint->message);
        }
    }

    public function setDependency(Connection $db)
    {
        $this->db = $db;
    }
}