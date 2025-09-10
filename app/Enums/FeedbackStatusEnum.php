<?php

namespace App\Enums;

enum FeedbackStatusEnum: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
}
