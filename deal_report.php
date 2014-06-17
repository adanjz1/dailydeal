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

		$item_id		= (int)getPostParameter('item_id');
		$member_id		= (int)getPostParameter('member_id');
		$report			= mysql_real_escape_string(nl2br(getPostParameter('report')));

		if (!($report))
		{
			$errs[] = "Please enter your reason";
		}
		else
		{
			$check_query = smart_mysql_query("SELECT * FROM abbijan_reports WHERE reporter_id='$userid' AND item_id='$item_id'");
			if (mysql_num_rows($check_query) != 0)
			{
				$errs[] = "You have currently reported for this deal.";
			}
		}

		if (count($errs) == 0)
		{
			$query = "INSERT INTO abbijan_reports SET reporter_id='$userid', item_id='$item_id', report='$report', viewed='0', status='active', added=NOW()";
			$result = smart_mysql_query($query);
	
			header("Location: /deal_report.php?id=$item_id&msg=1");
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
		$item_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}

	$query = "SELECT * FROM abbijan_items WHERE item_id='$item_id' LIMIT 1"; 
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Report Deal";

	require_once ("inc/header.inc.php");

?>

	<h1>Report Deal</h1>

	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div style="width: 54%;" class="success_msg">Thank you! Your report has been sent to us.</div>
	<?php } ?>

	<?php if ($total > 0) { $row = mysql_fetch_array($result); ?>

		<?php if (!(isset($_GET['msg']) && $_GET['msg'] == 1)) { ?>
		<img src="/images/icon_report2.png" align="right" />
		<div style="width: 400px; margin: 0 auto; padding: 5px;">
			<h3><?php echo $row['title']; ?></h3>
			What is wrong with this deal?<br/>

			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div style="width: 340px;" class="error_msg"><?php echo $allerrors; ?></div>
			<?php } ?>

			<form action="" method="post">
			<textarea name="report" cols="61" rows="7" class="textbox2"><?php echo getPostParameter('report'); ?></textarea>
			<input type="hidden" name="item_id" value="<?php echo (int)$row['item_id']; ?>" />
			<input type="hidden" name="action" value="report" /><br/>
			<input type="submit" class="submit" value="Submit" />&nbsp;&nbsp;
			<input type="button" class="cancel" name="cancel" value="Cancel" onclick="history.go(-1);return false;" />
			</form>
		</div>
		<?php } ?>

	<?php }else{ ?>
		<center><h2>Sorry, no deal found.</h2></center>
		<p align="center"><a class="goback" href="/">Go Back</a></p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>