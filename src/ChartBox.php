<?php

namespace atk4\chart;

use atk4\ui\Label;
use atk4\ui\View;

/**
 * Implements a box that contains a chart
 */
class ChartBox extends View
{
    public $ui = 'segment';
    public $label = 'Chart Box';


    public function init()
    {
        $this->defaultTemplate = dirname(__DIR__).'/template/chartbox.html';
        parent::init();

        $this->add(new Label($this->label), 'Label')->addClass('top attached');
    }
}
