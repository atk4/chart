<?php

declare(strict_types=1);

namespace atk4\chart;

use atk4\core\Exception;
use atk4\data\Model;
use atk4\ui\jsExpression;

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
    public function setModel(Model $model, array $columns = []): Model
    {
        if (!$columns) {
            throw new Exception('Second argument must be specified to Chart::setModel()');
        }

        $this->dataSets = [];
        $colors = [];

        // Initialize data-sets
        foreach ($columns as $key => $column) {
            $colors[$column] = $this->nice_colors;

            if ($key == 0) {
                $title_column = $column;

                continue; // skipping labels
            }

            $this->dataSets[$column] = [
                //'label' => $model->getField($column)->getCaption(),
                'data' => [],
                'backgroundColor' => [], //$colors[0],
                //'borderColor' => [], //$colors[1],
                //'borderWidth' => 1,
            ];
        }

        // Prepopulate data-sets
        foreach ($model as $row) {
            $this->labels[] = $row->get($title_column);
            foreach ($this->dataSets as $key => &$dataset) {
                $dataset['data'][] = $row->get($key);
                $color = array_shift($colors[$key]);
                $dataset['backgroundColor'][] = $color[0];
                $dataset['borderColor'][] = $color[1];
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
    public function withCurrency(string $char = '€', string $axis = 'y')
    {
        $options['tooltips'] = [
            //'enabled' => true,
            //'mode'    => 'single',
            'callbacks' => [
                'label' => new jsExpression('{}', [
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
