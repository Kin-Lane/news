<?php
$route = '/news/jobs/pull-prweb/';
$app->get($route, function () use ($app){

	$ReturnObject = array();

	for ($x = 0; $x <= 5; $x++)
		{

		if($x==0)
			{
			$URL = "http://www.prweb.com/search.aspx?search-releases=API&hitsPerPage=25&x=0&y=0";
			}
		else
			{
			$URL = "http://www.prweb.com/Search.aspx?Search-releases=API&start=" . $x;
			}

		$Press = file_get_contents($URL);
		$Begin_Tag = '<div class="release-content">';
		$End_Tag = '<div id="pagination">';
		$Items = return_between($Press, $Begin_Tag, $End_Tag, INCL);

		$beg_tag = '<div class="release-content">';
		$close_tag = "</div>";

		$ResultsArray = parse_array($Items, $beg_tag, $close_tag);

		foreach ($ResultsArray as $PressRelease)
			{

			$Begin_Tag = '<h3>';
			$End_Tag = '</h3>';
			$Press_Title = return_between($PressRelease, $Begin_Tag, $End_Tag, INCL);

			$Begin_Tag = '=';
			$End_Tag = '>';
			$Press_URL = return_between($Press_Title, $Begin_Tag, $End_Tag, INCL);
			$Press_URL = str_replace("=","",$Press_URL);
			$Press_URL = str_replace(">","",$Press_URL);
			$Press_URL = str_replace(chr(34),"",$Press_URL);

			$Begin_Tag = '<p class="extra-details">';
			$End_Tag = '</p>';
			$Press_Details = return_between($PressRelease, $Begin_Tag, $End_Tag, INCL);

			$Press_Detail_Array = explode("</span>",$Press_Details);
			$Press_Date = trim(strip_tags($Press_Detail_Array[0]));
			//echo $Press_Date . "<br />";
			$Press_Date = date('Y-m-d H:i:s',strtotime($Press_Date));

			$Press_Detail = trim($Press_Detail_Array[1]);
			$Press_Detail = str_replace("-- ","",$Press_Detail);

			$Press_Title = mysql_real_escape_string(trim(strip_tags($Press_Title)));
			$Press_Date = mysql_real_escape_string(trim(strip_tags($Press_Date)));
			$Press_URL = mysql_real_escape_string(trim(strip_tags($Press_URL)));
			$Press_Detail = mysql_real_escape_string(trim(strip_tags($Press_Detail)));

			//echo $Press_Title . "<br />";
			//echo $Press_Date . "<br />";
			//echo $Press_URL . "<br />";
			//echo $Press_Detail. "<br /><hr />";

			$author = "PR Web";
			$summary = "";
			$body = "";
			$body .= '<p><em><strong>Source: </strong><a href="' . $Press_URL . '">PRWeb</a></em></p>';
			$footer = "";

		  	$Query = "SELECT * FROM news WHERE Title = '" . mysql_real_escape_string($Press_Title) . "' AND Post_Date = '" . mysql_real_escape_string($Press_Date) . "'";
			//echo $Query . "<br />";
			$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

			if($Database && mysql_num_rows($Database))
				{
				$Thisews = mysql_fetch_assoc($Database);
				$news_id = $Thisews['ID'];
				}
			else
				{
				$Query = "INSERT INTO news(Post_Date,Title,Author,Summary,Body,Footer,URL)";
				$Query .= " VALUES(";
				$Query .= "'" . mysql_real_escape_string($Press_Date) . "',";
				$Query .= "'" . mysql_real_escape_string($Press_Title) . "',";
				$Query .= "'" . mysql_real_escape_string($author) . "',";
				$Query .= "'" . mysql_real_escape_string($summary) . "',";
				$Query .= "'" . mysql_real_escape_string($body) . "',";
				$Query .= "'" . mysql_real_escape_string($footer) . "',";
				$Query .= "'" . mysql_real_escape_string($Press_URL) . "'";
				$Query .= ")";
				//echo $Query . "<br />";
				mysql_query($Query) or die('Query failed: ' . mysql_error());
				$news_id = mysql_insert_id();
				}

			$F = array();
			$F['date'] = $Press_Date;
			$F['title'] = $Press_Title;
			$F['url'] = $Press_URL;
			array_push($ReturnObject, $F);

			}
		}

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
