<?php

declare(strict_types=1);

namespace Atk4\Chart;

/**
 * Supported chart types.
 */
class ChartType
{
    /** @const string */
    public const TYPE_BAR = 'bar';

    /** @const string */
    public const TYPE_LINE = 'line';

    /** @const string */
    public const TYPE_PIE = 'pie';

    /** @const string */
    public const TYPE_DOUGHNUT = 'doughnut';

    /** @const string */
    public const TYPE_SCATTER = 'scatter';

    /** @const string */
    public const TYPE_RADAR = 'radar';

    /** @const string */
    public const TYPE_BUBBLE = 'bubble';

    /** @const string */
    public const TYPE_POLAR_AREA = 'polarArea';
}
