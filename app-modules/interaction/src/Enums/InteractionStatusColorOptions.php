<?php

namespace Assist\Interaction\Enums;

enum InteractionStatusColorOptions: string
{
    case SUCCESS = 'success';

    case DANGER = 'danger';

    case WARNING = 'warning';

    case INFO = 'info';

    case PRIMARY = 'primary';

    case GRAY = 'gray';
}
