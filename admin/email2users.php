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

	$query = "SELECT * FROM abbijan_users WHERE email<>'' AND newsletter='1' AND status='active'";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$query2 = "SELECT * FROM abbijan_subscribers";
	$result2 = smart_mysql_query($query2);
	$total2 = mysql_num_rows($result2);


if (isset($_POST['action']) && $_POST['action'] == "email2users")
{
	$msubject	= trim($_POST['msubject']);
	$allmessage = stripslashes($_POST['allmessage']);

	unset($errs);
	$errs = array();

	if (!($msubject && $allmessage))
	{
		$errs[] = "Please enter subject and message";
	}

	switch ($_POST['subscribers_group'])
	{
		case "all": $query = "(SELECT email, activation_key AS unsubscribe_key, fname AS first_name, CONCAT(fname, \" \", lname) as full_name FROM abbijan_users WHERE email != '' AND newsletter='1' AND status='active') UNION (SELECT email, unsubscribe_key, \"Subscriber\" AS first_name, \"Newsletter Subscriber\" AS full_name FROM abbijan_subscribers WHERE email != '' AND status='active')"; break;
		case "registered": $query = "SELECT email, activation_key AS unsubscribe_key, fname AS first_name, CONCAT(fname, \" \", lname) as full_name FROM abbijan_users WHERE email != '' AND newsletter='1' AND status='active'"; break;
		case "unregistered": $query = "SELECT email, unsubscribe_key, \"Subscriber\" AS first_name, \"Newsletter Subscriber\" AS full_name WHERE email != '' AND status='active'"; break;
	}

	if (count($errs) == 0)
	{
		$result = smart_mysql_query($query);

		while ($row = mysql_fetch_array($result))
		{
			////////////////////////////////  Send Message  //////////////////////////////
			$message = "<html>
						<head>
							<title>".$subject."</title>
						</head>
						<body>
						<table width='700' border='0' cellpadding='10'>
						<tr>
							<td><p style='font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:13px;'>";
			$message .= $allmessage;
			$message .= "<p>
				<div style='font-family:tahoma, arial, sans-serif;width:700px;padding-top:12px;clear:both;font-size:11px;color:#5B5B5B;text-align:left;'>
				--------------------------------------------------------------------------------------------<br/>
				You are receiving this email as you have directly signed up to ".SITE_TITLE.".<br/>If you do not wish to receive these messages in the future, please <a href='".SITE_URL."unsubscribe.php?key=".$row['unsubscribe_key']."'>unsubscribe</a>.</div></p>";
			$message .= "</p></td>
						</tr>
						</table>
						</body>
						</html>";

			$message = str_replace("{first_name}", $row['fname'], $message);

			$to_email = $row['fname'].' '.$row['lname'].' <'.$row['email'].'>';		
			
			$subject = $msubject;		
			
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";
			
			@mail($to_email, $subject, $message, $headers);
			////////////////////////////////////////////////////////////////////////////////
		}

		header ("Location: email2users.php?msg=1");
		exit();
	}
	else
	{
		$allerrors = "";
		foreach ($errs as $errorname)
			$allerrors .= $errorname."<br/>\n";
	}
}

	$title = "Email Members";
	require_once ("inc/header.inc.php");

?>
 

      <?php if ($total > 0) { ?>

        <h2>Email Members</h2>

		<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
			<div style="width:100%;" class="success_box">Message has been successfully sent!</div>
		<?php }else{ ?>

			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div style="width:100%;" class="error_box"><?php echo $allerrors; ?></div>
			<?php } ?>

		<div class="subscribers">
			&nbsp; <span style="font-size:18px; color:#FFF; background:#8CF76F; padding:3px 6px;"><?php echo $total; ?></span>&nbsp; subscribed <?php echo ($total == 1) ? "member" : "members"; ?><br/><br/>
			&nbsp; <span style="font-size:18px; color:#444; background:#f5f5f5; padding:3px 6px;"><?php echo $total2; ?></span>&nbsp; unregistered <?php echo ($total2 == 1) ? "subscriber" : "subscribers"; ?>
		</div>

		<p>&nbsp;</p>

        <form action="" method="post">
          <table align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td valign="middle" align="right" class="tb1">Subject:</td>
            <td valign="top"><input type="text" name="msubject" id="msubject" value="<?php echo $msubject; ?>" size="80" class="textbox" /></td>
          </tr>
		  <script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script> 
          <tr>
            <td valign="middle" align="right" class="tb1">Message:</td>
            <td valign="top"><textarea cols="80" id="editor" name="allmessage" rows="10"><?php echo stripslashes($_POST['allmessage']); ?></textarea></td>
          </tr>
			<script>
				CKEDITOR.replace( 'editor' );
			</script>
          <tr>
            <td valign="middle" align="right" class="tb1">Send To:</td>
            <td valign="top">
				<select name="subscribers_group">
					<option value="all">All Subscribers (<?php echo $total+$total2; ?>)</option>
					<?php if ($total > 0 && $total2 > 0) { ?>
						<?php if ($total > 0) { ?><option value="registered">Registered members (<?php echo $total; ?>)</option><?php } ?>
						<?php if ($total2 > 0) { ?><option value="unregistered">Unregistered members (<?php echo $total2; ?>)</option><?php } ?>
					<?php } ?>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">&nbsp;</td>
            <td height="30" bgcolor="#F7F7F7" align="center" valign="middle">
				<span style="color:#AAA;">Optional tag: <b>{first_name}</b> - will be replaced with User First Name</span>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="action" id="action" value="email2users" />
			<input type="submit" name="Send" id="Send" class="submit" value="Send Message" />
			&nbsp;&nbsp;<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='index.php'" />
		  </td>
          </tr>
        </table>
      </form>

		<?php } ?>

      <?php }else{ ?>
				<div class="info_box">Sorry, you don't have subscribers for now.</div>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>