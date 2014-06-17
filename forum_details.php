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
	require_once("inc/pagination.inc.php");

	$cc = 0;

	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$forum_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}


	$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y') AS date_created FROM abbijan_forums WHERE forum_id='$forum_id' AND status='active' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		$row = mysql_fetch_array($result);
		$ptitle = $row['title'];

		//// ADD COMMENT //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if (isset($_POST['action']) && $_POST['action'] == "add_comment" && isset($_SESSION['userid']) && is_numeric($_SESSION['userid']))
		{
			$userid			= (int)$_SESSION['userid'];
			$forum_id		= (int)getPostParameter('forum_id');
			$comment		= mysql_real_escape_string(nl2br(trim(getPostParameter('comment'))));
			$comment		= ucfirst(strtolower($comment));

			unset($errs);
			$errs = array();

			if (!($userid && $forum_id && $comment))
			{
				$errs[] = "Please enter your comment";
			}
			else
			{
				$number_lines = count(explode("<br />", $comment));
				
				if (strlen($comment) > MAX_COMMENT_LENGTH)
					$errs[] = "The maximum comment length is ".MAX_COMMENT_LENGTH." characters";
				else if ($number_lines > 5)
					$errs[] = "Sorry, too many line breaks in the comment";
				else if (stristr($comment, 'http'))
					$errs[] = "You can not post links in the comment";
			}

			if (count($errs) == 0)
			{
				$comment = substr($comment, 0, MAX_COMMENT_LENGTH);
				$check_comment = mysql_num_rows(smart_mysql_query("SELECT * FROM abbijan_forum_comments WHERE forum_id='$forum_id' AND user_id='$userid' AND comment='$comment'"));

				if ($check_comment == 0)
				{
					(COMMENTS_APPROVE == 1) ? $status = "pending" : $status = "active";
					$comment_query = "INSERT INTO abbijan_forum_comments SET forum_id='$forum_id', user_id='$userid', item_id='".(int)$row['item_id']."', comment='$comment', status='$status', added=NOW()";
					$comment_result = smart_mysql_query($comment_query);
					$comment_added = 1;
				}
				else
				{
					$errormsg = "Your comment was added";
				}

				unset($_POST['comment']);
			}
			else
			{
				$errormsg = "";
				foreach ($errs as $errorname)
					$errormsg .= "&#155; ".$errorname."<br/>";
			}
		}
		else
		{
			// update forum views count
			smart_mysql_query("UPDATE abbijan_forums SET views=views+1 WHERE forum_id='$forum_id'");
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	}
	else
	{
		$ptitle = "Discussion not found";
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = $ptitle;

	require_once ("inc/header.inc.php");

?>

	<?php

		if ($total > 0) {

	?>
		<?php if ($row['item_id'] > 0) { ?>
		<div style="float: right; padding-top: 75px;">
			<a href="<?php echo SITE_URL."deal_details.php?id=".$row['item_id']; ?>"><?php echo GetDealThumb($row['item_id']); ?></a>		
		</div>
		<?php } ?>

		<h1 class="forum_title"><?php echo $row['title']; ?></h1>
		<div class="breadcrumbs"><a href="/" class="home_link">Home</a> &#155; <a href="/discussion.php">Discussion</a> &#155; <?php echo $row['title']; ?></div>
		<p><?php echo stripslashes($row['description']); ?></p>

		<?php
				// show comments //
				if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
				$from = ($page-1)*COMMENTS_PER_PAGE;

				$comments_query = "SELECT r.*, DATE_FORMAT(r.added, '%e/%m/%Y') AS comment_date, r.added AS comment_date2, u.user_id, u.username, u.fname, u.lname, u.avatar, u.show_as FROM abbijan_forum_comments r LEFT JOIN abbijan_users u ON r.user_id=u.user_id WHERE r.forum_id='$forum_id' AND r.status='active' ORDER BY r.added DESC LIMIT $from, ".COMMENTS_PER_PAGE;
				$comments_result = smart_mysql_query($comments_query);
				$comments_total = mysql_num_rows(smart_mysql_query("SELECT * FROM abbijan_forum_comments WHERE forum_id='$forum_id' AND status='active'"));
		?>

	<div style="width: 70%; margin: 0 auto;">

		<div id="add_comment_link"><a id="add-comment" href="javascript:void(0);">Add a Comment</a></div>
		<a name="comments"></a>
		<h3 class="comments">Comments <?php echo ($comments_total > 0) ? "(".$comments_total.")" : ""; ?></h3>

		<script>
		$("#add-comment").click(function () {
			$("#comment-form").toggle("slow");
		});
		</script>

		<div id="comment-form" class="comment-form" style="<?php if (!(isset($_POST['action']) && $_POST['action'] == "add_comment")) { ?>display: none;<?php } ?>">
			<?php if (isset($errormsg) && $errormsg != "") { ?>
				<div style="width: 94%;" class="error_msg"><?php echo $errormsg; ?></div>
			<?php } ?>
			<?php if (isset($_SESSION['userid']) && is_numeric($_SESSION['userid'])) { ?>
				<form method="post" action="#comments">
					<textarea id="comment" name="comment" cols="45" rows="5" class="textbox2"><?php echo getPostParameter('comment'); ?></textarea><br/>
					<input type="hidden" id="forum_id" name="forum_id" value="<?php echo $forum_id; ?>" />
					<input type="hidden" name="action" value="add_comment" />
					<input type="submit" class="submit" value="Add comment" />
				</form>
			<?php }else{ ?>
				You must be <a href="/login.php">logged in</a> to post a comment.
			<?php } ?>
		</div>

		<?php if (COMMENTS_APPROVE == 1 && $comment_added == 1) { ?>
			<div style="width: 90%;" class="success_msg">Your comment has been submitted and is awaiting approval</div>
		<?php } ?>


		<div style="clear: both"></div>
		<?php if ($comments_total > 0) { ?>

			<?php while ($comments_row = mysql_fetch_array($comments_result)) { $cc++ ; ?>
				<a name="<?php echo $comments_row['forum_comment_id']; ?>"></a>
				<div id="comment" style="background: <?php if (($cc%2) == 0) echo "#FFFFFF"; else echo "#F9F9F9"; ?>">
					<img src="<?php echo AVATARS_URL.$comments_row['avatar']; ?>" height="<?php echo AVATAR_HEIGHT; ?>" width="<?php echo AVATAR_WIDTH; ?>" alt="" class="thumb" align="left" />
					<span class="comment-author"><a href="/user_profile.php?id=<?php echo $comments_row['user_id']; ?>"><?php echo GetUsername($comments_row['user_id'], $comments_row['show_as']); ?></a></span>
					<span class="comment-date"><?php echo relative_date(strtotime($comments_row['comment_date2'])); ?></span><br/>
					<div class="comment-text"><?php echo $comments_row['comment']; ?></div>
					<?php if ($comments_row['reply'] != "") { ?>
					<div class="reply-text"><b>Admin</b><br/><?php echo stripslashes($comments_row['reply']); ?></div>
					<?php } ?>
					<div style="clear: both"></div>
				</div>
			<?php } ?>
		
			<?php echo ShowPagination("forum_comments",COMMENTS_PER_PAGE,"?id=$forum_id&","WHERE forum_id='$forum_id' AND status='active'"); ?>
		
		<?php }else{ ?>
				<p align="center">No comments yet. Be the first!</p>
		<?php } ?>

	</div>

	<?php }else{ ?>
		<h1>Discussion not found</h1>
		<p align="center">Sorry, no discussion found.</p>
		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
	<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>