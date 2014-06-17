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


	if (isset($_POST["action"]) && $_POST["action"] == "message_reply")
	{
		unset($errors);
		$errors = array();

		$message_id	= (int)getPostParameter('id');
		$user_id	= (int)getPostParameter('uid');
		$answer		= mysql_real_escape_string(nl2br(getPostParameter('answer')));

		if (!($message_id && $user_id && $answer))
		{
			$errors[] = "Please enter your reply";
		}

		if (count($errors) == 0)
		{
			$ins_query = "INSERT INTO abbijan_messages_answers SET message_id='$message_id', user_id='$user_id', is_admin='1', answer='$answer', answer_date=NOW()";
			if (smart_mysql_query($ins_query))
			{
				smart_mysql_query("UPDATE abbijan_messages SET viewed='1', status='replied' WHERE message_id='$message_id'");
				header("Location: messages.php?msg=sent");
				exit();
			}
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}

	if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
	{
		$mid	= (int)$_REQUEST['id'];
		$pn		= (int)$_GET['pn'];

		$query = "SELECT m.*, DATE_FORMAT(m.created, '%e %b %Y %h:%i %p') AS message_date, u.fname, u.lname FROM abbijan_messages m, abbijan_users u WHERE m.user_id=u.user_id AND m.message_id='$mid'";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}

	$title = "Message Reply";
	require_once ("inc/header.inc.php");

?>   
    
	<?php

		if ($total > 0)
			{
				$row = mysql_fetch_array($result);
	?>

	   <h2>Message Reply</h2>


		<?php if (isset($errormsg)) { ?>
			<div style="width:400px;" class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

		<form action="" method="post" name="form1">
          <table align="center" cellpadding="5" cellspacing="5" border="0">
            <tr>
              <td nowrap="nowrap" width="20%" valign="middle" align="right" class="tb2">To:</td>
              <td width="80%" valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
            </tr>
            <tr>
              <td nowrap="nowrap" valign="middle" align="right" class="tb2">Subject:</td>
              <td nowrap="nowrap" valign="top"><b><?php echo $row['subject']; ?></b></td>
            </tr>
			<?php if ($row['order_id'] > 0) { ?>
            <tr>
              <td nowrap="nowrap" valign="middle" align="right" class="tb2">Order #:</td>
              <td nowrap="nowrap" valign="top"><a href="order_details.php?id=<?php echo $row['order_id']; ?>"><?php echo $row['order_id']; ?></a></td>
            </tr>
			<?php } ?>
           <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb2">Reply:</td>
            <td align="left" valign="top"><textarea rows="7" cols="55" class="textbox2" name="answer"><?php echo getPostParameter('answer'); ?></textarea></td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="hidden" name="id" id="id" value="<?php echo (int)$row['message_id']; ?>" />
				<input type="hidden" name="uid" id="uid" value="<?php echo (int)$row['user_id']; ?>" />
				<input type="hidden" name="action" id="action" value="message_reply">
				<input type="submit" name="reply" id="reply" class="submit" value="Send" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='messages.php'" />
            </td>
          </tr>
          </table>
		</form>

      <?php }else{ ?>
				<div class="info_box">Sorry, no message found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>