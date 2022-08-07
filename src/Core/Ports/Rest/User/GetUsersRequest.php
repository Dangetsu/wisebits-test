<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\User;

use App\Shared\Infrastructure\Http\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class GetUsersRequest extends AbstractRequest
{
    /**
     * @var int|null
     */
    #[Assert\Type('integer')]
    #[Assert\GreaterThan(0)]
    #[Assert\LessThanOrEqual(50)]
    protected $limit;
    /**
     * @var int|null
     */
    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(0)]
    protected $offset;

    protected function preparePropertyValue(string $property, mixed $value): mixed
    {
        return match ($property) {
            'limit', 'offset' => is_numeric($value) ? (int)$value : $value,
        };
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }
}