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
		$cid	= (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(modified, '%e %b %Y %h:%i %p') AS modify_date FROM abbijan_content WHERE content_id='$cid'";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		$total = mysql_num_rows($result);
	}

	$title = "View Content";
	require_once ("inc/header.inc.php");

?>   
    
      <?php if ($total > 0) { ?>

          <h2>View Content</h2>

          <table width="100%" align="center" cellpadding="2" cellspacing="5" border="0">
          <tr>
            <td bgcolor="#F7F7F7" align="left" valign="top">
				<h1><?php echo stripslashes($row['title']); ?></h1>
            </td>
          </tr>
          <tr>
            <td><div class="sline"></div></td>
          </tr>
          <tr>
            <td valign="top"><?php echo stripslashes($row['description']); ?></td>
          </tr>
          <tr>
            <td><div class="sline"></div></td>
          </tr>
		  <?php if ($row['content_id'] > 5) { ?>
           <tr>
				<td height="30" bgcolor="#F7F7F7" align="center" valign="middle">Page URL: <a target="_blank" href="<?php echo SITE_URL."content.php?id=".$row['content_id']; ?>"><?php echo SITE_URL."content.php?id=".$row['content_id']; ?></a></td>
          </tr>
		  <?php } ?>
          <tr>
            <td align="right" valign="top">
				Last modified: <span class="date"><?php echo $row['modify_date']; ?></span><br/><br/>
				Page name: <b><?php echo $row['name']; ?></b><br/>
				<?php if ($row['content_id'] > 5) { ?>
					<p>Also you can show this content on any page by using following code:</p>
					<?php 
						highlight_string('<?php $content = GetContent(\''.$row['name'].'\'); ?>
								<h1><?php echo $content[\'title\']; ?></h1>
								<p><?php echo $content[\'text\']; ?></p>
								');
					?>
				<?php } ?>
			</td>
          </tr>
          <tr>
            <td align="center" valign="bottom">
			<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='content.php'" />
		  </td>
          </tr>

          </table>

      <?php }else{ ?>
				<div class="info_box">Sorry, no content found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>