<?php

declare(strict_types=1);

namespace Atk4\Chart;

class PolarAreaChart extends PieChart
{
    #[\Override]
    public string $type = self::TYPE_POLAR_AREA;
}
