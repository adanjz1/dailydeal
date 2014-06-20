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


	$pn = (int)$_GET['pn'];


	// delete images
	if (isset($_GET['del_img']) && is_numeric($_GET['del_img']))
	{
		$image_id = (int)$_GET['del_img'];
		DeleteImage($image_id);
		$img_deleted = 1;
	}


	// upload images
	if (isset($_POST["action"]) && $_POST["action"] == "upload_images")
	{
		$item_id		= (int)getPostParameter('item_id');
		$upload_dir		= PUBLIC_HTML_PATH.IMAGES_URL;

		$rnumber = mt_rand(1,10000).time();
		$ee = 0;

			while(list($key,$value) = each($_FILES['item_image']['name']))
			{
				if (is_uploaded_file($_FILES['item_image']['tmp_name'][$key]))
				{
					$ee ++;
					
					if ($key == 0) $img_id = "main"; else $img_id = "#".$key;

					list($width, $height, $type) = getimagesize($_FILES['item_image']['tmp_name'][$key]);

					if (!getimagesize($_FILES['item_image']['tmp_name'][$key]))
					{
						$errormsg = "Only image uploads are allowed";
					}
					elseif ($width < MIN_IMAGE_WIDTH || $height < MIN_IMAGE_HEIGHT)
					{
						$errormsg = "Too low $img_id image dimension. Please upload a larger image.";
					}
					elseif ($_FILES['item_image']['size'][$key] > 2097152)
					{
						$errormsg = "The $img_id image file size is too big. It exceeds 2Mb.";
					}
					elseif (preg_match('/\\.(gif|jpg|png|jpeg)$/i', $_FILES['item_image']['name'][$key]) != 1)
					{
						$errormsg = "Please upload a JPEG, PNG, or GIF image";
						unlink($_FILES['item_image']['tmp_name'][$key]);
					}
					else
					{
						$img_path = $upload_dir.$_FILES['item_image']['name'][$key];
						
						if ($ee == 1)
						{
							$random_num = $rnumber;
						}
						else
						{
							$random_num = $rnumber."_".$ee;
						}
							
						$new_img_name			= "deal_".$random_num.".jpg";
						$new_medium_img_name	= "deal_".$random_num."_medium.jpg";
						$new_thumb_name			= "deal_".$random_num."_thumb.jpg";

						$new_img_path			= $upload_dir.$new_img_name;
						$new_medium_img_path	= $upload_dir.$new_medium_img_name;
						$img_thumb_path			= $upload_dir.$new_thumb_name;

						create_thumb($_FILES['item_image']['tmp_name'][$key],$img_thumb_path, THUMB_WIDTH, THUMB_HEIGHT);
						resize_img($_FILES['item_image']['tmp_name'][$key],$new_medium_img_path, false, MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT);
						resize_img($_FILES['item_image']['tmp_name'][$key],$new_img_path);

						$item_images[]			= $new_img_name;
						$item_medium_images[]	= $new_medium_img_name;
						$item_thumb_images[]	= $new_thumb_name;
					}
				}

			}

			if (count($item_images) > 0)
			{
				foreach ($item_images as $k=>$img_name)
				{
					$check_main_img = smart_mysql_query("SELECT * FROM abbijan_item_images WHERE item_id='$item_id' AND main_image='1'");
					if (mysql_num_rows($check_main_img) == 0)
					{
						smart_mysql_query("INSERT INTO abbijan_item_images SET item_id='$item_id', image='$img_name', thumb_image='".$item_thumb_images[$k]."', medium_image='".$item_medium_images[$k]."', main_image='1'");
						smart_mysql_query("UPDATE abbijan_items SET image='$img_name', thumb='".$item_thumb_images[$k]."' WHERE item_id='$item_id'");
					}
					else
					{
						smart_mysql_query("INSERT INTO abbijan_item_images SET item_id='$item_id', image='$img_name', thumb_image='".$item_thumb_images[$k]."', medium_image='".$item_medium_images[$k]."'");
					}
				}

				header("Location: deal_edit.php?id=$item_id&msg=uploaded");
				exit();
			}

		}



