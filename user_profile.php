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
		$user_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}

	$query = "SELECT *, DATE_FORMAT(last_login, '%M %e, %Y') AS last_login_date, DATE_FORMAT(created, '%M %e, %Y') AS signup_date FROM abbijan_users WHERE user_id='$user_id' AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		$row = mysql_fetch_array($result);
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "User Profile ".GetUsername($row['user_id'], $row['show_as']);

	require_once ("inc/header.inc.php");

?>

	<h1>User Profile</h1>

	<?php if ($total > 0) { ?>
		
		<?php if ($row['user_id'] != $userid) { ?>
		<div style="width:20%; float:right; text-align:right;">
			<p><a class="report" href="/user_report.php?id=<?php echo $row['user_id']; ?>">Report this user</a></p>
		</div>
		<?php } ?>

		<h2><?php echo GetUsername($row['user_id'], $row['show_as']); ?></h2>
		<img src="<?php echo AVATARS_URL.$row['avatar']; ?>" width="<?php echo AVATAR_WIDTH; ?>" height="<?php echo AVATAR_HEIGHT; ?>" align="left" class="imgs" border="0" />

		<div style="float:left; dispay:block; width:60%; line-height:17px;">
			<?php if ($row['company'] != "") { ?>
				Company: <?php echo $row['company']; ?><br/>
			<?php } ?>
			Country: <?php echo GetCountry($row['country_id']); ?><br/>
			Member Since: <?php echo $row['signup_date']; ?>
		</div>

		<div style="clear: both"></div>

		<?php
			// show user's recent comments //
			$comments_query = "SELECT *, DATE_FORMAT(added, '%M %e, %Y') AS date_added FROM abbijan_forum_comments WHERE user_id='".(int)$row['user_id']."' AND status='active' GROUP BY forum_id ORDER BY added DESC LIMIT 10";
			$comments_result = smart_mysql_query($comments_query);
			$comments_total = mysql_num_rows($comments_result);

			if ($comments_total > 0) { 
		?>
			<h3>Recent comments</h3>
			<ul id="my_comments">
				<?php while ($comments_row = mysql_fetch_array($comments_result)) { ?>
				<li>
					<a href="/forum_details.php?id=<?php echo $comments_row['forum_id']; ?>#<?php echo $comments_row['forum_comment_id']; ?>"><b><?php echo GetForumTitle($comments_row['forum_id']); ?></b></a><br/>
					<?php echo (strlen($comments_row['comment']) > 350) ? substr($comments_row["comment"], 0, 350)."..." : $comments_row["comment"]; ?>
				</li>
				<?php } ?>
			</ul>
		<?php } ?>

	<?php }else{ ?>
		<center><h2>User not found</h2></center>
		<p align="center">Sorry, no user found!</p>
		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>