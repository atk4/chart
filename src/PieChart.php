<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Ui\JsExpression;

class PieChart extends Chart
{
    public string $type = ChartType::TYPE_PIE;

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

    public function setCurrencyLabel(string $char = '€', string $axis = 'y')
    {
        $options = [
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'point',
                    'callbacks' => [
                        'label' => new JsExpression('{}', [
                            'function(context) {
                                let label = context.label || "";
                                let value = context.parsed;
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
}
