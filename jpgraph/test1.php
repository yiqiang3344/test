<?php
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_line.php';

//创建画布,大小600*400
$graph = new Graph(600, 400);

//设置横纵坐标刻度样式
/*
 * line直线
 * text文本
 * int整数
 * log对数
 * */
//横坐标text 纵坐标int
$aAxisType = 'textint';
$graph->SetScale($aAxisType);

//设置统计图的标题,英文正常使用，中文会出现乱码
//$graph->title->Set('this is a test');
$graph->title->SetFont(FF_CHINESE);
$graph->title->Set("慕课网");

//根据数据画图
$data = array(0 => 10, 1 => 20, 2 => 30, 3 => 40, 4 => 50, 5 => 12, 6 => 38, 7 => 55, 8 => 100, 9 => 120, 10 => 30, 11 => 54);

//实例化画X-Y的类
$linePlot = new LinePlot($data);

//设置图例
$linePlot->SetLegend('中文测试');

//将统计图添加到画布上
$graph->Add($linePlot);

//设置统计图的颜色，一定要在添加到画布之后再设置
$linePlot->SetColor('red');

//画出整福统计图，输出画布
$graph->Stroke();