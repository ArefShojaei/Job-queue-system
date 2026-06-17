<?php

namespace Core\Events;

use Core\Events\Event;
use Core\Exceptions\EventNotRegisteredException;

final class EventEmitter implements Listener
{
    private array $listeners = [];

    public function on(Event|string $event, mixed $listener): void
    {
        $this->listeners[$event] = $listener;
    }

    public function emit(Event|string $event, mixed $data = null): void
    {
        if (!isset($this->listeners[$event])) {
            throw new EventNotRegisteredException("Event is not registered!");
        }

        $listener = $this->listeners[$event];

        call_user_func($listener, $data);
    }
}
