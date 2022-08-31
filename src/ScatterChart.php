<?php

declare(strict_types=1);

namespace Atk4\Chart;

// use Atk4\Ui\JsExpression;

class ScatterChart extends Chart
{
    public $type = ChartType::TYPE_SCATTER;

    /** @var string X field name */
    protected $xField;

    /** @var string Y field name */
    protected $yField;

    /** @var string R (radius) field name */
    protected $rField;

    public function prepareDatasets(): void
    {
        $columns = $this->columns;

        $label = array_shift($columns) ?? null;
        $this->xField = array_shift($columns) ?? 'x';
        $this->yField = array_shift($columns) ?? 'y';
        $this->rField = array_shift($columns) ?? 'r';

        // initialize data-set
        $dataset = [
            'label' => $this->model->getModelCaption(),
            'backgroundColor' => $this->niceColors[0][0],
            'borderColor' => $this->niceColors[0][1],
            'data' => [],
        ];

        // prepopulate data-sets
        foreach ($this->model as $entity) {
            $dataset['data'][] = [
                // 'label' => $label, // maybe some day this will be implemented in chartjs to add label to bubble
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
     * @param ?string $xTitle X axis title
     * @param ?string $yTitle Y axis title
     */
    public function withAxisTitles(string $xTitle = null, string $yTitle = null): void
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
            /* @todo maybe this can be used to tweak label to include city names ?
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
                                return label + (value ? value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "No Data");
                            }',
                        ]),
                    ],
                ],
            ],
            */
        ];

        $this->setOptions($options);
    }
}
