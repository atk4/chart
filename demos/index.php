<?php

declare(strict_types=1);

namespace Atk4\Chart\Demos;

use Atk4\Chart\BarChart;
use Atk4\Chart\BubbleChart;
use Atk4\Chart\Chart;
use Atk4\Chart\ChartBox;
use Atk4\Chart\Color;
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
    1 => ['name' => 'January', 'sales_cash' => 6000, 'sales_bank' => 14000, 'purchases' => 10000],
    2 => ['name' => 'February', 'sales_cash' => 5000, 'sales_bank' => 18000, 'purchases' => 12000],
    3 => ['name' => 'March', 'sales_cash' => 4000, 'sales_bank' => 12000, 'purchases' => 22000],
    4 => ['name' => 'April', 'sales_cash' => 7500, 'sales_bank' => 6500, 'purchases' => 13000],
    5 => ['name' => 'May', 'sales_cash' => 3000, 'sales_bank' => 8500, 'purchases' => 9000],
];

$m = new Model(new Persistence\Array_($t));
$m->addFields(['name', 'sales_cash', 'sales_bank', 'sales', 'purchases', 'profit']);
$m->onHook($m::HOOK_AFTER_LOAD, function ($m) {
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
    2 => ['name' => 'London', 'trees' => 500, 'cars' => 3100, 'pollution' => 50],
    3 => ['name' => 'Riga', 'trees' => 300, 'cars' => 700, 'pollution' => 13],
    4 => ['name' => 'Paris', 'trees' => 450, 'cars' => 2800, 'pollution' => 35],
    5 => ['name' => 'Mars', 'trees' => 350, 'cars' => 2500, 'pollution' => 20],
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
$color = new Color();
$chart->setDatasets([
    [
        'label' => 'Population',
        'backgroundColor' => $color->getColorPairByIndex(0)[0],
        'borderColor' => $color->getColorPairByIndex(0)[1],
        'data' => [
            0 => ['x' => 30, 'y' => 50, 'r' => 10],
            1 => ['x' => 10, 'y' => 20, 'r' => 50],
            2 => ['x' => 20, 'y' => 30, 'r' => 30],
        ],
    ],
    [
        'label' => 'Pollution',
        'backgroundColor' => $color->getColorPairByIndex(1)[0],
        'borderColor' => $color->getColorPairByIndex(1)[1],
        'data' => [
            0 => ['x' => 15, 'y' => 30, 'r' => 5],
            1 => ['x' => 10, 'y' => 10, 'r' => 20],
            2 => ['x' => 25, 'y' => 40, 'r' => 10],
        ],
    ],
]);
$chart->setAxisTitles();
