<?php

declare(strict_types=1);

require '../vendor/autoload.php';

use Atk4\Chart\BarChart;
use Atk4\Chart\ChartBox;
use Atk4\Chart\PieChart;
use Atk4\Data\Model;
use Atk4\Data\Persistence\Array_;
use Atk4\Ui\App;
use Atk4\Ui\Columns;
use Atk4\Ui\Layout;

$t = [ 
    1 => ['name' => 'January', 'sales' => 20000, 'purchases' => 10000],
    2 => ['id' => 2, 'name' => 'February', 'sales' => 23000, 'purchases' => 12000],
    3 => ['id' => 3,'name' => 'March', 'sales' => 16000, 'purchases' => 11000],
    4 => ['id' => 4,'name' => 'April', 'sales' => 14000, 'purchases' => 13000]];

$m = new \Atk4\Data\Model(new \Atk4\Data\Persistence\Array_($t));

$m->addFields(['name', 'sales', 'purchases', 'profit']);

$m->onHook($m::HOOK_AFTER_LOAD, function ($m) { $m->set('profit', $m->get('sales') - $m->get('purchases')); });
$app = new App('Chart Demo');
$app->initLayout([Layout\Centered::class]);

// split in columns
$columns = Columns::addTo($app->layout);

// Lets put your chart into a box:
$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Demo Bar Chart', 'icon' => 'book']]);
$chart = BarChart::addTo($cb);
$chart->setModel($m, ['name', 'sales', 'purchases', 'profit']);
$chart->withCurrency('$');

// Tweak our chart to support currencies better
$cb = ChartBox::addTo($columns->addColumn(8), ['label' => ['Demo Pie Chart', 'icon' => 'book']]);
$chart = PieChart::addTo($cb);
$chart->setModel($m, ['name', 'profit']);
$chart->withCurrency('$');
