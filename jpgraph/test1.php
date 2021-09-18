<?php // content="text/plain; charset=utf-8"
require_once('jpgraph/jpgraph.php');
require_once('jpgraph/jpgraph_scatter.php');

$graph = new Graph(500, 300);
$graph->clearTheme();
$graph->SetScale("linlin");

$graph->img->SetMargin(50, 50, 50, 50);
$graph->SetShadow();

$graph->title->Set("A simple scatter plot");
$graph->title->SetFont(FF_FONT1, FS_BOLD);

$data1 = $data2 = [];
for ($i = 0; $i < 100; $i++) {
    $data1[] = [
        'x' => mt_rand(0, 23),
        'y' => mt_rand(0, 59),
    ];
    if (mt_rand(0, 100) > 90) {
        $data2[] = [
            'x' => mt_rand(0, 23),
            'y' => mt_rand(0, 59),
        ];
    }
}

$datax = array_column($data1, 'x');
$datay = array_column($data1, 'y');
$sp1 = new ScatterPlot($datay, $datax);
$sp1->mark->SetFillColor("green");
$graph->Add($sp1);

$datax = array_column($data2, 'x');
$datay = array_column($data2, 'y');
$sp2 = new ScatterPlot($datay, $datax);
$sp2->mark->SetFillColor("red");
$graph->Add($sp2);
$graph->Stroke();

?>
