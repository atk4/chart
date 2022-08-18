<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Core\Exception;
use Atk4\Data\Model;
use Atk4\Ui\JsExpression;
use Atk4\Ui\View;

/**
 * Implement basic logic for ChartJS.
 */
class Chart extends View
{
    /** @var string HTML element type */
    public $element = 'canvas';

    /** @var string Type of chart - bar|pie etc. */
    public $type;

    /** @var bool should we add JS include into application body? Set "false" if you do it manually. */
    public $js_include = true;

    /** @var array We will use these colors in charts */
    public $nice_colors = [
        ['rgba(255, 99, 132, 0.2)', 'rgba(255,99,132,1)'],
        ['rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)'],
        ['rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)'],
        ['rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)'],
        ['rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)'],
        ['rgba(255, 159, 64, 0.2)', 'rgba(255, 159, 64, 1)'],
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
    protected function init(): void
    {
        parent::init();

        if ($this->js_include) {
            $this->getApp()->requireJS('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js');
        }
    }

    /**
     * Renders chart view.
     */
    public function renderView(): void
    {
        $this->js(true, new JsExpression('new Chart([], []);', [$this->name, $this->getConfig()]));

        parent::renderView();
    }

    /**
     * Builds configuration for a chart.
     */
    public function getConfig(): array
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
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Return datasets.
     */
    public function getDataSets(): array
    {
        return array_values($this->dataSets);
    }

    /**
     * Return options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set options.
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        // Important: use replace not merge here to preserve numeric keys !!!
        $this->options = array_replace_recursive($this->options, $options);

        return $this;
    }

    /**
     * Specify data source for this chart. The column must contain
     * the textual column first followed by sumber of data columns:
     * setModel($month_report, ['month', 'total_sales', 'total_purchases']);.
     *
     * This component will automatically figure out name of the chart,
     * series titles based on column captions etc.
     */
    public function setModel(Model $model, array $columns = []): void
    {
        if (!$columns) {
            throw new Exception('Second argument must be specified to Chart::setModel()');
        }

        $this->dataSets = [];

        // Initialize data-sets
        foreach ($columns as $key => $column) {
            if ($key === 0) {
                $title_column = $column;

                continue; // skipping labels
            }

            $colors = array_shift($this->nice_colors);

            $this->dataSets[$column] = [
                'label' => $model->getField($column)->getCaption(),
                'backgroundColor' => $colors[0],
                'borderColor' => $colors[1],
                'borderWidth' => 1,
                'data' => [],
            ];
        }

        // Prepopulate data-sets
        foreach ($model as $row) {
            $this->labels[] = $row->get($title_column);
            foreach ($this->dataSets as $key => &$dataset) {
                $dataset['data'][] = $row->get($key);
            }
        }

    }

    /**
     * Add currency label.
     *
     * @param string $char Currency symbol
     * @param string $axis y or x
     *
     * @return $this
     */
    public function withCurrency(string $char = '€', string $axis = 'y')
    {
        // magic regex adds commas as thousand separators: http://009co.com/?p=598
        $options['scales'][$axis . 'Axes'] =
            [['ticks' => [
                'userCallback' => new JsExpression('{}', ['function(value) { value=Math.round(value*1000000)/1000000; return "' . $char . ' " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }']),
            ]]];

        $options['tooltips'] = [
            'enabled' => true,
            'mode' => 'single',
            'callbacks' => ['label' => new JsExpression('{}', ['function(item, data) { return item.' . $axis . 'Label ? "' . $char . ' " +  item.' . $axis . 'Label.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "No Data"; }'])],
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
    public function withCurrencyX(string $char = '€')
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
    public function withCurrencyY(string $char = '€')
    {
        return $this->withCurrency($char, 'y');
    }

    /**
     * Will produce a graph showing summary of a certain model by grouping and aggregating data.
     *
     * Example:
     *
     *   // Pie or Bar chart
     *   $chart->summarize($users, ['by'=>'status', 'fx'=>'count']);
     *   $chart->summarize($users, ['by'=>'status', 'fx'=>'sum', 'field'=>'total_net']);
     *
     * or
     *
     *   // Bar chart
     *   $orders = $clients->ref('Orders');
     *   $chart->summarize($orders, [
     *      'by'=>$orders->expr('year([date])'),
     *      'fields'=>[
     *        'purchase'=>$orders->expr('sum(if([is_purchase], [amount], 0)'),
     *        'sale'=>$orders->expr('sum(if([is_purchase], 0, [amount])'),
     *      ],
     *   ])->withCurrency('$');
     */
    public function summarize(Model $model, array $options = [])
    {
        $fields = ['by'];

        // first lets query data
        if (isset($options['fields'])) {
            $qq = $model->action('select', [[]]);

            // now add fields
            foreach ($options['fields'] as $alias => $field) {
                if (is_numeric($alias)) {
                    $alias = $field;
                }
                if (is_string($field)) {
                    // sanitization needed!
                    $field = $model->expr(($options['fx'] ?? '') . '([' . $field . '])');
                }

                $qq->field($field, $alias);

                $fields[] = $alias;
            }
        } else {
            $fx = $options['fx'] ?? 'count';
            if ($fx === 'count') {
                $qq = $model->action('count', ['alias' => $fx]);
                $fields[] = $fx;
            } elseif (isset($options['fx'])) {
                $qq = $model->action('fx', [$fx, $options['field'] ?? $model->expr('*'), 'alias' => $fx]);
                $fields[] = $fx;
            } else {
                $qq = $model->action('select', [[$model->title_field]]);
                $fields[] = $model->title_field;
            }
        }

        // next we need to group
        if ($options['by'] ?? null) {
            $field = $options['by'];
            if (is_string($field)) {
                $field = $model->getField($field);
            }
            $qq->field($field, 'by');
            $qq->group('by');
        } else {
            $qq->field($model->getField($model->title_field), 'by');
        }

        // and then set it as chart source
        $this->setSource($qq->get(), $fields);

        return $this;
    }
}
