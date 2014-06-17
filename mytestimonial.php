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


	if (isset($_POST['action']) && $_POST['action'] == "mytestimonial")
	{
		unset($errs);
		$errs = array();

		$testimonial = mysql_real_escape_string(nl2br(getPostParameter('testimonial')));

		if(!$testimonial)
		{
			$errs[] = "Please enter your testimonial";
		}

		if (count($errs) == 0)
		{
			$user = mysql_fetch_array(smart_mysql_query("SELECT fname, lname FROM abbijan_users WHERE user_id='$userid' and status='active' LIMIT 1"));
			$author = $user['fname']." ".substr($user['lname'], 0, 1)."."; 
			
			$ins_query = "INSERT INTO abbijan_testimonials SET user_id='$userid', author='$author', testimonial='$testimonial', status='inactive', added=NOW()";

			// send notification
			if (NEW_TESTIMONIAL_ALERT == 1)
			{
				$message = "New testimonial was added.";

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: '.SITE_TITLE.' <'.SITE_MAIL.'>' . "\r\n";
				@mail(SITE_ALERTS_MAIL, "New Testimonial Added", $message, $headers);
			}

			if (smart_mysql_query($ins_query))
			{
				header("Location: mytestimonial.php?msg=1");
				exit();
			}
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Write Your Testimonial";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");

?>

<div id="account_content">

	<h1>Write Your Testimonial</h1>


	<?php if (isset($allerrors) and $allerrors != "") { ?>
		<div style="width: 94%;" class="error_msg"><?php echo $allerrors; ?></div>
	<?php } ?>

	<?php if (isset($_GET['msg']) and $_GET['msg'] == 1) { ?>
		<div style="width: 94%;" class="success_msg" style="width:90%;">Your testimonial has been sent. We will review it soon.</div>
	<?php }else{ ?>
		<p>Simply complete the form below and once reviewed we shall add your testimonial about us to our testimonials page.</p>
 		<form action="" method="post">
		<table border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td align="left" valign="top"><textarea rows="7" cols="50" class="textbox2" name="testimonial"><?php echo getPostParameter('testimonial'); ?></textarea></td>
          </tr>
          <tr>
			<td align="left" valign="middle">
				<input type="hidden" name="action" id="action" value="mytestimonial" />
				<input type="submit" class="submit" name="add" id="add" value="Add Testimonial" />
		  </td>
          </tr>
		</table>
		</form>
	<?php } ?>

</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>