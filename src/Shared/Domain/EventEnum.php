<?php

declare(strict_types=1);

namespace App\Shared\Domain;

enum EventEnum
{
    case CREATE;
    case UPDATE;
    case DELETE;
}