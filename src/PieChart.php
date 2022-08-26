<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Core\Exception;
use Atk4\Data\Model;
use Atk4\Ui\JsExpression;

class PieChart extends Chart
{
    /** @var string Type of chart */
    public $type = 'pie';

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

        $this->datasets = [];
        $colors = [];

        // initialize data-sets
        foreach ($columns as $key => $column) {
            $colors[$column] = $this->niceColors;

            if ($key === 0) {
                $titleColumn = $column;

                continue; // skipping labels
            }

            $this->datasets[$column] = [
                // 'label' => $model->getField($column)->getCaption(),
                'data' => [],
                'backgroundColor' => [], // $colors[0],
                // 'borderColor' => [], // $colors[1],
                // 'borderWidth' => 1,
            ];
        }

        // prepopulate data-sets
        foreach ($model as $entity) {
            $this->labels[] = $entity->get($titleColumn); // @phpstan-ignore-line
            foreach ($this->datasets as $key => &$dataset) {
                $dataset['data'][] = $entity->get($key);
                $color = array_shift($colors[$key]);
                $dataset['backgroundColor'][] = $color[0];
                $dataset['borderColor'][] = $color[1];
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
        $options = [];
        $options['tooltips'] = [
            // 'enabled' => true,
            // 'mode'  => 'single',
            'callbacks' => [
                'label' => new JsExpression('{}', [
                    'function(item, data, bb) {
                        var val = data.datasets[item.datasetIndex].data[item.index];

                        return "' . $char . '" +  val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }',
                ]),
            ],
        ];

        $this->setOptions($options);

        return $this;
    }
}
