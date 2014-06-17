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
	require_once("./inc/adm_functions.inc.php");

	
	$where = "";

	if (isset($_GET['filter']) && $_GET['filter'] != "")
	{
		$filter	= mysql_real_escape_string(trim(getGetParameter('filter')));
		$where .= " AND (username LIKE '%".$filter."%' OR email LIKE '%".$filter."%' OR fname LIKE '%".$filter."%' OR lname LIKE '%".$filter."%')";
	}

	if (isset($_GET['country']) && $_GET['country'] != "")
	{
		$country = mysql_real_escape_string(trim(getGetParameter('country')));

		if ($country == "us")
			$where .= " AND country='United States'";
		else if ($country == "int")
			$where .= " AND country!='United States'";
	}

	if (isset($_GET['date']) && $_GET['date'] != "")
	{
		$date	= mysql_real_escape_string(getGetParameter('date'));
		$where .= " AND DATE(created)='$date'";
	}

	if (isset($_GET['start_date']) && $_GET['start_date'] != "")
	{
		$start_date	= mysql_real_escape_string(getGetParameter('start_date'));
		$where .= " AND created>='$start_date 00:00:00'";
	}

	if (isset($_GET['end_date']) && $_GET['end_date'] != "")
	{
		$end_date = mysql_real_escape_string(getGetParameter('end_date'));
		$where .= " AND created<='$end_date 23:59:59'";
	}

	if (isset($_GET['pmethod']) && $_GET['pmethod'] != "")
	{
		$pmethod = mysql_real_escape_string(trim(getGetParameter('pmethod')));
		$where .= " AND payment_method = '$pmethod'";
	}


	$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS created FROM abbijan_users WHERE 1=1 ".$where." ORDER BY user_id DESC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{		
		$filename_add = "";

		if ($date)		$filename_add .= "_".$date;
		if ($country)	$filename_add .= "_".$country;

		if ($filename_add == "")
			$filename = "orders_report_".time().".xls";
		else
			$filename = "orders_report".$filename_add.".xls";


        $contents = "First Name \t Last Name \t Country \t Address \t City \t State \t Zip \t Phone \t E-mail Address \t Payment Method \t \n";

		while ($row = mysql_fetch_array($result))
		{
			$contents .= html_entity_decode($row['fname'], ENT_NOQUOTES, 'UTF-8')."\t";
			$contents .= html_entity_decode($row['lname'], ENT_NOQUOTES, 'UTF-8')."\t";
			$contents .= $row['country']."\t";
			$address = str_replace(array("?","#","!",",",".",";"), " ", $row['address']);
			$address = str_replace("  ", " ", $address);
			$contents .= html_entity_decode($address, ENT_NOQUOTES, 'UTF-8')."\t";
			$contents .= html_entity_decode($row['city'], ENT_NOQUOTES, 'UTF-8')."\t";
			$contents .= html_entity_decode($row['state'], ENT_NOQUOTES, 'UTF-8')."\t";
			$contents .= "=\"".$row['zip']."\"\t";
			$contents .= "=\"".$row['phone']."\"\t";
			$contents .= $row['email']."\t";
			$contents .= GetPaymentMethodName($row['payment_method_id'])."\t";
			$contents .= " \n"; 
        }

		header('Content-type: application/ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);

		echo $contents;
		exit;
	}

?>