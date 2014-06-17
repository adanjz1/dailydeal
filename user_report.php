<?php
/*******************************************************************\
 * 
 * 
 *
 * 
  * 
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");


	if (isset($_POST['action']) && $_POST['action'] == "report")
	{
		unset($errs);
		$errs = array();

		$user_id	= (int)getPostParameter('user_id');
		$report		= mysql_real_escape_string(nl2br(getPostParameter('report')));

		if (!($report))
		{
			$errs[] = "Please enter your reason";
		}
		else
		{
			$check_query = smart_mysql_query("SELECT * FROM abbijan_reports WHERE reporter_id='$userid' AND user_id='$user_id'");
			if (mysql_num_rows($check_query) != 0)
			{
				$errs[] = "You have currently reported for this user.";
			}
		}

		if (count($errs) == 0)
		{
			$query = "INSERT INTO abbijan_reports SET reporter_id='$userid', user_id='$user_id', report='$report', viewed='0', status='active', added=NOW()";
			$result = smart_mysql_query($query);
		
			header("Location: /user_report.php?id=$user_id&msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}

	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$user_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}

	$query = "SELECT * FROM abbijan_users WHERE user_id='$user_id' AND user_id!='$userid' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Report User";

	require_once ("inc/header.inc.php");

?>

	<h1>Report User</h1>


	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div style="width: 92%;" class="success_msg">Thank you! Your report has been sent to us.</div>
	<?php }?>

	<?php if (isset($allerrors) && $allerrors != "") { ?>
		<div style="width: 92%;" class="error_msg"><?php echo $allerrors; ?></div>
	<?php } ?>


	<?php if ($total > 0) { $row = mysql_fetch_array($result); ?>

		<h3><?php echo GetUsername($row['user_id'], $row['show_as']); ?></h3>
		<img src="/images/icon_report2.png" align="right" />

		Please describe why you are reporting this user:<br/>
		<form action="" method="post">
		<textarea name="report" cols="55" rows="7" class="textbox2"><?php echo getPostParameter('report'); ?></textarea>
			<input type="hidden" name="user_id" value="<?php echo (int)$row['user_id']; ?>" />
			<input type="hidden" name="action" value="report" /><br/>
			<input type="submit" class="submit" value="Submit" />
		</form>

	<?php }else{ ?>
		<center><h2>User not found</h2></center>
		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>