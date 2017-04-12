<?php
require '../vendor/autoload.php';

$p = ['t'=>[
    [ 'name'=>'January', 'sales'=>20000, 'purchases'=>10000, ],
    [ 'name'=>'February', 'sales'=>23000, 'purchases'=>12000, ],
    [ 'name'=>'March', 'sales'=>16000, 'purchases'=>11000, ],
    [ 'name'=>'April', 'sales'=>14000, 'purchases'=>13000, ],
]];
$m = new \atk4\data\Model(new \atk4\data\Persistence_Array($p), 't');
$m->addFields(['name', 'sales', 'purchases', 'profit']);
$m->addHook('afterLoad', function($m) { $m['profit'] = $m['sales'] - $m['purchases']; });
$app = new \atk4\ui\App('Chart Demo');
$app->initLayout('Centered');

// Lets put your chart into a box:
$columns = $app->layout->add('Columns');
$cb = $columns->addColumn(10)->add(new \atk4\chart\ChartBox(['label'=>['Demo Chart', 'icon'=>'book']]));
$chart = $cb->add(new \atk4\chart\BarChart());
$chart->setModel($m, ['name', 'sales', 'purchases','profit']);
$chart->withCurrency('$');

// Tweak our chart to support currencies better

$cb = $columns->addColumn(6)->add(new \atk4\chart\ChartBox(['label'=>['Demo Chart', 'icon'=>'book']]));
$chart = $cb->add(new \atk4\chart\PieChart());
$chart->setModel($m, ['name', 'profit']);
$chart->withCurrency('$');
