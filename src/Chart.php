<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Core\Exception;
use Atk4\Data\Model;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\View;

/**
 * Chart.js 3.9.1 documentation: https://www.chartjs.org/docs/3.9.1/
 * Examples https://www.chartjs.org/docs/latest/samples/information.html .
 */
class Chart extends View
{
    public const TYPE_BAR = 'bar';
    public const TYPE_LINE = 'line';
    public const TYPE_PIE = 'pie';
    public const TYPE_DOUGHNUT = 'doughnut';
    public const TYPE_SCATTER = 'scatter';
    public const TYPE_RADAR = 'radar';
    public const TYPE_BUBBLE = 'bubble';
    public const TYPE_POLAR_AREA = 'polarArea';

    public string $element = 'canvas';

    /** Type of chart - bar|pie etc. See TYPE_* constants */
    public string $type;

    /** @var array<string, mixed> Options for Chart.js widget */
    public $options = [];

    /** @var array<string, array<mixed, mixed>> Options for each data column for Chart.js widget */
    public $columnOptions = [];

    /** @var array<int, string> Columns (data model fields) used in chart */
    protected $columns;

    /** @var array<int, string> Labels for axis. Fills with setModel(). */
    protected $labels;

    /** @var array<mixed, array<string, mixed>> Datasets. Fills with setModel(). */
    protected $datasets;

    /** @var ColorGenerator */
    public $colorGenerator;

    protected function init(): void
    {
        parent::init();

        $this->colorGenerator = new ColorGenerator();

        $this->getApp()->requireJs($this->getApp()->cdn['chart.js'] . '/chart.min.js');
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
        foreach ($this->columnOptions as $column => $options) {
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
        $this->columnOptions = array_replace_recursive($this->columnOptions, $options);

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
        $datasets = [];

        // initialize data-sets
        foreach ($this->columns as $key => $column) {
            if ($key === 0) {
                $titleColumn = $column;

                continue; // skipping label column
            }

            $colors = $this->colorGenerator->getNextColorPair();

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
            foreach ($datasets as $column => $dataset) {
                $datasets[$column]['data'][] = $entity->get($column);
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
    public function setCurrencyLabel(string $char = '€', string $axis = 'y', int $digits = 2)
    {
        $options = [
            'scales' => [
                $axis => [
                    'ticks' => [
                        'callback' => new JsFunction(['value', 'index', 'ticks'], [
                            new JsExpression('return "' . $char . ' " + Number(value).toLocaleString(undefined, {minimumFractionDigits: ' . $digits . ', maximumFractionDigits: ' . $digits . '})'),
                        ]),
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'point',
                    'callbacks' => [
                        'label' => new JsFunction(['context'], [
                            new JsExpression(
                                <<<'EOF'
                                    let label = context.dataset.label || "";
                                    // let value = context.parsed.y; // or x (horizontal) or r (radar) etc
                                    let value = context.formattedValue.replace(/,/, "");
                                    if (label) {
                                        label += ": ";
                                    }
                                return label + (value ? [] +  Number(value).toLocaleString(undefined, {minimumFractionDigits: [], maximumFractionDigits: []}) : "No Data")
                                EOF, [$char, $digits, $digits]),
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
    public function setCurrencyLabelX(string $char = '€', int $digits = 2)
    {
        return $this->setCurrencyLabel($char, 'x', $digits);
    }

    /**
     * Add currency label to Y axis.
     *
     * @param string $char Currency symbol
     *
     * @return $this
     */
    public function setCurrencyLabelY(string $char = '€', int $digits = 2)
    {
        return $this->setCurrencyLabel($char, 'y', $digits);
    }
}
