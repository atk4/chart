<?php
namespace atk4\chart;

/**
 * Implement basic logic for ChartJS
 */
class Chart extends \atk4\ui\View {
    public $element = 'canvas';

    public $labels;

    public $nice_colors = [ 
        [ 'rgba(255, 99, 132, 0.2)', 'rgba(255,99,132,1)'], 
        [ 'rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)'],
        [ 'rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)'],
        [ 'rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)'],
        [ 'rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)'],
        [ 'rgba(255, 159, 64, 0.2)', 'rgba(255, 159, 64, 1)']
    ];

    public $options = [];

    function init() {
        parent::init();

        // Not yet supported, so will do manually
        //$this->app->requireJS('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.js');
        $this->app->html->template->appendHTML('HEAD', '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.js"></script>');

    }

    function renderView() {
        $this->js(true, new \atk4\ui\jsExpression('new Chart([], []);', [ $this->name, $this->getConfig() ]));
        parent::renderView();
    }



    /**
     * Builds configuration for a chart
     */
    function getConfig() {

        return [
            'type'=>$this->type,
            'data'=>[
                'labels'=>$this->getLabels(),
                'datasets'=>$this->getDataSets(),
            ],
            'options'=>$this->getOptions(),
        ];

    }

    function getLabels() {
        return $this->labels;
    }

    function getDataSets() {
        return array_values($this->dataSets);
    }

    function getOptions() {
        return $this->options;
    }

    /**
     * Specify data source for this chart. The column must contain
     * the textual column first followed by sumber of data columns:
     * setModel($month_report, ['month', 'total_sales', 'total_purchases']);
     *
     * This component will automatically figure out name of the chart,
     * series titles based on column captions etc.
     */
    function setModel(\atk4\data\Model $model, $columns = []) {

        if (!$columns) {
            throw new \atk4\core\Exception('Second argument must be specified to Chart::setModel()');
        }

        $this->dataSets = [];

        // Initialize data-sets
        foreach ($columns as $key=>$column) {

            if ($key == 0) {
                $title_column = $column;
                continue; // skipping labels
            }

            $colors = array_shift($this->nice_colors);

            $this->dataSets[$column] = [
                'label'=>$model->getElement($column)->getCaption(),
                'backgroundColor'=>$colors[0],
                'borderColor'=>$colors[1],
                'borderWidth'=>1,
                'data'=>[],
            ];
        }


        // Prepopulate data-sets
        foreach ($model as $row) {

            $this->labels[] = $row[$title_column];
            foreach ($this->dataSets as $key => &$dataset) {
                $dataset['data'][] = $row[$key];
            }
        }

    }

    function withCurrency($char = '€') {
        $this->options['scales']['yAxes'] =
            [['ticks'=>[
                'userCallback'=>new \atk4\ui\jsExpression('{}', ['function(value) { return "'.$char.'" + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }'])
            ]]];

        $this->options['tooltips'] = [
            'enabled'=>true,
            'mode'=>'single',
            'callbacks'=> ['label'=> new \atk4\ui\jsExpression('{}', ['function(item, data, bb) { return "'.$char.'" +  item.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }'])]
        ];
        return $this;
    }
}
