<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");


	function well_formed($str)
	{
		$str = strip_tags($str, "<br><br/>");
		//$str = preg_replace("/[^a-zA-Z0-9_\? (\n|\r\n)]+/", "", $str);
		$str = str_replace("&nbsp;", "", $str);
		$str = str_replace("&", "&amp;", $str);
		return $str;
	}


	$rss_query = "SELECT *, DATE_FORMAT(added, '%a, %d %b %Y %T') as pub_date FROM abbijan_items WHERE start_date<=NOW() AND end_date>NOW() AND status!='inactive' ORDER BY added DESC LIMIT 50";
	$rss_result = smart_mysql_query($rss_query);
	$rss_total = mysql_num_rows($rss_result);

	if ($rss_total > 0)
	{
		header("Content-Type: application/xml; charset=UTF-8");

		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<rss version="2.0">';
		echo '<channel>';
		echo '<title>'.SITE_TITLE.'</title>';
		echo '<link>'.SITE_URL.'</link>';
		echo '<description>'.SITE_HOME_TITLE.'</description>';
		echo '<image>';
			echo '<url>'.SITE_URL.'images/logo.png</url>';
			echo '<title>'.SITE_TITLE.'</title>';
			echo '<link>'.SITE_URL.'</link>';
		echo '</image>';

		while($rss_row = mysql_fetch_array($rss_result)) 
		{
			$deal_title		= well_formed($rss_row['title'])." - ".DisplayPrice($rss_row['price']);
			$deal_image		= "<img src='".substr(SITE_URL, 0, -1).IMAGES_URL.$rss_row['thumb']."' /><br/>";
			$deal_link		= SITE_URL."deal_details.php?id=".$rss_row['item_id'];
			$deal_pubdate	= $rss_row['pub_date']." PDT";

			if (strlen($rss_row['description']) > 800)
				$deal_description = substr(well_formed(stripslashes($rss_row['description'])),0,800).'...';
			else
				$deal_description = well_formed(stripslashes($rss_row['description']));

			echo '
				<item>
					<title><![CDATA['.$deal_title.']]></title>
					<link>'.$deal_link.'</link>
					<guid isPermaLink="true">'.$deal_link.'</guid>
					<pubDate>'.$deal_pubdate.'</pubDate>
					<description><![CDATA[ <p>'.$deal_image.$deal_description.'</p> ]]></description>
				</item>
				';
		} 
		
		echo '</channel>';
		echo '</rss>';
	}

?>