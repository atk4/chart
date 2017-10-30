<?php
namespace atk4\chart;

class PieChart extends Chart
{
    /** @var string Type of chart */
    public $type = 'pie';

    /**
     * Specify data source for this chart. The column must contain
     * the textual column first followed by sumber of data columns:
     * setModel($month_report, ['month', 'total_sales', 'total_purchases']);
     *
     * This component will automatically figure out name of the chart,
     * series titles based on column captions etc.
     *
     * @param \atk4\data\Model $model
     * @param array            $columns
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $model, $columns = [])
    {
        if (!$columns) {
            throw new \atk4\core\Exception('Second argument must be specified to Chart::setModel()');
        }

        $this->dataSets = [];
        $colors = [];

        // Initialize data-sets
        foreach ($columns as $key=>$column) {
            $colors[$column] = $this->nice_colors;

            if ($key == 0) {
                $title_column = $column;
                continue; // skipping labels
            }


            $this->dataSets[$column] = [
                //'label'=>$model->getElement($column)->getCaption(),
                'data'=>[],
                'backgroundColor'=>[], //$colors[0],
                //'borderColor'=>[], //$colors[1],
                //'borderWidth'=>1,
            ];
        }

        // Prepopulate data-sets
        foreach ($model as $row) {

            $this->labels[] = $row[$title_column];
            foreach ($this->dataSets as $key => &$dataset) {
                $dataset['data'][] = $row[$key];
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
     *
     * @return $this
     */
    public function withCurrency($char = '€')
    {
        $this->options['tooltips'] = [
            //'enabled' => true,
            //'mode' => 'single',
            'callbacks' => [
                'label' => new \atk4\ui\jsExpression('{}', [
                    'function(item, data, bb) {
                        var val = data.datasets[item.datasetIndex].data[item.index];
                        return "'.$char.'" +  val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }'
                ]),
            ],
        ];

        return $this;
    }
}
