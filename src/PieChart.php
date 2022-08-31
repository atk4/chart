<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Chart\ChartType;
use Atk4\Core\Exception;
use Atk4\Data\Model;
use Atk4\Ui\JsExpression;

class PieChart extends Chart
{
    /** @var string Type of chart */
    public $type = ChartType::TYPE_PIE;

    public function setModel(Model $model, array $columns = []): void
    {
        if ($columns === []) {
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
                'data' => [],
                'backgroundColor' => [],
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

    public function withCurrency(string $char = '€', string $axis = 'y')
    {
        $options = [];
        $options['tooltips'] = [
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
