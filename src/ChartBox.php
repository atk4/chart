<?php

namespace atk4\chart;

/**
 * Implements a box that contains a chart
 */
class ChartBox extends \atk4\ui\View {
    public $ui = 'segment';
    public $label = 'Chart Box';


    function init() {
        $this->defaultTemplate = dirname(dirname(__FILE__)).'/template/chartbox.html';
        parent::init();

        $this->add(new \atk4\ui\Label($this->label), 'Label')->addClass('top attached');
    }
}
