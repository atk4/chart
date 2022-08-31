<?php

declare(strict_types=1);

namespace Atk4\Chart;

class BarChart extends Chart
{
    use DirectionTrait;
    use StackedTrait;

    public $type = ChartType::TYPE_BAR;

    /**
     * @param array|string $label
     */
    public function __construct($label = [])
    {
        // Bar chart understand axis opposite as Line chart
        $this->horizontalAxis = 'y';
        $this->verticalAxis = 'x';

        parent::__construct($label);
    }
}
