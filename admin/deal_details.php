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
	require_once("../inc/pagination.inc.php");
	require_once("./inc/adm_functions.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$pn			= (int)$_GET['pn'];
		$item_id	= (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(added, '%d %b %Y %h:%i %p') AS date_added, DATE_FORMAT(start_date, '%d %b %Y %h:%i') AS sale_start_date, DATE_FORMAT(end_date, '%d %b %Y %h:%i') AS sale_end_date, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM abbijan_items WHERE item_id='$item_id' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Deal Details";
	require_once ("inc/header.inc.php");

?>
    
     <h2>Deal Details</h2>

	 <?php if ($total > 0) {
	
		 $row = mysql_fetch_array($result);

	 ?>

		<?php if ($row['featured'] == 1) { ?><img src="images/icons/featured-large.png" alt="Featured" title="Featured" align="right" /><?php } ?>
		<?php if ($row['main_deal'] == 1) { ?><span style="font-size:11px; font-weight:bold; float:right;"><img src="images/icons/main_deal.png" align="absmiddle" /> Main Deal</span><?php } ?>

		<div style="float: left; width: 55%">
        <table width="100%" cellpadding="3" cellspacing="6" border="0" align="center">
			<tr>
				<td valign="top" align="left" class="tb1">Deal ID:</td>
				<td align="left" valign="top"><?php echo $row['item_id']; ?></td>
            </tr>			
			<tr>
				<td valign="top" align="left" class="tb1">Title:</td>
				<td align="left" valign="top"><b><?php echo $row['title']; ?></b></td>
            </tr>
			<tr>
				<td valign="top" align="left" class="tb1">Deal Type:</td>
				<td align="left" valign="top"><?php if ($row['deal_type'] == "affiliate") echo "Affiliate link"; elseif ($row['deal_type'] == "own") echo "Own product"; ?></td>
            </tr>
			<?php if ($row['deal_type'] == "affiliate") { ?>
			<tr>
				<td valign="middle" align="left" class="tb1">Affiliate Link:</td>
				<td align="left" valign="middle"><input type="text" class="textbox" size="65" style="background: #F7F7F7;" readonly="readonly" onfocus="this.select();" onclick="this.focus();this.select();" value="<?php echo $row['url']; ?>" /> <img src="images/icons/url.png" /></td>
            </tr>
			<?php } ?>
			<?php if (GetDealCategory($row['item_id'])) { ?>
			<tr>
				<td width="70" valign="middle" align="left" class="tb1">Category:</td>
				<td align="left" valign="middle"><?php echo GetDealCategories($row['item_id']); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td valign="top" align="left" class="tb1">Quantity:</td>
				<td align="left" valign="top"><?php if ($row['quantity'] == 0) echo "unlimited"; else echo GetDealQuantity($row['item_id']); ?></td>
            </tr>
			<tr>
				<td valign="top" align="left" class="tb1">Limit per customer:</td>
				<td align="left" valign="top"><?php if ($row['quantity'] == 0) echo "no limit"; else echo $row['customer_limit']; ?></td>
            </tr>
			<tr>
				<td valign="middle" align="left" class="tb1">Price:</td>
				<td align="left" valign="middle">
					<?php if ($row['retail_price'] != "0.0000") { ?><span class="retail_price"><?php echo DisplayMoney($row['retail_price']); ?></span><?php } ?>
					<span class="price"><?php echo DisplayMoney($row['price']); ?></span>
				</td>
            </tr>
			<tr>
				<td valign="top" align="left" class="tb1">Start Date:</td>
				<td align="left" valign="top"><?php echo ($row['start_date'] == "0000-00-00 00:00:00") ? "---" : $row['sale_start_date']; ?></td>
            </tr>
			<tr>
				<td valign="top" align="left" class="tb1">End Date:</td>
				<td align="left" valign="top"><?php echo $row['sale_end_date']; ?></td>
            </tr>
			<tr>
				<td valign="top" align="left" class="tb1">Ends in:</td>
				<td align="left" valign="top"><?php echo GetTimeLeft($row['time_left']); ?></td>
            </tr>
			<tr>
				<td valign="top" align="left" class="tb1">Views:</td>
				<td align="left" valign="top"><?php echo number_format($row['views']); ?></td>
            </tr>
			<?php if ($row['visits'] > 0) { ?>
			<tr>
				<td valign="top" align="left" class="tb1">Visits:</td>
				<td align="left" valign="top"><?php echo number_format($row['visits']); ?></td>
            </tr>
			<?php }else{ ?>
			<tr>
				<td valign="top" align="left" class="tb1">Sales:</td>
				<td align="left" valign="top"><?php echo GetDealSalesTotal($row['item_id']); ?></td>
            </tr>
			<?php } ?>
			<?php if ($row['conditions'] != "") { ?>
			<tr>
				<td valign="top" align="left" class="tb1">Condition:</td>
				<td align="left" valign="top"><?php echo $row['conditions']; ?></td>
            </tr>
			<?php } ?>
			<?php if ($row['brief_description'] != "") { ?>
			<tr>
				<td colspan="2" valign="top" align="left">
					Brief Description:<br/>
					<div style="width:700px; min-height: 80px; background: #F9F9F9; padding: 5px;"><?php echo stripslashes($row['brief_description']); ?></div>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="2" valign="top" align="left">
					Description:<br/>
					<div style="width:700px; min-height: 150px; background: #F9F9F9; padding: 5px;"><?php echo stripslashes($row['description']); ?></div>
				</td>
			</tr>
			<?php if ($row['specs'] != "") { ?>
			<tr>
				<td colspan="2" valign="top" align="left">
					Specs:<br/>
					<div style="width:700px; min-height: 80px; background: #F9F9F9; padding: 5px;"><?php echo stripslashes($row['specs']); ?></div>
				</td>
			</tr>
			<?php } ?>
			<?php if ($row['youtube_video'] != "") { ?>
			<tr>
				<td valign="top" align="left" class="tb1">Youtube video:</td>
				<td align="left" valign="middle"><a href="#"><?php echo $row['youtube_video']; ?></a></td>
			</tr>
			<?php } ?>
			<?php if ($row['meta_description'] != "") { ?>
			<tr>
				<td valign="top" align="left" class="tb1">Meta Description:</td>
				<td align="left" valign="top"><?php echo $row['meta_description']; ?></td>
			</tr>
			<?php } ?>
			<?php if ($row['meta_keywords'] != "") { ?>
			<tr>
				<td valign="top" align="left" class="tb1">Meta Keywords:</td>
				<td align="left" valign="top"><?php echo $row['meta_keywords']; ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td valign="top" align="left" class="tb1">Allow Comments?</td>
				<td align="left" valign="middle"><?php echo ($row['allow_comments'] == 1) ? "<img src='./images/icons/yes.png'>" : "<img src='./images/icons/no.png'>"; ?></td>
			</tr>
			<tr>
				<td valign="top" align="left" class="tb1">Added:</td>
				<td align="left" valign="middle"><?php echo $row['date_added']; ?></td>
			</tr>
            <tr>
				<td valign="middle" align="left" class="tb1">Status:</td>
				<td align="left" valign="middle">
					<?php
						switch ($row['status'])
					  {
							case "active": echo "<span class='active_s'>".$row['status']."</span>"; break;
							case "inactive": echo "<span class='inactive_s'>".$row['status']."</span>"; break;
							case "expired": echo "<span class='expired_status'>".$row['status']."</span>"; break;
							case "sold": echo "<span class='sold_status'>".$row['status']."</span>"; break;
							default: echo "<span class='default_status'>".$row['status']."</span>"; break;
						}
					?>
				</td>
            </tr>
          </table>
		</div>
		<div style="width: 290px; float: right; text-align: center;">
			<?php
				$iresult = smart_mysql_query("SELECT * FROM abbijan_item_images WHERE item_id='".$row['item_id']."' ORDER BY main_image DESC, item_image_id");
				if (mysql_num_rows($iresult) > 0)
				{		
					while ($irow = mysql_fetch_array($iresult))
					{
						if ($irow['main_image'] == 1)
							echo "<a href='".IMAGES_URL.$irow['image']."' rel='group'><img src='".IMAGES_URL.$irow['image']."' width='150' height='150' class='thumb' /></a><br/>";
						else
							echo "<a href='".IMAGES_URL.$irow['image']."' rel='group'><img src='".IMAGES_URL.$irow['thumb_image']."' width='40' height='40' class='thumb' /></a>";
					}
				}
			?>
		</div>
		<div style="clear: both"></div>
		
		<p align="center">
			<input type="button" class="submit" name="edit" value="Edit Deal" onClick="javascript:document.location.href='deal_edit.php?id=<?php echo $row['item_id']; ?>&page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" /> &nbsp; 
			<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='deals.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
		</p>


	  <?php }else{ ?>
				<div class="info_box">Sorry, no deal found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>



<?php require_once ("inc/footer.inc.php"); ?>