if (isset($_POST["action"]) && $_POST["action"] == "edit")
{
		unset($errors);
		$errors = array();

		$item_id			= (int)getPostParameter('item_id');
		$deal_title			= mysql_real_escape_string(getPostParameter('deal_title'));
		$deal_type			= mysql_real_escape_string(getPostParameter('deal_type'));
		$affiliate_link		= mysql_real_escape_string(trim($_POST['affiliate_link']));

		$category			= array();
		$category			= $_POST['category_id'];

		$retail_price		= mysql_real_escape_string(getPostParameter('retail_price'));
		$price				= mysql_real_escape_string(getPostParameter('price'));
		if ($retail_price != "") $discount = $retail_price - $price;
		$condition			= mysql_real_escape_string(getPostParameter('condition'));
		$quantity			= mysql_real_escape_string(getPostParameter('quantity'));
		$customer_limit		= mysql_real_escape_string(getPostParameter('customer_limit'));
		$start_date			= mysql_real_escape_string(getPostParameter('start_date'));
		$start_time			= mysql_real_escape_string(getPostParameter('start_time'));
		$end_date			= mysql_real_escape_string(getPostParameter('end_date'));
                if($end_date == "") $end_date = '2099-12-31';
		$end_time			= mysql_real_escape_string(getPostParameter('end_time'));
		if ($end_time == "") $end_time = "00:00";
		$sale_start_date	= $start_date." ".$start_time;
		$sale_end_date		= $end_date." ".$end_time;
		$brief_description	= mysql_real_escape_string($_POST['brief_description']);
		$description		= mysql_real_escape_string($_POST['description']);
		$specs				= mysql_real_escape_string($_POST['specs']);
		$youtube_video		= mysql_real_escape_string(getPostParameter('youtube_video'));
		$meta_description	= mysql_real_escape_string(getPostParameter('meta_description'));
		$meta_keywords		= mysql_real_escape_string(getPostParameter('meta_keywords'));
		$featured			= (int)getPostParameter('featured');
		$main_deal			= (int)getPostParameter('main_deal');
		$allow_comments		= (int)getPostParameter('allow_comments');
		$status				= mysql_real_escape_string(getPostParameter('status'));


		if (!($deal_title && $price  && $description && $status))
		{
			$errors[] = "Please ensure that all fields marked with an asterisk are complete";
		}
		elseif ($deal_type == "")
		{
			$errors[] = "Please select deal type";
		}
		else
		{
			if (isset($deal_type) && $deal_type == "affiliate")
			{
				if (!$affiliate_link || $affiliate_link == "")
					$errors[] = "Please enter your affiliate link";

				if (substr($affiliate_link, 0, 7) != 'http://')
				{
					$errors[] = "Enter correct affiliate link format, enter the 'http://' statement before your link";
				}
				elseif ($affiliate_link == 'http://')
				{
					$errors[] = "Please enter correct affiliate link";
				}	
			}

			if (strlen($deal_title) < 3)
			{
				$errors[] = "Too short deal title";
			}

			if (!(is_numeric($price) && $price > 0))
			{
				$errors[] = "Please enter correct deal price";
				$price = "0.00";
			}

			if (isset($retail_price) && $retail_price != "" && !(is_numeric($retail_price) && $retail_price > 0))
			{
				$errors[] = "Please enter correct retail price";
				$retail_price = "0.00";

				if ($price >= $retail_price)
				{
					$errors[] = "Deal Price must be lower than Retail Price";
				}
			}

			if (isset($youtube_video) && $youtube_video != "" && (strpos($youtube_video, 'http://www.youtube.com/watch?v=') === false))
			{
				$errors[] = "Please enter correct youtube link";
			}

			if (isset($quantity)  && $quantity != "" && !(is_numeric($quantity) && $quantity >= 0))
			{
				$errors[] = "Please enter correct quantity";
			}

			if (isset($customer_limit)  && $customer_limit != "" && !(is_numeric($customer_limit) && $customer_limit >= 0))
			{
				$errors[] = "Please enter correct customer limit value";
			}

			if ($sale_start_date >= $sale_end_date)
			{
				$errors[] = "Deal start date must be less than end date";
			}
			else
			{
				// change deal status from 'expired' to 'active'
				if ($status == "expired" && $sale_end_date > date('Y-m-d H:i:s')) $status = "active";
				// change deal status to 'expired'
				if ($status != "expired" && $sale_end_date <= date('Y-m-d H:i:s')) $status = "expired";
			}

			if ($main_deal == 1)
			{
				$check_query = smart_mysql_query("SELECT * FROM abbijan_items WHERE item_id<>'$item_id' AND start_date<=NOW() AND end_date>NOW() AND main_deal='1' AND status='active'");
				if (mysql_num_rows($check_query) != 0)
				{
					$errors[] = "Sorry, you can mark just one deal as main.";
				}
			}
		}


		if (count($errors) == 0)
		{
			smart_mysql_query("UPDATE abbijan_items SET title='$deal_title', deal_type='$deal_type', url='$affiliate_link', quantity='$quantity', retail_price='$retail_price', price='$price', discount='$discount', customer_limit='$customer_limit', start_date='$sale_start_date', end_date='$sale_end_date', conditions='$condition', brief_description='$brief_description', description='$description', specs='$specs', youtube_video='$youtube_video', meta_description='$meta_description', meta_keywords='$meta_keywords', featured='$featured', main_deal='$main_deal', allow_comments='$allow_comments', status='$status' WHERE item_id='$item_id'");

			if (count($category) > 0)
			{
				smart_mysql_query("DELETE FROM abbijan_item_to_category WHERE item_id='$item_id'");
			
				foreach ($category as $cat_id)
				{
					$cats_insert_sql = "INSERT INTO abbijan_item_to_category SET item_id='$item_id', category_id='".(int)$cat_id."'";
					smart_mysql_query($cats_insert_sql);
				}
			}

			//update post in to discussions block
			//smart_mysql_query("UPDATE abbijan_forums SET title='$deal_title', disscusssion='$description', updated=NOW() WHERE item_id='$item_id' LIMIT 1");

			header("Location: deals.php?msg=updated");
			exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}

}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id	= (int)$_GET['id'];

		$query = "SELECT * FROM abbijan_items WHERE item_id='$id' LIMIT 1";
		$rs	= smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	$title = "Edit Deal";
	require_once ("inc/header.inc.php");

