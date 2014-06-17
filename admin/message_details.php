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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$mid	= (int)$_GET['id'];
		$pn		= (int)$_GET['pn'];

		$query = "SELECT m.*, DATE_FORMAT(m.created, '%e %b %Y %h:%i %p') AS message_date, u.fname, u.lname FROM abbijan_messages m, abbijan_users u WHERE m.user_id=u.user_id AND m.message_id='$mid'";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			// mark message as viewed //
			smart_mysql_query("UPDATE abbijan_messages SET viewed='1' WHERE message_id='$mid'");
		}
	}

	$title = "View Message";
	require_once ("inc/header.inc.php");

?>   
    
	<?php

		if ($total > 0)
			{
				$row = mysql_fetch_array($result);
	?>

	   <h2>View Message</h2>

		<form action="" method="post" name="form1">
          <table align="center" cellpadding="5" cellspacing="5" border="0">
            <tr>
              <td nowrap="nowrap" width="20%" valign="middle" align="right" class="tb2">From:</td>
              <td width="80%" valign="top">
				<a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a>
              </td>
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
              <td nowrap="nowrap" valign="middle" align="right" class="tb2">Date:</td>
              <td nowrap="nowrap" valign="top"><span class="date"><?php echo $row['message_date']; ?></span></td>
            </tr>
           <tr>
            <td nowrap="nowrap" valign="top" align="right" class="tb2">Message:</td>
            <td style="border: 1px dotted #eee;" valign="top"><div style="width: 400px; min-height: 70px;"><?php echo $row['message']; ?></div></td>
          </tr>


	<?php

		$aquery = "SELECT *, DATE_FORMAT(answer_date, '%e %b %Y %h:%i %p') AS a_date FROM abbijan_messages_answers WHERE user_id='".$row['user_id']."' AND message_id='$mid' ORDER BY answer_date ASC";
		$aresult = smart_mysql_query($aquery);
		$atotal = mysql_num_rows($aresult);
		if ($atotal > 0) {
			
			while ($arow = mysql_fetch_array($aresult)) {
				if ($arow['is_admin'] == 1) {$sender = "Admin"; $bg = "#FFFAF2";}else{$sender = "Member"; $bg = "#F7F7F7";}
				
		?>
				
				<tr>
					<td nowrap="nowrap" valign="top" align="right" class="tb2"><?php echo $sender; ?> Reply:</td>
					<td bgcolor="<?php echo $bg; ?>" style="border: 1px dotted #eee;" valign="top">
						<div style="width: 400px; min-height: 70px;">
							<div align="right"><small><font color="#A7A7A7"><?php echo $arow['a_date']; ?></font></small></div>
							<?php echo $arow['answer']; ?>
						</div>
					</td>
				</tr>
		
		<?php } ?> 
		
		
		<?php }	?>

          <tr>
          <td colspan="2" align="center" valign="bottom">
			<input type="button" class="submit" name="reply" value="Reply" onClick="javascript:document.location.href='message_reply.php?id=<?php echo $mid; ?>'" />
			<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='messages.php'" />
		  </td>
          </tr>
          </table>
		</form>

      <?php }else{ ?>
				<div class="info_box">Sorry, no message found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>