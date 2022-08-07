<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Constraint;

use App\Shared\Domain\DictionaryValueCheckerInterface;
use App\Shared\Domain\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class NonInDictionaryValidator extends ConstraintValidator
{
    public function __construct(
        private readonly DictionaryValueCheckerInterface $dictionaryValueChecker,
    ) {}

    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }
        if (empty($constraint->type)) {
            throw new ConstraintDefinitionException("Required properties for validator is not set.");
        }
        if ($this->dictionaryValueChecker->exists($constraint->type, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}