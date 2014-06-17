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

	$website = "http://";


if (isset($_POST['action']) && $_POST['action'] == "submit_deal")
{
	unset($errs);
	$errs = array();

	$name			= trim($_POST['name']);
	$email			= trim($_POST['email']);
	$company		= trim($_POST['company']);
	$website		= trim($_POST['website']);
	$umessage		= nl2br(trim($_POST['message']));

	if (!($name && $email && $umessage))
	{
		$errs[] = "Please fill in all required fields";
	}
	else
	{
		if (isset($website) && $website != "http://")
		{
			if (substr($website, 0, 7) != 'http://')
			{
				$errors[] = "Enter correct website link format, enter the 'http://' statement before your link";
			}
			elseif ($website == 'http://')
			{
				$errors[] = "Please enter correct website link";
			}
		}
		
		if (isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
		{
			$errs[] = "Please enter a valid email address";
		}
	}

	if (count($errs) == 0)
	{
		if (NEW_DEAL_ALERT == 1)
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
			
			if (isset($company) && $company != "")
			{
				$message .= "<br/>----------------<br/>";
				$message .= "Company: ".$company."<br/>";
			}

			if (isset($website) && $website != "http://")
			{
				$message .= "Website: <a href='".$website."' target='_blank'>".$website."</a><br/>";
				$message .= "<br/>----------------<br/>";
			}
			
			$message .= "</p>
							</td>
						</tr>
						</table>
						</body>
					</html>";

			$to_email = SITE_MAIL;
		
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '.$name.' <'.$email.'>' . "\r\n";

			if (@mail($to_email, "Deal Submitted", $message, $headers))
			{
				header ("Location: deal_submit.php?msg=1");
				exit();
			}
			////////////////////////////////////////////////////////////////////////////////
		}
	}
	else
	{
		foreach ($errs as $errorname)
		{
			$errormsg .= "&#155; ".$errorname."<br/>\n";
		}
	}
}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Submit a Deal";

	require_once ("inc/header.inc.php");

?>

	<h1>Submit a Deal</h1>

	<p>Do you have a great product or service? Run an amazing deal on our site and get massive exposure and an incredible instant sales boost!<br/>Please fill out the form below and let us know about your product or service and we'll get back to you as soon as possible.</p>


	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div style="width: 500px;" class="success_msg">Thank you! You deal has been successfully submitted. We'll get back to you soon.</div>
	<?php }?>

	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div style="width: 500px;" class="error_msg"><?php echo $errormsg; ?></div>
	<?php } ?>


	<p align="right"><span class="req">* denotes required field</span></p>

	<form action="" method="post">
	<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
            <td align="right" valign="middle"><span class="req">* </span>Your Name:</td>
			<td align="left" valign="top"><input type="text" name="name" class="textbox" value="<?php echo getPostParameter('name'); ?>" size="30" /></td>
		</tr>
		<tr>
			<td align="right" valign="middle"><span class="req">* </span>Your Email:</td>
			<td align="left" valign="top"><input type="text" name="email" class="textbox" value="<?php echo getPostParameter('email'); ?>" size="30" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle">Company:</td>
			<td align="left" valign="top"><input type="text" name="company" class="textbox" value="<?php echo getPostParameter('company'); ?>" size="30" /></td>
		</tr>
		<tr>
			<td align="right" valign="middle">Website:</td>
			<td align="left" valign="top"><input type="text" name="website" class="textbox" value="<?php echo $website; ?>" size="30" /></td>
		</tr>
		<tr>
			<td align="right" valign="top"><span class="req">* </span>Description:</td>
			<td align="left" valign="top">
				<textarea name="message" cols="60" rows="8" placeholder="Please enter a brief description of your product or service" class="textbox2"><?php echo getPostParameter('message'); ?></textarea>
			</td>
		</tr>
		<tr>
			<td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="action" id="action" value="submit_deal" />
				<input type="submit" class="submit" name="Submit" value="Submit a Deal" />
			</tr>
		</tr>
	</table>
	</form>


<?php require_once ("inc/footer.inc.php"); ?>