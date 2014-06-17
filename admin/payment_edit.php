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
	require_once("./inc/adm_functions.inc.php");


if (isset($_POST["action"]) && $_POST["action"] == "edit_payment")
{
	unset($errors);
	$errors = array();

	$transaction_id	= (int)getPostParameter('tid');
	$status			= mysql_real_escape_string(getPostParameter('status'));
	$reason			= mysql_real_escape_string(nl2br(getPostParameter('reason')));


	if (!$status)
	{
		$errors[] = "Please select payment status";
	}
	else
	{
		switch ($status)
		{
			case "paid": $status="paid"; break;
			case "pending": $status="pending"; break;
			case "declined": $status="declined"; break;
			default: $status="unknown"; break;
		}
	}

	if (count($errors) == 0)
	{
		$sql = "UPDATE abbijan_transactions SET status='$status', reason='$reason', updated=NOW() WHERE transaction_id='$transaction_id' AND status<>'request'";
		$result = smart_mysql_query($sql);

		header("Location: payments.php?msg=updated");
		exit();
	}
	else
	{
		$errormsg = "";
		foreach ($errors as $errorname)
			$errormsg .= "&#155; ".$errorname."<br/>";
	}
}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) { $id = (int)$_GET['id']; } elseif (isset($_POST['tid']) && is_numeric($_POST['tid'])) { $id = (int)$_POST['tid']; }

	if (isset($id) && is_integer($id))
	{
		$query = "SELECT t.*, DATE_FORMAT(t.created, '%e %b %Y %h:%i %p') AS payment_date, u.fname, u.lname FROM abbijan_transactions t, abbijan_users u WHERE t.user_id=u.user_id AND t.transaction_id='$id'";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Edit Payment";
	require_once ("inc/header.inc.php");

?>
    
    
     <h2>Edit Payment</h2>

		<?php if ($total > 0) { 

				$row = mysql_fetch_array($result);

		 ?>


		<script>
		<!--
			function hiddenDiv(id,showid){
				if(document.getElementById(id).value == "declined"){
					document.getElementById(showid).style.display = ""
				}else{
					document.getElementById(showid).style.display = "none"
				}
			}
		-->
		</script>


		<?php if (isset($errormsg)) { ?>
			<div style="width:450px;" class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

			<form action="" method="post" name="form1">
            <table bgcolor="#F7F7F7" style="border: 1px dotted #eee; padding: 20px;" width="70%" cellpadding="2" cellspacing="5" border="0" align="center">
			  <tr>
                <td valign="middle" align="right" class="tb1">Payment ID:</td>
                <td width="300" valign="top"><?php echo $row['transaction_id']; ?></td>
              </tr>
              <tr>
                <td nowrap="nowrap" valign="middle" align="right" class="tb1">Reference ID:</td>
                <td valign="top"><?php echo $row['reference_id']; ?></td>
              </tr>
              <tr>
                <td nowrap="nowrap" valign="middle" align="right" class="tb1">Member Name:</td>
                <td valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Payment type:</td>
                <td valign="top"><?php echo GetPaymentName($row['payment_type']); ?></td>
              </tr>
			  <?php if ($row['payment_details'] != "") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Payment Details:</td>
                <td valign="top"><?php echo $row['payment_details']; ?></td>
              </tr>
			  <?php } ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Amount:</td>
                <td valign="top"><?php echo DisplayMoney($row['amount']); ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Created:</td>
                <td valign="top"><?php echo $row['payment_date']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Current Status:</td>
                <td valign="top">
					<?php
						switch ($row['status'])
						{
							//case "confirmed": echo "<span style='margin:0;' class='confirmed_status'>".$row['status']."</span>"; break;
							case "pending": echo "<span style='margin:0;' class='pending_status'>".$row['status']."</span>"; break;
							case "declined": echo "<span style='margin:0;' class='declined_status'>".$row['status']."</span>"; break;
							case "failed": echo "<span style='margin:0;' class='failed_status'>".$row['status']."</span>"; break;
							case "request": echo "<span style='margin:0;' class='pending_status'>waiting for approval</span>"; break;
							case "paid": echo "<span style='margin:0;' class='paid_status'>".$row['status']."</span>"; break;
							default: echo "<span style='margin:0;' class='payment_status'>".$row['status']."</span>"; break;
						}
					?>
				</td>
              </tr>
			<?php if ($row['status'] == "declined" && $row['reason'] != "") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Decline Reason:</td>
                <td style="color: #EB0000; background: #FFEBEB; border-left: 2px #FF0000 solid" align="left" valign="top"><?php echo $row['reason']; ?></td>
              </tr>
			<?php } ?>
              <tr>
                <td colspan="2" style="border-bottom: 3px dotted #DDDDDD;">&nbsp;</td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Change Status to:</td>
                <td valign="top">
					<select name="status" id="status" onchange="javascript:hiddenDiv('status','reason')">
						<option value="paid">paid</option>
						<option value="pending">pending</option>
						<option value="declined">declined</option>
					</select>
				</td>
              </tr>
            <tr id="reason" style="display: none;">
                <td valign="middle" align="right" class="tb1">Reason:</td>
                <td valign="top"><textarea cols="55" rows="5" name="reason" class="textbox2"><?php echo getPostParameter('reason'); ?></textarea></td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="hidden" name="tid" id="tid" value="<?php echo (int)$row['transaction_id']; ?>" />
				<input type="hidden" name="action" id="action" value="edit_payment">
				<input type="submit" class="submit" name="process" value="Update" />
				<input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" />
			  </td>
            </tr>
          </table>
		  </form>
      
	  <?php }else{ ?>
			<div class="info_box">Sorry, no payment found.</div>
			<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>