<?php

declare(strict_types=1);

namespace Atk4\Chart\Demos;

use Atk4\Chart\BarChart;
use Atk4\Chart\BubbleChart;
use Atk4\Chart\Chart;
use Atk4\Chart\ChartBox;
use Atk4\Chart\ColorGenerator;
use Atk4\Chart\DoughnutChart;
use Atk4\Chart\LineChart;
use Atk4\Chart\PieChart;
use Atk4\Chart\PolarAreaChart;
use Atk4\Chart\RadarChart;
use Atk4\Chart\ScatterChart;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\App;
use Atk4\Ui\Columns;
use Atk4\Ui\Layout;

require '../vendor/autoload.php';

// setup example data model
$t = [
    1 => ['name' => 'January', 'sales_cash' => 6_000, 'sales_bank' => 14_000, 'purchases' => 10_000],
    2 => ['name' => 'February', 'sales_cash' => 5_000, 'sales_bank' => 18_000, 'purchases' => 12_000],
    3 => ['name' => 'March', 'sales_cash' => 4_000, 'sales_bank' => 12_000, 'purchases' => 22_000],
    4 => ['name' => 'April', 'sales_cash' => 7_500, 'sales_bank' => 6_500, 'purchases' => 13_000],
    5 => ['name' => 'May', 'sales_cash' => 3_000, 'sales_bank' => 8_500, 'purchases' => 9_000],
];

$m = new Model(new Persistence\Array_($t));
$m->addFields(['name', 'sales_cash', 'sales_bank', 'sales', 'purchases', 'profit']);
$m->onHook(Model::HOOK_AFTER_LOAD, static function (Model $m) {
    $m->set('sales', $m->get('sales_cash') + $m->get('sales_bank'));
    $m->set('profit', $m->get('sales') - $m->get('purchases'));
});

// setup app
$app = new App(['title' => 'Chart Demo']);
$app->initLayout([Layout\Centered::class]);

// split in columns - Bar Chart
$columns = Columns::addTo($app->layout);

// lets put your chart into a box
$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar Chart', 'icon' => 'book']]);
$chart = BarChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->setCurrencyLabel('$'); // tweak our chart to support currencies better

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar Chart Stacked', 'icon' => 'book']]);
$chart = BarChart::addTo($cb);
$chart->setModel($m, ['name', 'sales_cash', 'sales_bank', 'purchases', 'profit']);
$chart->setStacks([
    'Stack 1' => ['sales_cash', 'sales_bank'],
    'Stack 2' => ['purchases'],
]);
$chart->setCurrencyLabel('$');

// split in columns - Bar Chart horizontal
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar Chart Horizontal', 'icon' => 'book']]);
$chart = BarChart::addTo($cb);
$chart->setHorizontal();
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->setCurrencyLabel('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar Chart Horizontal', 'icon' => 'book']]);
$chart = BarChart::addTo($cb);
$chart->setHorizontal();
$chart->setModel($m, ['name', 'sales_cash', 'sales_bank', 'purchases', 'profit']);
$chart->setStacks([
    'Stack 1' => ['sales_cash', 'sales_bank'],
    'Stack 2' => ['purchases'],
]);
$chart->setCurrencyLabel('$');

// split in columns - Line Chart
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Line Chart', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->setCurrencyLabel('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Line Chart Stacked', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'sales_cash', 'sales_bank', 'purchases', 'profit']);
$chart->setStacks([
    'Stack 1' => ['sales_cash', 'sales_bank'],
    'Stack 2' => ['purchases'],
]);
$chart->setCurrencyLabel('$');

// split in columns - Line Chart Vertical and filled
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Line Chart Filled', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->setColumnOptions([
    'profit' => ['fill' => true],
]);
$chart->setCurrencyLabel('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Line Chart Vertical', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->setVertical();
$chart->setCurrencyLabel('$');

// split in columns - Line + Bar Chart
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar + Line Chart', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'profit', 'sales', 'purchases']);
$chart->setColumnOptions([
    'profit' => ['type' => Chart::TYPE_LINE],
    'sales' => ['type' => Chart::TYPE_BAR],
    'purchases' => ['type' => Chart::TYPE_BAR],
]);
$chart->setCurrencyLabel('$');

// split in columns - Pie Chart
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Pie Chart', 'icon' => 'book']]);
$chart = PieChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases']);
$chart->setCurrencyLabel('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Doughnut Chart', 'icon' => 'book']]);
$chart = DoughnutChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases']);
$chart->setCurrencyLabel('$');

// split in columns - Radar and Polar Area charts
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Radar Chart', 'icon' => 'book']]);
$chart = RadarChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->setCurrencyLabel('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Polar Area Chart', 'icon' => 'book']]);
$chart = PolarAreaChart::addTo($cb);
$chart->setModel($m, ['name', 'sales_cash', 'sales']);
$chart->setCurrencyLabel('$');

// setup example data model
$t = [
    1 => ['name' => 'Sahara', 'trees' => 100, 'cars' => 200, 'pollution' => 4],
    2 => ['name' => 'London', 'trees' => 500, 'cars' => 3_100, 'pollution' => 50],
    3 => ['name' => 'Riga', 'trees' => 300, 'cars' => 700, 'pollution' => 13],
    4 => ['name' => 'Paris', 'trees' => 450, 'cars' => 2_800, 'pollution' => 35],
    5 => ['name' => 'Mars', 'trees' => 350, 'cars' => 2_500, 'pollution' => 20],
];

$m = new Model(new Persistence\Array_($t), ['caption' => 'Pollution']);
$m->addFields(['name', 'trees', 'cars', 'pollution']);

// Scatter and Bubble charts
$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Scatter Chart', 'icon' => 'book']]);
$chart = ScatterChart::addTo($cb);
$chart->setModel($m, ['name', 'trees', 'cars', 'pollution']);
$chart->setAxisTitles();

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bubble Chart - dataset from model', 'icon' => 'book']]);
$chart = BubbleChart::addTo($cb);
$chart->setModel($m, ['name', 'trees', 'cars', 'pollution']);
$chart->setAxisTitles();

// custom bubble chart without model but with multiple manually set datasets
$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bubble Chart - multiple datasets', 'icon' => 'book']]);
$chart = BubbleChart::addTo($cb);
$colorGenerator = new ColorGenerator();
$chart->setDatasets([
    [
        'label' => 'Population',
        'backgroundColor' => $colorGenerator->getColorPairByIndex(0)[0],
        'borderColor' => $colorGenerator->getColorPairByIndex(0)[1],
        'data' => [
            ['x' => 30, 'y' => 50, 'r' => 10],
            ['x' => 10, 'y' => 20, 'r' => 50],
            ['x' => 20, 'y' => 30, 'r' => 30],
        ],
    ],
    [
        'label' => 'Pollution',
        'backgroundColor' => $colorGenerator->getColorPairByIndex(1)[0],
        'borderColor' => $colorGenerator->getColorPairByIndex(1)[1],
        'data' => [
            ['x' => 15, 'y' => 30, 'r' => 5],
            ['x' => 10, 'y' => 10, 'r' => 20],
            ['x' => 25, 'y' => 40, 'r' => 10],
        ],
    ],
]);
$chart->setAxisTitles();
