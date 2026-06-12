<?php

namespace Core\Queue;

interface Runable
{
    public function run(): void;
}
