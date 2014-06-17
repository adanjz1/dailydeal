<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("../inc/auth_adm.inc.php");
	require_once("../inc/config.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];

		smart_mysql_query("DELETE FROM abbijan_categories WHERE category_id='$id'");
		smart_mysql_query("DELETE FROM abbijan_item_to_category WHERE category_id='$id'");

		$res = smart_mysql_query("SELECT category_id FROM abbijan_categories WHERE parent_id='$id'");

		if (mysql_num_rows($res) > 0)
		{
			while ($row = mysql_fetch_array($res))
			{
				smart_mysql_query("DELETE FROM abbijan_categories WHERE category_id='".$row['category_id']."'");
			}
		}

		header("Location: categories.php?msg=deleted");
		exit();
	}

?>