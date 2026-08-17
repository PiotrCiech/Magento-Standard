<?php

declare(strict_types=1);

namespace Rokezzz\Invoice\Model\Invoice;

enum Unit: string
{
    case Service = 'service';
    case Item = 'item';
}
