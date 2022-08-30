<?php

declare(strict_types=1);

namespace Atk4\Chart;

class BarChart extends Chart
{
    public $type = 'bar';

    /**
     * Set this chart to be horizontal.
     */
    public function setHorizontal(): void
    {
        $this->type = 'horizontalBar';

        // in chartjs 3.9.1 replace with
        // $this->setOptions(['indexAxis' => 'y']);
    }
}
