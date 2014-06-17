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

	$ReferLink	= SITE_URL."?ref=".$userid;


	if (isset($_POST['action']) && $_POST['action'] == "friend")
	{
		unset($errs);
		$errs = array();

		$uname		= $_SESSION['FirstName'];
		$fname		= array();
		$fname		= $_POST['fname'];
		$femail		= array();
		$femail		= $_POST['femail'];

		if(!($fname[1] && $femail[1]))
		{
			$errs[] = "Please enter at least one friend's first name and email address";
		}
		else
		{
			foreach ($fname as $k=>$v)
			{
				if ($femail[$k] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $femail[$k]))
				{
					$errs[] = "Invalid email address"; break;
				}
			}
		}

		if (count($errs) == 0)
		{
			////////////////////////////////  Send Message  //////////////////////////////
			
			$etemplate = GetEmailTemplate('invite_friend');

				foreach ($fname as $k=>$v)
				{
					if (isset($v) && $v != "" && isset($femail[$k]) && $femail[$k] != "")
					{
						$friend_name = $v;
						$friend_email = $femail[$k];

						$esubject = $etemplate['email_subject'];
						$emessage = $etemplate['email_message'];

						$emessage = str_replace("{friend_name}", $friend_name, $emessage);
						$emessage = str_replace("{first_name}", $uname, $emessage);
						$emessage = str_replace("{referral_link}", $ReferLink, $emessage);

						$to_email = $friend_name.' <'.$friend_email.'>';
						$subject = $esubject;
						$message = $emessage;
						$from_email = SITE_MAIL;
		
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= 'From: '.SITE_TITLE.' <'.$from_email.'>' . "\r\n";

						@mail($to_email, $subject, $message, $headers);
					}
				}

			header("Location: invite.php?msg=1");
			exit();

			////////////////////////////////////////////////////////////////////////////////
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = "Refer a Friend";

	require_once ("inc/header.inc.php");
	require_once ("inc/usermenu.inc.php");

?>

<div id="account_content">

	<h1>Refer a Friend</h1>

	<table width="100%" align="center" border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="left" valign="top">
			Tell your friends about <?php echo SITE_TITLE; ?>.
			<?php if (REFER_FRIEND_BONUS > 0) { ?> You'll get a <b><?php echo DisplayMoney(REFER_FRIEND_BONUS); ?></b> credit for each friend you refer when they make their first purchase.<?php } ?>
			<p>Use your referral link to refer your friends to <?php echo SITE_TITLE; ?>. Why not add this link to your email signature or post it on your website, blog, etc.</p>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 1px #E5E5E5 solid;" bgcolor="#F7F7F7" align="left" valign="middle">
			&nbsp;<b>Your referral link:</b>
			<input type="text" style="border: 0px none; background: #F7F7F7; width: 350px;" class="textbox" readonly="readonly" onfocus="this.select();" onclick="this.focus();this.select();" value="<?php echo $ReferLink; ?>" />
		</td>
	</tr>
	</table>
	<br />

	<h1>Send Invitation</h1>

	<?php if (REFER_FRIEND_BONUS > 0) { ?>
	<table align="center" width="95%" border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="center" valign="top">
			Refer your friends and get <b><?php echo DisplayMoney(REFER_FRIEND_BONUS); ?></b>! It's easy. Enter up to 5 email addresses of your friends and family.<br/> Each friend will receive link to join us and you will receive <b><?php echo DisplayMoney(REFER_FRIEND_BONUS); ?></b> per each referred member.<br/><br/>
		</td>
	</tr>
	</table>
	<?php } ?>

	<form action="" method="post">
	<table align="center" border="0" cellpadding="3" cellspacing="0">
		<?php if (isset($_GET['msg']) and $_GET['msg'] == 1) { ?>
			<div style="width:70%;" class="success_msg">Thank you! Message has been sent to your friends. <a style="color:#000;" href="/invite.php">Send more invitations &#155;</a></div>
		<?php }else{ ?>
          
			<?php if (isset($allerrors) and $allerrors != "") { ?>
				<div style="width:70%;" class="error_msg"><?php echo $allerrors; ?></div>
			<?php } ?>

		  <?php for ($i=1; $i<=5; $i++) { ?>
          <tr>
			<td colspan="2" align="left" valign="top">
				<table width="100%" cellpadding="0" cellspacing="1" border="0">
                    <tr>
						<td align="left" valign="top">Friend #<?php echo $i; ?> First Name: <?php if ($i == 1) { ?><span class="req">* </span><?php } ?><br/>
							<input type="text" name="fname[<?php echo $i; ?>]" class="textbox" value="<?php echo $fname[$i]; ?>" size="27" />
						</td>
						<td width="15">&nbsp;</td>
						<td align="left" valign="top">Friend #<?php echo $i; ?> Email Address: <?php if ($i == 1) { ?><span class="req">* </span><?php } ?><br/>
							<input type="text" name="femail[<?php echo $i; ?>]" class="textbox" value="<?php echo $femail[$i]; ?>" size="27" />
						</td>
					</tr>
				</table>
			</td>
          </tr>
		  <?php } ?>
          <tr>
			<td colspan="2" align="center" valign="middle">
				<input type="hidden" name="action" id="action" value="friend" />
				<input type="submit" class="submit" name="Send" id="Send" value="Send Invitation" />
			</td>
          </tr>

		  <?php } ?>
	</table>
	</form>


	<h1>My Referrals</h1>
	<a name="referrals"></a>

	<?php

	$results_per_page = 10;
	$cc = 0;

	////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
			{
				case "fname": $rrorder = "fname"; break;
				case "country": $rrorder = "country"; break;
				case "created": $rrorder = "created"; break;
				default: $rrorder = "created"; break;
			}
		}
		else
		{
			$rrorder = "created";
		}

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
			{
				case "asc": $rorder = "asc"; break;
				case "desc": $rorder = "desc"; break;
				default: $rorder = "desc"; break;
			}
		}
		else
		{
			$rorder = "desc";
		}
	//////////////////////////////////////////////////


		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$refs_query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS signup_date FROM abbijan_users WHERE ref_id='$userid' ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
		$total_refs_result = smart_mysql_query("SELECT * FROM abbijan_users WHERE ref_id='$userid'");
		$total_refs = mysql_num_rows($total_refs_result);

		$refs_result = smart_mysql_query($refs_query);
		$total_refs_on_page = mysql_num_rows($refs_result);

		if ($total_refs > 0)
		{
	?>

			<div class="browse_top">
			<div class="sortby">
				<form action="#referrals" id="form1" name="form1" method="get">
					<span>Sort by:</span>
					<select name="column" id="column" onChange="document.form1.submit()">
						<option value="created" <?php if ($_GET['column'] == "added") echo "created"; ?>>Signup Date</option>
						<option value="fname" <?php if ($_GET['column'] == "fname") echo "selected"; ?>>Name</option>
						<option value="country" <?php if ($_GET['column'] == "country") echo "selected"; ?>>Country</option>
					</select>
					<select name="order" id="order" onChange="document.form1.submit()">
						<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
						<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
					</select>
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
				</form>
			</div>
			<div class="results">
				Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_refs_on_page, $total_refs); ?> of <?php echo $total_refs; ?>
			</div>
			</div>

			<table align="center" class="btb" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<th width="50%">Name</th>
				<th width="25%">Country</th>
				<th width="25%">Signup Date</th>
			</tr>
			<?php while ($refs_row = mysql_fetch_array($refs_result)) { $cc++; ?>
			<tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
				<td align="left" valign="middle"><img src="/images/referral_icon.png" align="absmiddle" /> &nbsp; <b><?php echo $refs_row['fname']." ".$refs_row['lname']; ?></b></td>
				<td align="center" valign="middle"><?php echo $refs_row['country']; ?></td>
				<td align="center" valign="middle"><?php echo $refs_row['signup_date']; ?></td>
			</tr>
			<?php } ?>

			<?php echo ShowPagination("users",$results_per_page,"invite.php?column=$rrorder&order=$rorder&", "WHERE ref_id='".(int)$userid."'"); ?>
		
		<?php }else{ ?>
			<p>You have not received any referrals at this time.</p>
		<?php } ?>

</div>
<div style="clear: both"></div>


<?php require_once ("inc/footer.inc.php"); ?>