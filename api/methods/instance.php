<?php$route = '/news/jobs/pull-nouns/';$app->get($route, function () use ($app){	$ReturnObject = array();	$Query = "SELECT * FROM news WHERE Author = 'PR Web' LIMIT 3,1";	//echo $Query . "<br />";	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());	if($Database && mysql_num_rows($Database))		{		$ThisBlog = mysql_fetch_assoc($Database);		$nouns_title = array();		$nouns_body = array();		$blog_id = $ThisBlog['ID'];		$post_date = $ThisBlog['Post_Date'];		$title = $ThisBlog['Title'];		$author = $ThisBlog['Author'];		$summary = $ThisBlog['Summary'];		$body = $ThisBlog['Body'];		$footer = $ThisBlog['Footer'];		$url = $ThisBlog['URL'];		$status = $ThisBlog['Status'];		$buildpage = $ThisBlog['Build_Page'];		$showonsite = $ThisBlog['Show_On_Site'];		$image = $ThisBlog['Feature_Image'];		$clean_title = $title;		$clean_title = str_replace(",","",$clean_title);		$clean_title = str_replace(".","",$clean_title);		// Capital Words		preg_match_all("([A-Z][a-z]{1,2}\.\s+(?:[A-Z][a-z]+\s*)*|(?<!\. )(?<!;)(?:[A-Z][a-z]+\s*)+)", $body, $matches);		//var_dump($matches);		foreach($matches[0] as $item)			{			if (strpos($clean_title,$item) !== FALSE)				{				echo $item . "<br />";				$I = array();				$I = $item;				array_push($nouns_body, $I);				}			}		$F = array();		$F['date'] = $post_date;		$F['title'] = $title;		$F['url'] = $url;		$F['nouns_title'] = $nouns_title;		$F['nouns_body'] = $nouns_body;		array_push($ReturnObject, $F);		}	$app->response()->header("Content-Type", "application/json");	echo stripslashes(format_json(json_encode($ReturnObject)));	});?>