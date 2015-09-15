<?php
$route = '/news/:news_id/';
$app->get($route, function ($news_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$news_id = prepareIdIn($news_id,$host);

	$ReturnObject = array();

	$Query = "SELECT * FROM news WHERE ID = " . $news_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$news_id = $Database['ID'];
		$post_date = $Database['Post_Date'];
		$title = $Database['Title'];
		$author = $Database['Author'];
		$summary = $Database['Summary'];
		$body = $Database['Body'];
		$url = $Database['URL'];
		$image = $Database['Feature_Image'];
		$footer = $Database['Footer'];
		$status = $Database['Status'];
		$buildpage = $Database['Build_Page'];
		$githubbuild = $Database['Github_Build'];
		$twitterbuild = $Database['Twitter_Build'];
		$linkedinbuild = $Database['LinkedIn_Build'];
		$showonsite = $Database['Show_On_Site'];
		$archive = $Database['Archive'];

		$F['tags'] = array();

		$TagQuery = "SELECT t.tag_id, t.tag from tags t";
		$TagQuery .= " INNER JOIN news_tag_pivot ntp ON t.tag_id = ntp.tag_id";
		$TagQuery .= " WHERE ntp.News_ID = " . $news_id;
		$TagQuery .= " ORDER BY t.tag DESC";
		$TagResult = mysql_query($TagQuery) or die('Query failed: ' . mysql_error());

		while ($Tag = mysql_fetch_assoc($TagResult))
			{
			$thistag = $Tag['tag'];

			$T = array();
			$T = $thistag;
			array_push($F['tags'], $T);
			//echo $thistag . "<br />";
			if($thistag=='Archive')
				{
				$archive = 1;
				}
			}

		// manipulation zone

		$news_id = prepareIdOut($news_id,$host);

		$F = array();
		$F['news_id'] = $news_id;
		$F['post_date'] = $post_date;
		$F['title'] = $title;
		$F['author'] = $author;
		$F['summary'] = $summary;
		$F['body'] = $body;
		$F['url'] = $url;
		$F['footer'] = $footer;
		$F['status'] = $status;
		$F['image'] = $image;
		$F['build_page'] = $buildpage;
		$F['github_build'] = $githubbuild;
		$F['twitter_build'] = $twitterbuild;
		$F['linkedin_build'] = $linkedinbuild;
		$F['show_on_site'] = $showonsite;
		$F['archive'] = $archive;

		$ReturnObject = $F;
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
