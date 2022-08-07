<?php

declare(strict_types=1);

namespace App\Core\Domain\User;

use App\Shared\Domain\AbstractEntityEvent;

/**
 * @method UserInterface getEntity()
 */
final class UserCreatedEvent extends AbstractEntityEvent
{

}