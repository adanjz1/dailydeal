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
		$eid	= (int)$_GET['id'];

		$query = "SELECT * FROM abbijan_email_templates WHERE template_id='$eid'";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		$total = mysql_num_rows($result);
	}

	$title = "View Email Template";
	require_once ("inc/header.inc.php");

?>   
    
      <?php if ($total > 0) { ?>

          <h2>View Email Template</h2>

          <table width="100%" cellpadding="2" cellspacing="5" border="0">
            <tr>
              <td valign="middle" align="right" class="tb1">&nbsp;</td>
              <td align="left" valign="top"><b><?php echo stripslashes($row['email_subject']); ?></b></td>
            </tr>
            <tr>
              <td colspan="2"><div class="sline"></div></td>
            </tr>
           <tr>
            <td width="10" valign="middle" align="right" class="tb1">&nbsp;</td>
            <td valign="top"><?php echo stripslashes($row['email_message']); ?></td>
          </tr>
          <tr>
            <td colspan="2"><div class="sline"></div></td>
           </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="button" class="submit" name="edit" value="Edit Template" onClick="javascript:document.location.href='etemplate_edit.php?id=<?php echo $row['template_id']; ?>'" /> &nbsp; 
				<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='etemplates.php'" />
		  </td>
          </tr>
          </table>

      <?php }else{ ?>
				<div class="info_box">Sorry, no template found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>