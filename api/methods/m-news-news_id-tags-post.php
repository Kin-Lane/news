<?php
$route = '/news/:news_id/tags/';
$app->post($route, function ($News_ID)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['tag']))
		{
		$tag = trim(mysql_real_escape_string($param['tag']));

		$CheckTagQuery = "SELECT Tag_ID FROM tags where Tag = '" . $tag . "'";
		$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());
		if($CheckTagResults && mysql_num_rows($CheckTagResults))
			{
			$Tag = mysql_fetch_assoc($CheckTagResults);
			$Tag_ID = $Tag['Tag_ID'];
			}
		else
			{

			$query = "INSERT INTO tags(Tag) VALUES('" . trim($_POST['Tag']) . "'); ";
			mysql_query($query) or die('Query failed: ' . mysql_error());
			$Tag_ID = mysql_insert_id();
			}

		$CheckTagPivotQuery = "SELECT * FROM news_tag_pivot where Tag_ID = " . trim($Tag_ID) . " AND News_ID = " . trim($News_ID);
		$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());

		if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
			{
			$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
			}
		else
			{
			$query = "INSERT INTO news_tag_pivot(Tag_ID,News_ID) VALUES(" . $Tag_ID . "," . $News_ID . "); ";
			mysql_query($query) or die('Query failed: ' . mysql_error());
			}

		$F = array();
		$F['tag_id'] = $Tag_ID;
		$F['tag'] = $tag;
		$F['news_count'] = 0;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
