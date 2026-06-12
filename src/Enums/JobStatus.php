<?php

namespace Core\Enums;

enum JobStatus
{
    case PENDING;

    case PROCESSING;

    case COMPLETED;

    case FAILED;
}