?>


    <h2>Edit Deal</h2>

	<?php if ($total > 0) {
		
		$row = mysql_fetch_array($rs);

	?>

		<script type="text/javascript">
		<!--
			function hiddenDiv(id,showid){
				if(document.getElementById(id).value == "affiliate"){
					document.getElementById(showid).style.display = "";
				}else{
					document.getElementById(showid).style.display = "none";
				}
			}
		-->
		</script>


	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div style="width: 90%" class="error_box"><?php echo $errormsg; ?></div>
	<?php } ?>

<div style="position: relative">
	<div style="width: 270px; background: #F9F9F9; border: 1px dotted #EEE; padding: 5px; position: absolute; top: 0; right: 0;">
	<center><b>Deal Images</b></center>
	<!--
	<?php if (isset($_GET['msg']) && $_GET['msg'] == "uploaded") { ?><div style="width: 100%; background: #71E512; border: 1px dotted #4EA506; color: #FFF; text-align: center; padding: 3px; margin: 3px 0;">Images uploaded successfully</div><?php } ?>
	<?php if (isset($img_deleted) && $img_deleted == 1) { ?><div style="width: 100%; background: #71E512; border: 1px dotted #4EA506; color: #FFF; text-align: center; padding: 3px; margin: 3px 0;">Image was deleted</div><?php } ?>
	-->
	<div style="margin: 5px;">
		<?php
				$iresult = smart_mysql_query("SELECT * FROM abbijan_item_images WHERE item_id='".$row['item_id']."' ORDER BY main_image DESC, item_image_id");
				if (mysql_num_rows($iresult) > 0)
				{		
					while ($irow = mysql_fetch_array($iresult))
					{
						if ($irow['main_image'] == 1)
							echo "<div style='text-align: center; position: relative; '><a href='".IMAGES_URL.$irow['image']."' rel='group'><img src='".IMAGES_URL.$irow['image']."' width='110' height='110' class='thumb' /></a> <a href='?id=".$row['item_id']."&del_img=".$irow['item_image_id']."'><img src='images/icons/delete.png' style='display: block; position: absolute; bottom: 5px; right:65px;' /></a></div>";
						else
							echo "<div style='width: 50px; float: left; position: relative; margin-right: 2px;'><a href='".IMAGES_URL.$irow['image']."' rel='group'><img src='".IMAGES_URL.$irow['thumb_image']."' width='40' height='40' class='thumb' /></a> <a href='?id=".$row['item_id']."&del_img=".$irow['item_image_id']."'><img src='images/icons/delete.png' style='display: block; position: absolute; bottom: 3px; right: 0;' /></a></div>";
						}
				}
				else
				{
						echo "<center>There are no uploaded images!</center>";
				}
		?>
		</div>
		<div style="clear: both"></div>
			<form action="" method="post" name="form2" enctype="multipart/form-data">
			<table bgcolor="#F7F7F7" cellpadding="2" cellspacing="3" border="0" align="center">
			<?php 
					$check_main_img = smart_mysql_query("SELECT * FROM abbijan_item_images WHERE item_id='$id' AND main_image='1'");
					if (mysql_num_rows($check_main_img) == 0) {
			?>
			<tr>
				<td style="background: #FCBEB0; border: 1px solid #FFB4A3; color: #FFF; padding: 5px;" bgcolor="" colspan="2" valign="middle" align="center">
					<p>Please upload deal's main image!</p>
					Main:
					<input type="file" name="item_image[]" class="textbox" size="15" /><!--<span class="note">Min Size <?php echo MIN_IMAGE_WIDTH."x".MIN_IMAGE_HEIGHT; ?> px</span>--></td>
			</tr>
			<?php } ?>
			<?php for ($e=0; $e<3; $e++) { ?>
			<tr>
				<td valign="middle" align="right" class="tb1">Image:</td>
				<td valign="top"><input type="file" name="item_image[]" class="textbox" size="15" /></td>
			</tr>
			<?php } ?>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="hidden" name="item_id" id="item_id" value="<?php echo (int)$row['item_id']; ?>" />
				<input type="hidden" name="action" id="action" value="upload_images">
				<input type="submit" class="submit" name="update" id="update" value="Upload images" />
              </td>
            </tr>
			</table>
			</form>
	</div>
	
	
		<form action="" method="post" name="form1">
        <table width="100%" cellpadding="2" cellspacing="5" border="0" align="center">
          <tr>
            <td width="70" valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td valign="top"><input type="text" name="deal_title" id="deal_title" value="<?php echo $row['title']; ?>" size="60" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Type:</td>
            <td valign="top">
				<select name="deal_type" id="deal_type" onchange="javascript:hiddenDiv('deal_type','affiliate_link')">
					<option value="own" <?php if ($row['deal_type'] == "own") echo "selected='selected'"; ?>>Own Product</option>
					<option value="affiliate" <?php if ($row['deal_type'] == "affiliate") echo "selected='selected'"; ?>>Affiliate Product</option>
				</select>
			</td>
          </tr>
          <tr id="affiliate_link" <?php if ($deal_type != "affiliate" && $row['deal_type'] != "affiliate") { ?>style="display: none;" <?php } ?>>
			<td valign="middle" align="right" class="tb1"><span class="req">* </span>Affiliate Link:</td>
			<td valign="top"><input type="text" name="affiliate_link" value="<?php echo $row['url']; ?>" size="60" class="textbox" />&nbsp; <small class="note">Your affiliate link</small></td>
          </tr>
		  <?php if (GetCategoriesTotal() > 0) { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Category:</td>
            <td valign="top">
				<div class="scrollbox">
				<?php

					unset($deal_cats);
					$deal_cats = array();

					$sql_deal_cats = smart_mysql_query("SELECT category_id FROM abbijan_item_to_category WHERE item_id='$id'");		
					
					while ($row_deal_cats = mysql_fetch_array($sql_deal_cats))
					{
						$deal_cats[] = $row_deal_cats['category_id'];
					}

					$allcategories = array();
					$allcategories = CategoriesList(0);
					foreach ($allcategories as $category_id => $category_name)
					{
						$cc++;
						if (is_array($deal_cats) && in_array($category_id, $deal_cats)) $checked = 'checked="checked"'; else $checked = '';

						if (($cc%2) == 0)
							echo "<div class=\"even\"><input name=\"category_id[]\" value=\"".(int)$category_id."\" ".$checked." type=\"checkbox\">".$category_name."</div>";
						else
							echo "<div class=\"odd\"><input name=\"category_id[]\" value=\"".(int)$category_id."\" ".$checked." type=\"checkbox\">".$category_name."</div>";
					}

				?>
				</div>
			</td>
			</tr>
			<?php } ?>
			<tr>
				<td valign="middle" align="right" class="tb1">Retail Price:</td>
				<td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="retail_price" id="retail_price" value="<?php echo ($row['retail_price'] != "0.0000") ? $row['retail_price'] : ""; ?>" size="7" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1"><span class="req">* </span>Price:</td>
				<td valign="top"><?php echo SITE_CURRENCY; ?> <input type="text" name="price" id="price" value="<?php echo $row['price']; ?>" size="7" class="textbox" /></td>
			</tr>
