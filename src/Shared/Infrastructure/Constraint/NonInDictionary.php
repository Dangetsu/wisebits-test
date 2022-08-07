<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class NonInDictionary extends Constraint
{

    public function __construct(
        public readonly string $type,
        public readonly string $message = 'The string "{{ string }}" contains an illegal word.',
        mixed $options = null,
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }
}