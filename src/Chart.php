<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Core\Exception;
use Atk4\Data\Model;
use Atk4\Ui\JsExpression;
use Atk4\Ui\View;

<<<<<<< Updated upstream
=======
/**
 * ChartJS 3.9.1 documentation https://www.chartjs.org/docs/3.9.1/
 * Chart examples https://www.chartjs.org/docs/latest/samples/information.html.
 */
>>>>>>> Stashed changes
class Chart extends View
{
    /** @var string HTML element type */
    public $element = 'canvas';

    /** @var string Type of chart - bar|pie etc. See ChartType class */
    public $type;

    /** @var bool should we add JS include into application body? Set "false" if you do it manually. */
    public $jsInclude = true;

    /** @var array<int, array{string, string}> We will use these colors in charts */
    public $niceColors = [
        ['rgba(255, 99, 132, 0.2)', 'rgba(255,99,132,1)'],
        ['rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)'],
        ['rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)'],
        ['rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)'],
        ['rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)'],
        ['rgba(255, 159, 64, 0.2)', 'rgba(255, 159, 64, 1)'],
    ];

    /** @var array<string, mixed> Options for chart.js widget */
    public $options = [];

    /** @var array<string, array{mixed, mixed}> Options for each data column for chart.js widget */
    public $column_options = [];

    /** @var array<int, string> Labels for axis. Fills with setModel(). */
    protected $labels;

    /** @var array<string, array<string, mixed>> Datasets. Fills with setModel(). */
    protected $datasets;

    protected function init(): void
    {
        parent::init();

        if ($this->jsInclude) {
            $this->getApp()->requireJs('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js');
        }
    }

    public function renderView(): void
    {
        $this->js(true, new JsExpression('new Chart([], []);', [$this->name, $this->getConfig()]));

        parent::renderView();
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        if ($this->type === null) {
            throw new Exception('Chart type should be set');
        }

        foreach ($this->column_options as $column => $options) {
            $this->datasets[$column] = array_merge($this->datasets[$column], $options);
        }

        return [
            'type' => $this->type,
            'data' => [
                'labels' => $this->getLabels(),
                'datasets' => $this->getDatasets(),
            ],
            'options' => $this->getOptions(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getDatasets(): array
    {
        return array_values($this->datasets);
    }

    /**
     * @param array<int, array{string, mixed}> $datasets
     *
     * @return $this
     */
    public function setDatasets(array $datasets)
    {
        $this->datasets = $datasets;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        // IMPORTANT: use replace not merge here to preserve numeric keys !!!
        $this->options = array_replace_recursive($this->options, $options);

        return $this;
    }

    /**
     * @param array<string, array{mixed, mixed}> $options column_name => array of options
     *
     * @return $this
     */
    public function setColumnOptions(array $options)
    {
        // IMPORTANT: use replace not merge here to preserve numeric keys !!!
        $this->column_options = array_replace_recursive($this->column_options, $options);

        return $this;
    }

    /**
     * Specify data source for this chart. The column must contain
     * the textual column first followed by sumber of data columns:
     * setModel($month_report, ['month', 'total_sales', 'total_purchases']);.
     *
     * This component will automatically figure out name of the chart,
     * series titles based on column captions etc.
     *
     * @param array<int, string> $columns
     */
    public function setModel(Model $model, array $columns = []): void
    {
        if ($columns === []) {
            throw new Exception('Second argument must be specified to Chart::setModel()');
        }

        $this->datasets = [];

        // initialize data-sets
        foreach ($columns as $key => $column) {
            if ($key === 0) {
                $titleColumn = $column;

                continue; // skipping labels
            }

            $colors = array_shift($this->niceColors);

            $this->datasets[$column] = [
                'label' => $model->getField($column)->getCaption(),
                'backgroundColor' => $colors[0],
                'borderColor' => $colors[1],
                'borderWidth' => 1,
                'data' => [],
            ];
        }

        // prepopulate data-sets
        foreach ($model as $entity) {
            $this->labels[] = $entity->get($titleColumn); // @phpstan-ignore-line
            foreach ($this->datasets as $key => &$dataset) {
                $dataset['data'][] = $entity->get($key);
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
        $options = [];
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
     *   $chart->summarize($users, ['by' => 'status', 'fx' => 'count']);
     *   $chart->summarize($users, ['by' => 'status', 'fx' => 'sum', 'field' => 'total_net']);
     *
     * or
     *
     *   // Bar chart
     *   $orders = $clients->ref('Orders');
     *   $chart->summarize($orders, [
     *       'by'=>$orders->expr('year([date])'),
     *       'fields'=>[
     *            'purchase' => $orders->expr('sum(if([is_purchase], [amount], 0)'),
     *           'sale' => $orders->expr('sum(if([is_purchase], 0, [amount])'),
     *       ],
     *   ])->withCurrency('$');
     *
     * @param array<string, mixed> $options
     *
     * @return $this
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
                $qq = $model->action('select', [[$model->titleField]]);
                $fields[] = $model->titleField;
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
            $qq->field($model->getField($model->titleField), 'by');
        }

        // and then set it as chart source
        $this->setSource($qq->getRows(), $fields);

        return $this;
    }
}
