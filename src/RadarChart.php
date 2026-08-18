<?php

declare(strict_types=1);

namespace Atk4\Chart;

class RadarChart extends Chart
{
    #[\Override]
    public string $type = self::TYPE_RADAR;
}
