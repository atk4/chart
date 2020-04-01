<?php
require '../vendor/autoload.php';

use atk4\chart\ChartBox;
use atk4\chart\BarChart;
use atk4\chart\PieChart;
use atk4\data\Model;
use atk4\data\Persistence\Array_;
use atk4\ui\App;

$p = ['t'=>[
    [ 'name'=>'January', 'sales'=>20000, 'purchases'=>10000, ],
    [ 'name'=>'February', 'sales'=>23000, 'purchases'=>12000, ],
    [ 'name'=>'March', 'sales'=>16000, 'purchases'=>11000, ],
    [ 'name'=>'April', 'sales'=>14000, 'purchases'=>13000, ],
]];
$m = new Model(new Array_($p), 't');
$m->addFields(['name', 'sales', 'purchases', 'profit']);
$m->addHook('afterLoad', function($m) { $m['profit'] = $m['sales'] - $m['purchases']; });
$app = new App('Chart Demo');
$app->initLayout('Centered');

// Lets put your chart into a box:
$columns = $app->layout->add('Columns');
$cb = $columns->addColumn(10)->add(new ChartBox(['label'=>['Demo Chart', 'icon'=>'book']]));
$chart = $cb->add(new BarChart());
$chart->setModel($m, ['name', 'sales', 'purchases','profit']);
$chart->withCurrency('$');

// Tweak our chart to support currencies better

$cb = $columns->addColumn(6)->add(new ChartBox(['label'=>['Demo Chart', 'icon'=>'book']]));
$chart = $cb->add(new PieChart());
$chart->setModel($m, ['name', 'profit']);
$chart->withCurrency('$');
