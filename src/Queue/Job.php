<?php

namespace Core\Queue;

interface Job
{
    public function handle(): void;
}
