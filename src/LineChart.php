<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Chart\ChartType;
use Atk4\Chart\DirectionTrait;
use Atk4\Chart\StackedTrait;

class LineChart extends Chart
{
    use DirectionTrait;
    use StackedTrait;

    public $type = ChartType::TYPE_LINE;
}
