<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Core\Exception;
use Atk4\Data\Model;
use Atk4\Ui\JsExpression;
use Atk4\Ui\View;

/**
 * ChartJS 3.9.1 documentation https://www.chartjs.org/docs/3.9.1/
 * Chart examples https://www.chartjs.org/docs/latest/samples/information.html.
 */
class Chart extends View
{
    /** @var string HTML element type */
    public $element = 'canvas';

    /** @var string Type of chart - bar|pie etc. See ChartType class */
    public string $type;

    /** @var bool should we add JS include into application body? Set "false" if you do it manually. */
    public $jsInclude = true;

    /** @var string */
    protected $cdnUrl = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';

    /** @var array<int, array<int, string>> We will use these colors in charts */
    public $niceColors = [
        ['rgba(255, 99, 132, 0.2)', 'rgba(255,99,132,1)'],
        ['rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)'],
        ['rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)'],
        ['rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)'],
        ['rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)'],
        ['rgba(255, 159, 64, 0.2)', 'rgba(255, 159, 64, 1)'],
        ['rgba(20, 20, 20, 0.2)', 'rgba(20, 20, 20, 1)'],
    ];

    /** @var array<string, mixed> Options for chart.js widget */
    public $options = [];

    /** @var array<string, array<mixed, mixed>> Options for each data column for chart.js widget */
    public $column_options = [];

    /** @var array<int, string> Columns (data model fields) used in chart */
    protected $columns;

    /** @var array<int, string> Labels for axis. Fills with setModel(). */
    protected $labels;

    /** @var array<mixed, array<string, mixed>> Datasets. Fills with setModel(). */
    protected $datasets;

    protected function init(): void
    {
        parent::init();

        if ($this->jsInclude) {
            $this->getApp()->requireJs($this->cdnUrl);
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
    protected function getConfig(): array
    {
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
     * @return array<int, string>|null
     */
    protected function getLabels(): ?array
    {
        return $this->labels;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getDatasets(): array
    {
        foreach ($this->column_options as $column => $options) {
            $this->datasets[$column] = array_merge_recursive($this->datasets[$column], $options);
        }

        return array_values($this->datasets);
    }

    /**
     * @param array<mixed, array<string, mixed>> $datasets
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
    protected function getOptions(): array
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
     * @param array<string, array<mixed, mixed>> $options column_name => array of options
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
     * the textual column first followed by number of data columns:
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
        $this->columns = $columns;

        parent::setModel($model);

        $this->prepareDatasets();
    }

    /**
     * Fills dataset with data from data model.
     */
    protected function prepareDatasets(): void
    {
        if ($this->model === null || $this->columns === null) {
            return;
        }

        $datasets = [];

        // initialize data-sets
        foreach ($this->columns as $key => $column) {
            if ($key === 0) {
                $titleColumn = $column;

                continue; // skipping label column
            }

            $colors = array_shift($this->niceColors);

            $datasets[$column] = [
                'label' => $this->model->getField($column)->getCaption(),
                'backgroundColor' => $colors[0],
                'borderColor' => $colors[1],
                'borderWidth' => 1,
                'data' => [],
            ];
        }

        // prepopulate data-sets
        foreach ($this->model as $entity) {
            $this->labels[] = $entity->get($titleColumn); // @phpstan-ignore-line
            foreach ($datasets as $key => &$dataset) {
                $dataset['data'][] = $entity->get($key);
            }
        }

        $this->setDatasets($datasets);
    }

    /**
     * Add currency label.
     *
     * @param string $char Currency symbol
     * @param string $axis y or x
     *
     * @return $this
     */
    public function setCurrencyLabel(string $char = '€', string $axis = 'y')
    {
        // magic regex adds commas as thousand separators: http://009co.com/?p=598
        $options = [
            'scales' => [
                $axis => [
                    'ticks' => [
                        'callback' => new JsExpression('{}', [
                            'function(value, index, ticks) {
                                value = Math.round(value*1000000)/1000000;
                                return "' . $char . ' " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            }',
                        ]),
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'point',
                    'callbacks' => [
                        'label' => new JsExpression('{}', [
                            'function(context) {
                                let label = context.dataset.label || "";
                                let value = context.parsed.y;
                                if (label) {
                                    label += ": ";
                                }
                                return label + (value ? "' . $char . ' " +  value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "No Data");
                            }',
                        ]),
                    ],
                ],
            ],
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
    public function setCurrencyLabelX(string $char = '€')
    {
        return $this->setCurrencyLabel($char, 'x');
    }

    /**
     * Add currency label to Y axis.
     *
     * @param string $char Currency symbol
     *
     * @return $this
     */
    public function setCurrencyLabelY(string $char = '€')
    {
        return $this->setCurrencyLabel($char, 'y');
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
     *   ])->setCurrencyLabel('$');
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
