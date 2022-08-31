<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Ui\JsExpression;

class PieChart extends Chart
{
    /** @var string Type of chart */
    public $type = ChartType::TYPE_PIE;

    public function prepareDatasets(): void
    {
        $datasets = [];
        $colors = [];

        // initialize data-sets
        foreach ($this->columns as $key => $column) {
            if ($key === 0) {
                $titleColumn = $column;

                continue; // skipping label column
            }

            $colors[$column] = $this->niceColors;

            $datasets[$column] = [
                'data' => [],
                'backgroundColor' => [],
            ];
        }

        // prepopulate data-sets
        foreach ($this->model as $entity) {
            $this->labels[] = $entity->get($titleColumn); // @phpstan-ignore-line
            foreach ($datasets as $key => &$dataset) {
                $dataset['data'][] = $entity->get($key);
                $color = array_shift($colors[$key]);
                $dataset['backgroundColor'][] = $color[0];
                $dataset['borderColor'][] = $color[1];
            }
        }

        $this->setDatasets($datasets);
    }

    public function withCurrency(string $char = '€', string $axis = 'y')
    {
        $options = [
            'tooltips' => [
                'callbacks' => [
                    'label' => new JsExpression('{}', [
                        'function(item, data, bb) {
                            var val = data.datasets[item.datasetIndex].data[item.index];
                            return "' . $char . '" +  val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }',
                    ]),
                ],
            ],
        ];

        $this->setOptions($options);

        return $this;
    }
}
