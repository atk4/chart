<?php
namespace atk4\chart;

/**
 * Implement basic logic for ChartJS.
 */
class Chart extends \atk4\ui\View {

    /** @var string HTML element type */
    public $element = 'canvas';

    /** @var string Type of chart - bar|pie etc. */
    public $type;

    /** @var array We will use these colors in charts */
    public $nice_colors = [
        [ 'rgba(255, 99, 132, 0.2)', 'rgba(255,99,132,1)'],
        [ 'rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)'],
        [ 'rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)'],
        [ 'rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)'],
        [ 'rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)'],
        [ 'rgba(255, 159, 64, 0.2)', 'rgba(255, 159, 64, 1)']
    ];

    /** @var array Options for chart.js widget */
    public $options = [];

    /** @var array Labels for axis. Fills with setModel(). */
    protected $labels;

    /** @var array Datasets. Fills with setModel(). */
    protected $dataSets;

    /**
     * Initialization.
     */
    public function init()
    {
        parent::init();

        // Not yet supported, so will do manually
        //$this->app->requireJS('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js');
        $this->app->html->template->appendHTML('HEAD', '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>');
    }

    /**
     * Renders chart view.
     */
    public function renderView()
    {
        $this->js(true, new \atk4\ui\jsExpression('new Chart([], []);', [$this->name, $this->getConfig()]));
        parent::renderView();
    }

    /**
     * Builds configuration for a chart.
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'type' => $this->type,
            'data' => [
                'labels' => $this->getLabels(),
                'datasets' => $this->getDataSets(),
            ],
            'options' => $this->getOptions(),
        ];

    }

    /**
     * Return labels.
     *
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Return datasets.
     *
     * @return array
     */
    public function getDataSets()
    {
        return array_values($this->dataSets);
    }

    /**
     * Return options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        // Important: use replace not merge here to preserve numeric keys !!!
        $this->options = array_replace_recursive($this->options, $options);

        return $this;
    }

    /**
     * Specify data source for this chart. The column must contain
     * the textual column first followed by sumber of data columns:
     * setModel($month_report, ['month', 'total_sales', 'total_purchases']);
     *
     * This component will automatically figure out name of the chart,
     * series titles based on column captions etc.
     *
     * @param \atk4\data\Model $model
     * @param array            $columns
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $model, $columns = []) {

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

        return $model;
    }

    /**
     * Add currency label.
     *
     * @param string $char Currency symbol
     * @param string $axis y or x
     *
     * @return $this
     */
    public function withCurrency($char = '€', $axis = 'y') {
        $options['scales'][$axis.'Axes'] =
            [['ticks'=>[
                'userCallback'=>new \atk4\ui\jsExpression('{}', ['function(value) { return "'.$char.'" + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }'])
            ]]];

        $options['tooltips'] = [
            'enabled'=>true,
            'mode'=>'single',
            'callbacks'=> ['label'=> new \atk4\ui\jsExpression('{}', ['function(item, data) { return "'.$char.'" +  item.'.$axis.'Label.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }'])]
        ];

        $this->setOptions($options);

        return $this;
    }

    /**
     * Add currency label to X axis.
     *
     * @param string $char Currency symbol
     *
     * @return $this
     */
    public function withCurrencyX($char = '€')
    {
        return $this->withCurrency($char, 'x');
    }

    /**
     * Add currency label to Y axis.
     *
     * @param string $char Currency symbol
     *
     * @return $this
     */
    public function withCurrencyY($char = '€')
    {
        return $this->withCurrency($char, 'y');
    }
}
