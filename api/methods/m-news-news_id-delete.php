<?php
$route = '/news/:news_id/';
$app->delete($route, function ($news_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$news_id = prepareIdIn($news_id,$host);

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$_POST = $request->params();

	$query = "DELETE FROM news WHERE ID = " . $news_id;
	//echo $query . "<br />";
	mysql_query($query) or die('Query failed: ' . mysql_error());

	});
?>
