<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Ui\App;

/**
 * Adds horizontal / vertical functionality.
 */
trait DirectionTrait
{
    /** @var string Horizontal axis */
    protected $horizontalAxis = 'x';

    /** @var string Vertical axis */
    protected $verticalAxis = 'y';

    /**
     * Set this chart to be horizontal.
     */
    public function setHorizontal(): void
    {
        $this->setOptions(['indexAxis' => $this->horizontalAxis]);
    }

    /**
     * Set this chart to be vertical.
     */
    public function setVertical(): void
    {
        $this->setOptions(['indexAxis' => $this->verticalAxis]);
    }
}
