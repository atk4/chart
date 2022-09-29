<?php

declare(strict_types=1);

namespace Atk4\Chart;

/**
 * Adds horizontal / vertical functionality.
 */
trait DirectionTrait
{
    /** @var string */
    protected $horizontalAxis = 'x';

    /** @var string */
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
