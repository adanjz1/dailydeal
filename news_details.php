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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$news_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: news.php");
		exit();
	}

	$result = smart_mysql_query("SELECT *, DATE_FORMAT(added, '%M %e, %Y') AS news_date FROM abbijan_news WHERE news_id='$news_id' AND status='active' ORDER BY added DESC");
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "News";

	require_once ("inc/header.inc.php");

?>

	<h1>News</h1>


	<?php if ($total > 0) { $row = mysql_fetch_array($result); ?>

		<div class="news_date"><?php echo $row['news_date']; ?></div>
		<div class="news_title"><?php echo $row['news_title']; ?></div>
		<div class="news_description"><?php echo $row['news_description']; ?></div>

		<p align="right"><a class="seemore" href="/news.php">read other news</a></p>

	<?php }else{ ?>
			<p align="center">Sorry, news not found.</p>
			<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>