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

	$content = GetContent('contact');

	if (isset($_SESSION['userid']) && is_numeric($_SESSION['userid']))
	{
		header ("Location: mysupport.php");
		exit();
	}


	if (isset($_POST['action']) && $_POST['action'] == 'contact')
	{
		unset($errs);
		$errs = array();

		$fname			= trim($_POST['fname']);
		$email			= trim($_POST['email']);
		$email_subject	= trim($_POST['email_subject']);
		$umessage		= nl2br(trim($_POST['umessage']));

		if (!($fname && $email && $email_subject && $umessage))
		{
			$errs[] = "Please fill in all fields";
		}
		else
		{
			if (isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				$errs[] = "Please enter a valid email address";
			}
		}


		if (count($errs) == 0)
		{
				////////////////////////////////  Send Message  //////////////////////////////
				$message = "<html>
							<head>
								<title>".SITE_TITLE."</title>
							</head>
							<body>
							<table width='80%' border='0' cellpadding='10'>
							<tr>
								<td>";
				$message .= "<p style='font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px;'>";
				$message .= $umessage;
				$message .= "</p>
								</td>
							</tr>
							</table>
							</body>
						</html>";

				$to_email = SITE_MAIL;
			
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.$fname.' <'.$email.'>' . "\r\n";

				if (@mail($to_email, $email_subject, $message, $headers))
				{
					header ("Location: contact.php?msg=1");
					exit();
				}
				////////////////////////////////////////////////////////////////////////////////
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = $content['title'];

	require_once ("inc/header.inc.php");
	
?>

	<h1><?php echo $content['title']; ?></h1>


	<div style="float: left; width: 50%; padding: 20px 5px 5px 5px;">

	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div style="width: 410px;" class="success_msg">Your message has been sent.</div>
	<?php }?>

	<?php if (isset($allerrors) && $allerrors != "") { ?>
		<div style="width: 410px;" class="error_msg"><?php echo $allerrors; ?></div>
	<?php } ?>

	<form action="" method="post">
	<table width="400" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
            <td nowrap="nowrap" align="right" valign="middle">Your Name:</td>
			<td align="left" valign="top"><input name="fname" class="textbox" type="text" value="<?php echo getPostParameter('fname'); ?>" size="27" /></td>
		</tr>
		<tr>
			<td nowrap="nowrap" align="right" valign="middle">Your Email:</td>
			<td align="left" valign="top"><input name="email" class="textbox" type="text" value="<?php echo getPostParameter('email'); ?>" size="27" /></td>
		</tr>
		<tr>
			<td nowrap="nowrap" align="right" valign="middle">Subject:</td>
			<td align="left" valign="top"><input name="email_subject" class="textbox" type="text" value="<?php echo getPostParameter('email_subject'); ?>" size="27" /></td>
		</tr>
		<tr>
			<td nowrap="nowrap" align="right" valign="middle">Message:</td>
			<td align="left" valign="top"><textarea rows="10" cols="50" class="textbox2" name="umessage"><?php echo getPostParameter('umessage'); ?></textarea></td>
		</tr>
		<tr>
			<td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="action" id="action" value="contact" />
				<input type="submit" class="submit" name="Submit" value="Send message" />
			</td>
		</tr>
	</table>
	</form>
	</div>

	<div style="float: right; width: 40%; padding: 20px 5px 5px 5px;">
		<p><?php echo $content['text']; ?></p>
	</div>
	<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>