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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$query = "SELECT t.*, DATE_FORMAT(t.created, '%e %b %Y %h:%i %p') AS payment_date, DATE_FORMAT(t.updated, '%e %b %Y %h:%i %p') AS updated_date, DATE_FORMAT(t.process_date, '%e %b %Y %h:%i %p') AS processed_date, u.username, u.email, u.fname, u.lname FROM abbijan_transactions t, abbijan_users u WHERE t.transaction_id='$id' AND t.user_id=u.user_id";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Payment Details";
	require_once ("inc/header.inc.php");

?>
    
    
     <h2>Payment Details</h2>

		<?php if ($total > 0) { 

				$row = mysql_fetch_array($result);
		 ?>
            <table bgcolor="#F7F7F7" style="border: 1px dotted #eee; padding: 20px;" width="70%" cellpadding="3" cellspacing="5" border="0" align="center">
			  <tr>
                <td valign="middle" align="right" class="tb1">Payment ID:</td>
                <td valign="top"><?php echo $row['transaction_id']; ?></td>
              </tr>
              <tr>
                <td nowrap="nowrap" valign="middle" align="right" class="tb1">Reference ID:</td>
                <td valign="top"><?php echo $row['reference_id']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Username:</td>
                <td valign="top"><?php echo $row['username']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Member's Name:</td>
                <td valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Email:</td>
                <td valign="top"><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Payment type:</td>
                <td valign="top"><b><?php echo GetPaymentName($row['payment_type']); ?></b></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Payment method:</td>
                <td valign="top">
					<?php if ($row['payment_method'] == "paypal") { ?><img src="images/icon/paypal.png" align="absmiddle" />&nbsp;<?php } ?>
					<?php echo GetPaymentMethodName($row['payment_method']); ?>
                </td>
              </tr>
			  <?php if ($row['payment_details'] != "") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Payment Details:</td>
                <td valign="top"><?php echo $row['payment_details']; ?></td>
              </tr>
			  <?php } ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Amount:</td>
                <td valign="top"><span style="color:#FFFFFF; background:#89D70D; padding:3px 8px;"><?php echo DisplayMoney($row['amount']); ?></span></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Status:</td>
                <td valign="top">
					<?php
						switch ($row['status'])
						{
							case "confirmed": echo "<span style='margin:0;' class='confirmed_status'>".$row['status']."</span>"; break;
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
                <td style="color: #EB0000; background: #FFEBEB; border-left: 2px #FF0000 solid" valign="top"><?php echo $row['reason']; ?></td>
              </tr>
			  <?php } ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Created:</td>
                <td valign="top"><?php echo $row['payment_date']; ?></td>
              </tr>
			  <?php if ($row['updated'] != "0000-00-00 00:00:00") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Updated:</td>
                <td valign="top"><?php echo $row['updated_date']; ?></td>
              </tr>
			  <?php } ?>
			  <?php if ($row['payment_type'] == "withdraw" && $row['status'] != "request" && $row['process_date'] != "0000-00-00 00:00:00") { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Process Date:</td>
                <td valign="top"><?php echo $row['processed_date']; ?></td>
              </tr>
			  <?php } ?>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<?php if ($row['status'] == "pending" || $row['status'] == "request") { ?>
					<input type="button" class="submit" name="edit" value="Process Payment" onClick="javascript:document.location.href='payment_process.php?id=<?php echo $row['transaction_id']; ?>'" /> &nbsp;
				<?php } ?>
				<input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" />
			  </td>
            </tr>
          </table>


      <?php }else{ ?>
			<div class="info_box">Sorry, no payment found.</div>
			<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>