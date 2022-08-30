<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Core\Exception;
use Atk4\Data\Model;
use Atk4\Ui\JsExpression;
use Atk4\Ui\View;

/**
 * ChartJS 2.7.x documentation https://www.chartjs.org/docs/2.7.3/
 * ChartJS 3.9.1 documentation https://www.chartjs.org/docs/3.9.1/.
 */
class Chart extends View
{
    /** @var string HTML element type */
    public $element = 'canvas';

    /** @var string Type of chart - bar|pie etc. */
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
        ['rgba(20, 20, 20, 0.2)', 'rgba(20, 20, 20, 1)'],
    ];

    /** @var array<string, mixed> Options for chart.js widget */
    public $options = [];

    /** @var array<int, string> Labels for axis. Fills with setModel(). */
    protected $labels;

    /** @var array<string, array<string, mixed>> Datasets. Fills with setModel(). */
    protected $datasets;

    protected function init(): void
    {
        parent::init();

        if ($this->jsInclude) {
            $this->getApp()->requireJs('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js');
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
     * Specify data source for this chart. The column must contain
     * the textual column first followed by sumber of data columns:
     * setModel($month_report, ['month', 'total_sales', 'total_purchases']);.
     *
     * This component will automatically figure out name of the chart,
     * series titles based on column captions etc.
     *
     * Example for bar chart with two side-by side bars per category, and one of them stacked:
     *
     * $chart->setModel(
     *      $model,
     *      ['month', 'turnover_month_shoes', 'turnover_month_shirts', 'turnover_month_trousers', 'turnover_month_total_last_year'],
     *      [1, 1, 1, 2] // 1 => shoes+shirts+trousers, 2 => total last year
     *  );
     *
     * @param array<int, string> $columns
     * @param array<int, mixed> $stacks
     */
    public function setModel(Model $model, array $columns = [], array $stacks = []): void
    {
        if ($columns === []) {
            throw new Exception('Second argument must be specified to Chart::setModel()');
        }

        $this->datasets = [];

        // initialize data-sets
        foreach ($columns as $key => $column) {
            if ($key === 0) {
                $titleColumn = $column;

                continue; // skipping label column
            }

            $colors = array_shift($this->niceColors);
            $stack = array_shift($stacks);

            $this->datasets[$column] = [
                'label' => $model->getField($column)->getCaption(),
                'backgroundColor' => $colors[0],
                'borderColor' => $colors[1],
                'borderWidth' => 1,
                'data' => [],
            ];

            if ($stack !== null) {
                $this->datasets[$column]['stack'] = $stack;
            }
        }

        if ($stacks !== []) {
            $this->setOptions(['scales' => ['yAxes' => [0 => ['stacked' => true]], 'xAxes' => [0 => ['stacked' => true]]]]);
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
