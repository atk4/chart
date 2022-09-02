<?php

declare(strict_types=1);

namespace Atk4\Chart;

class LineChart extends Chart
{
    use DirectionTrait;
    use StackedTrait;

    public string $type = ChartType::TYPE_LINE;
}
