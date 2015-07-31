<?php
$route = '/news/jobs/pull-prweb-details/';
$app->get($route, function () use ($app){

	$ReturnObject = array();

	$Query = "SELECT * FROM news WHERE Author = 'PR Web' AND LENGTH(Body) < 250 LIMIT 5";
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($ThisNews = mysql_fetch_assoc($Database))
		{
		$news_id = $ThisNews['ID'];
		$post_date = $ThisNews['Post_Date'];
		$title = $ThisNews['Title'];
		$author = $ThisNews['Author'];
		$summary = $ThisNews['Summary'];
		$body = $ThisNews['Body'];
		$footer = $ThisNews['Footer'];
		$url = $ThisNews['URL'];
		$status = $ThisNews['Status'];
		$buildpage = $ThisNews['Build_Page'];
		$showonsite = $ThisNews['Show_On_Site'];
		$image = $ThisNews['Feature_Image'];

		//echo $title . "<br />";
		//echo $url . "<br />";

		$referer = "";

		$web_page = http_get($url, $referer);

		$Press = $web_page['FILE'];

		//echo $Press;

		$Begin_Tag = '<div class="middle-reset"></div>';
		$End_Tag = ' </div>';
		$ReturnBody = return_between($Press, $Begin_Tag, $End_Tag, EXCL);
		if($ReturnBody==''){ $ReturnBody = $Press; }
		//echo $ReturnBody;
		$body = '<p>' . $ReturnBody . '</p><p><em><strong>Source: </strong><a href="' . $url . '">PRWeb</a></em></p>';

		$UpdateQuery = "UPDATE news SET Body = '" . mysql_real_escape_string($body)  . "' WHERE ID = " . $news_id;
		echo $UpdateQuery . "<br />";
		$UpdateResult = mysql_query($UpdateQuery) or die('Query failed: ' . mysql_error());

		$F = array();
		$F['date'] = $post_date;
		$F['title'] = $title;
		$F['body'] = $body;
		$F['url'] = $url;
		array_push($ReturnObject, $F);

		}

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
