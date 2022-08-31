<?php

declare(strict_types=1);

namespace Atk4\Chart\Demos;

use Atk4\Chart\BarChart;
use Atk4\Chart\Chart;
use Atk4\Chart\ChartBox;
use Atk4\Chart\ChartType;
use Atk4\Chart\DoughnutChart;
use Atk4\Chart\LineChart;
use Atk4\Chart\PieChart;
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
$chart->withCurrency('$'); // tweak our chart to support currencies better

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar Chart Stacked', 'icon' => 'book']]);
$chart = BarChart::addTo($cb);
$chart->setModel($m, ['name', 'sales_cash', 'sales_bank', 'purchases', 'profit']);
$chart->setStacks([
    'Stack 1' => ['sales_cash', 'sales_bank'],
    'Stack 2' => ['purchases'],
]);
$chart->withCurrency('$');

// split in columns - Bar Chart horizontal
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar Chart Horizontal', 'icon' => 'book']]);
$chart = BarChart::addTo($cb);
$chart->setHorizontal();
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->withCurrency('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar Chart Horizontal', 'icon' => 'book']]);
$chart = BarChart::addTo($cb);
$chart->setHorizontal();
$chart->setModel($m, ['name', 'sales_cash', 'sales_bank', 'purchases', 'profit']);
$chart->setStacks([
    'Stack 1' => ['sales_cash', 'sales_bank'],
    'Stack 2' => ['purchases'],
]);
$chart->withCurrency('$');

// split in columns - Line Chart
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Line Chart', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->withCurrency('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Line Chart Stacked', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'sales_cash', 'sales_bank', 'purchases', 'profit']);
$chart->setStacks([
    'Stack 1' => ['sales_cash', 'sales_bank'],
    'Stack 2' => ['purchases'],
]);
$chart->withCurrency('$');

// split in columns - Line Chart Vertical and filled
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Line Chart Filled', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->setColumnOptions([
    'profit' => ['fill' => true],
]);
$chart->withCurrency('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Line Chart Vertical', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->setVertical();
$chart->withCurrency('$');

// split in columns - Line + Bar Chart
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bar + Line Chart', 'icon' => 'book']]);
$chart = LineChart::addTo($cb);
$chart->setModel($m, ['name', 'profit', 'sales', 'purchases']);
$chart->setColumnOptions([
    'profit' => ['type' => ChartType::TYPE_LINE],
    'sales' => ['type' => ChartType::TYPE_BAR],
    'purchases' => ['type' => ChartType::TYPE_BAR],
]);
$chart->withCurrency('$');

// split in columns - Pie Chart
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Pie Chart', 'icon' => 'book']]);
$chart = PieChart::addTo($cb);
$chart->setModel($m, ['name', 'purchases']);
$chart->withCurrency('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Doughnut Chart', 'icon' => 'book']]);
$chart = DoughnutChart::addTo($cb);
$chart->setModel($m, ['name', 'purchases']);
$chart->withCurrency('$');

// split in columns - More charts
$columns = Columns::addTo($app->layout);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Radar Chart', 'icon' => 'book']]);
$chart = Chart::addTo($cb, ['type' => ChartType::TYPE_RADAR]);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->withCurrency('$');

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Polar Area Chart', 'icon' => 'book']]);
$chart = Chart::addTo($cb, ['type' => ChartType::TYPE_POLAR_AREA]);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->withCurrency('$');

// setup example data model
$t = [
    1 => ['name' => 'January', 'x' => 30, 'y' => 50, 'r' => 5],
    2 => ['name' => 'February', 'x' => -10, 'y' => 20, 'r' => 15],
    3 => ['name' => 'March', 'x' => 20, 'y' => 30, 'r' => 10],
];

$m = new Model(new Persistence\Array_($t));
$m->addFields(['name', 'x', 'y', 'r']);

$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Bubble Chart', 'icon' => 'book']]);
$chart = Chart::addTo($cb, ['type' => ChartType::TYPE_BUBBLE]);
$chart->setModel($m, ['name', 'x', 'y', 'r']);
/*
$chart->datasets['data'] = [
    0 => ['x' => 30, 'y' => 50, 'r' => 1],
    1 => ['x' => -10, 'y' => 20, 'r' => 5],
    2 => ['x' => 20, 'y' => 30, 'r' => 3],
];
*/
$chart->setDatasets([
    [
        'label' => 'Population',
        'backgroundColor' => $chart->niceColors[0][0],
        'borderColor' => $chart->niceColors[0][1],
        'data' => [
            0 => ['x' => 30, 'y' => 50, 'r' => 10],
            1 => ['x' => -10, 'y' => 20, 'r' => 50],
            2 => ['x' => 20, 'y' => 30, 'r' => 30],
        ],
    ],
    [
        'label' => 'Pollution',
        'backgroundColor' => $chart->niceColors[1][0],
        'borderColor' => $chart->niceColors[1][1],
        'data' => [
            0 => ['x' => 15, 'y' => 30, 'r' => 5],
            1 => ['x' => 10, 'y' => 10, 'r' => 20],
            2 => ['x' => 25, 'y' => 40, 'r' => 10],
        ],
    ],
]);

//
// $cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Scatter Chart', 'icon' => 'book']]);
// $chart = Chart::addTo($cb, ['type' => ChartType::TYPE_SCATTER]);
// $chart->setModel($m, ['name', 'x', 'y', 'z']);
//
