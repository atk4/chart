<?php

declare(strict_types=1);

namespace Atk4\Chart;

class DoughnutChart extends PieChart
{
    #[\Override]
    public string $type = self::TYPE_DOUGHNUT;
}
