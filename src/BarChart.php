<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Chart\ChartType;
use Atk4\Chart\DirectionTrait;
use Atk4\Chart\StackedTrait;

class BarChart extends Chart
{
<<<<<<< Updated upstream
    public $type = 'bar';
=======
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
>>>>>>> Stashed changes
}
