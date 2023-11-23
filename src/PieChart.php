<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;

class PieChart extends Chart
{
    public string $type = self::TYPE_PIE;

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

            $colors[$column] = new ColorGenerator();

            $datasets[$column] = [
                'data' => [],
                'label' => $this->model->getField($column)->getCaption(),
                'backgroundColor' => [],
                'borderColor' => [],
                'borderWidth' => 1,
                'hoverOffset' => 8,
                'borderAlign' => 'inner',
            ];
        }

        // prepopulate data-sets
        foreach ($this->model as $entity) {
            $this->labels[] = $entity->get($titleColumn); // @phpstan-ignore-line
            foreach ($datasets as $column => $dataset) {
                $datasets[$column]['data'][] = $entity->get($column);
                $color = $colors[$column]->getNextColorPair();
                $datasets[$column]['backgroundColor'][] = $color[0];
                $datasets[$column]['borderColor'][] = $color[1];
            }
        }

        $this->setDatasets($datasets);
    }

    public function setCurrencyLabel(string $char = '€', string $axis = 'y', int $digits = 2)
    {
        $options = [
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'point',
                    'callbacks' => [
                        'label' => new JsFunction(['context'], [
                            new JsExpression(<<<'EOF'
                                let label = context.dataset.label || '';
                                // let value = context.parsed; // y or x (horizontal) or r (radar) etc
                                let value = context.formattedValue.replace(/,/, '');
                                if (label) {
                                    label += ': ';
                                }
                                return label + (value ? [char] + Number(value).toLocaleString(undefined, {minimumFractionDigits: [digits], maximumFractionDigits: [digits]}) : 'No Data');
                                EOF, ['char' => $char, 'digits' => $digits]),
                        ]),
                    ],
                ],
            ],
        ];

        $this->setOptions($options);

        return $this;
    }
}
