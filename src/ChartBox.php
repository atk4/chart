<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Ui\Label;
use Atk4\Ui\View;

/**
 * Box that contains a chart.
 */
class ChartBox extends View
{
    #[\Override]
    public $ui = 'segment';

    /** @var string */
    public $label = 'Chart Box';

    #[\Override]
    protected function init(): void
    {
        $this->defaultTemplate = dirname(__DIR__) . '/template/chartbox.html';

        parent::init();

        Label::addTo($this, [$this->label], ['Label'])->addClass('top attached');
    }
}
