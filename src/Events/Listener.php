<?php

namespace Core\Events;

interface Listener
{
    public function on(Event|string $event, callable $callback): void;

    public function emit(Event|string $event, mixed $data = null): void;
}
