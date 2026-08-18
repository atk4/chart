<?php

declare(strict_types=1);

namespace Atk4\Chart;

class BubbleChart extends ScatterChart
{
    #[\Override]
    public string $type = self::TYPE_BUBBLE;
}
