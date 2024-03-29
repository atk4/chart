<?php

declare(strict_types=1);

namespace Atk4\Chart;

use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;

class ScatterChart extends Chart
{
    public string $type = self::TYPE_SCATTER;

    /** @var string X field name */
    protected $xField;

    /** @var string Y field name */
    protected $yField;

    /** @var string R (radius) field name */
    protected $rField;

    public function prepareDatasets(): void
    {
        $columns = $this->columns;

        $titleColumn = array_shift($columns) ?? null;
        $this->xField = array_shift($columns) ?? 'x';
        $this->yField = array_shift($columns) ?? 'y';
        $this->rField = array_shift($columns) ?? 'r';

        // initialize data-set
        $colors = $this->colorGenerator->getNextColorPair();
        $dataset = [
            'label' => $this->model->getModelCaption(),
            'backgroundColor' => $colors[0],
            'borderColor' => $colors[1],
            'data' => [],
        ];

        // prepopulate data-sets
        foreach ($this->model as $entity) {
            $dataset['data'][] = [
                // 'label' => $entity->get($titleColumn), // maybe some day this will be implemented in Chart.js to add label to bubble
                'x' => $entity->get($this->xField),
                'y' => $entity->get($this->yField),
                'r' => $entity->get($this->rField),
            ];
        }

        $this->setDatasets([$dataset]);
    }

    /**
     * Add titles on axis.
     *
     * @param string|null $xTitle X axis title
     * @param string|null $yTitle Y axis title
     */
    public function setAxisTitles(string $xTitle = null, string $yTitle = null): void
    {
        $options = [
            'scales' => [
                'x' => [
                    'title' => [
                        'text' => $xTitle ?? ($this->model ? $this->model->getField($this->xField)->getCaption() : ''),
                        'display' => true,
                    ],
                ],
                'y' => [
                    'title' => [
                        'text' => $yTitle ?? ($this->model ? $this->model->getField($this->yField)->getCaption() : ''),
                        'display' => true,
                    ],
                ],
            ],
            /* @todo maybe this can be used to tweak label to include city names? See example chart in demos.
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'point',
                    'callbacks' => [
                        'label' => new JsFunction(['context'], [
                            new JsExpression(<<<'EOF'
                                let label = context.dataset.label || '';
                                let value = context.parsed.y;
                                if (label) {
                                    label += ': ';
                                }
                                return label + (value ? Number(value).toLocaleString(undefined, {minimumFractionDigits: [digits], maximumFractionDigits: [digits]}) : 'No Data');
                                EOF, ['digits' => $digits]),
                        ]),
                    ],
                ],
            ],
            */
        ];

        $this->setOptions($options);
    }
}
