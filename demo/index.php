<?php

declare(strict_types=1);

require '../vendor/autoload.php';

use atk4\chart\BarChart;
use atk4\chart\ChartBox;
use atk4\chart\PieChart;
use atk4\data\Model;
use atk4\data\Persistence\Array_;
use atk4\ui\App;
use atk4\ui\Columns;

$p = ['t' => [
    ['name' => 'January', 'sales' => 20000, 'purchases' => 10000],
    ['name' => 'February', 'sales' => 23000, 'purchases' => 12000],
    ['name' => 'March', 'sales' => 16000, 'purchases' => 11000],
    ['name' => 'April', 'sales' => 14000, 'purchases' => 13000],
]];
$m = new Model(new Array_($p), 't');
$m->addFields(['name', 'sales', 'purchases', 'profit']);
$m->onHook($m::HOOK_AFTER_LOAD, function ($m) { $m->set('profit', $m->get('sales') - $m->get('purchases')); });
$app = new App('Chart Demo');
$app->initLayout(\atk4\ui\Layout\Centered::class);

// Lets put your chart into a box:
$columns = $app->layout->add(Columns::class);
$cb = $columns->addColumn(8)->add(new ChartBox(['label' => ['Demo Bar Chart', 'icon' => 'book']]));
$chart = $cb->add(new BarChart());
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->withCurrency('$');

// Tweak our chart to support currencies better

$cb = $columns->addColumn(8)->add(new ChartBox(['label' => ['Demo Pie Chart', 'icon' => 'book']]));
$chart = $cb->add(new PieChart());
$chart->setModel($m, ['name', 'profit']);
$chart->withCurrency('$');
