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

	$userid = (int)$_SESSION['userid'];
	$cc = 0;

	function getRepliesNum($message_id)
	{
		global $userid;
		$message_id = (int)$message_id;
		$query = "SELECT COUNT(answer_id) as total_replies FROM abbijan_messages_answers WHERE message_id='$message_id' AND user_id='$userid' AND is_admin='1'";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		$total_replies = $row['total_replies'];

		if ($total_replies > 0) 
			return "<span class='replies_num'>".$total_replies."</span>";
		else
			return "<span class='no_replies'>".$total_replies."</span>";
	}


	if (isset($_POST['action']) && $_POST['action'] == "mysupport")
	{
		unset($errs);
		$errs = array();

		$subject	= mysql_real_escape_string(getPostParameter('subject'));
		$order_id	= (int)getPostParameter('order_id');
		$message	= mysql_real_escape_string(nl2br(getPostParameter('message')));

		if(!($subject && $message))
		{
			$errs[] = "Please fill in all required fields";
		}

		if (count($errs) == 0)
		{
			smart_mysql_query("INSERT INTO abbijan_messages SET user_id='$userid', subject='$subject', order_id='$order_id', message='$message', status='new', created=NOW()");
			$new_ticket_id = mysql_insert_id();

			// send notification
			if (NEW_TICKET_ALERT == 1)
			{
				$message = "New ticket submitted";

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";
				@mail(SITE_ALERTS_MAIL, "New ticket submitted", $message, $headers);
			}

			header("Location: mysupport.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}


	if (isset($_POST['action']) && $_POST['action'] == "reply")
	{
		unset($errs2);
		$errs2 = array();

		$message_id = mysql_real_escape_string(getPostParameter('mid'));
		$answer = mysql_real_escape_string(nl2br(getPostParameter('answer')));

		if(!($message_id && $answer))
		{
			$errs2[] = "Please enter your message";
		}

		if (count($errs2) == 0)
		{
			$ins_query = "INSERT INTO abbijan_messages_answers SET message_id='$message_id', user_id='$userid', answer='$answer', answer_date=NOW()";
			if (smart_mysql_query($ins_query))
			{
				header("Location: mysupport.php?msg=1");
				exit();
			}
		}
		else
		{
			$allerrors2 = "";
			foreach ($errs2 as $errorname2)
				$allerrors2 .= $errorname2."<br/>\n";
		}
	}



	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Members Support";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");

?>

<div id="account_content">
		
		<?php if (isset($_GET['msg']) and $_GET['msg'] == 1) { ?>
			<div class="success_msg" style="width: 93%;">Your message has been sent. We will get back to you as soon as possible.</div>
		<?php }else if (!(isset($_REQUEST['mid']) && is_numeric($_REQUEST['mid']))) { ?>

		<h1>Members Support</h1> 

		<?php if (isset($allerrors) and $allerrors != "") { ?>
			<div class="error_msg"><?php echo $allerrors; ?></div>
		<?php } ?>

		<p align="center"><img src="/images/icon_support.png" border="0" /></p>
		<p align="center">Get your questions answered! Please fill form below and we'll be happy to help you as soon as possible.</p>

 		<form action="" method="post">
		<table align="center" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td align="right" valign="middle"><span class="req">* </span>Subject:</td>
            <td align="left" valign="top"><input type="text" class="textbox" name="subject" value="<?php echo getPostParameter('subject'); ?>" size="30" /></td>
          </tr>
            <td align="right" valign="middle">Order #:</td>
            <td align="left" valign="top"><input type="text" class="textbox" name="order_id" value="<?php echo getPostParameter('order_id'); ?>" size="15" /></td>
          </tr>
          <tr>
            <td align="right" valign="top"><span class="req">* </span>Message:</td>
            <td align="left" valign="top"><textarea rows="10" cols="50" class="textbox2" name="message"><?php echo getPostParameter('message'); ?></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="action" id="action" value="mysupport" />
				<input type="submit" class="submit" name="Send" id="Sent" value="Send Message" />
		  </td>
          </tr>
		  </table>
		</form>

	<?php } ?>

	<?php

		$mquery = "SELECT m.*, DATE_FORMAT(m.created, '%e %b %Y %h:%i %p') AS message_date, u.fname, u.lname FROM abbijan_messages m, abbijan_users u WHERE u.user_id='$userid' AND m.is_admin='0' AND m.user_id=u.user_id ORDER BY created DESC";
		$mresult = smart_mysql_query($mquery);
		$mtotal = mysql_num_rows($mresult);

	?>

	<h1>My Messages</h1>

	<?php

	if (isset($_REQUEST['mid']) && is_numeric($_REQUEST['mid']))
	{
		$message_id = (int)$_REQUEST['mid'];
		$ms_query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS sent_date FROM abbijan_messages WHERE user_id='$userid' AND message_id='$message_id' LIMIT 1";
		$ms_result = smart_mysql_query($ms_query);
			
		if (mysql_num_rows($ms_result) > 0)
		{
			$ms_row = mysql_fetch_array($ms_result);
		?>
			<div class="message_date"><?php echo $ms_row['sent_date']; ?></div>
			<div class="message_subject"><?php echo $ms_row['subject']; ?></div>
			<div class="message_text">
				<?php if ($ms_row['order_id'] > 0) { ?><p align="right">Order #: <b><?php echo $ms_row['order_id']; ?></b></p><?php } ?>
				<?php echo $ms_row['message']; ?>
			</div>
		<?php
		}
		?>

		<?php

		$aquery = "SELECT *, DATE_FORMAT(answer_date, '%e %b %Y %h:%i %p') AS a_date FROM abbijan_messages_answers WHERE user_id='$userid' AND message_id='$message_id' ORDER BY answer_date ASC";
		$aresult = smart_mysql_query($aquery);
		
		if (mysql_num_rows($aresult) > 0)
		{
			// mark message as viewed //
			smart_mysql_query("UPDATE abbijan_messages_answers SET viewed='1' WHERE message_id='$message_id' AND user_id='$userid'");
			while ($arow = mysql_fetch_array($aresult)) {
		?>
				<div class="answer_date"><?php echo $arow['a_date']; ?></div>
				<div class="<?php echo ($arow['is_admin'] == 1) ? "answer_support" : "answer_sender"; ?>"><?php echo ($arow['is_admin'] == 1) ? "Support" : $_SESSION['FirstName']; ?></div>
				<div class="answer_text"><?php echo $arow['answer']; ?></div>
			<?php 
			}
		}
		?>



		<?php if (isset($allerrors2) and $allerrors2 != "") { ?>
			<div class="error_msg"><?php echo $allerrors2; ?></div>
		<?php } ?>

 		<form action="" method="post">
		<table align="center" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td align="left" valign="top">
				<a name="reply"></a>
				<b>Reply</b><br/>
				<textarea rows="5" cols="50" class="textbox2" name="answer"><?php echo getPostParameter('answer'); ?></textarea>
			</td>
          </tr>
          <tr>
			<td align="left" valign="middle">
				<input type="hidden" name="mid" id="mid" value="<?php echo $message_id; ?>" />
				<input type="hidden" name="action" id="action" value="reply" />
				<input type="submit" class="submit" name="Send" id="Sent" value="Reply" />
		  </td>
          </tr>
		</table>
		</form>

		<p><div class="sline"></div></b></p>
		<p align="center"><a class="goback" href="/mysupport.php">Go Back</a></p>

	<?php

	 }
	 else
	 {
		 
	?>

	<?php if ($mtotal > 0) { ?>

			<table align="center" width="100%" style="border: solid 1px #EEEEEE;" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="53%">Subject</th>
				<th width="22%">Date</th>
				<th width="7%">Replies</th>
				<th width="10%">Actions</th>
			</tr>
			<?php while ($mrow = mysql_fetch_array($mresult)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
					<td align="left" valign="middle">
						<a href="/mysupport.php?mid=<?php echo $mrow['message_id']; ?>" title="View">
							<?php if (strlen($mrow["subject"]) > 100) $msubject = substr($mrow["subject"], 0, 100)."..."; else $msubject = $mrow["subject"]; echo $msubject; ?>
						</a>
					</td>
					<td align="center" valign="middle"><?php echo $mrow["message_date"]; ?></td>
					<td align="center" valign="middle"><a href="/mysupport.php?mid=<?php echo $mrow['message_id']; ?>" title="View"><?php echo getRepliesNum($mrow['message_id']); ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="/mysupport.php?mid=<?php echo $mrow['message_id']; ?>" title="View"><img src="images/icon_view.png" alt="View" border="0" /></a>
						<a href="/mysupport.php?mid=<?php echo $mrow['message_id']; ?>#reply" title="Reply"><img src="images/icon_reply.png" alt="Reply" border="0" /></a>
					</td>
				  </tr>
			<?php } ?>
			</table>

        <?php }else{ ?>
				<p align="center">There are no messages at this time.</p>
        <?php } ?>
	

	<?php } ?>

</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>