<script>
    $(function() {
        $('#start_date').calendricalDate();
        $('#end_date').calendricalDate();
        $('#start_time').calendricalTime({
            minTime: {hour: 0, minute: 0},
            maxTime: {hour: 23, minute: 59},
            timeInterval: 30
        });
        $('#end_time').calendricalTime({
            minTime: {hour: 0, minute: 0},
            maxTime: {hour: 23, minute: 59},
            timeInterval: 30
        })
    });
</script>
            <tr>
				<td valign="middle" align="right" class="tb1">Sale Start Date:</td>
				<td valign="middle"><input type="text" name="start_date" id="start_date" value="<?php echo substr($row['start_date'], 0, 10); ?>" size="10" maxlength="10" class="textbox" />&nbsp; <input type="text" name="start_time" id="start_time" value="<?php echo substr($row['start_date'], -8, 5); ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Sale End Date:</td>
				<td valign="middle"><input type="text" name="end_date" id="end_date" value="<?php echo substr($row['end_date'], 0, 10); ?>" size="10" maxlength="10" class="textbox" />&nbsp; <input type="text" name="end_time" id="end_time" value="<?php echo substr($row['end_date'], -8, 5); ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">&nbsp;</td>
				<td colspan="2" valign="middle"><img src="images/icons/small_time.png" align="absmiddle" /> <span style="font-size: 10px; color:#838383;">Current Site Date/Time: <?php $server_time = mysql_fetch_array(smart_mysql_query("SELECT CURRENT_TIMESTAMP;")); echo substr($server_time['CURRENT_TIMESTAMP'], 0, -3); ?></span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Quantity:</td>
				<td valign="middle"><input type="text" name="quantity" id="quantity" value="<?php echo (int)$row['quantity']; ?>" size="2" class="textbox" /><span class="note">0 means unlimited</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Limit per customer:</td>
				<td valign="middle"><input type="text" name="customer_limit" id="customer_limit" value="<?php echo (int)$row['customer_limit']; ?>" size="2" class="textbox" /><span class="note">0 means no limit</span></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Condition:</td>
				<td valign="middle"><input type="text" name="condition" id="condition" value="<?php echo $row['conditions']; ?>" size="10" class="textbox" /><span class="note">Used, New, etc</span></td>
            </tr>
 			<tr>
 				<td valign="middle" align="right" class="tb1">Youtube video:</td>
				<td valign="middle"><input type="text" name="youtube_video" id="youtube_video" value="<?php echo $row['youtube_video']; ?>" size="45" class="textbox" /><span class="note">e.g: http://www.youtube.com/watch?v=wJhSnb789gk</span></td>
            </tr>
			<tr>
 				<td colspan="2" valign="top" align="left" class="tb1">
					Brief Description:<br/>
					<textarea cols="80" id="editor1" name="brief_description" rows="10"><?php echo stripslashes($row['brief_description']); ?></textarea>
				</td>
             </tr>
             <tr>
 				<td colspan="2" valign="top" align="left" class="tb1">
					<span class="req">* </span>Description:<br/>
					<textarea cols="80" id="editor2" name="description" rows="10"><?php echo stripslashes($row['description']); ?></textarea>
				</td>
             </tr>
             <tr>
 				<td colspan="2" valign="top" align="left" class="tb1">
					Specs:<br/>
					<textarea cols="80" id="editor3" name="specs" rows="10"><?php echo stripslashes($row['specs']); ?></textarea>
				</td>
             </tr>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor1' );
					CKEDITOR.replace( 'editor2' );
					CKEDITOR.replace( 'editor3' );
				</script>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="115" rows="2" class="textbox2"><?php echo strip_tags($row['meta_description']); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo $row['meta_keywords']; ?>" size="118" class="textbox" /></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Featured?</td>
				<td valign="middle"><input type="checkbox" class="checkboxx" name="featured" value="1" <?php if ($row['featured'] == 1) echo "checked=\"checked\""; ?> /> Yes!</td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Main Deal?</td>
				<td valign="middle"><input type="checkbox" class="checkboxx" name="main_deal" value="1" <?php if ($row['main_deal'] == 1) echo "checked=\"checked\""; ?> /> Yes! <span class="note" title="site's homepage main deal"></span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Allow Comments?</td>
				<td valign="middle"><input type="checkbox" class="checkboxx" name="allow_comments" value="1" <?php if ($row['allow_comments'] == 1) echo "checked=\"checked\""; ?> /> Yes!</td>
            </tr>
            <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status" id="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
					<option value="expired" <?php if ($row['status'] == "expired") echo "selected"; ?>>expired</option>
					<option value="sold" <?php if ($row['status'] == "sold") echo "selected"; ?>>sold out</option>
				</select>
			</td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="hidden" name="item_id" id="item_id" value="<?php echo (int)$row['item_id']; ?>" />
				<input type="hidden" name="action" id="action" value="edit">
				<input type="submit" class="submit" name="update" id="update" value="Update" />&nbsp;&nbsp;
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='deals.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
              </td>
            </tr>
          </table>
		  </form>
</div>


      <?php }else{ ?>
				<div class="info_box">Sorry, no deal found.</div>
				<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>