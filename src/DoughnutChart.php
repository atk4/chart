<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Chart\ChartType;
use Atk4\Core\Exception;
use Atk4\Data\Model;
use Atk4\Ui\JsExpression;

class DoughnutChart extends PieChart
{
    /** @var string Type of chart */
    public $type = ChartType::TYPE_DOUGHNUT;
}
