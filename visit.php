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
		$item_id = (int)$_GET['id'];

		$query = "SELECT * FROM abbijan_items WHERE item_id='$item_id' LIMIT 1";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			smart_mysql_query("UPDATE abbijan_items SET visits=visits+1 WHERE item_id='$item_id' LIMIT 1");

			$row = mysql_fetch_array($result);

			if ($row['url'] != "")
			{
				$deal_website = $row['url'];
				header("Location: ".$deal_website);
				exit();
			}
		}
		else
		{
			///////////////  Page config  ///////////////
        	$PAGE_TITLE = "No deal found";

			require_once ("inc/header.inc.php");

				echo "<p align='center'>Sorry, no deal found.</p>";
				echo "<p align='center'><a class='goback' href='#' onclick='history.go(-1);return false;'>Go Back</a></p>";

			require_once ("inc/footer.inc.php");
		}
	}
	else
	{	
		header("Location: index.php");
		exit();
	}

?>